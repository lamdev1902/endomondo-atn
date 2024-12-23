<?php
class Exercise extends Manage_Exercise
{

    private $searchOption;
    private $exlId;
    private $sectionId;
    private $type;

    private $exercise_data;
    private $schedule;

    public function __construct($exlId = array(), $searchOption = "", $sectionId = "", $type = '', $schedule = array())
    {
        parent::__construct(); // Gọi đến constructor của lớp cha nếu có
        $this->exlId = $exlId;
        $this->searchOption = $searchOption;
        $this->sectionId = $sectionId;
        $this->type = $type;
        $this->schedule = $schedule;

    }

    private function get_exercise($search = "")
    {
        global $wpdb;

        if (!empty($search)) {
            return $wpdb->get_results(
                "SELECT * from {$wpdb->prefix}exercise WHERE id Like '%{$search}%' OR name LIKE '%{$search}%' OR other_name LIKE '%{$search}%'",
                ARRAY_A
            );
        } else {
            return $wpdb->get_results(
                "SELECT * From {$wpdb->prefix}exercise",
                ARRAY_A
            );
        }
    }

    // Define table columns
    function get_columns()
    {
        if (!$this->type) {
            $columns = array(
                'cb' => '<input type="checkbox"/>',
                'id' => 'ID',
                'name' => 'Name',
                'other_name' => 'Other Name',
                'description' => 'Description',
                'active' => 'Active',
                'created_at' => 'Create At',
            );
        } else {
            $columns = array(
                'cb' => '<input type="checkbox"/>',
                'id' => 'ID',
                'name' => 'Name',
                'duration' => 'Duration',
                'reps' => 'Reps',
                'note' => 'Note',
            );
        }

        return $columns;
    }

    // Bind table with columns, data and all
    function prepare_items()
    {


        if ($this->type) {
            global $wpdb;


            $exercise_table = $wpdb->prefix . 'exercise';
            $schedule_table = $wpdb->prefix . 'exercise_schedule';

            if(!empty($this->searchOption)){
                $exercise_query = "SELECT id,name FROM $exercise_table WHERE id Like '%{$this->searchOption}%' OR name LIKE '%{$this->searchOption}%' OR other_name LIKE '%{$this->searchOption}%'";
            }else {
                $exercise_query = "SELECT id,name FROM $exercise_table";
            }

            $exercise_items = $wpdb->get_results($exercise_query, ARRAY_A);

            $exercise_data = array();

            foreach ($exercise_items as $exercise) {
                $exercise_data[$exercise['id']] = $exercise;
            }

            if ($this->sectionId) {
                if (!empty($this->exlId)) {
                    $placeholders = implode(',', array_fill(0, count($this->exlId), '%d'));
                    $schedule_query = $wpdb->prepare(
                        "SELECT exercise_id, duration, reps, note 
                         FROM $schedule_table 
                         WHERE section_id = %d AND exercise_id IN ($placeholders)",
                        array_merge(array($this->sectionId), $this->exlId)
                    );

                    $schedule_items = $wpdb->get_results($schedule_query, ARRAY_A);

                    foreach ($schedule_items as $schedule) {
                        $exercise_id = $schedule['exercise_id'];
                        if (isset($exercise_data[$exercise_id])) {
                            $exercise_data[$exercise_id]['duration'] = $schedule['duration'];
                            $exercise_data[$exercise_id]['reps'] = $schedule['reps'];
                            $exercise_data[$exercise_id]['note'] = $schedule['note'];
                        }
                    }

                    foreach ($this->exlId as $key => $exercise_id) {
                        $duration = 0;
                        $reps = 0;
                        $note = '';

                        if (!empty($this->schedule)) {
                            
                            $schedule = $this->schedule;

                            if (!empty($schedule['duration'])) {
                                if (!empty($schedule['duration'][$key])) {
                                    $duration = $schedule['duration'][$key];
                                }
                            }

                            if (!empty($schedule['reps'][$key])) {
                                $reps = $schedule['reps'][$key];
                            }

                            if (!empty($schedule['note'][$key])) {
                                $note = $schedule['note'][$key];
                            }
                            if(!$this->searchOption){
                                if (!isset($exercise_data[$exercise_id])) {
                                    $exercise_data[$exercise_id] = [
                                        'id' => $exercise_id,
                                        'duration' => $duration,
                                        'reps' => $reps,
                                        'note' => $note
                                    ];
                                } else {
                                    if ($duration) {
                                        $exercise_data[$exercise_id]['duration'] = $exercise_data[$exercise_id]['duration'] == $duration ? $exercise_data[$exercise_id]['duration'] : $duration;
                                    }
        
                                    if ($reps) {
                                        $exercise_data[$exercise_id]['reps'] = $exercise_data[$exercise_id]['reps'] == $reps ? $exercise_data[$exercise_id]['reps'] : $reps;
                                    }
        
                                    if ($note) {
                                        $exercise_data[$exercise_id]['note'] = $exercise_data[$exercise_id]['note'] == $note ? $exercise_data[$exercise_id]['note'] : $note;
                                    }
        
                                }
                            }else {
                                if (isset($exercise_data[$exercise_id])) {
                                    if ($duration) {
                                        $exercise_data[$exercise_id]['duration'] = $exercise_data[$exercise_id]['duration'] == $duration ? $exercise_data[$exercise_id]['duration'] : $duration;
                                    }
        
                                    if ($reps) {
                                        $exercise_data[$exercise_id]['reps'] = $exercise_data[$exercise_id]['reps'] == $reps ? $exercise_data[$exercise_id]['reps'] : $reps;
                                    }
        
                                    if ($note) {
                                        $exercise_data[$exercise_id]['note'] = $exercise_data[$exercise_id]['note'] == $note ? $exercise_data[$exercise_id]['note'] : $note;
                                    }
                                }
                            }
                        }
                    }
                }
                $this->exercise_data = array_values($exercise_data);
            } else {
                if (!empty($this->exlId)) {
                    foreach ($this->exlId as $key => $exercise_id) {
                        if (!empty($this->schedule)) {

                            $schedule = $this->schedule;

                            if (!empty($schedule['duration'])) {
                                if (!empty($schedule['duration'][$key])) {
                                    $exercise_data[$exercise_id]['duration'] = $schedule['duration'][$key];
                                }
                            }

                            if (!empty($schedule['reps'][$key])) {
                                $exercise_data[$exercise_id]['reps'] = $schedule['reps'][$key];
                            }

                            if (!empty($schedule['note'][$key])) {
                                $exercise_data[$exercise_id]['note'] = $schedule['note'][$key];
                            }

                        }
                    }
                    $this->exercise_data = array_values($exercise_data);
                }
            }
        } else {
            if (!$this->searchOption) {
                if (isset($_POST['page']) && isset($_POST['s'])) {
                    $this->exercise_data = $this->get_exercise($_POST['s']);
                } else {
                    $this->exercise_data = $this->get_exercise();
                }
            } else {
                $this->exercise_data = $this->get_exercise($this->searchOption);
            }
        }

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();
        /* pagination */
        $per_page = $this->get_items_per_page('exercise_per_page', 15);
        $current_page = $this->get_pagenum();
        $total_items = count($this->exercise_data);

        $this->exercise_data = array_slice($this->exercise_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(
            array(
                'total_items' => $total_items, // total number of items
                'per_page' => $per_page // items to show on a page
            )
        );

        // usort($this->exercise_data, array(&$this, 'usort_reorder'));

        $this->items = $this->exercise_data;
    }

    // bind data with column
    function column_default($item, $column_name)
    {

        if (!$this->exlId) {
            switch ($column_name) {
                case 'id':
                case 'name':
                case 'other_name':
                case 'description':
                case 'active':
                case 'created_at':
                    return $item[$column_name];
                default:
                    return print_r($item, true); //Show the whole array for troubleshooting purposes
            }
        } else {
            switch ($column_name) {
                case 'id':
                case 'name':
                    return $item[$column_name];
                case 'duration':
                    return sprintf(
                        '<input type="text" data-exercise-id="%s" class="duration hidden-input" value="%s"/>',
                        $item['id'],
                        !empty($item['duration']) ? $item['duration'] : ''
                    );
                case 'reps':
                    return sprintf(
                        '<input type="text" data-exercise-id="%s" class="reps hidden-input" value="%s"/>',
                        $item['id'],
                        !empty($item['reps']) ? $item['reps'] : ''
                    );
                case 'note':
                    return sprintf(
                        '<input type="text" data-exercise-id="%s" class="note hidden-input" value="%s"/>',
                        $item['id'],
                        !empty($item['note']) ? $item['note'] : ''
                    );
                default:
                    return print_r($item, true); //Show the whole array for troubleshooting purposes
            }
        }
    }

    // To show checkbox with each row
    function column_cb($item)
    {
        $checked = '';
        $classSelected = '';

        if ($this->exlId) {
            $checked = in_array($item['id'], $this->exlId) ? 'checked' : '';
            $classSelected = in_array($item['id'], $this->exlId) ? 'selected' : '';
        }

        return sprintf(
            '<input type="checkbox" name="exercise_id[]" class="%s" value="%d" %s/>',
            $classSelected,
            $item['id'],
            $checked
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
                $this->delete_data_by_id('exercise', $_POST['exercise_id']);
            }

            if ($action == 'published_mutiple') {
                $this->publish_data_by_id('exercise', $_POST['exercise_id']);
            }

            if ($action == 'unpublished_mutiple') {
                $this->unpublish_data_by_id('exercise', $_POST['exercise_id']);
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

                fputcsv($fp, $header_row);

                $Table_Name = 'wp_exercise';
                $sql_query = "SELECT * FROM $Table_Name";
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
                $this->redirect_page(admin_url('admin.php?page=exerciseui_manage_exercise'));
            }
        }
    }

    function import_data_from_csv($file_path)
    {
        global $wpdb;

        $csv_file = fopen($file_path, "r");

        $header = fgetcsv($csv_file);

        $table_name = 'wp_exercise';

        while (($data = fgetcsv($csv_file)) !== FALSE) {
            $record = array_combine($header, $data);

            $primary = array();
            $secondary = array();
            $equipment = array();
            $training = array();

            foreach ($header as $key => $value) {
                if ($value == 'primary') {
                    $primary[] = $data[$key];
                } elseif ($value == 'secondary') {
                    $secondary[] = $data[$key];
                } elseif ($value == 'equipment') {
                    $equipment[] = $data[$key];
                } elseif ($value == 'training') {
                    $training[] = $data[$key];
                }
            }

            $options = array(
                'primary' => $primary,
                'secondary' => $secondary,
                'equipment' => $equipment,
                'training' => $training
            );

            $prepared_data = array(
                'name' => $record['name'],
                'other_name' => $record['other_name'],
                'description' => $record['description'],
                'video_white_male' => $record['video_white_male'],
                'video_green' => $record['video_green'],
                'video_transparent' => $record['video_transparent'],
                'image_female' => $record['image_female'],
                'image_male' => $record['image_male']
            );

            $new_primary = array();
            $new_secondary = array();
            $new_equipment = array();
            $new_training = array();
            foreach ($options as $key => $type) {

                $option_table = 'wp_exercise_muscle_anatomy';

                if ($key == 'equipment') {
                    $option_table = 'wp_exercise_equipment';
                } elseif ($key == 'training') {
                    $option_table = 'wp_exercise_training_type';
                }


                foreach ($type as $value) {
                    $check = $wpdb->get_row($wpdb->prepare("SELECT * FROM $option_table WHERE name = %s", $value), ARRAY_A);

                    if ($check) {
                        if ($key == 'primary') {
                            $new_primary[] = $check['id'];
                        } elseif ($key == 'secondary') {
                            $new_secondary[] = $check['id'];
                        } elseif ($key == 'equipment') {
                            $new_equipment[] = $check['id'];
                        } elseif ($key == 'training') {
                            $new_training[] = $check['id'];
                        }
                    }
                }
            }

            $options = array(
                'primary' => $new_primary,
                'secondary' => $new_secondary,
                'equipment' => $new_equipment,
                'training_type' => $new_training,
            );

            $existing_record = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE name = %s", $record['name']), ARRAY_A);

            if ($existing_record) {
                $wpdb->update($table_name, $prepared_data, array('name' => $record['name']));

                $this->hanlde_option_data($existing_record['id'], $options);
            } else {
                $wpdb->insert($table_name, $prepared_data);
                $id = $wpdb->insert_id;
                $this->hanlde_option_data($id, $options);
            }
        }

        // Đóng tệp CSV sau khi hoàn thành
        fclose($csv_file);
        $this->redirect_page(admin_url('admin.php?page=exerciseui_manage_exercise'));
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
            'option' => 'exercise_per_page'
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
            'edit' => sprintf('<a href="?page=%s&action=%s&exercise=%s">Edit</a>', 'exerciseui_manage_exercise', 'edit', $item['id']),
            'delete' => sprintf('<a href="?page=%s&action=%s&exercise=%s">Delete</a>', 'exerciseui_manage_exercise', 'delete', $item['id']),
        );
        return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions));
    }


    function column_active($item)
    {
        $text = $item['active'] == 1 ? "Pulished" : "Unpublished";
        $class = $item['active'] == 1 ? "published" : "unpublished";

        return sprintf("<p class='%s'>%s</p>", $class, $text);
    }

    function column_image_female($item)
    {
        if (!empty($item['image_female'])) {
            return sprintf(
                '<img src="%s" style="width: 150px;height: auto; object-fit: cover"/>',
                $item['image_female']
            );
        } else {
            return sprintf(
                ''
            );
        }

    }

    function column_image_male($item)
    {
        if (!empty($item['image_male'])) {
            return sprintf(
                '<img src="%s" style="width: 150px;height: auto; object-fit: cover"/>',
                $item['image_male']
            );
        } else {
            return sprintf(
                ''
            );
        }

    }

    function handle_data($type, $data)
    {
        $result = $this->handle_muscle_data($type, $data);
        return $result;
    }

    function delete_exercise($id)
    {
        $this->delete_data_by_id('exercise', $id);
    }

    function get_muscle_by_exercise_id($id, $type)
    {
        $muscle_arr_id = array();
        $data = array();
        if ($id) {

            $data = $type == 'primary' ? $this->get_primary_option($id) : $this->get_secondary_option($id);

            foreach ($data as $item) {
                $muscle_arr_id[] = $item['muscle_id'];
            }
        }

        return $muscle_arr_id;
    }

    function get_equipment_by_exercise_id($id)
    {
        $equipment_arr_id = array();
        $data = array();
        if ($id) {

            $data = $this->get_equipment_option($id);

            foreach ($data as $item) {
                $equipment_arr_id[] = $item['equipment_id'];
            }
        }

        return $equipment_arr_id;
    }

    function render_exercise($data, $action, $text = '')
    {
        $id = !empty($data->id) ? $data->id : '';
        $title = $action == 'edit' ? "Edit" : "Add";

        $name = !empty($data->name) ? $data->name : '';
        $slug = !empty($data->slug) ? $data->slug : '';
        $other_name = !empty($data->other_name) ? $data->other_name : '';
        $imgMale = !empty($data->image_male) ? $data->image_male : '';
        $imgFemale = !empty($data->image_female) ? $data->image_female : '';
        $vdWMale = !empty($data->video_white_male) ? $data->video_white_male : '';
        $vdGreen = !empty($data->video_green) ? $data->video_green : '';
        $vdTransparent = !empty($data->video_transparent) ? $data->video_transparent : '';
        $description = !empty($data->description) ? $data->description : '';
        $status = $data->active;
        $primary_data = array();
        $secondary_data = array();
        $equipment_data = array();
        $content_meta = array();
        if ($id) {
            $primary_data = $this->get_primary_option($id);
            $secondary_data = $this->get_secondary_option($id);
            $equipment_data = $this->get_equipment_option($id);

            $content_meta = $this->get_content_meta($id);
        }
        ?>
        <div id="overlay">
            <div class="cv-spinner">
                <span class="spinner-ex"></span>
            </div>
        </div>
        <div class="exercice-form-section">
            <h1 class="wp-heading-inline">
                <?= $title . " exercise" ?>
                <input type="submit" data-link="<?= admin_url('admin.php?page=exerciseui_manage_exercise') ?>"
                    value="Save and close" class="button button-primary exercise-button" id="muscle-button-save-top">
                <input type="submit" value="Save and new"
                    data-link="<?= admin_url('admin.php?page=exerciseui_manage_exercise&action=new') ?>"
                    class="button button-primary exercise-button" id="muscle-button-save-new-top">
                <input type="submit"
                    data-link="<?= admin_url('admin.php?page=exerciseui_manage_exercise&action=edit&exercise=') . $id ?>"
                    value="Save" class="button exercise-button" id="muscle-button-apply-top">
            </h1>
            <?= $text ?>
            <div class="container">
                <form method="POST" id="muscle-form">
                    <div class=" field">
                        <div class="field-label attention">
                            <label for="name">Name</label>
                        </div>
                        <div class="field-item">
                            <input type="text" name="exercise[name]" id="name" value="<?= $name ?>">
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
                                'textarea_name' => 'exercise[description]',
                                'textarea_rows' => 4,
                                'media_buttons' => false,
                                'quicktags' => false,
                                'editor_class' => 'my-wp-editor'
                            );
                            wp_editor($description, $editor_id, $settings);
                            ?>
                        </div>
                    </div>
                    <div class=" field">
                        <div class="field-label">
                            <label for="slug">Slug</label>
                        </div>
                        <div class="field-item">
                            <input type="text" name="exercise[slug]" id="slug" value="<?= $slug ?>">
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">
                            <label>
                                Video White Male
                            </label>
                        </div>
                        <div class="field-item">
                            <input type="text" name="exercise[video_white_male]" class="video-section" value="<?= $vdWMale ?>">
                            <div class="exercise-video-container">
                                <iframe width="560" height="315" src="<?= $vdWMale ?>" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                                <video width="560" height="315" controls style="display:none;">
                                    <source src="" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">
                            <label>
                                Video Green
                            </label>
                        </div>
                        <div class="field-item">
                            <input type="text" name="exercise[video_green]" class="video-section" value="<?= $vdGreen ?>">
                            <div class="exercise-video-container" style="<?= $vdGreen ? 'display: block' : 'display:none'; ?>">
                                <iframe width="560" height="315" src="<?= $vdGreen ?>" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                                <video width="560" height="315" controls style="display:none;">
                                    <source src="" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">
                            <label>
                                Video Transparent
                            </label>
                        </div>
                        <div class="field-item">
                            <input type="text" name="exercise[video_transparent]" class="video-section"
                                value="<?= $vdTransparent ?>">
                            <div class="exercise-video-container"
                                style="<?= $vdTransparent ? 'display: block' : 'display:none'; ?>">
                                <iframe width="560" height="315" src="<?= $vdTransparent ?>" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                                <video width="560" height="315" controls style="display:none;">
                                    <source src="" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">
                            <label>
                                Image Male
                                <a href="javascript:void(0)"
                                    class="add-exercise-image"><?= $imgMale ? "Edit Image" : "Add Image" ?></a>
                            </label>
                        </div>
                        <div class="exercise-image-container" style="<?= $imgMale ? 'display: block' : 'display:none'; ?>">
                            <span class="remove-exercise-img">x</span>
                            <img src="<?= $imgMale ?>" class="exercise-img" />
                            <input type="hidden" name="exercise[image_male]" class="exercise-image" value="<?= $imgMale ?>" />
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">
                            <label>
                                Image Female
                                <a href="javascript:void(0)"
                                    class="add-exercise-image"><?= $imgFemale ? "Edit Image" : "Add Image" ?></a>
                            </label>
                        </div>
                        <div class="exercise-image-container" style="<?= $imgFemale ? 'display: block' : 'display:none'; ?>">
                            <span class="remove-exercise-img">x</span>
                            <img src="<?= $imgFemale ?>" class="exercise-img" />
                            <input type="hidden" name="exercise[image_female]" class="exercise-image"
                                value="<?= $imgFemale ?>" />
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">
                            <label for="type">Status</label>
                        </div>
                        <div class="field-item field-item-radio">
                            <div class="item">
                                <input type="radio" name="exercise[active]" <?= $status == 1 ? 'checked' : '' ?> value="1"
                                    id="published">
                                <label for="published">Published</label>
                            </div>
                            <div class="item">
                                <input type="radio" name="exercise[active]" <?= $status == 0 ? 'checked' : '' ?> value="0"
                                    id="unpublished">
                                <label for="unpublished">Unpublished</label>
                            </div>
                        </div>
                    </div>
                    <?php
                    $training_type_arr = $this->get_type('training');

                    $arr_training = array();

                    if ($id) {
                        $arr_training = $this->get_type_by_exercise($id);
                    }
                    ?>
                    <div class="field type">
                        <div class="field-label">
                            <label for="type">Training Type</label>
                        </div>
                        <div class="field-item field-item-radio">
                            <div class="item">
                                <div class="wrapper">
                                    <button type="button" class="form-control toggle-next ellipsis">Select training
                                        type
                                        <span class="dropdown"></span>
                                    </button>

                                    <div class="checkboxes" id="Training">
                                        <label class="apply-selection">
                                            <input type="checkbox" value="" class="ajax-link" />
                                            &#x2714; apply selection
                                        </label>

                                        <div class="inner-wrap">
                                            <?php if (count($training_type_arr) > 0): ?>
                                                <label>
                                                    <input type="hidden" class="val-all" name="exercise[training][]">
                                                    <input type="checkbox" value="all" class="ckkBox all" />
                                                    <span>All Training Type ( <?= count($training_type_arr) ?> )</span>
                                                </label><br>
                                            <?php endif; ?>
                                            <?php foreach ($training_type_arr as $item): ?>
                                                <?php $checked = in_array($item->id, $arr_training) ? 'checked' : '' ?>
                                                <label>
                                                    <input type="hidden" class="val-<?= $item->id ?>" name="exercise[training][]">
                                                    <input type="checkbox" <?= $checked ?> value="<?= $item->id ?>"
                                                        class="ckkBox val" />
                                                    <span><?= $item->name ?></span>
                                                </label><br>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="exercise-table-wrap">
                        <?php
                        require_once (MY_PLUGIN_DIR . "/inc/options/primary_option.php");
                        require_once (MY_PLUGIN_DIR . "/inc/options/secondary_option.php");
                        require_once (MY_PLUGIN_DIR . "/inc/options/equipment_option.php");
                        require_once (MY_PLUGIN_DIR . "/inc/options/content.php");
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

    private function get_primary_option($id)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_primary_option';

        $table_name2 = $wpdb->prefix . $this->get_table_name('muscle');

        $query = $wpdb->prepare("
                SELECT a.exercise_id, a.muscle_id, b.name
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

        $table_name2 = $wpdb->prefix . $this->get_table_name('muscle');

        $query = $wpdb->prepare("
                SELECT a.exercise_id, a.muscle_id, b.name
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

        $table_name2 = $wpdb->prefix . $this->get_table_name('equipment');

        $query = $wpdb->prepare("
                SELECT a.exercise_id, a.equipment_id, b.name
                FROM $table_name a
                INNER JOIN $table_name2 b ON a.equipment_id = b.id
                WHERE a.exercise_id = %d
            ", $id);

        $results = $wpdb->get_results($query, ARRAY_A);

        return $results;
    }

    function get_exercise_by_id($id)
    {
        $data = $this->get_data_by_id('exercise', $id);

        return $data;
    }
}

function exerciseui_manage_exercise()
{

    $exercise = new Exercise();

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
            $linkNew = admin_url('admin.php?page=exerciseui_manage_exercise&action=new');
            echo '<div class="wrap">
        <h2 class="exercise-list-title">Manage Exercise
        <a href="' . $linkNew . '" class="button btn-new">Add New</a></h2>
        ' . $text . '
        </div>';
            ?>
            <div class="upload-import-file-wrap">
                <div class="upload-import-file">
                    <p class="install-help">
                        <?= "If you have questions in a .csv, .xlsx or .json format, you may add it by uploading it here." ?>
                        <a class="ays_help" data-toggle="tooltip"
                            title="<?= 'Make sure the categories of the questions start with letters, instead of numbers, while importing. Please note, that the categories must start with letters so that the functionality can work correctly for you.' ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </p>
                    <form action="<?= admin_url('admin.php?page=exerciseui_manage_exercise') ?>" method="post"
                        enctype="multipart/form-data" class="ays-dn">
                        <label for="simple_import_check" class="install-help">
                            <?php
                            echo (
                                sprintf(
                                    __("%sTick this checkbox if you're importing a %sSimple XLSX%s file.%s"),
                                    '<span>',
                                    '<strong>',
                                    '</strong>',
                                    '</span>'
                                )
                            );
                            ?>
                            <input type="checkbox" name="import_simple_xlsx" value="on" id="simple_import_check"
                                style="margin-left: 5px;">
                        </label>
                        <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, .json"
                            name="quiz_import_file" id="import_file" />
                        <label class="screen-reader-text" for="import_file"><?= "Import file" ?></label>
                        <input type="submit" name="import-file-submit" class="button" value="<?= "Import now" ?>">
                        <input type="hidden" name="action" class="button" value="import">
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
            <form action="<?= admin_url('admin.php?page=exerciseui_manage_exercise') ?>" method="POST">
                <?php
                $exercise->display();
                ?>
            </form>
            <?php
            echo '</div>';
        } elseif ($_POST['action'] == 'import') {
            $exercise->process_bulk_action('import');
        } else {
            $exercise->process_bulk_action();
        }
    } elseif ($_GET['action'] == 'delete') {
        if (!empty($_GET['exercise'])) {
            $exercise->delete_exercise($_GET['exercise']);
        } else {
            wp_redirect(admin_url('admin.php?page=exerciseui_manage_exercise'));
            exit;
        }
    } else {
        if (!empty($_GET['exercise'])) {
            $data = $exercise->get_exercise_by_id($_GET['exercise']);
            $exercise->render_exercise($data, 'edit');
        } else {
            $exercise->render_exercise(array(), 'add', $text);
        }
    }
}


add_action('wp_ajax_get_muscle_anatomy', 'get_muscle_anatomy');

function get_muscle_anatomy()
{

    $exercise = new Exercise();

    $arrMuscle = array();

    $searchValue = "";

    if (!empty($_POST['exercise_id'])) {
        $arrMuscle = $exercise->get_muscle_by_exercise_id($_POST['exercise_id'], $_POST['type']);
    }

    if (!empty($_POST['search'])) {
        $searchValue = $_POST['search'];
    }

    $muscle = new Muscle_Anatomy($arrMuscle, $searchValue);

    $muscle->prepare_items();

    ob_start();

    $muscle->display();
    ?>

    <?php
    $html = ob_get_clean();

    echo $html;

    wp_die();
}

add_action('wp_ajax_get_equipment_data', 'get_equipment_data');

function get_equipment_data()
{

    $exercise = new Exercise();

    $searchValue = "";

    $arrMuscle = array();

    if (!empty($_POST['search'])) {
        $searchValue = $_POST['search'];
    }

    if (!empty($_POST['exercise_id'])) {
        $arrMuscle = $exercise->get_equipment_by_exercise_id($_POST['exercise_id'], $_POST['type']);
    }
    $muscle = new Exercise_Equipment($arrMuscle, $searchValue);

    $muscle->prepare_items();

    ob_start();

    $muscle->display();
    ?>

    <?php
    $html = ob_get_clean();

    echo $html;

    wp_die();
}

// Hook to create AJAX endpoint for adding exercise data
add_action('wp_ajax_handle_exercise_data', 'handle_exercise_data');
function handle_exercise_data()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['data']['exercise'])) {

        $exercise = new Exercise();

        $result = $exercise->handle_data('exercise', $_POST['data']);

        $result = trim($result);

        echo json_encode(array('redirect_url' => $result));

    } else {
        echo json_encode(array('redirect_url' => admin_url('admin.php?page=exerciseui_manage_exercise&action=new')));
    }
}
function exercise_screen_options()
{
    global $exercise_page;

    // return if not on our settings page
    $screen = get_current_screen();
    if (!is_object($screen) || $screen->id !== $exercise_page) {
        return;
    }

    $args = array(
        'label' => 'Exercise per page',
        'default' => 15,
        'option' => 'exercise_per_page'
    );
    add_screen_option('per_page', $args);

    // create an instance of class
    $table = new Exercise();
}