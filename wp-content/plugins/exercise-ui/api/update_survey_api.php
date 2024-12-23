<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Custom_Update_Survey_Controller extends WP_REST_Controller
{
    public function register_routes()
    {
        register_rest_route(
            'wp/v2',
            '/update-survey',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'update_survey'),
                'permission_callback' => '__return_true', // Không cần xác thực
            )
        );
    }

    public function update_survey(WP_REST_Request $request)
    {
        global $wpdb;

        $token = $request->get_header('Authorization');

        if (!$token) {
            error_log('No token provided.');
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }
        
        $token_response = $this->generate_jwt_token($token);

        if (is_wp_error($token_response)) {
            return $token_response;
        }

        $survey = $request->get_param('survey');

        $requireFields = ['goal_id', 'muscle_type_id', 'additional_goal', 'height', 'weight', 'appearance_option', 'page_plan_option', 'training_date'];
        foreach ($requireFields as $field) {
            if (empty($survey[$field])) {
                return new WP_Error('missing_fields', "$field is required in survey.", array('status' => 400));
            }
        }
        
        $table = $wpdb->prefix . 'exercise_usermeta';

        $result = 0;

        $sql_check = $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE  user_id = %s ", $token_response->user_id);
        if ($wpdb->get_var($sql_check) > 0) {
            $result = $wpdb->update(
                $table,
                $survey,
                array('user_id' => $token_response['user_id'])
            );
        }

        $message = $result == 0 ? "Survey update failed." : "Survey updated successfully.";
        $status = $result == 0 ? 400 : 200;

        $response = [
            "message" => $message,
            "status" => $status
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
    $controller = new Custom_Update_Survey_Controller();
    $controller->register_routes();
});
