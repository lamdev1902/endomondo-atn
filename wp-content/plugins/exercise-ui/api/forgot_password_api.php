<?php
if (!defined('ABSPATH')) {
    exit;
}
class Custom_Password_Reset_Controller extends WP_REST_Controller {

    /**
     * Register the routes for the controller.
     */
    public function register_routes() {
        register_rest_route('wp/v2', '/forgot-password', array(
            'methods' => 'POST',
            'callback' => array($this, 'custom_forgot_password_callback'),
            'permission_callback' => '__return_true', // No permissions check needed for initiating password reset
        ));

        register_rest_route('wp/v2', '/check-code', array(
            'methods' => 'POST',
            'callback' => array($this, 'custom_check_code_callback'),
            'permission_callback' => '__return_true', // No permissions check needed for checking the verification code
        ));
    }

    /**
     * Callback function to handle forgot password request.
     */
    public function custom_forgot_password_callback(WP_REST_Request $request) {
        $user_data = $request->get_json_params();

        if (empty($user_data['email'])) {
            return new WP_Error('invalid_data', 'Invalid username or email.', array('status' => 400));
        }

        $user = get_user_by('email', $user_data['email']);
        if (!$user) {
            $user = get_user_by('login', $user_data['user_login']);
        }

        if (!$user) {
            return new WP_Error('user_not_found', 'User not found.', array('status' => 404));
        }

        // Generate a verification code and store it in user meta
        
        $characters = '0123456789';
        $code = '';
        for ($i = 0; $i < 4; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }


        update_user_meta($user->ID, 'verification_code', $code);

        // Send verification code via email
        $subject = 'Password Reset Verification Code';
        $message = 'Your verification code is: ' . $code;

        $headers = array('Content-Type: text/html; charset=UTF-8');

        $sent = wp_mail($user->user_email, $subject, $message, $headers);

        if (!$sent) {
            return new WP_Error('email_send_error', 'Failed to send verification code via email.', array('status' => 500));
        }

        return new WP_REST_Response('Verification code sent to your email.', 200);
    }

    /**
     * Callback function to handle verification code check.
     */
    public function custom_check_code_callback(WP_REST_Request $request) {
        $user_data = $request->get_json_params();

        if (empty($user_data['email']) || empty($user_data['verification_code'])) {
            return new WP_Error('invalid_data', 'Invalid username or verification code.', array('status' => 400));
        }

        $user = get_user_by('email', $user_data['email']);

        if (!$user) {
            return new WP_Error('user_not_found', 'User not found.', array('status' => 404));
        }

        $saved_code = get_user_meta($user->ID, 'verification_code', true);

        if ($saved_code != $user_data['verification_code']) {
            return new WP_Error('invalid_code', 'Invalid verification code.', array('status' => 400));
        }

        // Clear the verification code after successful verification
        delete_user_meta($user->ID, 'verification_code');

        $data = [
            'description' => 'Verification code verified successfully.',
            'status' => 200
        ];
        
        return new WP_REST_Response($data);
    }
}

// Register the custom controller
add_action('rest_api_init', function () {
    $controller = new Custom_Password_Reset_Controller();
    $controller->register_routes();
});