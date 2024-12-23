<?php

class Muscle_Anatomy extends Manage_Exercise
{

    private $exlId;
    private $muscle_data;

    private $searchPrimary;

    public function __construct($exlId = array(), $searchPrimary = "")
    {
        parent::__construct(); // Gọi đến constructor của lớp cha nếu có
        $this->exlId = $exlId;
        $this->searchPrimary = $searchPrimary;
    }

    private function get_muscle($search = "")
    {
        global $wpdb;

        if (!empty($search)) {
            return $wpdb->get_results(
                "SELECT * from {$wpdb->prefix}exercise_muscle_anatomy WHERE id Like '%{$search}%' OR name LIKE '%{$search}%' OR other_name LIKE '%{$search}%' OR type_id LIKE '%{$search}%' OR type_id LIKE '%{$search}%'",
                ARRAY_A
            );
        } else {
            return $wpdb->get_results(
                "SELECT * From {$wpdb->prefix}exercise_muscle_anatomy",
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
            'other_name' => 'Other Name',
            'type_id' => 'Type',
            'image' => 'Image',
            'description' => 'Description',
            'active' => 'Status',
            'created_at' => 'Create At',
        );

        return $columns;
    }

    // Bind table with columns, data and all
    function prepare_items()
    {
        if(!$this->searchPrimary){
            if (isset($_POST['page']) && isset($_POST['s'])) {
                $this->muscle_data = $this->get_muscle($_POST['s']);
            } else {
                $this->muscle_data = $this->get_muscle();
            }
        }else {
            if ($this->searchPrimary) {
                $this->muscle_data = $this->get_muscle($this->searchPrimary);
            } else {
                $this->muscle_data = $this->get_muscle();
            }
        }

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        if (!$this->exlId) {
            $this->process_bulk_action();
        }

        /* pagination */
        $per_page = $this->get_items_per_page('muscle_per_page', 15);
        $current_page = $this->get_pagenum();
        $total_items = count($this->muscle_data);

        $this->muscle_data = array_slice($this->muscle_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(
            array(
                'total_items' => $total_items, // total number of items
                'per_page' => $per_page // items to show on a page
            )
        );

        // usort($this->muscle_data, array(&$this, 'usort_reorder'));

        $this->items = $this->muscle_data;
    }

    // bind data with column
    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'name':
            case 'other_name':
                return $item[$column_name];
            case 'type_id':
                return $this->get_type_name_by_id($item['type_id']);
            case 'image':
            case 'description':
            case 'active':
                return '';
            case 'created_at':
                return $item[$column_name];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
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
            '<input type="checkbox" name="muscle_id[]" class="%s" value="%s" %s />',
            $classSelected,
            $item['id'],
            $checked
        );
    }

    // Sorting function
    function usort_reorder($a, $b)
    {

        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : '';

        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';

        $result = strcmp($a[$orderby], $b[$orderby]);

        return ($order === 'asc') ? $result : -$result;
    }

    function get_bulk_actions()
    {
        if (!empty($this->exlId)) {
            return array();
        }
        $actions = array(
            'delete_multiple' => __('Move to Trash', 'supporthost-admin-table'),
            'published_mutiple' => __('Publish', 'supporthost-admin-table'),
            'unpublished_mutiple' => __('Unpublish', 'supporthost-admin-table'),
            'export-all' => 'Export All',
            'export-selected' => 'Export Selected'
        );

        return $actions;
    }

    function process_bulk_action($text = '')
    {
        if (!$text) {
            $action = $this->current_action();

            if ($action == 'delete_multiple') {
                $this->delete_data_by_id('muscle', $_POST['muscle_id']);
            }

            if ($action == 'published_mutiple') {
                $this->publish_data_by_id('muscle', $_POST['muscle_id']);
            }

            if ($action == 'unpublished_mutiple') {
                $this->unpublish_data_by_id('muscle', $_POST['muscle_id']);
            }

            if ("export-all" === $this->current_action()) {
                global $wpdb;

                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="muscle.csv"');

                // clean out other output buffers
                ob_end_clean();

                $fp = fopen('php://output', 'w');

                // CSV/Excel header label
                $header_row = array(
                    0 => 'ID',
                    1 => 'Name',
                    2 => 'Other Name',
                    3 => 'Type ID',
                    4 => 'Image',
                    5 => 'Description',
                    6 => 'Active',
                    7 => 'Created At',
                    8 => 'Updated At',
                );

                fputcsv($fp, $header_row);

                $Table_Name = 'wp_exercise_muscle_anatomy';
                $sql_query = "SELECT * FROM $Table_Name";
                $rows = $wpdb->get_results($sql_query, ARRAY_A);
                if (!empty($rows)) {
                    foreach ($rows as $Record) {
                        $OutputRecord = array(
                            $Record['id'],
                            $Record['name'],
                            $Record['other_name'],
                            $Record['type_id'],
                            $Record['image'],
                            $Record['description'],
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
                $ids = isset($_POST['muscle_id']) ? $_POST['muscle_id'] : array();
                if (is_array($ids))
                    $ids = implode(',', $ids);

                if (!empty($ids)) {
                    // Use headers so the data goes to a file and not displayed
                    global $wpdb;

                    header('Content-Type: text/csv');
                    header('Content-Disposition: attachment; filename="muscle-seleted.csv"');

                    // clean out other output buffers
                    ob_end_clean();

                    $fp = fopen('php://output', 'w');

                    // CSV/Excel header label
                    $header_row = array(
                        0 => 'ID',
                        1 => 'Name',
                        2 => 'Other Name',
                        3 => 'Type ID',
                        4 => 'Image',
                        5 => 'Description',
                        6 => 'Active',
                        7 => 'Created At',
                        8 => 'Updated At',
                    );

                    //write the header
                    fputcsv($fp, $header_row);

                    $Table_Name = 'wp_exercise_muscle_anatomy';
                    $sql_query = $wpdb->prepare("SELECT * FROM $Table_Name WHERE id IN (%s)", $ids);
                    $rows = $wpdb->get_results($sql_query, ARRAY_A);
                    if (!empty($rows)) {
                        foreach ($rows as $Record) {
                            $OutputRecord = array(
                                $Record['id'],
                                $Record['name'],
                                $Record['other_name'],
                                $Record['type_id'],
                                $Record['image'],
                                $Record['description'],
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
                $this->redirect_page(admin_url('admin.php?page=exerciseui_manage_muscle'));
            }
        }
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
            'name' => array('name', false),
            'type_id' => array('type_id', false),
        );

        return $sortable_columns;
    }

    function get_type_name_by_id($type_id)
    {
        global $wpdb;
        $sql = "SELECT name FROM {$wpdb->prefix}exercise_muscle_type WHERE id = %d";
        return $wpdb->get_var($wpdb->prepare($sql, $type_id));
    }


    function get_data()
    {
        $this->prepare_items();
        return $this->items;
    }


    function add_screen_options()
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Muscles',
            'default' => 15,
            'option' => 'muscle_per_page'
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
            'edit' => sprintf('<a href="?page=%s&action=%s&muscle=%s">Edit</a>', 'exerciseui_manage_muscle', 'edit', $item['id']),
            'delete' => sprintf('<a href="?page=%s&action=%s&muscle=%s">Delete</a>', 'exerciseui_manage_muscle', 'delete', $item['id']),
        );
        return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions));
    }

    function get_muscle_by_id($id)
    {
        $data = $this->get_data_by_id('muscle', $id);

        return $data;
    }

    function column_image($item)
    {
        if (!empty($item['image'])) {
            return sprintf(
                '<img src="%s" style="width: 150px;height: auto; object-fit: cover"/>',
                $item['image']
            );
        } else {
            return sprintf(
                ''
            );
        }

    }

    function column_active($item)
    {
        $text = $item['active'] == 1 ? "Pulished" : "Unpublished";
        $class = $item['active'] == 1 ? "published" : "unpublished";

        return sprintf("<p class='%s'>%s</p>", $class, $text);
    }

    function render_muscle($data, $action, $text = '')
    {
        $id = !empty($data->id) ? $data->id : '';
        $title = $action == 'edit' ? "Edit" : "Add";
        $types = $this->get_muscle_type();

        $name = !empty($data->name) ? $data->name : '';
        $othname = !empty($data->other_name) ? $data->other_name : '';
        $img = !empty($data->image) ? $data->image : '';
        $description = !empty($data->description) ? $data->description : '';
        $status = !empty($data->active) ? $data->active : 1;
        $typesId = !empty($data->type_id) ? $data->type_id : 1;
        ?>
        <div id="overlay">
            <div class="cv-spinner">
                <span class="spinner-ex"></span>
            </div>
        </div>
        <div class="exercice-form-section">
            <h1 class="wp-heading-inline">
                <?= $title . " muscle" ?>
                <input type="submit" data-link="<?= admin_url('admin.php?page=exerciseui_manage_muscle') ?>"
                    value="Save and close" class="button button-primary muscle-button" id="muscle-button-save-top">
                <input type="submit" value="Save and new"
                    data-link="<?= admin_url('admin.php?page=exerciseui_manage_muscle&action=new') ?>"
                    class="button button-primary muscle-button" id="muscle-button-save-new-top">
                <input type="submit"
                    data-link="<?= admin_url('admin.php?page=exerciseui_manage_muscle&action=edit&muscle=') . $id ?>"
                    value="Save" class="button muscle-button" id="muscle-button-apply-top">
            </h1>
            <?= $text ?>
            <div class="container">
                <form method="POST" id="muscle-form">
                    <div class=" field">
                        <div class="field-label attention">
                            <label for="name">Name</label>
                        </div>
                        <div class="field-item">
                            <input type="text" name="muscle[name]" id="name" value="<?= $name ?>">
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">
                            <label for="other_name">Other Name</label>
                        </div>
                        <div class="field-item">
                            <input type="text" name="muscle[other_name]" id="other_name" value="<?= $othname ?>">
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label attention">
                            <label for="type_id">Type</label>
                        </div>
                        <div class="field-item">
                            <select name="muscle[type_id]" id="type_id">
                                <?php foreach ($types as $type): ?>
                                    <option <?= $typesId == $type->id ? 'checked' : '' ?> value="<?= $type->id ?>"><?= $type->name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">
                            <label for="image">
                                Image
                                <a href="javascript:void(0)"
                                    class="add-exercise-image"><?= $img ? "Edit Image" : "Add Image" ?></a>
                            </label>
                        </div>
                        <div class="exercise-image-container" style="<?= $img ? 'display: block' : 'display:none'; ?>">
                            <span class="remove-exercise-img">x</span>
                            <img src="<?= $img ?>" class="exercise-img" />
                            <input type="hidden" name="muscle[image]" class="exercise-image" value="<?= $img ?>" />
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">
                            <label for="description">Description</label>
                        </div>
                        <div class="field-item">
                            <?php
                            $editor_id = 'description';
                            $settings = array(
                                'textarea_name' => 'muscle[description]',
                                'textarea_rows' => 4,
                                'media_buttons' => true,
                                'quicktags' => false,
                                'editor_class' => 'my-wp-editor'
                            );
                            wp_editor($description, $editor_id, $settings);
                            ?>
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">
                            <label for="type">Status</label>
                        </div>
                        <div class="field-item field-item-radio">
                            <div class="item">
                                <input type="radio" name="muscle[active]" <?= $status == 1 ? 'checked' : '' ?> value="1"
                                    id="published">
                                <label for="published">Published</label>
                            </div>
                            <div class="item">
                                <input type="radio" name="muscle[active]" <?= $status == 0 ? 'checked' : '' ?> value="0"
                                    id="unpublished">
                                <label for="unpublished">Unpublished</label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id" value="<?= $id ?>" />
                    <input type="hidden" name="action" value="<?= $action ?>" />
                    <input type="hidden" name="link" value="" />
                </form>
            </div>
        </div>
        <?php
    }


    function import_data_from_csv($file_path)
    {
        global $wpdb;

        $csv_file = fopen($file_path, "r");

        $header = fgetcsv($csv_file);

        $headerCount = count($header);
        $table_name = 'wp_exercise_muscle_anatomy';
        $table_name2 = 'wp_exercise_muscle_type';

        while (($data = fgetcsv($csv_file)) !== FALSE) {
            $record = array_combine($header, $data);

            $existing_record_type = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name2 WHERE name = %s", $record['type']), ARRAY_A);

            if ($existing_record_type) {
                $prepared_data = array(
                    'name' => $record['name'],
                    'other_name' => $record['other_name'],
                    'type_id' => $existing_record_type['id'],
                    'image' => $record['image'],
                    'description' => $record['description'],
                );

                $existing_record = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE name = %s", $record['name']), ARRAY_A);

                if ($existing_record_type) {
                    if ($existing_record) {
                        $wpdb->update($table_name, $prepared_data, array('name' => $record['name']));
                    } else {
                        $wpdb->insert($table_name, $prepared_data);
                    }
                }
            }

        }

        // Đóng tệp CSV sau khi hoàn thành
        fclose($csv_file);
        $this->redirect_page(admin_url('admin.php?page=exerciseui_manage_muscle'));
    }

    function handle_data($type, $data)
    {
        $result = $this->handle_muscle_data($type, $data);
        return $result;
    }

    function delete_muscle($id)
    {
        $this->delete_data_by_id('muscle', $id);
    }
}

// Hook to create AJAX endpoint for adding muscle data
add_action('wp_ajax_handle_muscle_data', 'handle_muscle_data');
function handle_muscle_data()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['data']['muscle'])) {

        $muscle = new Muscle_Anatomy();

        $result = $muscle->handle_data('muscle', $_POST['data']);

        $result = trim($result);

        echo json_encode(array('redirect_url' => $result));

    } else {
        echo json_encode(array('redirect_url' => admin_url('admin.php?page=exerciseui_manage_muscle&action=new')));
    }
}

function exerciseui_manage_muscle()
{

    $muscleTable = new Muscle_Anatomy();

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
            $linkNew = admin_url('admin.php?page=exerciseui_manage_muscle&action=new');
            echo '<div class="wrap">
        <h2 class="exercise-list-title">Manage Muscle Anatomy
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
                    <form action="<?= admin_url('admin.php?page=exerciseui_manage_muscle') ?>" method="post"
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
            $muscleTable->prepare_items();
            ?>

            <form method="post">
                <input type="hidden" name="page" value="" />
                <?php $muscleTable->search_box('search', 'search_id'); ?>
            </form>
            <form action="<?= admin_url('admin.php?page=exerciseui_manage_muscle') ?>" method="POST">
                <?php
                $muscleTable->display();
                ?>
            </form>
            <?php
            echo '</div>';
        } elseif ($_POST['action'] == 'import') {
            $muscleTable->process_bulk_action('import');
        } else {
            $muscleTable->process_bulk_action();
        }
    } elseif ($_GET['action'] == 'delete') {
        if (!empty($_GET['muscle'])) {
            $muscleTable->delete_muscle($_GET['muscle']);
        } else {
            wp_redirect(admin_url('admin.php?page=exerciseui_manage_muscle'));
            exit;
        }
    } else {
        if (!empty($_GET['muscle'])) {
            $data = $muscleTable->get_muscle_by_id($_GET['muscle']);
            $muscleTable->render_muscle($data, 'edit');
        } else {
            $muscleTable->render_muscle(array(), 'add', $text);
        }
    }
}

function muscle_screen_options()
{
    global $muscle_page;

    // return if not on our settings page
    $screen = get_current_screen();
    if (!is_object($screen) || $screen->id !== $muscle_page) {
        return;
    }

    $args = array(
        'label' => 'Muscle per page',
        'default' => 15,
        'option' => 'muscle_per_page'
    );
    add_screen_option('per_page', $args);

    // create an instance of class
    $table = new Muscle_Anatomy();
}
