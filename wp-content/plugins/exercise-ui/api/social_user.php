<?php


use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

class Social_User
{

	public function __init()
	{
		add_filter('json_endpoints', array($this, 'register_routes'));
		add_action('delete_user', array($this, '__user_delete'));
	}

	public function register_routes()
	{
		register_rest_route(
			'wp/v2',
			'/social-login',
			array(
				'methods' => 'POST',
				'callback' => array($this, '__social_login'),
				'permission_callback' => '__return_true',
			)
		);

	}

	public function __social_login(WP_REST_Request $request)
	{
		$access_token = $request->get_param('accessToken');

		$id_token = $request->get_param('idToken');

		$identityToken = $request->get_param('identityToken');

		$token = '';

		$type = 0;


		if (!$access_token && !$id_token && !$identityToken) {
			return new WP_Error('missing_access_token', 'Missing token parameter.', array('status' => 400));
		}

		if (!empty($id_token)) {
			$type = 1;
			$token = $id_token;
		}

		if (!empty($access_token)) {
			$type = 2;
			$token = $access_token;
		}

		if (!empty($identityToken)) {
			$ios = $this->check_ios($identityToken);

			if(is_array($ios) && !empty($ios['error'])){
				return new WP_Error('error_token', $ios['error'], array('status' => 401));
			}
		}

		$google_user_info = array();


		if(!empty($identityToken)){
			$google_user_info['email'] = $ios->email;
			$google_user_info['name'] = $ios->sub;
			$type = 3;
		}else {
			$url = $this->retrieve_url($type);

			$google_user_info = $this->validate_social_access_token($type, $url, $token);
		}

		if (isset($google_user_info['error_description'])) {
			return new WP_Error('invalid_access_token', 'Invalid access token.', array('status' => 401));
		}
		
		$token = $this->handle_user_data($google_user_info,$type);

		if (!empty($token['error'])) {
			return new WP_Error('error_token', 'Invalid access token.', array('status' => 401));
		}
		$data = [
			'status' => 200,
			'data' => $token
		];

		return $data;
	}

	private function check_ios($identityToken)
	{

		$appleKeysUrl = "https://appleid.apple.com/auth/keys";
		$appleKeys = json_decode(file_get_contents($appleKeysUrl), true);

		$publicKeys = JWK::parseKeySet($appleKeys);
		try {
		$jwt = JWT::decode($identityToken, $publicKeys, ['RS256']);

		}catch (Exception $e) {
			$jwt['error'] = $e->getMessage();
		}

		return $jwt;

	}

	private function validate_social_access_token($type, $url, $access_token)
	{

		if ($type == 2) {
			$response = wp_remote_get($url, array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $access_token
				)
			)
			);
		} elseif ($type == 1) {
			$response = wp_remote_get($url . $access_token);
		}

		if (is_wp_error($response)) {
			return is_wp_error($response);
		}

		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, true);


		return $data;
	}

	private function retrieve_url($type)
	{
		if ($type == 2) {
			return "https://graph.facebook.com/v20.0/me";
		} elseif ($type == 1) {
			return "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=";
		}
	}

	private function handle_user_data($data, $type = 0)
	{

		$token = array();

		if (empty($data['email'])) {
			$token['error'] = "Something went wrong, cannot retrieve email for this token";
		}

		if (empty($data['name'])) {
			$token['error'] = "Something went wrong, cannot retrieve username for this token";
		}

		$email = $data['email'];

		$username = $data['name'];

		$random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);

		if($type == 3) {
			$user = get_user_by('login', $username);
		}
		else {
			$user = get_user_by('email', $email);
			$username = $email;
		}

		if (empty($user)) {
			wp_create_user($username, $random_password, $email);
		} else {
			wp_set_password($random_password, $user->ID);
		}

		$token = $this->generate_jwt_token($email, $random_password);

		return $token;
	}

	private function generate_jwt_token($username, $password)
	{
		// Create a new WP_REST_Request object to use the generate_token function from JWT Authentication plugin
		$request = new WP_REST_Request('POST', '/wp-json/jwt-auth/v1/token');
		$request->set_param('email', $username);
		$request->set_param('password', $password);


		if (!class_exists('JWT_AUTH')) {
			require_once plugin_dir_path(__FILE__) . 'jwt-authentication-for-wp-rest-api/public/class-jwt-auth-public.php'; // Ensure you have the JWT plugin included.
		}

		// Get the JWT Authentication Controller instance
		$jwt_token = new Jwt_Auth_Public("jwt-authentication-for-wp-rest-api", "1.3.4");


		$token = $jwt_token->generate_token($request);


		// Check for errors
		if (is_wp_error($token)) {
			return $token;
		}

		// Return the token
		return $token;
	}

}

// Register the custom controller
add_action('rest_api_init', function () {
	$controller = new Social_User();
	$controller->register_routes();
});