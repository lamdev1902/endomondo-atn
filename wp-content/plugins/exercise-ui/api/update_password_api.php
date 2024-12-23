<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Custom_Update_Password_Controller extends WP_REST_Controller
{
    public function register_routes()
    {
        register_rest_route(
            'wp/v2',
            '/change-password-by-token',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'update_password_by_token_callback'),
                'permission_callback' => '__return_true', // Không cần xác thực
            )
        );

        register_rest_route(
            'wp/v2',
            '/change-password',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'update_password_callback'),
                'permission_callback' => '__return_true', // Không cần xác thực
            )
        );
    }

    public function update_password_by_token_callback(WP_REST_Request $request)
    {
        $email = $request->get_param('email');
        $old_password = $request->get_param('old_password');
        $new_password = $request->get_param('new_password');
        $token = $request->get_header('Authorization');

        if (!$token) {
            error_log('No token provided.');
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        if (empty($email) || empty($old_password) || empty($new_password)) {
            return new WP_Error('missing_fields', 'Email, old password, và new password is required.', array('status' => 400));
        }

        $user = get_user_by('email', $email);
        if (!$user) {
            return new WP_Error('user_not_found', 'User not found.', array('status' => 404));
        }

        if (!wp_check_password($old_password, $user->user_pass, $user->ID)) {
            return new WP_Error('incorrect_password', 'Old password is incorrect.', array('status' => 403));
        }

        $token_response = $this->generate_jwt_token($token);

        if (is_wp_error($token_response)) {
            return $token_response;
        }

        wp_set_password($new_password, $user->ID);

        $response = [
            "message" => "Password updated successfully.",
            "status" => 200
        ];

        // Thông báo thành công
        return new WP_REST_Response($response, 200);
    }

    public function update_password_callback(WP_REST_Request $request)
    {
        $email = $request->get_param('email');
        $new_password = $request->get_param('new_password');

        if (empty($email) || empty($new_password)) {
            return new WP_Error('missing_fields', 'Email, old password, và new password is required.', array('status' => 400));
        }

        $user = get_user_by('email', $email);

        if (!$user) {
            return new WP_Error('user_not_found', 'User not found.', array('status' => 404));
        }

        wp_set_password($new_password, $user->ID);

        $response = [
            "message" => "Password updated successfully.",
            "status" => 200
        ];

        // Thông báo thành công
        return new WP_REST_Response($response, 200);
    }

    private function generate_jwt_token($token)
    {
        $request = new WP_REST_Request('POST', '/wp-json/jwt-auth/v1/token/validate');
        $request->set_header('Authorization', $token);


        if (!class_exists('JWT_AUTH')) {
            require_once plugin_dir_path(__FILE__) . 'jwt-authentication-for-wp-rest-api/public/class-jwt-auth-public.php'; // Ensure you have the JWT plugin included.
        }
    
        // Get the JWT Authentication Controller instance
        $jwt_token = new Jwt_Auth_Public("jwt-authentication-for-wp-rest-api", "1.3.4");

        
        $token = $jwt_token->validate_token($request);


        // Check for errors
        if (is_wp_error($token)) {
            return $token;
        }

        // Return the token
        return $token;
    }
}

// Đăng ký route trong REST API
add_action('rest_api_init', function () {
    $controller = new Custom_Update_Password_Controller();
    $controller->register_routes();
});
