<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Plan_Controller extends WP_REST_Controller
{

    public function register_routes()
    {
        register_rest_route(
            'wp/v2',
            '/plan',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'response'),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'wp/v2',
            '/plan/week',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'response_weeks'),
                'permission_callback' => '__return_true',
                'args' => array(
                    'plan_id' => array(
                        'required' => false,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        }
                    )
                )
            )
        );

        register_rest_route(
            'wp/v2',
            '/plan/week/day',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'response_days'),
                'permission_callback' => '__return_true',
                'args' => array(
                    'week_id' => array(
                        'required' => false,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        }
                    )
                )
            )
        );

        register_rest_route(
            'wp/v2',
            '/workout',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'response_section'),
                'permission_callback' => '__return_true',
                'args' => array(
                    'day_id' => array(
                        'required' => false,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        }
                    )
                )
            )
        );

        register_rest_route(
            'wp/v2',
            '/exercise-list',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'response_exercise_list'),
                'permission_callback' => '__return_true',
                'args' => array(
                    'section_id' => array(
                        'required' => false,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        }
                    )
                )
            )
        );

        register_rest_route(
            'wp/v2',
            '/exercise-guide',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'response_exercise_guide'),
                'permission_callback' => '__return_true',
                'args' => array(
                    'id' => array(
                        'required' => false,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        }
                    )
                )
            )
        );

        register_rest_route(
            'wp/v2',
            '/exercise',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'response_exercise'),
                'permission_callback' => '__return_true',
                'args' => array(
                    'id' => array(
                        'required' => false,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric($param);
                        }
                    )
                )
            )
        );

        register_rest_route(
            'wp/v2',
            '/section/finish',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'response_exercise_finish'),
                'permission_callback' => '__return_true',
            )
        );

    }

    public function response(WP_REST_Request $request)
    {
        $token = $request->get_header('Authorization');

        if (!$token) {
            error_log('No token provided.');
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $token_response = $this->generate_jwt_token($token);

        if (is_wp_error($token_response)) {
            return $token_response;
        }

        $user_id = $token_response['data']['user'];

        $plan = $this->get_plan($user_id);

        if (empty($plan)) {
            return new WP_Error('no_exercise_plan', 'No exercise plan set for this user', ['status' => 400]);
        }

        $userMeta = $this->get_user_meta($user_id);

        $plan = $plan[0];

        $week = $this->get_weeks($plan['plan_id']);

        $plan['duration'] = count($week);

        $goals = $this->get_goal();

        $plan['current_week'] = $userMeta[0]['current_week'];

        $arr = array();

        foreach ($goals as $key => $goal) {

            $name = strtolower(preg_replace('/\s+/', '_', $goal['name']));

            $arr[$name] = $this->handle_meta($user_id, $goal['id']);

        }

        foreach ($arr as $keys => $items) {
            foreach ($items as $key => $item) {
                $planWeek = $this->get_weeks($item['id']);

                $arr[$keys][$key]['duration'] = count($planWeek);
            }
        }

        $data = [
            'status' => 200,
            'plan' => $plan,
            'workout' => $arr
        ];

        return new WP_REST_Response($data, 200);
    }

    public function response_weeks(WP_REST_Request $request)
    {
        $token = $request->get_header('Authorization');

        if (!$token) {
            error_log('No token provided.');
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $plan_id = $request->get_param('plan_id');

        $token_response = $this->generate_jwt_token($token);

        if (is_wp_error($token_response)) {
            return $token_response;
        }

        $user_id = $token_response['data']['user'];

        $plan = $this->get_plan($user_id);

        $plan = $plan[0];

        if (empty($plan_id)) {
            return new WP_Error('missing_fields', 'Plan id are required.', array('status' => 400));
        }

        $weeks = $this->get_weeks($plan_id);

        $plan['duration'] = count($weeks);

        $plan['week'] = $weeks;

        if (empty($weeks)) {
            return new WP_Error('no_week', 'No week set for this plan', ['status' => 200]);
        }

        $data = [
            'status' => 200,
            'plan' => $plan
        ];

        return new WP_REST_Response($data, 200);
    }

    public function response_days(WP_REST_Request $request)
    {
        $token = $request->get_header('Authorization');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $reponse_token = $this->generate_jwt_token($token);

        $user_id = $reponse_token['data']['user'];

        $userMeta = $this->get_user_meta($user_id);

        $week_id = $request->get_param('week_id');

        if (empty($week_id)) {
            return new WP_Error('missing_fields', 'Week id are required.', array('status' => 400));
        }

        $week = $this->get_week_by_id($week_id);

        $total = count($this->get_weeks($week[0]['plan_id']));

        $days = $this->get_days($week_id, $userMeta[0]['training_date']);

        if (empty($days)) {
            return new WP_Error('no_day', 'No day set for this week', ['status' => 200]);
        }

        foreach ($days as $key => $day) {
            $finish = 1;

            $sections = $this->get_sections($day['day_id']);

            foreach ($sections as $section) {
                if ($section['type'] == 1) {
                    $finishSection = $this->check_section_finish($section['id'], $user_id);

                    if (empty($finishSection)) {
                        $finish = 0;
                    } elseif ($finishSection[0]['finish'] == 0) {
                        $finish = 0;
                    }
                }
            }

            if (empty($sections)) {
                unset($days[$key]);
            } else {
                $days[$key]['finish'] = $finish;
            }

        }

        $data = [
            'status' => 200,
            'data' => [
                'week_name' => $week[0]['week_name'],
                'week_number' => $week[0]['week_number'],
                'week_description' => $week[0]['week_description'],
                'total_week' => $total,
                'days' => $days
            ]
        ];

        return new WP_REST_Response($data, 200);
    }

    public function response_section(WP_REST_Request $request)
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

        $day_id = $request->get_param('day_id');

        if (empty($day_id)) {
            return new WP_Error('missing_fields', 'Day id are required.', array('status' => 400));
        }

        $sections = $this->get_sections($day_id);

        $userMeta = $this->get_user_meta($user_id);

        $weight = $userMeta[0]['weight'];

        if (empty($sections)) {
            return new WP_Error('no_section', 'No sections set for this day.', array('status' => 200));
        }

        $data = $this->handle_section($sections, $weight, $user_id, $userMeta[0]['duration']);

        $response = [
            'status' => 200,
            'data' => $data
        ];

        return new WP_REST_Response($response, 200);
    }

    public function response_exercise_list(WP_REST_Request $request)
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

        $userMeta = $this->get_user_meta($user_id);

        $section_id = $request->get_param('section_id');

        if (empty($section_id)) {
            return new WP_Error('missing_fields', 'Section id are required.', array('status' => 400));
        }

        $excSchedule = $this->get_exercise_schedule($section_id);


        if (empty($excSchedule)) {
            return new WP_Error('no_schedule', 'No exercise set for this section.', array('status' => 200));
        }

        $section = $this->get_sections_by_id($section_id);

        if (empty($section)) {
            return new WP_Error('no_schedule', 'Section not found.', array('status' => 404));
        }

        $name = 'Warm Up';

        $image = "https://wordpress-1308981-4772530.cloudwaysapps.com/wp-content/uploads/2024/07/Warmup.png";


        $duration = 0;

        $data = array();

        if ($section[0]['type'] == 1) {
            $name = 'Main Workout';
            $image = "https://wordpress-1308981-4772530.cloudwaysapps.com/wp-content/uploads/2024/07/Super-Cardio-Burner.png";
        } elseif ($section[0]['type'] == 2) {
            $name = 'Cooldown';
            $image = "https://wordpress-1308981-4772530.cloudwaysapps.com/wp-content/uploads/2024/07/Cooldown.png";
        }

        $data['name'] = $name;
        $data['image'] = $image;

        $calorie = 0;

        $image = "https://wordpress-1308981-4772530.cloudwaysapps.com/wp-content/uploads/2024/07/Warmup.png";

        $time = 0;

        $exercise = array();

        if ($section[0]['type'] == 1) {
            if ($userMeta[0]['duration'] == 1) {
                $time = 20;
            } elseif ($userMeta[0]['duration'] == 2) {
                $time = 40;
            } else {
                $time = 60;
            }
        }


        $duration = 0;

        foreach ($excSchedule as $exc) {

            if (empty($exc['duration'])) {
                $duration += $exc['reps'] * 5;
            } else {
                $duration += $exc['duration'];
            }
            ;
            $exerciseItem = $this->get_exercise_by_id($exc['exercise_id'], true);

            if (empty($exerciseItem)) {
                return new WP_Error('no_exercise', 'Excercise not found.', array('status' => 404));
            }

            $exerciseItem[0]['id'] = $exc['exercise_id'];
            $exerciseItem[0]['duration'] = $exc['duration'];
            $exerciseItem[0]['finish'] = $exc['finish'];
            $exerciseItem[0]['reps'] = $exc['reps'];
            $exerciseItem[0]['note'] = $exc['note'];

            $exercise[] = $exerciseItem[0];
        }

        $duration = round($duration / 60);

        $round = $section[0]['round'];

        if ($time) {
            if ($duration * $round < $time) {

                $round = floor($time / $duration);

                $round == 0 ? $round = 1 : $round;

            } elseif ($duration * $section[0]['round'] > $time) {

                for ($y = ($section[0]['round'] - 1); $y > 0 && $y < $section[0]['round']; $y--) {

                    if ($duration * $y <= $time) {

                        $round = $y;

                        $y = 0;
                    }

                    if ($y == 1 && $duration * $y > $time) {

                        $round = 1;

                        $y = 0;
                    }
                }
            }
        }

        $duration = $duration * $round;

        $report = $this->get_report_exercise($section_id);

        foreach ($report as $item) {

            if (!$item['duration']) {
                $calorie += (($item['met_value'] * 3.5 * $userMeta[0]['weight']) / 200 * (($round * $item['reps'] * 5) / 60));
            } else {
                $calorie += (($item['met_value'] * 3.5 * $userMeta[0]['weight']) / 200 * (($round * $item['duration']) / 60));
            }
        }

        $data['id'] = $section_id;
        $data['duration'] = $duration;
        $data['calorie'] = round($calorie);
        $data['exercise'] = $exercise;

        $response = [
            'status' => 200,
            'data' => $data
        ];

        return new WP_REST_Response($response, 200);
    }

    public function response_exercise_guide(WP_REST_Request $request)
    {
        $token = $request->get_header('Authorization');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $id = $request->get_param('id');

        if (empty($id)) {
            return new WP_Error('missing_fields', 'Exercise id are required.', array('status' => 400));
        }


        $data = array();

        $exercise = $this->get_exercise_by_id($id);

        if (empty($exercise)) {
            return new WP_Error('no_exercise', 'Exercise not found.', array('status' => 404));
        }


        $data['exercise'] = $exercise[0];


        $primary_data = $this->get_primary_option($id);

        $secondary_data = $this->get_secondary_option($id);

        $content_meta = $this->get_content_meta($id);


        $data['primary']['list'] = $primary_data;

        $data['secondary']['list'] = $secondary_data;

        $content = array(
            0 => 'How to do',
            1 => 'Tip From Expert',
            2 => 'Optimal Sets and Reps',
            3 => 'How to put in your workout split',
            4 => 'Primary Content',
            5 => 'Secondary Content',
            6 => 'Equipment Content'
        );

        $description = array();

        for ($i = 0; $i <= count($content); $i++) {
            if (!empty($content_meta[$i]) && $content_meta[$i]['content_title'] == $content[$i] && $content_meta[$i]['content']) {
                $description[$i]['title'] = $content[$i];
                $description[$i]['content'] = $content_meta[$i]['content'];
            }
        }

        if (!empty($description[4])) {
            $data['primary']['content'] = $description[4];
            unset($description[4]);
        }

        if (!empty($description[5])) {
            $data['secondary']['content'] = $description[5];
            unset($description[5]);
        }

        if (!empty($description[6])) {
            $data['equipment']['content'] = $description[6];
            unset($description[6]);
        }

        $data['exercise_content'] = $description;

        $response = [
            'status' => 200,
            'data' => $data
        ];

        return new WP_REST_Response($response, 200);
    }

    public function response_exercise(WP_REST_Request $request)
    {
        $token = $request->get_header('Authorization');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $id = $request->get_param('id');

        if (empty($id)) {
            return new WP_Error('missing_fields', 'Exercise id are required.', array('status' => 400));
        }

        $data = array();

        $exercise = $this->get_exercise_by_id($id);

        if (empty($exercise)) {
            return new WP_Error('no_exercise', 'Exercise not found.', array('status' => 404));
        }


        $data['exercise'] = $exercise[0];

        $response = [
            'status' => 200,
            'data' => $data
        ];

        return new WP_REST_Response($response, 200);
    }

    public function response_exercise_finish(WP_REST_Request $request)
    {
        global $wpdb;

        $token = $request->get_header('Authorization');

        if (!$token) {
            return new WP_Error('no_token', 'Token is required.', array('status' => 403));
        }

        $reponse_token = $this->generate_jwt_token($token);

        $user_id = $reponse_token['data']['user'];

        $sec_id = $request->get_param('section_id');

        $round = !empty($request->get_param('round')) ? $request->get_param('round') : 1;

        if (empty($sec_id)) {
            return new WP_Error('missing_fields', 'Section id are required.', array('status' => 400));
        }

        $userMeta = $this->get_user_meta($user_id);

        $data = array();

        $section_by_id = $this->get_sections_by_id($sec_id);

        if (empty($section_by_id)) {
            return new WP_Error('no_section', 'Section not found.', array('status' => 404));
        }

        $where = array();

        $table_name = $wpdb->prefix . "exercise_finish_section";

        $prepareFinish = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND section_id = %d", $user_id, $sec_id);

        if ($wpdb->get_var($prepareFinish) > 0) {

            $arr = array(
                'user_id' => $user_id,
                'section_id' => $sec_id,
                'round' => $round
            );

            $wpdb->update(
                $table_name,
                array('finish' => 1),
                $arr
            );
        } else {
            $arr = array('user_id' => $user_id, 'section_id' => (int) $sec_id, 'finish' => (int) 1, 'round' => $round);

            $wpdb->insert(
                $table_name,
                $arr
            );
        }

        $day_id = $section_by_id[0]['day_id'];

        $day = $this->get_day_by_id($day_id);

        $days = $this->get_days($day[0]['week_id'], $userMeta[0]['training_date']);

        $check = true;

        foreach ($days as $dayData) {
            $sections = $this->get_sections($dayData['day_id']);

            foreach ($sections as $section) {

                if ($section['type'] == 1) {
                    $finishSection = $this->check_section_finish($section['id'], $user_id);

                    if (empty($finishSection)) {
                        $check = false;
                    }

                    if (!empty($finishSection) && $finishSection[0]['finish'] == 0) {
                        $check = false;
                    }
                }
            }
        }

        $this->current_week_user($user_id, $day[0]['week_id'], $check);

        $response = [
            'status' => 200,
            'message' => 'Update status section successfully.'
        ];

        return new WP_REST_Response($response, 200);
    }

    private function current_week_user($user_id, $week_id, $check)
    {
        global $wpdb;

        $table = $wpdb->prefix . "exercise_usermeta";

        $currentWeekId = $wpdb->get_results("SELECT current_week FROM $table WHERE user_id = $user_id", ARRAY_A);

        $week = $this->get_week_by_id($week_id);

        $week_number = $week[0]['week_number'];

        $countWeek = count($this->get_weeks($week[0]['plan_id']));

        if (!$check) {

            $arr = ['current_week' => $week_id];

            $wpdb->update(
                $table,
                $arr,
                array('user_id' => $user_id)
            );
        } else {

            $currentWeek = $this->get_week_by_id($currentWeekId[0]['current_week']);

            $currentWeekNumber = $currentWeek[0]['week_number'];

            if ((int) $currentWeekNumber < $countWeek) {
                $currentWeekNumber += 1;
            }

            $idWeek = $this->get_weeks_by_number_week($currentWeekNumber);

            $arr = ['current_week' => $idWeek[0]['id']];

            $wpdb->update(
                $table,
                $arr,
                array('user_id' => $user_id)
            );
        }

        return;
    }
    private function get_plan($user_id)
    {
        global $wpdb;

        $table_user_plan = $wpdb->prefix . 'exercise_user_plan';
        $table_plan = $wpdb->prefix . 'exercise_plan';

        $query = $wpdb->prepare(
            "SELECT 
                p.id AS plan_id,
                p.name,
                p.cardio,
                p.strength,
                p.description,
                p.image,
                p.vertical_image
            FROM $table_user_plan up
            INNER JOIN $table_plan p ON up.plan_id = p.id
            WHERE up.user_id = %d",
            $user_id
        );

        $results = $wpdb->get_results($query, ARRAY_A);


        return $results;
    }

    private function get_plan_by_id($plan_id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_plan';

        $result = $wpdb->get_results("SELECT id,name, cardio, strength, description FROM $table_name Where id = " . $plan_id, ARRAY_A);

        return $result;
    }

    private function get_user_meta($user_id)
    {
        global $wpdb;

        $table = $wpdb->prefix . "exercise_usermeta";

        $result = $wpdb->prepare("Select * from $table where user_id = " . $user_id);

        return $wpdb->get_results($result, ARRAY_A);
    }

    private function get_goal()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_goal';

        $result = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

        return $result;
    }

    private function get_plan_by_goal($goal_id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_plan_goal';

        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE goal_id = " . $goal_id, ARRAY_A);

        return $result;
    }

    private function get_weeks($plan_id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_weeks';

        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE plan_id = " . $plan_id, ARRAY_A);

        return $result;
    }

    private function get_weeks_by_number_week($number)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_weeks';

        $result = $wpdb->get_results("SELECT id FROM $table_name WHERE week_number = " . $number, ARRAY_A);

        return $result;
    }

    private function get_week_by_id($week_id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_weeks';

        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE id = " . $week_id, ARRAY_A);

        return $result;
    }

    private function get_days($week_id, $limit)
    {

        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_days';

        $table_training_method = $wpdb->prefix . 'exercise_training_method';

        $query = $wpdb->prepare("
        SELECT 
            d.id AS day_id,
            d.num_days,
            tm.name AS training_method_name
        FROM $table_name d
        INNER JOIN $table_training_method tm ON d.training_method_id = tm.id
        WHERE d.week_id = %d
        ORDER BY d.priority DESC
        LIMIT %d
    ", $week_id, $limit);

        $result = $wpdb->get_results($query, ARRAY_A);

        return $result;
    }

    private function get_day_by_id($day_id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_days';

        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE id = " . $day_id, ARRAY_A);

        return $result;
    }

    private function get_sections($day_id)
    {

        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_section';


        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE day_id = " . $day_id, ARRAY_A);

        return $result;
    }

    private function get_report_exercise($section_id)
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT sec.round, ex.met_value, es.duration, es.reps
            FROM {$wpdb->prefix}exercise_section sec
            INNER JOIN {$wpdb->prefix}exercise_schedule es ON sec.id = es.section_id
            INNER JOIN {$wpdb->prefix}exercise ex ON ex.id = es.exercise_id
            WHERE sec.id = %d",
            $section_id
        );

        $results = $wpdb->get_results($query, ARRAY_A);

        return $results;
    }

    private function get_sections_by_id($section_id)
    {

        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_section';


        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE id = " . $section_id, ARRAY_A);

        return $result;
    }

    private function get_exercise_schedule($section_id)
    {

        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_schedule';


        $result = $wpdb->get_results("SELECT  * FROM $table_name  WHERE section_id = 
        " . $section_id, ARRAY_A);

        return $result;
    }

    private function check_section_finish($section_id, $user_id)
    {

        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_finish_section';

        $query = $wpdb->prepare(
            "SELECT * FROM $table_name WHERE section_id = %d AND user_id = %d",
            $section_id,
            $user_id
        );

        $result = $wpdb->get_results($query, ARRAY_A);

        return $result;
    }

    private function get_exercise_by_id($exercise_id, $list = false)
    {

        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise';


        if ($list) {
            $result = $wpdb->get_results("SELECT  name,image_male,image_female FROM $table_name  WHERE id = " . $exercise_id, ARRAY_A);
        } else {
            $result = $wpdb->get_results("SELECT  * FROM $table_name  WHERE id = " . $exercise_id, ARRAY_A);
        }

        return $result;
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

    private function handle_section($sections, $weight, $user_id, $userTime)
    {

        $data = array();

        $i = 0;

        foreach ($sections as $key => $section) {

            $duration = 0;

            $name = 'Warm Up';

            $excSchedule = $this->get_exercise_schedule($section['id']);

            $report = $this->get_report_exercise($section['id']);

            $calorie = 0;

            $image = "https://wordpress-1308981-4772530.cloudwaysapps.com/wp-content/uploads/2024/07/Warmup.png";

            $time = 0;

            if ($section['type'] == 1) {
                if ($userTime == 1) {
                    $time = 20;
                } elseif ($userTime == 2) {
                    $time = 40;
                } else {
                    $time = 60;
                }
            }

            foreach ($excSchedule as $exc) {
                if (empty($exc['duration'])) {
                    $duration += $exc['reps'] * 5;
                } else {
                    $duration += $exc['duration'];
                }
            }

            $duration = round($duration / 60);

            $round = $section['round'];

            if ($time) {
                if ($duration * $round < $time) {

                    $round = floor($time / $duration);

                    $round == 0 ? $round = 1 : $round;

                } elseif ($duration * $section['round'] > $time) {

                    for ($y = ($section['round'] - 1); $y > 0 && $y < $section['round']; $y--) {

                        if ($duration * $y <= $time) {

                            $round = $y;

                            $y = 0;
                        }

                        if ($y == 1 && $duration * $y > $time) {

                            $round = 1;

                            $y = 0;
                        }
                    }
                }
            }

            $duration = $duration * $round;

            foreach ($report as $item) {

                if (!$item['duration']) {
                    $calorie += (($item['met_value'] * 3.5 * $weight) / 200 * (($round * $item['reps'] * 5) / 60));
                } else {
                    $calorie += (($item['met_value'] * 3.5 * $weight) / 200 * (($round * $item['duration']) / 60));
                }
            }

            if ($i == 1) {
                $name = 'Main Workout';
                $image = "https://wordpress-1308981-4772530.cloudwaysapps.com/wp-content/uploads/2024/07/Super-Cardio-Burner.png";
            } elseif ($i == 2) {
                $name = 'Cooldown';
                $image = "https://wordpress-1308981-4772530.cloudwaysapps.com/wp-content/uploads/2024/07/Cooldown.png";
            }

            $finishSection = $this->check_section_finish($section['id'], $user_id);

            if (empty($finishSection)) {
                $finishSection = 0;
            } else {
                $finishSection = $finishSection[0]['finish'];
            }
            $data[$i] = [
                'section_id' => $section['id'],
                'name' => $name,
                'image' => $image,
                'duration' => $duration,
                'round' => (int) $round,
                'finish' => (int) $finishSection,
                'calorie' => round($calorie)
            ];

            $i++;
        }

        return $data;
    }

    private function get_primary_option($id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_primary_option';

        $table_name2 = $wpdb->prefix . 'exercise_muscle_anatomy';

        $query = $wpdb->prepare("
                SELECT a.exercise_id, a.muscle_id, b.name,b.description,b.image
                FROM $table_name a
                INNER JOIN $table_name2 b ON a.muscle_id = b.id
                WHERE a.exercise_id = %d
            ", $id);

        $results = $wpdb->get_results($query, ARRAY_A);

        return $results;
    }

    private function get_secondary_option($id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_secondary_option';

        $table_name2 = $wpdb->prefix . 'exercise_muscle_anatomy';

        $query = $wpdb->prepare("
                SELECT a.exercise_id, a.muscle_id, b.name,b.description,b.image
                FROM $table_name a
                INNER JOIN $table_name2 b ON a.muscle_id = b.id
                WHERE a.exercise_id = %d
            ", $id);

        $results = $wpdb->get_results($query, ARRAY_A);

        return $results;
    }


    private function get_equipment_option($id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_equipment_option';

        $table_name2 = $wpdb->prefix . 'exercise_equipment';

        $query = $wpdb->prepare("
                SELECT a.exercise_id, a.equipment_id, b.name
                FROM $table_name a
                INNER JOIN $table_name2 b ON a.equipment_id = b.id
                WHERE a.exercise_id = %d
            ", $id);

        $results = $wpdb->get_results($query, ARRAY_A);

        return $results;
    }

    protected function get_content_meta($id)
    {
        global $wpdb;

        $result = array();
        if ($id) {
            $table_name = $wpdb->prefix . 'exercise_content';
            $query = $wpdb->prepare("
                SELECT *  FROM $table_name WHERE exercise_id = %d
            ", $id);
            $results = $wpdb->get_results($query, ARRAY_A);
        }

        return $results;

    }

    private function handle_meta($user_id, $goal_id)
    {
        $userMeta = $this->get_user_meta($user_id);

        $month = $userMeta[0]['experience'];

        $appearance = (int) $userMeta[0]['appearance_option'];

        $page_plan_option = (int) $userMeta[0]['page_plan_option'];

        if ($month == 1) {

            $plan_type = "easy";

        } elseif ($month == 2) {

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

        $planId = $this->get_plan_by_goal_a($goal_id, $plan_type);

        return $planId;

    }
    private function get_plan_by_goal_a($goal_id, $plan_type)
    {
        global $wpdb;

        $table_plan = $wpdb->prefix . "exercise_plan";
        $table_plan_goal = $wpdb->prefix . "exercise_plan_goal";

        if ($plan_type == 'easy') {
            $count_query = $wpdb->prepare(
                "SELECT p.id,p.name,p.description,p.cardio,p.strength,p.image,p.vertical_image
                 FROM $table_plan p
                 INNER JOIN $table_plan_goal pg ON p.id = pg.plan_id
                 WHERE pg.goal_id = %d
                 AND (p.cardio + p.strength) <= 2",
                $goal_id
            );
        } elseif ($plan_type == 'moderate') {
            $count_query = $wpdb->prepare(
                "SELECT p.id,p.name,p.description,p.cardio,p.strength,p.image,p.vertical_image
                 FROM $table_plan p
                 INNER JOIN $table_plan_goal pg ON p.id = pg.plan_id
                 WHERE pg.goal_id = %d
                 AND (p.cardio + p.strength) > 2 AND (p.cardio + p.strength) < 6",
                $goal_id
            );
        } else {
            $count_query = $wpdb->prepare(
                "SELECT  p.id,p.name,p.description,p.cardio,p.strength,p.image,p.vertical_image
                 FROM $table_plan p
                 INNER JOIN $table_plan_goal pg ON p.id = pg.plan_id
                 WHERE pg.goal_id = %d
                 AND (p.cardio + p.strength) >= 6",
                $goal_id
            );
        }

        $result = $wpdb->get_results($count_query, ARRAY_A);

        return $result;

    }
}

add_action('rest_api_init', function () {
    $controller = new Plan_Controller();
    $controller->register_routes();
});
