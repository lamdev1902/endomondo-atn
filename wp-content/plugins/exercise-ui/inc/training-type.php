<?php

class Training_Type extends Manage_Exercise
{

    private $training_type_data;

    private function get_training_type($search = "")
    {
        global $wpdb;

        if (!empty($search)) {
            return $wpdb->get_results(
                "SELECT * From {$wpdb->prefix}exercise_training_type WHERE id Like '%{$search}%' OR name LIKE '%{$search}%' OR other_name LIKE '%{$search}%' OR type_id LIKE '%{$search}%' OR type_id LIKE '%{$search}%'",
                ARRAY_A
            );
        } else {
            return $wpdb->get_results(
                "SELECT * From {$wpdb->prefix}exercise_training_type",
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
            'created_at' => 'Create At',
        );

        return $columns;
    }

    // Bind table with columns, data and all
    function prepare_items()
    {
        if (isset($_POST['page']) && isset($_POST['s'])) {
            $this->training_type_data = $this->get_training_type($_POST['s']);
        } else {
            $this->training_type_data = $this->get_training_type();
        }
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        /* pagination */
        $per_page = $this->get_items_per_page('training_type_per_page', 15);
        $current_page = $this->get_pagenum();
        $total_items = count($this->training_type_data);

        $this->training_type_data = array_slice($this->training_type_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(
            array(
                'total_items' => $total_items, // total number of items
                'per_page' => $per_page // items to show on a page
            )
        );

        // usort($this->training_type_data, array(&$this, 'usort_reorder'));

        $this->items = $this->training_type_data;
    }

    function display()
    {
        $records = $this->items;
        echo '<div class="bulk-actions">';
        $this->bulk_actions('top');
        echo '</div>';
        echo '<table class="wp-list-table widefat fixed striped">';

        echo '<thead>';
        $this->print_column_headers();
        echo '</thead>';
        echo '<tbody id="the-list">';

        foreach ($records as $record) {
            echo '<tr data-id="' . $record['id'] . '">';
            foreach ($this->get_columns() as $column_name => $column_display_name) {
                echo '<td data-column="' . $column_name . '">';
                if ($column_name == 'cb') {
                    echo $this->column_cb($record);
                } else {
                    echo $this->column_default($record, $column_name);
                }
                echo '</td>';
            }
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }

    // bind data with column
    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
                return $item[$column_name];
            case 'name':
                return '<span class="inline-edit">' . $item[$column_name] . '</span>' .
                    '<input type="text" data-ajax="update_training_type" class="inline-edit-input" value="' . $item[$column_name] . '" style="display:none;" />';
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
            '<input type="checkbox" name="training_type_id[]"  value="%s" />',
            $item['id']
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
        // if (!empty($this->exlId)) {
        //     return array();
        // }
        $actions = array(
            'delete_multiple' => __('Move to Trash', 'supporthost-admin-table'),
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
                $this->delete_data_by_id('training_type', $_POST['training_type_id']);
            }

            if ($action == 'published_mutiple') {
                $this->publish_data_by_id('training_type', $_POST['training_type_id']);
            }

            if ($action == 'unpublished_mutiple') {
                $this->unpublish_data_by_id('training_type', $_POST['training_type_id']);
            }

            if ("export-all" === $this->current_action()) {
                global $wpdb;

                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="training-type.csv"');

                // clean out other output buffers
                ob_end_clean();

                $fp = fopen('php://output', 'w');

                // CSV/Excel header label
                $header_row = array(
                    0 => 'ID',
                    1 => 'Name',
                    2 => 'Created At',
                    3 => 'Updated At',
                );

                fputcsv($fp, $header_row);

                $Table_Name = 'wp_exercise_training_type';
                $sql_query = "SELECT * FROM $Table_Name";
                $rows = $wpdb->get_results($sql_query, ARRAY_A);
                if (!empty($rows)) {
                    foreach ($rows as $Record) {
                        $OutputRecord = array(
                            $Record['id'],
                            $Record['name'],
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
                $ids = isset($_POST['training_type_id']) ? $_POST['training_type_id'] : array();
                if (is_array($ids))
                    $ids = implode(',', $ids);

                if (!empty($ids)) {
                    // Use headers so the data goes to a file and not displayed
                    global $wpdb;

                    header('Content-Type: text/csv');
                    header('Content-Disposition: attachment; filename="training-type-seleted.csv"');

                    // clean out other output buffers
                    ob_end_clean();

                    $fp = fopen('php://output', 'w');

                    // CSV/Excel header label
                    $header_row = array(
                        0 => 'ID',
                        1 => 'Name',
                        7 => 'Created At',
                        8 => 'Updated At',
                    );

                    //write the header
                    fputcsv($fp, $header_row);

                    $Table_Name = 'wp_exercise_training_type';
                    $sql_query = $wpdb->prepare("SELECT * FROM $Table_Name WHERE id IN (%s)", $ids);
                    $rows = $wpdb->get_results($sql_query, ARRAY_A);
                    if (!empty($rows)) {
                        foreach ($rows as $Record) {
                            $OutputRecord = array(
                                $Record['id'],
                                $Record['name'],
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
                $this->redirect_page(admin_url('admin.php?page=exerciseui_manage_training_type'));
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
            'id' => array('id', true)
        );

        return $sortable_columns;
    }


    function get_data()
    {
        $this->prepare_items();
        return $this->items;
    }

    function get_option($option, $default = 10)
    {
        return get_user_meta(get_current_user_id(), $option, true) ?: $default;
    }


    function render_muscle($data, $action, $text = '')
    {
        $id = !empty($data->id) ? $data->id : '';
        $title = $action == 'edit' ? "Edit" : "Add";

        $name = !empty($data->name) ? $data->name : '';
        ?>
        <div class="exercice-form-section">
            <h1 class="wp-heading-inline">
                <?= $title . " training type" ?>
                <input type="submit" data-link="<?= admin_url('admin.php?page=exerciseui_manage_training_type') ?>"
                    value="Save and close" class="button button-primary training-type-button"
                    id="training-type-button-save-top">
                <input type="submit" value="Save and new"
                    data-link="<?= admin_url('admin.php?page=exerciseui_manage_training_type&action=new') ?>"
                    class="button button-primary training-type-button" id="training-type-button-save-new-top">
                <input type="submit"
                    data-link="<?= admin_url('admin.php?page=exerciseui_manage_training_type&action=edit&training-type=') . $id ?>"
                    value="Save" class="button training-type-button" id="training-type-button-apply-top">
            </h1>
            <?= $text ?>
            <div class="container">
                <form method="POST" id="muscle-form">
                    <div class=" field">
                        <div class="field-label">
                            <label for="name">Name</label>
                        </div>
                        <div class="field-item">
                            <input type="text" name="training_type[name]" id="name" value="<?= $name ?>">
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

        $table_name = 'wp_exercise_training_type';

        while (($data = fgetcsv($csv_file)) !== FALSE) {
            $record = array_combine($header, $data);

            $prepared_data = array(
                'name' => $record['name'],
            );



            $existing_record = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE name = %s", $record['name']), ARRAY_A);

            if ($existing_record) {
                $wpdb->update($table_name, $prepared_data, array('name' => $record['name']));
            } else {
                $wpdb->insert($table_name, $prepared_data);
            }
        }

        // Đóng tệp CSV sau khi hoàn thành
        fclose($csv_file);
        $this->redirect_page(admin_url('admin.php?page=exerciseui_manage_training_type'));
    }

    function handle_data($data)
    {
        $result = $this->handle_muscle_data($data);
        return $result;
    }

}

// Hook to create AJAX endpoint for adding muscle data
add_action('wp_ajax_handle_training_type_data', 'handle_training_type_data');
function handle_training_type_data()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['data']['training_type'])) {

        $trainingType = new Training_Type();

        $result = $trainingType->handle_data('training_type', $_POST['data']);

        $result = trim($result);

        echo json_encode(array('redirect_url' => $result));

    } else {
        echo json_encode(array('redirect_url' => admin_url('admin.php?page=exerciseui_manage_training_type&action=new')));
    }
}

add_action('wp_ajax_update_training_type', 'update_training_type');
function update_training_type()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'exercise_training_type';

    $id = intval($_POST['id']);
    $column = sanitize_text_field($_POST['column']);
    $value = sanitize_text_field($_POST['value']);

    $wpdb->update($table_name, array($column => $value), array('id' => $id));

    wp_send_json_success();
    wp_die();
}

function exerciseui_manage_training_type()
{

    $trainingTypeTable = new Training_Type();

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
            $linkNew = admin_url('admin.php?page=exerciseui_manage_training_type&action=new');
            echo '<div class="wrap">
        <h2 class="exercise-list-title">Manage Training Type
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
                    <form action="<?= admin_url('admin.php?page=exerciseui_manage_training_type') ?>" method="post"
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
            $trainingTypeTable->prepare_items();
            ?>

            <form method="post">
                <input type="hidden" name="page" value="" />
                <?php $trainingTypeTable->search_box('search', 'search_id'); ?>
            </form>
            <form action="<?= admin_url('admin.php?page=exerciseui_manage_training_type') ?>" method="POST">
                <?php
                $trainingTypeTable->display();
                ?>
            </form>
            <?php
            echo '</div>';
        } elseif ($_POST['action'] == 'import') {
            $trainingTypeTable->process_bulk_action('import');
        } else {
            $trainingTypeTable->process_bulk_action();
        }
    } elseif ($_GET['action'] == 'delete') {
        if (!empty($_GET['training_type'])) {
            $trainingTypeTable->delete_exercise($_GET['training_type']);
        } else {
            wp_redirect(admin_url('admin.php?page=exerciseui_manage_training_type'));
            exit;
        }
    } else {
        if (!empty($_GET['training_type'])) {
            $data = $trainingTypeTable->get_training_type_by_id(!empty($_GET['training_type']));
            $trainingTypeTable->render_muscle($data, 'edit');
        } else {
            $trainingTypeTable->render_muscle(array(), 'add', $text);
        }
    }
}

function training_type_screen_options()
{
    global $training_type_page;

    // return if not on our settings page
    $screen = get_current_screen();
    if (!is_object($screen) || $screen->id !== $training_type_page) {
        return;
    }

    $args = array(
        'label' => 'training type per page',
        'default' => 15,
        'option' => 'training_type_per_page'
    );
    add_screen_option('per_page', $args);

    // create an instance of class
    $table = new Training_Type();
}
