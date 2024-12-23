<?php
class Exercise_Equipment extends Manage_Exercise
{
    private $searchOption;
    private $exlId;
    private $equipment_data;


    public function __construct($exlId = array(),$searchOption = "")
    {
        parent::__construct(); // Gọi đến constructor của lớp cha nếu có
        $this->exlId = $exlId;
        $this->searchOption = $searchOption;
    }
    private function get_equipment($search = "")
    {
        global $wpdb;

        if (!empty($search)) {
            return $wpdb->get_results(
                "SELECT * from {$wpdb->prefix}exercise_equipment WHERE id Like '%{$search}%' OR name LIKE '%{$search}%'",
                ARRAY_A
            );
        } else {
            return $wpdb->get_results(
                "SELECT * From {$wpdb->prefix}exercise_equipment",
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
            'image' => 'Image',
            'description' => 'Description',
            'active' => 'Active',
            'created_at' => 'Create At',
        );

        return $columns;
    }

    // Bind table with columns, data and all
    function prepare_items()
    {
        if(!$this->searchOption){
            if (isset($_POST['page']) && isset($_POST['s'])) {
                $this->equipment_data = $this->get_equipment($_POST['s']);
            } else {
                $this->equipment_data = $this->get_equipment();
            }
        }else {
            $this->equipment_data = $this->get_equipment($this->searchOption);
        }
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();
        /* pagination */
        $per_page = $this->get_items_per_page('equipment_per_page', 15);
        $current_page = $this->get_pagenum();
        $total_items = count($this->equipment_data);

        $this->equipment_data = array_slice($this->equipment_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(
            array(
                'total_items' => $total_items, // total number of items
                'per_page' => $per_page // items to show on a page
            )
        );

        // usort($this->equipment_data, array(&$this, 'usort_reorder'));

        $this->items = $this->equipment_data;
    }

    // bind data with column
    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'id':
            case 'name':
            case 'image':
            case 'description':
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
        $checked = '';
        $classSelected = '';
        if ($this->exlId) {
            $checked = in_array($item['id'], $this->exlId) ? 'checked' : '';
            $classSelected = in_array($item['id'], $this->exlId) ? 'selected' : '';
        }

        return sprintf(
            '<input type="checkbox" name="equipment_id[]" class="%s" value="%s" %s/>',
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

    public function process_bulk_action($text = '')
    {
        if (!$text) {
            $action = $this->current_action();

            if ($action == 'delete_multiple') {
                $this->delete_data_by_id('equipment', $_POST['equipment_id']);
            }

            if ($action == 'published_mutiple') {
                $this->publish_data_by_id('equipment', $_POST['equipment_id']);
            }

            if ($action == 'unpublished_mutiple') {
                $this->unpublish_data_by_id('equipment', $_POST['equipment_id']);
            }

            if ("export-all" === $this->current_action()) {
                global $wpdb;

                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="equipment.csv"');

                // clean out other output buffers
                ob_end_clean();

                $fp = fopen('php://output', 'w');

                // CSV/Excel header label
                $header_row = array(
                    0 => 'ID',
                    1 => 'Name',
                    4 => 'Image',
                    5 => 'Description',
                    6 => 'Active',
                    7 => 'Created At',
                    8 => 'Update At',
                );

                fputcsv($fp, $header_row);

                $Table_Name = 'wp_exercise_equipment';
                $sql_query = "SELECT * FROM $Table_Name";
                $rows = $wpdb->get_results($sql_query, ARRAY_A);
                if (!empty($rows)) {
                    foreach ($rows as $Record) {
                        $OutputRecord = array(
                            $Record['id'],
                            $Record['name'],
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
                $ids = isset($_POST['equipment_id']) ? $_POST['equipment_id'] : array();
                if (is_array($ids))
                    $ids = implode(',', $ids);

                if (!empty($ids)) {
                    // Use headers so the data goes to a file and not displayed
                    global $wpdb;

                    header('Content-Type: text/csv');
                    header('Content-Disposition: attachment; filename="equipment-seleted.csv"');

                    // clean out other output buffers
                    ob_end_clean();

                    $fp = fopen('php://output', 'w');

                    // CSV/Excel header label
                    $header_row = array(
                        0 => 'ID',
                        1 => 'Name',
                        4 => 'Image',
                        5 => 'Description',
                        6 => 'Active',
                        7 => 'Created At',
                        8 => 'Update At',
                    );

                    //write the header
                    fputcsv($fp, $header_row);

                    $Table_Name = 'wp_exercise_equipment';
                    $sql_query = $wpdb->prepare("SELECT * FROM $Table_Name WHERE id IN (%s)", $ids);
                    $rows = $wpdb->get_results($sql_query, ARRAY_A);
                    if (!empty($rows)) {
                        foreach ($rows as $Record) {
                            $OutputRecord = array(
                                $Record['id'],
                                $Record['name'],
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

                echo "Import successful!";
            } else {
                echo "Error uploading file!";
            }
        }
    }

    function import_data_from_csv($file_path)
    {
        global $wpdb;

        $csv_file = fopen($file_path, "r");

        $header = fgetcsv($csv_file);

        $table_name = 'wp_exercise_equipment';

        while (($data = fgetcsv($csv_file)) !== FALSE) {
            $record = array_combine($header, $data);

            $prepared_data = array(
                'name' => $record['name'],
                'image' => $record['image'],
                'description' => $record['description'],
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
        $this->redirect_page(admin_url('admin.php?page=exerciseui_manage_equipment'));
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
            'label' => 'Equipment',
            'default' => 15,
            'option' => 'equipment_per_page'
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
            'edit' => sprintf('<a href="?page=%s&action=%s&equipment=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['id']),
            'delete' => sprintf('<a href="?page=%s&action=%s&equipment=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['id']),
        );
        return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions));
    }

    function column_active($item)
    {
        $text = $item['active'] == 1 ? "Pulished" : "Unpublished";
        $class = $item['active'] == 1 ? "published" : "unpublished";

        return sprintf("<p class='%s'>%s</p>", $class, $text);
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

    function get_equipment_by_id($id)
    {
        $data = $this->get_data_by_id('equipment', $id);

        return $data;
    }


    function render_equipment($data, $action, $text = '')
    {
        $id = !empty($data->id) ? $data->id : '';
        $title = $action == 'edit' ? "Edit" : "Add";

        $name = !empty($data->name) ? $data->name : '';
        $img = !empty($data->image) ? $data->image : '';
        $description = !empty($data->description) ? $data->description : '';
        $status = !empty($data->active) ? $data->active : 1;
        ?>
        <div id="overlay">
            <div class="cv-spinner">
                <span class="spinner-ex"></span>
            </div>
        </div>
        <div class="exercice-form-section">
            <h1 class="wp-heading-inline">
                <?= $title . " equipment" ?>
                <input type="submit" data-link="<?= admin_url('admin.php?page=exerciseui_manage_equipment') ?>"
                    value="Save and close" class="button button-primary equipment-button" id="muscle-button-save-top">
                <input type="submit" value="Save and new"
                    data-link="<?= admin_url('admin.php?page=exerciseui_manage_equipment&action=new') ?>"
                    class="button button-primary equipment-button" id="muscle-button-save-new-top">
                <input type="submit"
                    data-link="<?= admin_url('admin.php?page=exerciseui_manage_equipment&action=edit&equipment=') . $id ?>"
                    value="Save" class="button equipment-button" id="muscle-button-apply-top">
            </h1>
            <?= $text ?>
            <div class="container">
                <form method="POST" id="muscle-form">
                    <div class="field">
                        <div class="field-label attention">
                            <label for="name">Name</label>
                        </div>
                        <div class="field-item">
                            <input type="text" name="equipment[name]" id="name" value="<?= $name ?>">
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
                            <input type="hidden" name="equipment[image]" class="exercise-image" value="<?= $img ?>" />
                        </div>
                    </div>
                    <div class="field">
                        <div class="field-label">
                            <label for="type">Status</label>
                        </div>
                        <div class="field-item field-item-radio">
                            <div class="item">
                                <input type="radio" name="equipment[active]" <?= $status == 1 ? 'checked' : '' ?> value="1"
                                    id="published">
                                <label for="published">Published</label>
                            </div>
                            <div class="item">
                                <input type="radio" name="equipment[active]" <?= $status == 0 ? 'checked' : '' ?> value="0"
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

    function handle_data($type, $data)
    {
        $result = $this->handle_muscle_data($type, $data);
        return $result;
    }

    function delete_equipment($id)
    {
        $this->delete_data_by_id('equipment', $id);
    }

}

// Hook to create AJAX endpoint for adding muscle data
add_action('wp_ajax_handle_equipment_data', 'handle_equipment_data');
function handle_equipment_data()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['data']['equipment'])) {

        $muscle = new Exercise_Equipment();

        $result = $muscle->handle_data('equipment', $_POST['data']);

        $result = trim($result);

        echo json_encode(array('redirect_url' => $result));

    } else {
        echo json_encode(array('redirect_url' => admin_url('admin.php?page=exerciseui_manage_equipment&action=new')));
    }
}

function exerciseui_manage_equipment()
{
    $equipment = new Exercise_Equipment();

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
            $linkNew = admin_url('admin.php?page=exerciseui_manage_equipment&action=new');
            echo '<div class="wrap">
        <h2 class="exercise-list-title">Manage Equipment
        <a href="' . $linkNew . '" class="button btn-new">Add New</a></h2>
        ' . $text . '
        </div>';
            ?>
            <div class="upload-import-file-wrap">
                <div class="upload-import-file">
                    <p class="install-help">
                        <?= "If you have questions in a .csv, you may add it by uploading it here." ?>
                        <a class="ays_help" data-toggle="tooltip"
                            title="<?= 'Make sure the categories of the questions start with letters, instead of numbers, while importing. Please note, that the categories must start with letters so that the functionality can work correctly for you.' ?>">
                            <i class="ays_fa ays_fa_info_circle"></i>
                        </a>
                    </p>
                    <form action="<?= admin_url('admin.php?page=exerciseui_manage_equipment') ?>" method="post"
                        enctype="multipart/form-data" class="ays-dn">
                        <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, .json"
                            name="quiz_import_file" id="import_file" />
                        <label class="screen-reader-text" for="import_file"><?= "Import file" ?></label>
                        <input type="submit" name="import-file-submit" class="button" value="<?= "Import now" ?>">
                        <input type="hidden" name="action" class="button" value="import">
                    </form>
                </div>
            </div>
            <?php
            $equipment->prepare_items();
            ?>

            <form method="post">
                <input type="hidden" name="page" value="" />
                <?php $equipment->search_box('search', 'search_id'); ?>
            </form>
            <form action="<?= admin_url('admin.php?page=exerciseui_manage_equipment') ?>" method="POST">
                <?php
                $equipment->display();
                ?>
            </form>
            <?php
            echo '</div>';
        } elseif ($_POST['action'] == 'import') {
            $equipment->process_bulk_action('import');
        } else {
            $equipment->process_bulk_action();
        }
    } elseif ($_GET['action'] == 'delete') {
        if (!empty($_GET['equipment'])) {
            $equipment->delete_equipment($_GET['equipment']);
        } else {
            wp_redirect(admin_url('admin.php?page=exerciseui_manage_equipment'));
            exit;
        }
    } else {
        if (!empty($_GET['equipment'])) {
            $data = $equipment->get_equipment_by_id($_GET['equipment']);
            $equipment->render_equipment($data, 'edit');
        } else {
            $equipment->render_equipment(array(), 'add', $text);
        }
    }
}

function equipment_screen_options()
{
    global $equipment_page;

    // return if not on our settings page
    $screen = get_current_screen();
    if (!is_object($screen) || $screen->id !== $equipment_page) {
        return;
    }

    $args = array(
        'label' => 'Equipment per page',
        'default' => 15,
        'option' => 'equipment_per_page'
    );
    add_screen_option('per_page', $args);

    // create an instance of class
    $table = new Exercise_Equipment();
}