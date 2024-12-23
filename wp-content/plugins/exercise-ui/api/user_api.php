<?php
/*
Plugin Name: Custom User Registration
Description: Custom endpoint for user registration.
Version: 1.0
Author: Your Name
*/

/*  Exit if accessed directly. */

if (!defined('ABSPATH')) {
    exit;
}

class Custom_Register_User_Controller extends WP_REST_Controller
{
    public function register_routes()
    {
        register_rest_route(
            'wp/v2',
            '/register',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'custom_user_registration'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'wp/v2',
            '/survey-register',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'survey_register_callback'),
                'permission_callback' => '__return_true', // No permissions check needed for checking the verification code
            )
        );

        register_rest_route(
            'wp/v2',
            '/plan/change',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'response_plan_change'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'wp/v2',
            '/exercise/me',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'response'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'wp/v2',
            '/exercise/me/profile',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'response_profile'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'wp/v2',
            '/exercise/me/profile/update',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'profile_update'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'wp/v2',
            '/exercise/me/profile/weight',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'weight_update'),
                'permission_callback' => '__return_true',
            )
        );

    }

    public function response(WP_REST_Request $request)
    {
        global $wpdb;

        $token = $request->get_header('Authorization');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $token_response = $this->validate_jwt_token($token);

        $user_id = $token_response['data']['user'];

        $user = get_user_by('ID', $user_id);

        $usermeta = $this->get_user_meta($user_id);

        $avt = $usermeta[0]['avt'];

        $name = $user->display_name;

        $report = $this->get_report_exercise($user_id);

        $duration = 0;

        $weight = preg_replace('/\D/', '', $usermeta[0]['weight']);

        if (str_contains($usermeta[0]['weight'], 'lbs')) {
            $weight = $weight * 0.45359237;
        }

        $workout = 0;

        $sectionId = 0;

        $calorie = 0;

        foreach ($report as $item) {

            if ($item['section_id'] != $sectionId) {
                $workout++;
            }

            $round = $item['round'];

            $sectionId = $item['section_id'] == $sectionId ? $sectionId : $item['section_id'];

            if (empty($item['duration'])) {
                $duration += $item['reps'] * 5 * $round;
            } else {
                $duration += $item['duration'] * $round;
            }

            if (!$item['duration']) {
                $calorie += (($item['met_value'] * 3.5 * $weight) / 200 * (($round * $item['reps'] * 5) / 60));
            } else {
                $calorie += (($item['met_value'] * 3.5 * $weight) / 200 * (($round * $item['duration']) / 60));
            }

        }

        $data = [
            'status' => 200,
            'user' => [
                'calorie' => round($calorie),
                'duration' => round($duration / 60),
                'username' => $name,
                'workout' => $workout,
                'avatar_link' => $avt
            ]
        ];

        return new WP_REST_Response($data, 200);
    }

    public function response_profile(WP_REST_Request $request)
    {
        global $wpdb;

        $token = $request->get_header('Authorization');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $token_response = $this->validate_jwt_token($token);

        $user_id = $token_response['data']['user'];

        $user = get_user_by('ID', $user_id);

        $name = $user->display_name;

        $email = $user->user_email;

        $usermeta = $this->get_user_meta($user_id);

        $usermeta[0]['username'] = $name;
        $usermeta[0]['email'] = $email;

        $data = [
            'status' => 200,
            'user' => $usermeta[0]
        ];

        return new WP_REST_Response($data, 200);
    }

    public function profile_update(WP_REST_Request $request)
    {
        global $wpdb;

        $token = $request->get_header('Authorization');

        $avt = $request->get_param('avatar');

        $name = $request->get_param('username');

        $height = $request->get_param('height');

        $weight = $request->get_param('weight');

        $gender = $request->get_param('gender');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $token_response = $this->validate_jwt_token($token);

        $user_id = $token_response['data']['user'];

        if (!empty($name)) {
            wp_update_user(array('ID' => $user_id, 'user_nicename' => $name, 'display_name' => $name));
        }

        $userMeta = array();

        if ($avt) {
            if (preg_match('/^data:image\/(\w+);base64,/', $avt, $type)) {
                $data = substr($avt, strpos($avt, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);

                $file_extension = $type;
                $file_name = 'user_' . $user_id . '.' . $file_extension;
                $temp_dir = sys_get_temp_dir() . '/avt/';

                // Create directory if it doesn't exist
                if (!file_exists($temp_dir)) {
                    mkdir($temp_dir, 0755, true);
                }

                $temp_file_path = $temp_dir . $file_name;

                // Remove existing file if it exists
                if (file_exists($temp_file_path)) {
                    unlink($temp_file_path);
                }

                // Write data to the temporary file
                if (file_put_contents($temp_file_path, $data) === false) {
                    return new WP_Error('file_write_error', 'Failed to write temporary file.', array('status' => 400));
                }

                require_once (ABSPATH . 'wp-admin/includes/file.php');
                require_once (ABSPATH . 'wp-admin/includes/image.php');
                require_once (ABSPATH . 'wp-admin/includes/media.php');

                // Prepare the file array
                $file_array = array(
                    'name' => $file_name,
                    'tmp_name' => $temp_file_path,
                    'type' => mime_content_type($temp_file_path),
                    'error' => 0,
                    'size' => filesize($temp_file_path)
                );

                // Use wp_handle_sideload to handle the temporary file
                $overrides = array(
                    'test_form' => false,
                    'mimes' => array(
                        'jpg|jpeg|jpe' => 'image/jpeg',
                        'gif' => 'image/gif',
                        'png' => 'image/png',
                        'bmp' => 'image/bmp',
                        'tif|tiff' => 'image/tiff',
                        'ico' => 'image/x-icon'
                    )
                );

                $movefile = wp_handle_sideload($file_array, $overrides);

                if ($movefile && !isset($movefile['error'])) {
                    // File is uploaded successfully
                    $attachment_url = $movefile['url'];

                    // Store attachment URL in user meta
                    $userMeta['avt'] = $attachment_url;
                } else {
                    // Error occurred
                    return new WP_Error('upload_error', isset($movefile['error']) ? $movefile['error'] : 'Unknown error.', array('status' => 400));
                }
            } else {
                return new WP_Error('invalid_image_format', 'Invalid image format.', array('status' => 400));
            }
        }





        if (!empty($height)) {
            $userMeta['height'] = $height;
        }

        if (!empty($weight)) {
            $userMeta['weight'] = $weight;
        }

        if (!empty($gender)) {
            $userMeta['gender'] = $gender;
        }

        if (!empty($userMeta)) {
            $table_name = $wpdb->prefix . "exercise_usermeta";

            $wpdb->update(
                $table_name,
                $userMeta,
                array('user_id' => $user_id)
            );
        }

        $data = [
            'status' => 200,
            'message' => 'Updated user info successfully'
        ];

        return new WP_REST_Response($data, 200);
    }

    public function weight_update(WP_REST_Request $request)
    {
        global $wpdb;

        $token = $request->get_header('Authorization');

        $weight = $request->get_param('weight');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        if (empty($weight)) {
            return new WP_Error('missing_fields', 'Weight are required.', array('status' => 400));
        }

        $token_response = $this->validate_jwt_token($token);

        $user_id = $token_response['data']['user'];

        $table = $wpdb->prefix . "exercise_report_weight";

        $userMeta = array();

        $userMeta['weight'] = (int) $weight;

        $userMeta['user_id'] = $user_id;

        $current_date = date('Y-m-d');

        $query = $wpdb->prepare("
            SELECT id 
            FROM $table
            WHERE user_id = %d 
            AND DATE(created_at) = %s
        ", $user_id, $current_date);

        $exists = $wpdb->get_var($query);

        if ($exists) {
            $update_query = $wpdb->prepare("
                UPDATE $table 
                SET weight = %d, 
                created_at = CURRENT_TIMESTAMP,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = %d
            ", $weight, $exists);

            $wpdb->query($update_query);
        } else {
            $wpdb->insert(
                $table,
                $userMeta
            );
        }

        $data = [
            'status' => 200,
            'message' => 'Add new weight for this user successfully'
        ];

        return new WP_REST_Response($data, 200);
    }
    public function custom_user_registration(WP_REST_Request $request)
    {

        global $wpdb;
        // Get parameters from request.
        $password = $request->get_param('password');
        $email = $request->get_param('email');

        // Check if the username, password, and email are provided.
        if (empty($password) || empty($email)) {
            return new WP_Error('missing_fields', 'Username, password, and email are required.', array('status' => 400));
        }

        // Check if the email already exists.
        if (email_exists($email)) {
            return new WP_Error('email_exists', 'This email is already registered.', array('status' => 400));
        }

        // Create the user.
        $user_id = wp_create_user($email, $password, $email);

        // Check for errors.
        if (is_wp_error($user_id)) {
            return $user_id;
        }

        // Get user data.
        $token = $this->generate_jwt_token($email, $password);

        $data = [
            'status' => 200,
            'message' => 'User register successfully.',
            'data' => $token
        ];

        return new WP_REST_Response($data, 200);
    }
    public function survey_register_callback(WP_REST_Request $request)
    {
        global $wpdb;

        $survey = $request->get_param('survey');

        $token = $request->get_header('Authorization');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $token_response = $this->validate_jwt_token($token);

        $user_id = $token_response['data']['user'];

        // Check if the survey
        if (empty($survey)) {
            return new WP_Error('missing_fields', 'Survey are required.', array('status' => 400));
        }

        $requireFields = ['goal_id', 'gender', 'experience', 'muscle_type_id', 'additional_goal', 'height', 'weight', 'appearance_option', 'page_plan_option', 'training_date'];
        foreach ($requireFields as $field) {
            if (empty($survey[$field])) {
                return new WP_Error('missing_fields', "$field is required in survey.", array('status' => 400));
            }
        }

        $table = $wpdb->prefix . 'exercise_usermeta';

        if (!empty($survey['username'])) {
            $name = $survey['username'];

            unset($survey['username']);

            wp_update_user(array('ID' => $user_id, 'user_nicename' => $name, 'display_name' => $name));
        }

        $queryUsermeta = $wpdb->prepare("SELECT id FROM " . $table . " WHERE user_id = " . $user_id);

        $record = $wpdb->get_var($queryUsermeta);

        if ($record > 0) {
            return new WP_Error('survey_exists', 'Survey for this user is already registered.', array('status' => 400));
        }

        $avtDefault = 'https://wordpress-1308981-4772530.cloudwaysapps.com/wp-content/uploads/2024/07/avt-default.png';
        $survey['muscle_type_id'] = implode(",", $survey['muscle_type_id']);
        $survey['user_id'] = $user_id;
        $survey['avt'] = $avtDefault;

        $recordSurvey = $wpdb->insert(
            $table,
            $survey
        );

        if ($recordSurvey) {

            $weight = $survey['weight'];

            $wpdb->insert(
                $wpdb->prefix . 'exercise_report_weight',
                array(
                    'user_id' => $user_id,
                    'weight' => $weight
                )
            );

            $month = (int) $survey['experience'];

            $appearance = (int) $survey['appearance_option'];

            $page_plan_option = (int) $survey['page_plan_option'];

            if ($month == 1) {

                $plan_type = "easy";

            } elseif ($month > 3 && $month < 6) {
                if ($appearance == 1) {
                    $plan_type = "hard";
                } else {
                    if ($page_plan_option == 1) {
                        $plan_type = "hard";
                    } else {
                        $plan_type = "moderate";
                    }
                }
            } else {
                if ($page_plan_option == 1) {
                    $plan_type = "hard";
                } else {
                    $plan_type = "moderate";
                }
            }

            $planId = $this->get_plan_by_goal($survey['goal_id'], $plan_type, $survey['muscle_type_id']);

            $id = 1;

            $table_user_plan = $wpdb->prefix . "exercise_user_plan";

            if(empty($planId)){
                $id = 1;
            }
            else {
                $id = $planId[0]['plan_id'];
            }

            $user_plan = [
                'user_id' => $user_id,
                'plan_id' => $id
            ];

            $wpdb->insert($table_user_plan, $user_plan);

            // $goal = $this->get_goal($survey['goal_id']);

            // $duration = $this->get_min_max_duration_by_plan_id($planId[0]['id']);

            // $frequency = $this->get_total_days_by_plan_id_and_week_number($planId[0]['id'], 1);
        }

        $data = [
            'status' => 200,
            'message' => "Survey register succesfully.",
            'data' => [
                'plan_id' => $id,
                // 'goal' => $goal[0],
                // 'duration' => $duration,
                // 'frequency' => $frequency
            ]
        ];

        // Return success response.
        return new WP_REST_Response($data, 200);
    }

    public function response_plan_change(WP_REST_Request $request)
    {
        global $wpdb;

        $plan_id = $request->get_param('plan_id');

        $token = $request->get_header('Authorization');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $token_response = $this->validate_jwt_token($token);

        $user_id = $token_response['data']['user'];

        // Check if the plan_id
        if (empty($plan_id)) {
            return new WP_Error('missing_fields', 'Plan ID are required.', array('status' => 400));
        }

        $table = $wpdb->prefix . 'exercise_user_plan';


        $queryUsermeta = $wpdb->prepare("SELECT * FROM " . $table . " WHERE user_id = " . $user_id);

        $record = $wpdb->get_var($queryUsermeta);

        $message = 'Something went wrong';

        if ($record > 0) {

            $arr = ['plan_id' => $plan_id];

            $result = $wpdb->update(
                $table,
                $arr,
                array('user_id' => $user_id)
            );

            $message = 'Updated plan for this user successfully!';

        } else {

            $arr = [
                'plan_id' => $plan_id,
                'user_id' => $user_id
            ];

            $result = $wpdb->insert(
                $table,
                $arr
            );

            $message = 'Added plan for this user successfully!';
        }

        if ($result) {
            $status = 200;
        } else {
            $status = 400;
        }


        $data = [
            'status' => $status,
            'message' => $message,
        ];

        // Return success response.
        return new WP_REST_Response($data, 200);
    }

    public function set_current_week(WP_REST_Request $request)
    {
        global $wpdb;

        $week_id = $request->get_param('week_id');

        $token = $request->get_header('Authorization');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $token_response = $this->validate_jwt_token($token);

        $user_id = $token_response['data']['user'];

        // Check if the week_id
        if (empty($week_id)) {
            return new WP_Error('missing_fields', 'Week ID are required.', array('status' => 400));
        }

        $table = $wpdb->prefix . 'exercise_usermeta';


        $queryUsermeta = $wpdb->prepare("SELECT * FROM " . $table . " WHERE user_id = " . $user_id);

        $record = $wpdb->get_var($queryUsermeta);

        if ($record > 0) {

            $arr = ['current_week' => $week_id];

            $result = $wpdb->update(
                $table,
                $arr,
                array('user_id' => $user_id)
            );

        } else {
            return new WP_Error('wrong_data', 'User meta infomation not found', array('status' => 400));
        }


        $data = [
            'status' => 200,
            'message' => 'Updated current week for this user successfully.',
        ];

        // Return success response.
        return new WP_REST_Response($data, 200);
    }
    private function get_total_days_by_plan_id_and_week_number($plan_id, $week_number)
    {
        global $wpdb;

        $result = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(ed.id)
             FROM {$wpdb->prefix}exercise_days ed
             INNER JOIN {$wpdb->prefix}exercise_weeks ew ON ed.week_id = ew.id
             WHERE ew.plan_id = %d AND ew.week_number = %d",
                $plan_id,
                $week_number
            )
        );

        return $result;
    }
    private function get_goal($goal_id)
    {
        global $wpdb;

        $table = $wpdb->prefix . "exercise_goal";

        return $wpdb->get_results("SELECT id,name From " . $table . " WHERE id = " . $goal_id, ARRAY_A);
    }
    private function get_plan_by_goal($goal_id, $plan_type, $muscle_id)
    {
        global $wpdb;



        $table_plan = $wpdb->prefix . "exercise_plan";
        $table_plan_goal = $wpdb->prefix . "exercise_plan_goal";

        if ($plan_type == 'easy') {
            $count_query = $wpdb->prepare(
                "SELECT *
                 FROM $table_plan p
                 INNER JOIN $table_plan_goal pg ON p.id = pg.plan_id
                 WHERE pg.goal_id = %d
                 AND (p.cardio + p.strength) <= 2",
                $goal_id
            );
        } elseif ($plan_type == 'moderate') {
            $count_query = $wpdb->prepare(
                "SELECT *
                 FROM $table_plan p
                 INNER JOIN $table_plan_goal pg ON p.id = pg.plan_id
                 WHERE pg.goal_id = %d
                 AND (p.cardio + p.strength) > 2 AND (p.cardio + p.strength) < 6",
                $goal_id
            );
        } else {
            $count_query = $wpdb->prepare(
                "SELECT *
                 FROM $table_plan p
                 INNER JOIN $table_plan_goal pg ON p.id = pg.plan_id
                 WHERE pg.goal_id = %d
                 AND (p.cardio + p.strength) >= 6",
                $goal_id
            );
        }

        $record = $wpdb->get_results($count_query);

        if (count($record) > 1) {
            $count_query .= " AND p.muscle_type_id LIKE '%{$muscle_id}%'";
        }

        $query = $wpdb->prepare($count_query, $goal_id);

        $results = $wpdb->get_results($query, ARRAY_A);

        if(count($results) > 1) {
            $randomNumber = array_rand($results);

            $result[] = $results[$randomNumber];
        }else {
            $result = $results;
        }

        return $result;

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
    private function validate_jwt_token($token)
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
    private function get_plan($goal_id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "exercise_plan";

    }

    private function get_user_meta($user_id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "exercise_usermeta";

        $result = $wpdb->get_results("Select * from $table_name Where user_id = $user_id", ARRAY_A);

        return $result;
    }
    private function get_min_max_duration_by_plan_id($plan_id)
    {
        global $wpdb;

        $result = $wpdb->get_row($wpdb->prepare(
            "SELECT MIN(section_total) as min_duration, MAX(section_total) as max_duration
             FROM (
                 SELECT SUM(es.duration) as section_total
                 FROM {$wpdb->prefix}exercise_schedule es
                 INNER JOIN {$wpdb->prefix}exercise_section sec ON es.section_id = sec.id
                 INNER JOIN {$wpdb->prefix}exercise_days ed ON sec.day_id = ed.id
                 INNER JOIN {$wpdb->prefix}exercise_weeks ew ON ed.week_id = ew.id
                 WHERE ew.plan_id = %d
                 GROUP BY sec.id
             ) as section_totals",
            $plan_id
        ), ARRAY_A);

        return $result;
    }

    private function get_report_exercise($user_id)
    {
        global $wpdb;


        $query = $wpdb->prepare(
            "SELECT ex.id, sec.id AS section_id,efs.round, sec.type AS section_type , ex.met_value, es.duration,es.reps
     FROM {$wpdb->prefix}exercise_finish_section efs
     INNER JOIN {$wpdb->prefix}exercise_section sec ON efs.section_id = sec.id
     INNER JOIN {$wpdb->prefix}exercise_schedule es ON sec.id = es.section_id
     INNER JOIN {$wpdb->prefix}exercise ex ON ex.id = es.exercise_id
     WHERE efs.user_id = %d AND efs.finish = 1
     GROUP BY ex.id",
            $user_id
        );

        $results = $wpdb->get_results($query, ARRAY_A);

        return $results;
    }

}

// Register the custom controller
add_action('rest_api_init', function () {
    $controller = new Custom_Register_User_Controller();
    $controller->register_routes();
});
