<?php

if (!defined('ABSPATH')) {
    exit;
}

class Exercise_Survey_API {

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        register_rest_route('wp/v2', '/exercise-survey', [
            'methods' => 'GET',
            'callback' => [$this, 'get_exercise_survey']
        ]);
    }

    public function get_exercise_survey(WP_REST_Request $request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'exercise_goal';
        $table_name2 = $wpdb->prefix . 'exercise_muscle_type';

        $args = ['id','name'];
        $args = implode(',',$args);
        
        $goal = $wpdb->get_results("SELECT $args FROM $table_name WHERE active = 1", ARRAY_A);
        $muscle_type = $wpdb->get_results("SELECT $args FROM $table_name2 ", ARRAY_A);
        

        if (empty($goal) && empty($muscle_type)) {
            return new WP_Error('no_exercise_goals', 'No exercise survey found', ['status' => 404]);
        }

        $results['data'] = [
            'goal' => $goal,
            'muscle_type' => $muscle_type
        ];
        $results['status'] = 200;
        
        return new WP_REST_Response($results, 200);
    }

    public function permissions_check($request) {
        return current_user_can('read');
    }
}
