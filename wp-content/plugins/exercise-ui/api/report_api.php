<?php

if (!defined('ABSPATH')) {
    exit;
}

class Report_Api extends WP_REST_Controller
{

    public function register_routes()
    {
        register_rest_route(
            'wp/v2',
            '/report',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'response'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'wp/v2',
            '/report/image',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'set_image_user'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'wp/v2',
            '/report/weight',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'response_weight'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'wp/v2',
            '/report/workout',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'response_workout'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'wp/v2',
            '/report/userimage',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'response_user_image'),
                'permission_callback' => '__return_true',
            )
        );
    }

    public function response(WP_REST_Request $request)
    {
        $token = $request->get_header('Authorization');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $token_response = $this->generate_jwt_token($token);

        if (is_wp_error($token_response)) {
            return $token_response;
        }

        $user_id = $token_response['data']['user'];

        $usermeta = $this->get_user_meta($user_id);

        $currentWeek = $usermeta[0]['current_week'];

        if (!$currentWeek) {
            $planId = $this->get_plan($user_id);

            if (empty($planId)) {
                return new WP_Error('no_plan', 'No plan for this user', array('status' => 403));
            }

            $allWeek = $this->get_weeks($planId[0]['plan_id']);

            if (empty($allWeek)) {
                return new WP_Error('no_week', 'No week for this plan', array('status' => 403));
            }

            $currentWeek = $allWeek[0]['id'];

        }

        $week = $this->get_week_by_id($currentWeek);

        $limit = $usermeta[0]['duration'];

        $week = $week[0];

        $days = $this->get_days($currentWeek, $limit);

        $weightWeek = $this->get_report_weight($user_id);

        $lists = $this->get_list_report($user_id);

        $weightArr = array();
        $current_date = date('Y-m-d');


        if (empty($lists) || (count($lists) == 1)) {

            $created_at = $usermeta[0]['created_at'];

            if (count($lists) == 1) {
                $created_at = $lists[0]['day'];
            }

            if ($created_at) {

                $created_at_date = date('Y-m-d', strtotime($created_at));

                if ($current_date == $created_at_date) {
                    $weightArr[0]['day'] = date('F j', strtotime($created_at));
                    ;

                    $weightArr[0]['weight'] = $usermeta[0]['weight'];

                    for ($i = 1; $i <= 2; $i++) {

                        $day = $i == 1 ? ' day' : ' days';

                        $datee = date('F j', strtotime($created_at . '+' . $i . $day));

                        $weightArr[$i]['day'] = $datee;

                        $weightArr[$i]['weight'] = null;
                    }
                } elseif ($created_at_date < $current_date) {

                    if (empty($lists)) {

                        $weightArr[0]['weight'] = $usermeta[0]['weight'];

                        $weightArr[0]['day'] = $usermeta[0]['created_at'];

                    } else {

                        $weightArr = $lists;

                    }

                    $weightArr[0]['day'] = date('F j', strtotime($weightArr[0]['day']));

                    $date1 = new DateTime($created_at);

                    $date2 = new DateTime($current_date);

                    $interval = $date1->diff($date2);

                    if ($interval->days == 0) {

                        $weightArr[1]['weight'] = null;
                        $weightArr[1]['day'] = date('F j', strtotime($current_date));

                        $weightArr[2]['weight'] = null;
                        $weightArr[2]['day'] = date('F j', strtotime($current_date . '+1 day'));


                    } else {
                        $weightArr[1]['day'] = date('F j', strtotime($current_date . ' -1 day'));
                        $weightArr[1]['weight'] = null;

                        $weightArr[2]['day'] = date('F j', strtotime($current_date));
                        $weightArr[2]['weight'] = null;
                    }

                }

            }
        } else {
            $weightArr = $lists;

            foreach ($weightArr as $key => $dayItem) {
                $weightArr[$key]['day'] = date('F j', strtotime($dayItem['day']));
            }

            $endArr = end($weightArr);

            $created_at_date = date('Y-m-d', strtotime($endArr['day']));

            if ($created_at_date < $current_date) {
                $arr = array(
                    'weight' => null,
                    'day' => date('F j', strtotime($current_date)),
                );
            } else {
                $arr = array(
                    'weight' => null,
                    'day' => date('F j', strtotime($current_date . '+1 day')),
                );
            }

            array_push($weightArr, $arr);
        }

        $report = $this->week_title();

        foreach ($days as $key => $day) {
            $reports = $this->get_days_report($user_id, $day['id']);

            $duration = 0;

            foreach ($reports as $item) {
                $duration += round(($item['total_duration'] + $item['total_reps']) * $item['round'] / 60);
            }

            $report[$key]['minutes'] = $duration;
        }

        $bodyImage = $this->get_image_body($user_id);

        $results = [
            'status' => 200,
            'data' => array(
                'weight' => $weightArr,
                'workout' => $report,
                'user_body' => $bodyImage
            )
        ];

        return new WP_REST_Response($results, 200);
    }

    public function response_weight(WP_REST_Request $request)
    {
        $token = $request->get_header('Authorization');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $token_response = $this->generate_jwt_token($token);

        if (is_wp_error($token_response)) {
            return $token_response;
        }

        $user_id = $token_response['data']['user'];

        $usermeta = $this->get_user_meta($user_id);


        $lists = $this->get_list_report($user_id);

        $weightArr = array();
        $current_date = date('Y-m-d');


        if (empty($lists) || (count($lists) == 1)) {

            $created_at = $usermeta[0]['created_at'];

            if (count($lists) == 1) {
                $created_at = $lists[0]['day'];
            }

            if ($created_at) {

                $created_at_date = date('Y-m-d', strtotime($created_at));

                if ($current_date == $created_at_date) {
                    $weightArr[0]['day'] = date('F j', strtotime($created_at));
                    ;

                    $weightArr[0]['weight'] = $usermeta[0]['weight'];

                    for ($i = 1; $i <= 2; $i++) {

                        $day = $i == 1 ? ' day' : ' days';

                        $datee = date('F j', strtotime($created_at . '+' . $i . $day));

                        $weightArr[$i]['day'] = $datee;

                        $weightArr[$i]['weight'] = null;
                    }
                } elseif ($created_at_date < $current_date) {

                    if (empty($lists)) {

                        $weightArr[0]['weight'] = $usermeta[0]['weight'];

                        $weightArr[0]['day'] = $usermeta[0]['created_at'];

                    } else {

                        $weightArr = $lists;

                    }

                    $weightArr[0]['day'] = date('F j', strtotime($weightArr[0]['day']));

                    $date1 = new DateTime($created_at);

                    $date2 = new DateTime($current_date);

                    $interval = $date1->diff($date2);

                    if ($interval->days == 0) {

                        $weightArr[1]['weight'] = null;
                        $weightArr[1]['day'] = date('F j', strtotime($current_date));

                        $weightArr[2]['weight'] = null;
                        $weightArr[2]['day'] = date('F j', strtotime($current_date . '+1 day'));


                    } else {
                        $weightArr[1]['day'] = date('F j', strtotime($current_date . ' -1 day'));
                        $weightArr[1]['weight'] = null;

                        $weightArr[2]['day'] = date('F j', strtotime($current_date));
                        $weightArr[2]['weight'] = null;
                    }

                }

            }
        } else {
            $weightArr = $lists;

            foreach ($weightArr as $key => $dayItem) {
                $weightArr[$key]['day'] = date('F j', strtotime($dayItem['day']));
            }

            $endArr = end($weightArr);

            $created_at_date = date('Y-m-d', strtotime($endArr['day']));

            if ($created_at_date < $current_date) {

                $arr = array(
                    'weight' => null,
                    'day' => date('F j', strtotime($current_date)),
                );
                array_push($weightArr, $arr);

            } else {

                if (count($weightArr) == 2) {
                    $arr = array(
                        'weight' => null,
                        'day' => date('F j', strtotime($current_date . ' +1 day')),
                    );

                    array_push($weightArr, $arr);

                }
            }

        }

        foreach ($lists as $key => $item) {

            $lists[$key]['day'] = date('F j', strtotime($item['day']));

            $weightArr[$key]['day'] = date('F j', strtotime($item['day']));

            $weightArr[$key]['weight'] = $item['weight'];
        }

        $results = [
            'status' => 200,
            'data' => array(
                'weight' => $weightArr,
                'list' => $lists
            )
        ];

        return new WP_REST_Response($results, 200);
    }

    public function response_workout(WP_REST_Request $request)
    {
        $token = $request->get_header('Authorization');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $token_response = $this->generate_jwt_token($token);

        if (is_wp_error($token_response)) {
            return $token_response;
        }

        $user_id = $token_response['data']['user'];

        $usermeta = $this->get_user_meta($user_id);

        $currentWeek = $usermeta[0]['current_week'];

        $weight = $usermeta[0]['weight'];

        if (!$currentWeek) {
            $planId = $this->get_plan($user_id);

            if (empty($planId)) {
                return new WP_Error('no_plan', 'No plan for this user', array('status' => 403));
            }

            $allWeek = $this->get_weeks($planId[0]['plan_id']);

            if (empty($allWeek)) {
                return new WP_Error('no_week', 'No week for this plan', array('status' => 403));
            }

            $currentWeek = $allWeek[0]['id'];

        }

        $week = $this->get_week_by_id($currentWeek);

        $limit = $usermeta[0]['duration'];

        $week = $week[0];

        $days = $this->get_days($currentWeek, $limit);

        $report = $this->week_title();

        $listWorkout = array();

        $today = array();
        $check = 0;
        $priority = 0;

        $currentDate = new DateTime();
        $formattedCurrentDate = $currentDate->format('l, F j');
        foreach ($days as $key => $day) {

            $priority = $day['priority'];

            $reports = $this->get_days_report($user_id, $day['id']);

            $durationSection = 0;

            foreach ($reports as $keyy => $item) {
                $date = new DateTime($item['day']);
                $formattedDate = $date->format('l, F j');

                if ($item['type'] == 1) {
                    $check = 1;
                }

                $durationSection += round(($item['total_duration'] + $item['total_reps']) * $item['round'] / 60);
            }

            if ($check == 0) {
                if (empty($today)) {

                    $today = $this->get_today($day['id']);

                    $today[0]['day'] = "Today " . " " . $formattedCurrentDate;
                }
            }
            $report[$key]['minutes'] = $durationSection;
        }

        if (empty($today)) {
            if (count($days) > 0) {
                $priority -= 1;
            } else {
                $priority = 6;
            }

            if ($priority > 0) {
                $today = $this->get_today_by_week($currentWeek, $priority);
                $today[0]['day'] = "Today " . "" . $formattedCurrentDate;
            }
        }

        $daysFinish = $this->get_days_report($user_id, 0);

        $listWorkout = array();

        foreach ($daysFinish as $key => $dayFinish) {
            $reportsDayfinish = $this->get_days_report($user_id, $dayFinish['day_id']);

            $calorie = 0;
            $duration = 0;
            $durationSection = 0;

            foreach ($reportsDayfinish as $keyy => $reportDayFinish) {

                $exReport = $this->get_report_exercise($reportDayFinish['section_id']);

                $date = new DateTime($reportDayFinish['day']);
                $formattedDate = $date->format('l, F j Y');


                foreach ($exReport as $exKey => $ex) {
                    if (!$ex['duration']) {
                        $duration += ($reportDayFinish['round'] * $ex['reps'] * 5) / 60;
                        $calorie += (($ex['met_value'] * 3.5 * $weight) / 200 * (($reportDayFinish['round'] * $ex['reps'] * 5) / 60));
                    } else {
                        $duration += ($reportDayFinish['round'] * $ex['duration']) / 60;
                        $calorie += (($ex['met_value'] * 3.5 * $weight) / 200 * (($reportDayFinish['round'] * $ex['duration']) / 60));
                    }
                }

            }
            $listWorkout[$key]['name'] = $dayFinish['name'];
            $listWorkout[$key]['day'] = $formattedDate;
            $listWorkout[$key]['duration'] = round($duration);
            $listWorkout[$key]['calorie'] = round($calorie);

        }


        $groupedData = [];

        foreach ($listWorkout as $item) {
            $date = new DateTime($item['day']);
            $monthYear = $date->format('F Y');

            $found = false;
            foreach ($groupedData as &$group) {
                if ($group['title'] === $monthYear) {
                    $group['data'][] = $item;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $groupedData[] = [
                    'title' => $monthYear,
                    'data' => [$item]
                ];
            }
        }

        $results = [
            'status' => 200,
            'data' => array(
                'workout' => $report,
                'today' => $today,
                'list' => $groupedData
            )
        ];

        return new WP_REST_Response($results, 200);
    }
    public function set_image_user(WP_REST_Request $request)
    {

        global $wpdb;

        $token = $request->get_header('Authorization');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $token_response = $this->generate_jwt_token($token);

        if (is_wp_error($token_response)) {
            return $token_response;
        }

        $user_id = $token_response['data']['user'];

        $user = array();

        $user['user_id'] = $user_id;
        $avt = $request->get_param('image');

        if ($avt) {
            if (preg_match('/^data:image\/(\w+);base64,/', $avt, $type)) {
                $data = substr($avt, strpos($avt, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);

                $file_extension = $type;
                $file_name = 'user_' . $user_id . '.' . $file_extension;
                $temp_dir = sys_get_temp_dir() . '/weight/';

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
                    $user['image'] = $attachment_url;
                } else {
                    // Error occurred
                    return new WP_Error('upload_error', isset($movefile['error']) ? $movefile['error'] : 'Unknown error.', array('status' => 400));
                }
            } else {
                return new WP_Error('invalid_image_format', 'Invalid image format.', array('status' => 400));
            }
        }

        $status = 200;
        $message = 'Updated image for user body successfully';

        $table_name = $wpdb->prefix . "exercise_report_body";

        $result = $wpdb->insert(
            $table_name,
            $user
        );

        if (!$result) {
            $status = 400;
            $message = 'Something went wrong.';
        }

        $data = [
            'status' => $status,
            'message' => $message
        ];

        return new WP_REST_Response($data, 200);

    }

    public function response_user_image(WP_REST_Request $request)
    {
        $token = $request->get_header('Authorization');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $token_response = $this->generate_jwt_token($token);

        if (is_wp_error($token_response)) {
            return $token_response;
        }

        $user_id = $token_response['data']['user'];

        $bodyImage = $this->get_image_body($user_id);

        $results = [
            'status' => 200,
            'data' => array(
                'user_body' => $bodyImage
            )
        ];

        return new WP_REST_Response($results, 200);
    }

    private function get_plan($user_id)
    {
        global $wpdb;

        $table_user_plan = $wpdb->prefix . 'exercise_user_plan';

        $query = $wpdb->prepare(
            "SELECT 
                plan_id
            FROM $table_user_plan 
            WHERE user_id = %d",
            $user_id
        );

        $results = $wpdb->get_results($query, ARRAY_A);


        return $results;
    }

    private function get_weeks($plan_id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_weeks';

        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE plan_id = " . $plan_id, ARRAY_A);

        return $result;
    }

    private function get_week_by_id($week_id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_weeks';

        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE id = " . $week_id, ARRAY_A);

        return $result;
    }

    private function handle_weight_report($weights, $currentWeight, $type = 'week')
    {

        foreach ($weights as $key => $data) {
            if ($data['weight'] != 1) {
                $currentWeight = $data['weight'];
            } else {
                $weights[$key]['weight'] = $currentWeight;
            }
        }

        return $weights;
    }

    private function get_report_exercise($section_id)
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT sec.round, ex.name, ex.met_value, es.duration, es.reps
            FROM {$wpdb->prefix}exercise_section sec
            INNER JOIN {$wpdb->prefix}exercise_schedule es ON sec.id = es.section_id
            INNER JOIN {$wpdb->prefix}exercise ex ON ex.id = es.exercise_id
            WHERE sec.id = %d",
            $section_id
        );

        $results = $wpdb->get_results($query, ARRAY_A);

        return $results;
    }

    private function get_report_weight($user_id, $number = 28)
    {
        global $wpdb;

        $number = $number - 1;

        $table_weight = $wpdb->prefix . 'exercise_report_weight';

        $end_date = date('Y-m-d');
        $start_date = date('Y-m-d', strtotime('-' . $number . 'days'));

        $dates = [];
        $current_date = strtotime($start_date);
        $end_date_timestamp = strtotime($end_date);

        while ($current_date <= $end_date_timestamp) {
            $dates[] = date('Y-m-d', $current_date);
            $current_date = strtotime('+1 day', $current_date);
        }

        $existing_dates_query = $wpdb->prepare("
        SELECT DATE(created_at) as report_date, weight 
        FROM $table_weight 
        WHERE user_id = %d 
        AND DATE(created_at) BETWEEN %s AND %s
    ", $user_id, $start_date, $end_date);
        $existing_dates_results = $wpdb->get_results($existing_dates_query, ARRAY_A);

        $existing_dates = [];
        foreach ($existing_dates_results as $result) {
            $existing_dates[$result['report_date']] = $result['weight'];
        }

        $results = [];
        foreach ($dates as $date) {
            if (isset($existing_dates[$date])) {
                $results[] = [
                    'report_date' => $date,
                    'weight' => $existing_dates[$date]
                ];
            } else {
                $results[] = [
                    'report_date' => $date,
                    'weight' => 1
                ];
            }
        }

        return $results;
    }

    private function get_list_report($user_id)
    {
        global $wpdb;

        $table_user_plan = $wpdb->prefix . 'exercise_report_weight';

        $query = $wpdb->prepare(
            "SELECT 
                weight,created_at AS day
            FROM $table_user_plan 
            WHERE user_id = %d",
            $user_id
        );

        $results = $wpdb->get_results($query, ARRAY_A);


        return $results;

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
    private function get_user_meta($user_id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . "exercise_usermeta";

        $result = $wpdb->get_results("Select weight,current_week,duration,created_at from $table_name Where user_id = $user_id", ARRAY_A);

        return $result;
    }

    private function get_days($week_id, $limit)
    {

        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_days';

        $query = $wpdb->prepare("
        SELECT 
            *
        FROM $table_name
        WHERE week_id = %d
        ORDER BY priority DESC
        LIMIT %d
    ", $week_id, $limit);

        $result = $wpdb->get_results($query, ARRAY_A);

        return $result;
    }

    private function get_today_by_week($week_id, $priority)
    {
        global $wpdb;

        $table_section = $wpdb->prefix . 'exercise_section';
        $table_days = $wpdb->prefix . 'exercise_days';

        $query = $wpdb->prepare("
            SELECT 
            tm.name
        FROM $table_days ed 
        INNER JOIN {$wpdb->prefix}exercise_training_method tm ON tm.id = ed.training_method_id
        WHERE ed.week_id = %d
        AND ed.priority = %d
        ", $week_id, $priority);

        $result = $wpdb->get_results($query, ARRAY_A);

        return $result;
    }

    private function get_today($day_id)
    {

        global $wpdb;

        $table_section = $wpdb->prefix . 'exercise_section';
        $table_days = $wpdb->prefix . 'exercise_days';

        $query = $wpdb->prepare("
            SELECT 
            tm.name
        FROM $table_days ed 
        INNER JOIN {$wpdb->prefix}exercise_training_method tm ON tm.id = ed.training_method_id
        WHERE ed.id = %d
        ", $day_id);

        $result = $wpdb->get_results($query, ARRAY_A);

        return $result;

    }

    private function get_days_report($user_id, $day_id)
    {

        global $wpdb;

        $table_finish_section = $wpdb->prefix . 'exercise_finish_section';
        $table_section = $wpdb->prefix . 'exercise_section';
        $table_days = $wpdb->prefix . 'exercise_days';
        $table_schedule = $wpdb->prefix . 'exercise_schedule';


        if ($day_id > 0) {
            $query = $wpdb->prepare("
    SELECT 
        efs.section_id, efs.created_at as day, efs.round,
        COUNT(esch.id) AS total_exercises, 
        SUM(esch.duration) AS total_duration, 
        SUM(esch.reps * 5) AS total_reps,
        sec.type
    FROM $table_finish_section efs
    INNER JOIN $table_section sec ON efs.section_id = sec.id
    INNER JOIN $table_days ed ON sec.day_id = ed.id
    INNER JOIN $table_schedule esch ON sec.id = esch.section_id
    WHERE efs.user_id = %d
    AND ed.id = %d
    AND efs.finish = 1
    GROUP BY efs.section_id
", $user_id, $day_id);
        } else {
            $query = $wpdb->prepare("
    SELECT 
    s.day_id AS day_id,
    tm.name
FROM {$wpdb->prefix}exercise_section s
INNER JOIN {$wpdb->prefix}exercise_finish_section fn ON fn.section_id = s.id
INNER JOIN $table_days ed ON s.day_id = ed.id
INNER JOIN {$wpdb->prefix}exercise_training_method tm ON tm.id = ed.training_method_id
WHERE fn.finish = 1
AND fn.user_id = %d
AND s.type = 1
GROUP BY s.day_id
", $user_id);
        }

        $result = $wpdb->get_results($query, ARRAY_A);

        return $result;

    }

    private function week_title()
    {
        $week = array(
            array(
                'title' => 'Mon',
                'minutes' => 0
            ),
            array(
                'title' => 'Tue',
                'minutes' => 0
            ),
            array(
                'title' => 'Wed',
                'minutes' => 0
            ),
            array(
                'title' => 'Thu',
                'minutes' => 0
            ),
            array(
                'title' => 'Fri',
                'minutes' => 0
            ),
            array(
                'title' => 'Sat',
                'minutes' => 0
            ),
            array(
                'title' => 'Sun',
                'minutes' => 0
            ),
        );

        return $week;
    }

    private function get_image_body($user_id)
    {
        global $wpdb;

        $table_body = $wpdb->prefix . 'exercise_report_body';

        $query = $wpdb->prepare("
            SELECT * 
            FROM $table_body 
            WHERE user_id = %d 
            ORDER BY created_at DESC 
            LIMIT 2
        ", $user_id);

        $results = $wpdb->get_results($query, ARRAY_A);


        if (!empty($results)) {
            $results = array_reverse($results);
        } else {
            $results[0]['image'] = "https://wordpress-1308981-4772530.cloudwaysapps.com/wp-content/uploads/2024/07/before.png";
            $results[1]['image'] = "https://wordpress-1308981-4772530.cloudwaysapps.com/wp-content/uploads/2024/07/after.png";
        }

        return $results;
    }
}

add_action('rest_api_init', function () {
    $controller = new Report_Api();
    $controller->register_routes();
});



