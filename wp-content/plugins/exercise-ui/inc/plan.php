<?php
class Plan extends Manage_Exercise
{

    private $plan_data;

    private function get_exercise($search = "")
    {
        global $wpdb;

        if (!empty($search)) {
            return $wpdb->get_results(
                "SELECT * from {$wpdb->prefix}exercise_plan WHERE id Like '%{$search}%' OR name LIKE '%{$search}%'",
                ARRAY_A
            );
        } else {
            return $wpdb->get_results(
                "SELECT * From {$wpdb->prefix}exercise_plan",
                ARRAY_A
            );
        }
    }

    // Define table columns
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox"/>',
            'id' => 'ID',
            'name' => 'Name',
            'cardio' => 'Cardio Level',
            'strength' => 'Strength Level',
            'active' => 'Active',
            'created_at' => 'Create At',
        );

        return $columns;
    }

    // Bind table with columns, data and all
    function prepare_items()
    {
        if (isset($_POST['page']) && isset($_POST['s'])) {
            $this->plan_data = $this->get_exercise($_POST['s']);
        } else {
            $this->plan_data = $this->get_exercise();
        }
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();
        /* pagination */
        $per_page = $this->get_items_per_page('plan_per_page', 15);
        $current_page = $this->get_pagenum();
        $total_items = count($this->plan_data);

        $this->plan_data = array_slice($this->plan_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(
            array(
                'total_items' => $total_items, // total number of items
                'per_page' => $per_page // items to show on a page
            )
        );

        // usort($this->plan_data, array(&$this, 'usort_reorder'));

        $this->items = $this->plan_data;
    }

    // bind data with column
    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'name':
            case 'cardio':
            case 'strength':
            case 'active':
            case 'created_at':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    // To show checkbox with each row
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="plan_id[]" value="%s" />',
            $item['id']
        );
    }

    // Sorting function
    function usort_reorder($a, $b)
    {
        // If no sort, default to user_login
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : '';
        // If no order, default to asc
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
        // Determine sort order
        $result = strcmp($a[$orderby], $b[$orderby]);
        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }

    function get_bulk_actions()
    {
        $actions = array(
            'delete_multiple' => __('Move to Trash', 'supporthost-admin-table'),
            'published_mutiple' => __('Publish', 'supporthost-admin-table'),
            'unpublished_mutiple' => __('Unpublish', 'supporthost-admin-table'),
            'export-all' => 'Export All',
            'export-selected' => 'Export Selected'
        );
        return $actions;
    }

    public function process_bulk_action($text = '')
    {
        if (!$text) {
            $action = $this->current_action();

            if ($action == 'delete_multiple') {
                $this->delete_data_by_id('exercise_plan', $_POST['plan_id']);
            }

            if ($action == 'published_mutiple') {
                $this->publish_data_by_id('exercise_plan', $_POST['plan_id']);
            }

            if ($action == 'unpublished_mutiple') {
                $this->unpublish_data_by_id('exercise_plan', $_POST['plan_id']);
            }

            if ("export-all" === $this->current_action()) {
                global $wpdb;

                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="exercise.csv"');

                // clean out other output buffers
                ob_end_clean();

                $fp = fopen('php://output', 'w');

                // CSV/Excel header label
                $header_row = array(
                    0 => 'Name',
                    1 => 'Cardio',
                    2 => 'Strength',
                    3 => 'Muscle Type',
                    4 => 'Active'
                );

                fputcsv($fp, $header_row);

                $Table_Name = 'wp_exercise';
                $sql_query = "SELECT * FROM $Table_Name";
                $rows = $wpdb->get_results($sql_query, ARRAY_A);

                $muscle_table = $wpdb->prefix . 'exercise_muscle_type';

                $muscle_id = array();
                if (!empty($rows)) {
                    foreach ($rows as $Record) {

                        foreach ($Record['muscle_type_id'] as $key => $muscle) {
                            $query = $wpdb->prepare("Select id From $muscle_table Where name = %d", $muscle);

                            $result = $wpdb->get_results($query, ARRAY_A);

                            if (!empty($result)) {
                                array_push($muscle_id, $result[0]['id']);
                            }
                        }

                        if (!empty($muscle_id)) {
                            $muscle_id = implode(',', $muscle_id);
                        }

                        $OutputRecord = array(
                            $Record['name'],
                            $Record['cardio'],
                            $Record['strength'],
                            $muscle_id,
                            $Record['active'],
                        );
                        fputcsv($fp, $OutputRecord);
                    }
                }

                fclose($fp);
                exit;
            }

            if ("export-selected" === $this->current_action()) {
                $ids = isset($_POST['equipment_id']) ? $_POST['equipment_id'] : array();
                if (is_array($ids))
                    $ids = implode(',', $ids);

                if (!empty($ids)) {
                    // Use headers so the data goes to a file and not displayed
                    global $wpdb;

                    header('Content-Type: text/csv');
                    header('Content-Disposition: attachment; filename="exercise-seleted.csv"');

                    // clean out other output buffers
                    ob_end_clean();

                    $fp = fopen('php://output', 'w');

                    // CSV/Excel header label
                    $header_row = array(
                        0 => 'ID',
                        1 => 'Name',
                        2 => 'Other Name',
                        3 => 'Description',
                        4 => 'Video White Male',
                        5 => 'Video Green',
                        6 => 'Video Transparent',
                        7 => 'Image Male',
                        8 => 'Image Female',
                        9 => 'Active',
                        10 => 'Created_at',
                        11 => 'Updated_at',
                    );

                    //write the header
                    fputcsv($fp, $header_row);

                    $Table_Name = 'wp_exercise';
                    $sql_query = $wpdb->prepare("SELECT * FROM $Table_Name WHERE id IN (%s)", $ids);
                    $rows = $wpdb->get_results($sql_query, ARRAY_A);
                    if (!empty($rows)) {
                        foreach ($rows as $Record) {
                            $OutputRecord = array(
                                $Record['id'],
                                $Record['name'],
                                $Record['other_name'],
                                $Record['description'],
                                $Record['video_white_male'],
                                $Record['video_green'],
                                $Record['video_transparent'],
                                $Record['image_male'],
                                $Record['image_female'],
                                $Record['active'],
                                $Record['created_at'],
                                $Record['updated_at']
                            );
                            fputcsv($fp, $OutputRecord);
                        }
                    }

                    fclose($fp);
                    exit;
                }
            }
        } elseif ($text == 'import') {
            if ($_FILES['quiz_import_file']['error'] == UPLOAD_ERR_OK) {
                $file_path = $_FILES['quiz_import_file']['tmp_name'];

                $this->import_data_from_csv($file_path);

            } else {
                $this->redirect_page(admin_url('admin.php?page=exerciseui_manage_plan'));
            }
        } elseif ($text == 'import_week') {
            if ($_FILES['quiz_import_file']['error'] == UPLOAD_ERR_OK) {
                $file_path = $_FILES['quiz_import_file']['tmp_name'];

                $this->import_week_data_from_csv($file_path);

            } else {
                $this->redirect_page(admin_url('admin.php?page=exerciseui_manage_plan'));
            }
        } elseif ($text == 'import_day') {
            if ($_FILES['quiz_import_file']['error'] == UPLOAD_ERR_OK) {
                $file_path = $_FILES['quiz_import_file']['tmp_name'];

                $this->import_day_data_from_csv($file_path);

            } else {
                $this->redirect_page(admin_url('admin.php?page=exerciseui_manage_plan'));
            }
        }
    }

    function import_data_from_csv($file_path)
    {
        global $wpdb;

        $csv_file = fopen($file_path, "r");

        $header = fgetcsv($csv_file);

        $table_name = $wpdb->prefix . 'exercise_plan';

        while (($data = fgetcsv($csv_file)) !== FALSE) {
            $muscle_table = $wpdb->prefix . 'exercise_muscle_type';

            $muscle_id = array();

            $record = array_combine($header, $data);

            $muscles = explode(',', $record['muscle']);

            foreach ($muscles as $key => $muscle) {
                $query = $wpdb->prepare("Select id From $muscle_table Where name = %s", str_replace(' ', '', $muscle));

                $result = $wpdb->get_results($query, ARRAY_A);

                if (!empty($result)) {
                    array_push($muscle_id, $result[0]['id']);
                }
            }

            $muscle_id = implode(',', $muscle_id);

            $prepared_data = array(
                'name' => $record['name'],
                'cardio' => $record['cardio'],
                'strength' => $record['strength'],
                'description' => $record['description'],
                'image' => $record['image'],
                'vertical_image' => $record['vertical_image'],
                'muscle_type_id' => $muscle_id
            );


            $existing_record = $wpdb->get_row($wpdb->prepare("SELECT id FROM $table_name WHERE name = %s", $record['name']));

            $id = 0;
            if ($existing_record) {
                $wpdb->update($table_name, $prepared_data, array('name' => $record['name']));
                $id = $existing_record->id;
            } else {
                $wpdb->insert($table_name, $prepared_data);
                $id = $wpdb->insert_id;
            }

            if ($id && !empty($record['goal'])) {

                $goals = explode(',', $record['goal']);

                $tableGoal = $wpdb->prefix . 'exercise_goal';

                foreach ($goals as $goal) {
                    $goalQuery = $wpdb->prepare("Select id From $tableGoal Where name = %s", $goal);

                    $resultGoal = $wpdb->get_row($goalQuery);

                    if ($resultGoal) {

                        $tablePlanGoal = $wpdb->prefix . 'exercise_plan_goal';

                        $planGoal = $wpdb->prepare("Select COUNT(*) FROM $tablePlanGoal Where plan_id = %d AND goal_id = %d", $id, $resultGoal->id);

                        $resultPlanGoal = $wpdb->get_var($planGoal);
                        if (!$resultPlanGoal) {

                            $arr['plan_id'] = $id;
                            $arr['goal_id'] = $resultGoal->id;

                            $wpdb->insert(
                                $tablePlanGoal,
                                $arr
                            );
                        }

                    }

                }
            }
        }

        // Đóng tệp CSV sau khi hoàn thành
        fclose($csv_file);
        $this->redirect_page(admin_url('admin.php?page=exerciseui_manage_plan'));
    }

    function import_week_data_from_csv($file_path)
    {
        global $wpdb;

        $csv_file = fopen($file_path, "r");

        $header = fgetcsv($csv_file);

        $table = $wpdb->prefix . 'exercise_plan';

        $tableWeek = $wpdb->prefix . 'exercise_weeks';

        while (($data = fgetcsv($csv_file)) !== FALSE) {

            $record = array_combine($header, $data);

            $planQuery = $wpdb->prepare("Select id From $table Where name = %s", $record['plan_name']);

            $plan_id = $wpdb->get_var($planQuery);

            if ($plan_id) {

                $prepared_data = array(
                    'plan_id' => $plan_id,
                    'week_name' => $record['week_name'],
                    'week_description' => $record['week_description'],
                    'week_number' => $record['week_number'],
                );

                $weekQuery = $wpdb->prepare("Select id * From $tableWeek Where plan_id = %d AND week_number = %d", $plan_id, $record['week_number']);

                $resultWeek = $wpdb->get_var($weekQuery);

                if (!$resultWeek) {
                    $wpdb->insert(
                        $tableWeek,
                        $prepared_data
                    );
                }
            }
        }

        // Đóng tệp CSV sau khi hoàn thành
        fclose($csv_file);
        $this->redirect_page(admin_url('admin.php?page=exerciseui_manage_plan'));
    }

    function import_day_data_from_csv($file_path)
    {
        global $wpdb;

        $csv_file = fopen($file_path, "r");

        $header = fgetcsv($csv_file);

        $table = $wpdb->prefix . 'exercise_plan';

        $tableWeek = $wpdb->prefix . 'exercise_weeks';

        $tableDay = $wpdb->prefix . 'exercise_days';

        $tableTraining = $wpdb->prefix . 'exercise_training_method';

        $tableSection = $wpdb->prefix . 'exercise_section';

        $tableExercise = $wpdb->prefix . 'exercise';

        $tableSchedule = $wpdb->prefix . 'exercise_schedule';


        while (($data = fgetcsv($csv_file)) !== FALSE) {

            $record = array_combine($header, $data);

            $planQuery = $wpdb->prepare("Select id From $table Where name = %s", $record['plan_name']);

            $plan_id = $wpdb->get_var($planQuery);

            if ($plan_id) {

                $weekQuery = $wpdb->prepare("Select id From $tableWeek Where plan_id = %d AND week_number = %d", $plan_id, $record['week_number']);

                $idWeek = $wpdb->get_var($weekQuery);

                if ($idWeek) {

                    $dayQuery = $wpdb->prepare("Select id  From $tableDay Where week_id = %d AND num_days = %d", $idWeek, $record['num_days']);

                    $idDay = $wpdb->get_var($dayQuery);

                    if (!$idDay) {
                        $trainingQuery = $wpdb->prepare("Select id  From $tableTraining Where name = %s", $record['training_method']);

                        $idTraining = $wpdb->get_var($trainingQuery);

                        if ($idTraining) {

                            $prepare_day = array(
                                'week_id' => $idWeek,
                                'num_days' => $record['num_days'],
                                'training_method_id' => $idTraining,
                                'priority' => $record['priority']
                            );

                            $result = $wpdb->insert(
                                $tableDay,
                                $prepare_day
                            );

                            if ($result) {
                                $idDay = $wpdb->insert_id;
                            }
                        }

                    }

                    if ($record['section'] == 'Warm Up') {
                        $type = 0;
                    } elseif ($record['section'] == 'Main Workout') {
                        $type = 1;
                    } else {
                        $type = 2;
                    }

                    $sectionQuery = $wpdb->prepare("Select id  From $tableSection Where day_id = %d AND type = %d", $idDay, $type);

                    $idSection = $wpdb->get_var($sectionQuery);

                    if (!$idSection) {
                        $prepare_section = array(
                            'day_id' => $idDay,
                            'type' => $type,
                            'round' => $record['round']
                        );

                        $result = $wpdb->insert(
                            $tableSection,
                            $prepare_section
                        );

                        $idSection = $result ? $wpdb->insert_id : "";
                    }

                    $exerciseQuery = $wpdb->prepare("Select id  From $tableExercise Where name = %s", $record['exercise']);

                    $idExercise = $wpdb->get_var($exerciseQuery);

                    if ($idExercise) {

                        $scheduleQuery = $wpdb->prepare("Select id  From $tableSchedule Where section_id = %d AND exercise_id = %d", $idSection, $idExercise);

                        $idSchedule = $wpdb->get_var($scheduleQuery);

                        if (!$idSchedule || $idExercise == 150) {

                            $prepare_schedule = array(
                                'section_id' => $idSection,
                                'exercise_id' => $idExercise,
                                'duration' => $record['duration'],
                                'reps' => $record['reps'],
                                'note' => $record['note']
                            );

                            $wpdb->insert(
                                $tableSchedule,
                                $prepare_schedule
                            );
                        }

                    }

                }

            }
        }

        // Đóng tệp CSV sau khi hoàn thành
        fclose($csv_file);
        $this->redirect_page(admin_url('admin.php?page=exerciseui_manage_plan'));
    }

    function extra_tablenav($which)
    {
        if ($which == "top") {
            echo '<div class="alignleft actions bulkactions update-multiple" style="display:flex;">
                    <button class="button update-multiple-reviews" style="display: none">Update</button>
                </div>';
        }
        if ($which == "bottom") {
            // echo"Hi, I'm after the table";
        }
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'id' => array('id', false),
        );

        return $sortable_columns;
    }

    // Fetch, prepare, sort, and filter our data...
    function get_data()
    {
        $this->prepare_items();
        return $this->items;
    }

    // Add screen options
    function add_screen_options()
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Exercises',
            'default' => 15,
            'option' => 'plan_per_page'
        );
        add_screen_option($option, $args);
    }

    function get_option($option, $default = 10)
    {
        return get_user_meta(get_current_user_id(), $option, true) ?: $default;
    }


    function column_name($item)
    {
        $actions = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&plan=%s">Edit</a>', 'exerciseui_manage_plan', 'edit', $item['id']),
            'delete' => sprintf('<a href="?page=%s&action=%s&plan=%s">Delete</a>', 'exerciseui_manage_plan', 'delete', $item['id']),
        );
        return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions));
    }


    function column_active($item)
    {
        $text = $item['active'] == 1 ? "Pulished" : "Unpublished";
        $class = $item['active'] == 1 ? "published" : "unpublished";

        return sprintf("<p class='%s'>%s</p>", $class, $text);
    }

    function handle_data($type, $data)
    {
        global $wpdb;

        $tablePlan = $wpdb->prefix . 'exercise_plan';

        $plan = $data['plan'];

        $weeks = array();

        if ($plan['week']) {
            $weeks = $plan['week'];
            unset($plan['week']);
        }

        $query = $wpdb->prepare("Select id From $tablePlan Where name = %s", $plan['name']);

        $row = $wpdb->get_var($query);

        $check = false;

        if ($row) {
            $wpdb->update(
                $tablePlan,
                $plan,
                array(
                    "id" => $row
                )
            );

            $check = true;
        } else {
            $result = $wpdb->insert(
                $tablePlan,
                $plan
            );

            $row = $wpdb->insert_id;
        }

        if ($row) {
            if ($weeks) {
                $this->handle_week($check, $row, $weeks);
            }
        }

        return admin_url('admin.php?page=exerciseui_manage_plan');
    }

    function handle_week($check, $planId, $weeks)
    {
        global $wpdb;

        $tableWeeks = $wpdb->prefix . 'exercise_weeks';
        $tableDays = $wpdb->prefix . 'exercise_days';
        $tableTraining = $wpdb->prefix . 'exercise_training_method';
        $tableSection = $wpdb->prefix . 'exercise_section';
        $tableSchedule = $wpdb->prefix . 'exercise_schedule';

        if ($check) {
            $wpdb->delete(
                $tableWeeks,
                array(
                    "plan_id" => $planId
                )
            );
        }

        foreach ($weeks as $keyy => $week) {
            $days = array();

            if (!empty($week['days'])) {
                $days = $week['days'];
                unset($week['days']);
            }
            ;

            $week['plan_id'] = $planId;
            $week['week_number'] = $keyy + 1;

            $resultWeek = $wpdb->insert(
                $tableWeeks,
                $week
            );

            if ($resultWeek) {
                $weekId = $wpdb->insert_id;
            }

            $priority = 6;
            $nums_day = 1;
            foreach ($days as $key => $day) {
                $sections = array();

                if (!empty($day['sections'])) {
                    $sections = $day['sections'];
                    unset($day['sections']);
                }


                if ($weekId) {
                    $day['week_id'] = $weekId;
                    $day['num_days'] = $nums_day;
                    $day['priority'] = $priority;

                    $resultDays = 0;

                    $checkRound = false;
                    for($i = 0; $i < 3; $i++){
                        if(!empty($sections)) {
                            if(!empty(($sections['section-'.$i]['round']))) {
                                $checkRound = true;
                            }
                        }
                    }

                    if($checkRound == true) {
                        $resultDays = $wpdb->insert(
                            $tableDays,
                            $day
                        );
                    }

                    if ($resultDays) {
                        $dayId = $wpdb->insert_id;
                        $priority--;
                        $nums_day++;
                        $resultSection = 0;
                        $sectionType = 0;
                        
                        foreach ($sections as $key => $section) {
                            if (!empty($section['exercise']) && !empty($section['round'])) {
                                $section['day_id'] = $dayId;
                                $resultSection = $wpdb->insert(
                                    $tableSection,
                                    array(
                                        'day_id' => $dayId,
                                        'type' => $sectionType,
                                        'round' => $section['round'],
                                    )
                                );
                            }

                            if ($resultSection && !empty($section['exercise'])) {
                                $sectionType++;
                                $sectionId = $wpdb->insert_id;
                                $sec = explode(',', $section['exercise']);

                                $duration = !empty($section['duration']) ? explode(',', $section['duration']) : 0;
                                $reps = !empty($section['reps']) ? explode(',', $section['reps']) : 0;
                                $note = !empty($section['note']) ? explode(',', $section['note']) : '';

                                foreach ($sec as $exKey => $exercise) {
                                    $durationItem = !empty($duration[$exKey]) ? $duration[$exKey] : $duration;
                                    $repsItem = !empty($reps[$exKey]) ? $reps[$exKey] : $reps;
                                    $noteItem = !empty($note[$exKey]) ? $note[$exKey] : $note;

                                    $wpdb->insert(
                                        $tableSchedule,
                                        array(
                                            'section_id' => $sectionId,
                                            'exercise_id' => $exercise,
                                            'duration' =>  $durationItem,
                                            'reps' => $repsItem,
                                            'note' => $noteItem
                                        )
                                    );

                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function delete_plan($id)
    {
        $this->delete_data_by_id('exercise_plan', $id);
    }

    function get_training_method()
    {
        global $wpdb;

        $table = $wpdb->prefix . 'exercise_training_method';

        $query = "Select * From $table";

        $result = $wpdb->get_results($query, ARRAY_A);

        return $result;
    }

    function get_training_method_by_plan($id, $weekId)
    {
        global $wpdb;

        $query = $wpdb->prepare("
            SELECT etraining.id, edays.num_days, edays.id as day_id
            FROM {$wpdb->prefix}exercise_plan eplan
            INNER JOIN {$wpdb->prefix}exercise_weeks eweeks ON eplan.id = eweeks.plan_id
            INNER JOIN {$wpdb->prefix}exercise_days edays ON eweeks.id = edays.week_id
            INNER JOIN {$wpdb->prefix}exercise_training_method etraining ON edays.training_method_id = etraining.id
            WHERE eplan.id = %d
            AND eweeks.id = %d
        ", $id, $weekId);

        $resutls = $wpdb->get_results($query, ARRAY_A);

        return $resutls;
    }

    function get_week_by_plan($planId)
    {
        global $wpdb;

        $tableWeeks = $wpdb->prefix . 'exercise_weeks';

        $query = $wpdb->prepare("Select * From $tableWeeks Where plan_id = %d", $planId);

        $result = $wpdb->get_results($query, ARRAY_A);

        return $result;
    }

    function get_days_by_week($weekId)
    {
    }

    function in_array_recursive($needle, $haystack)
    {
        foreach ($haystack as $item) {
            if (is_array($item)) {
                if ($this->in_array_recursive($needle, $item)) {
                    return true;
                }
            } else {
                if ($needle == $item) {
                    return true;
                }
            }
        }
        return false;
    }
    function render_plan($data, $action, $text = '')
    {
        $id = !empty($data->id) ? $data->id : '';
        $title = $action == 'edit' ? "Edit" : "Add";

        $mets = $this->get_mets();

        $name = !empty($data->name) ? $data->name : '';
        $cardio = !empty($data->cardio) ? $data->cardio : '';
        $strength = !empty($data->strength) ? $data->strength : '';
        $img = !empty($data->image) ? $data->image : '';
        $verticalImg = !empty($data->vertical_image) ? $data->vertical_image : '';
        $description = !empty($data->description) ? $data->description : '';
        $status = !empty($data->description) ? $data->active : 1;
        $training_methods = $this->get_training_method();
        $weeks = array();

        if ($id) {
            $weeks = $this->get_week_by_plan($id);
        }
        ?>
        <div id="overlay">
            <div class="cv-spinner">
                <span class="spinner-ex"></span>
            </div>
        </div>
        <div class="exercice-form-section">
            <h1 class="wp-heading-inline">
                <?= $title . $name . " Plan" ?>
<!--                 <input type="submit"
                    data-link="<?= admin_url('admin.php?page=exerciseui_manage_plan&action=edit&plan=') . $id ?>" value="Save"
                    class="button plan-button" id="muscle-button-apply-top"> -->
            </h1>
            <?= $text ?>
            <div class="container">
                <form method="POST" id="muscle-form">
                    <div class="field">
                        <div class="field-label attention">
                            <label for="name">Name</label>
                        </div>
                        <div class="field-item">
                            <input type="text" name="plan[name]" id="name" value="<?= $name ?>">
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">
                            <label for="excDescription">Description</label>
                        </div>
                        <div class="field-item">
                            <?php
                            $editor_id = 'excDescription';
                            $settings = array(
                                'textarea_name' => 'plan[description]',
                                'textarea_rows' => 4,
                                'media_buttons' => false,
                                'quicktags' => false,
                                'editor_class' => 'my-wp-editor'
                            );
                            wp_editor($description, $editor_id, $settings);
                            ?>
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label attention">
                            <label for="cardio">Cardio Level</label>
                        </div>
                        <div class="field-item">
                            <input type="number" value="1" min="1" max="3" name="plan[cardio]" id="cardio" value="<?= $cardio ?>">
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label attention">
                            <label for="strength">Strength Level</label>
                        </div>
                        <div class="field-item">
                            <input type="number" value="1" min="1" max="3" name="plan[strength]" id="strength" value="<?= $strength ?>">
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">
                            <label>
                                Image
                                <a href="javascript:void(0)"
                                    class="add-exercise-image"><?= $img ? "Edit Image" : "Add Image" ?></a>
                            </label>
                        </div>
                        <div class="exercise-image-container" style="<?= $img ? 'display: block' : 'display:none'; ?>">
                            <span class="remove-exercise-img">x</span>
                            <img src="<?= $img ?>" class="exercise-img" />
                            <input type="hidden" name="plan[image]" class="exercise-image" value="<?= $img ?>" />
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">
                            <label>
                                Vertical Image
                                <a href="javascript:void(0)"
                                    class="add-exercise-image"><?= $verticalImg ? "Edit Image" : "Add Image" ?></a>
                            </label>
                        </div>
                        <div class="exercise-image-container" style="<?= $verticalImg ? 'display: block' : 'display:none'; ?>">
                            <span class="remove-exercise-img">x</span>
                            <img src="<?= $verticalImg ?>" class="exercise-img" />
                            <input type="hidden" name="plan[vertical_image]" class="exercise-image"
                                value="<?= $verticalImg ?>" />
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">
                            <label for="type">Status</label>
                        </div>
                        <div class="field-item field-item-radio">
                            <div class="item">
                                <input type="radio" name="plan[active]" <?= $status == 1 ? 'checked' : '' ?> value="1"
                                    id="published">
                                <label for="published">Published</label>
                            </div>
                            <div class="item">
                                <input type="radio" name="plan[active]" <?= $status == 0 ? 'checked' : '' ?> value="0"
                                    id="unpublished">
                                <label for="unpublished">Unpublished</label>
                            </div>
                        </div>
                    </div>
                    <div class="exercise-table-wrap">
                        <?php
                        require_once (MY_PLUGIN_DIR . "/inc/plan/weeks.php");
                        ?>
                    </div>
                    <input type="hidden" name="id" value="<?= $id ?>" />
                    <input type="hidden" name="action" value="<?= $action ?>" />
                    <input type="hidden" name="link" value="" />
                </form>
            </div>
        </div>
        <?php
    }

    function get_exercise_by_section($dayId, $type)
    {
        global $wpdb;

        $tableSection = $wpdb->prefix . 'exercise_section';
        $tableSchedule = $wpdb->prefix . 'exercise_schedule';

        $querySection = $wpdb->prepare(
            "Select id From $tableSection WHERE day_id = %s AND type = %s",
            $dayId,
            $type
        );

        $resultSection = $wpdb->get_results($querySection, ARRAY_A);

        $result = array();

        if (!empty($resultSection)) {
            $id = $resultSection[0]['id'];

            $query = $wpdb->prepare("
                Select section_id,exercise_id,duration,reps,note
                From $tableSchedule 
                WHERE section_id = %s
            ", $id);

            $resultEx = $wpdb->get_results($query, ARRAY_A);

            if (!empty($resultEx)) {
                $result = $resultEx;
            }

        }

        return $result;
    }

    function get_round($dayId, $type)
    {
        global $wpdb;

        $tableSection = $wpdb->prefix . 'exercise_section';
        $tableSchedule = $wpdb->prefix . 'exercise_schedule';

        $querySection = $wpdb->prepare(
            "Select round From $tableSection WHERE day_id = %s AND type = %s",
            $dayId,
            $type
        );

        $resultSection = $wpdb->get_results($querySection, ARRAY_A);

        return $resultSection;
    }
    function get_plan_by_id($id)
    {
        $data = $this->get_data_by_id('exercise_plan', $id);

        return $data;
    }
}

function exerciseui_manage_plan()
{

    $exercise = new Plan();

    $message = get_transient('message');
    $text = '';
    if ($message) {
        if (!empty($message['count']) && $message['count'] > 0) {
            $text = '<div class="notice notice-success"><p style="color: #4F8A10;">' . $message['text'] . '</p></div>';
        } else {
            $text = '<div class="notice notice-error"><p style="color: #D8000C;">' . $message['text'] . '</p></div>';
        }
        delete_transient('message');
    }

    if (empty($_GET['action'])) {
        if (empty($_POST['action'])) {
            $linkNew = admin_url('admin.php?page=exerciseui_manage_plan&action=new');
            echo '<div class="wrap">
        <h2 class="exercise-list-title">Manage Exercise
        <a href="' . $linkNew . '" class="button btn-new">Add New</a></h2>
        ' . $text . '
        </div>';
            ?>
            <div class="upload-import-file-wrap">
                <div class="upload-import-file">
                    <form action="<?= admin_url('admin.php?page=exerciseui_manage_plan') ?>" method="post"
                        enctype="multipart/form-data" class="ays-dn">
                        <select name="action" id="">
                            <option value="import">Exercise</option>
                            <option value="import_week">Week</option>
                            <option value="import_day">Day</option>
                        </select>
                        <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, .json"
                            name="quiz_import_file" id="import_file" />
                        <label class="screen-reader-text" for="import_file"><?= "Import file" ?></label>
                        <input type="submit" name="import-file-submit" class="button" value="<?= "Import now" ?>">
                    </form>
                </div>
            </div>
            <?php
            $exercise->prepare_items();
            ?>

            <form method="post">
                <input type="hidden" name="page" value="" />
                <?php $exercise->search_box('search', 'search_id'); ?>
            </form>
            <form action="<?= admin_url('admin.php?page=exerciseui_manage_plan') ?>" method="POST">
                <?php
                $exercise->display();
                ?>
            </form>
            <?php
            echo '</div>';
        } elseif ($_POST['action'] == 'import') {
            $exercise->process_bulk_action('import');
        } else if ($_POST['action'] == 'import_week') {
            $exercise->process_bulk_action('import_week');
        } else if ($_POST['action'] == 'import_day') {
            $exercise->process_bulk_action('import_day');
        } else {
            $exercise->process_bulk_action();
        }
    } elseif ($_GET['action'] == 'delete') {
        if (!empty($_GET['exercise'])) {
            $exercise->delete_plan($_GET['exercise']);
        } else {
            wp_redirect(admin_url('admin.php?page=exerciseui_manage_plan'));
            exit;
        }
    } else {
        if (!empty($_GET['plan'])) {
            $data = $exercise->get_plan_by_id($_GET['plan']);
            $exercise->render_plan($data, 'edit');
        } else {
            $exercise->render_plan(array(), 'add', $text);
        }
    }
}

add_action('wp_ajax_get_section_exercise', 'get_section_exercise');

function get_section_exercise()
{

    $arrExercise = array();

    $schedule = array();

    $searchValue = "";

    $type = 1; 

    $sectionId = "";

    if (!empty($_POST['section_id'])) {
        $sectionId = $_POST['section_id'];
    }

    if (!empty($_POST['search'])) {
        $searchValue = $_POST['search'];
    }

    if (!empty($_POST['exercise_id'])) {
        $arrExercise = $_POST['exercise_id'];
    }

    if (!empty($_POST['schedule'])) {
        $schedule = $_POST['schedule'];
    }


    $exercise = new Exercise($arrExercise, $searchValue,$sectionId,$type,$schedule);

    $exercise->prepare_items();

    ob_start();

    $exercise->display();
    ?>

    <?php
    $html = ob_get_clean();

    echo $html;

    wp_die();
}

// Hook to create AJAX endpoint for adding exercise data
add_action('wp_ajax_handle_plan_data', 'handle_plan_data');
function handle_plan_data()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['data']['plan'])) {

        $exercise = new Plan();

        $result = $exercise->handle_data('plan', $_POST['data']);

        $result = trim($result);

        echo json_encode(array('redirect_url' => $result));

    } else {
        echo json_encode(array('redirect_url' => admin_url('admin.php?page=exerciseui_manage_plan&action=new')));
    }
}
function plan_screen_options()
{
    global $plan_page;

    // return if not on our settings page
    $screen = get_current_screen();
    if (!is_object($screen) || $screen->id !== $plan_page) {
        return;
    }

    $args = array(
        'label' => 'Plan per page',
        'default' => 15,
        'option' => 'plan_per_page'
    );
    add_screen_option('per_page', $args);

    // create an instance of class
    $table = new Plan();
}