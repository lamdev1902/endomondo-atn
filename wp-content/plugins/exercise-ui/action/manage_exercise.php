<?php
ob_clean();
ob_start();
// Define the muscle anatomy class
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
class Manage_Exercise extends WP_List_Table
{
    protected function get_data_by_id($type, $id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->get_table_name($type);

        $data = $wpdb->get_results(
            "SELECT * FROM $table_name WHERE id = $id"
        );
        return $data[0];
    }

    protected function get_muscle_type()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_muscle_type';

        $data = $wpdb->get_results(
            "SELECT * FROM $table_name",
        );
        return $data;
    }

    protected function get_type($type)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'exercise_' . $type . '_type';

        $data = $wpdb->get_results(
            "SELECT * FROM $table_name",
        );


        return $data;
    }

    protected function get_type_by_exercise($id)
    {
        global $wpdb;

        $ids = array();

        $table_name = $wpdb->prefix . 'exercise_training_type_option';

        $data = $wpdb->get_results(
            "SELECT * FROM $table_name WHERE exercise_id = " . $id
        );

        foreach ($data as $item) {
            $ids[] = $item->training_id;
        }

        return $ids;
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
    protected function handle_muscle_data($type, $data)
    {
        global $wpdb;

        if (current_user_can('administrator')) {
            if (!empty($data[$type]['name'])) {
                $muscle_data = array();
                $training = array();
                $option = array(
                    'primary' => array(),
                    'secondary' => array(),
                    'equipment' => array()
                );
                $content = array();
                $check = 0;

                $message = array();

                foreach ($data[$type] as $key => $value) {

                    if ($key == 'type_id' || $key == 'active') {
                        $value = (int) $value;
                    }

                    if ($key == 'description' && $value) {
                        $value = wp_unslash($value);
                    }

                    if ($key == 'primary' || $key == 'secondary' || $key == 'equipment') {
                        if ($key == 'primary') {
                            $option['primary'] = $value;
                            unset($data[$type][$key]);
                        }

                        if ($key == 'secondary') {
                            $option['secondary'] = $value;
                            unset($data[$type][$key]);
                        }

                        if ($key == 'equipment') {
                            $option['equipment'] = $value;
                            unset($data[$type][$key]);
                        }
                    } elseif ($key == 'training' && !empty($data[$type][$key])) {
                        if ($data[$type][$key][0] == 'all') {
                            $arr = $this->get_type('training');
                            foreach ($arr as $id) {
                                $training[] = $id->id;
                            }
                        } else {
                            $training = $data[$type][$key];
                        }
                    } elseif ($key == 'exercise_content') {
                        $content = $data[$type][$key];

                    } else {
                        $muscle_data[$key] = $value;
                    }
                }

                $table_name = $wpdb->prefix . $this->get_table_name($type);

                if ($data['action'] == 'add') {

                    $check = $wpdb->insert(
                        $table_name,
                        $muscle_data
                    );

                    if ($check !== false) {
                        $newid = $wpdb->insert_id;

                        $this->hanlde_option_data($newid, $option);

                        $this->hanlde_training_data($newid, $training);

                        $this->handle_content($newid, $content);
                    }

                    $text = ucfirst($type) . " added successfully!";

                } elseif ($data['action'] == 'edit') {
                    if (!empty($data['id'])) {
                        $check = $wpdb->update(
                            $table_name,
                            $muscle_data,
                            array('id' => $data['id'])
                        );

                        $this->hanlde_option_data($data['id'], $option);

                        $this->hanlde_training_data($data['id'], $training);

                        $this->handle_content($data['id'], $content);

                        $text = ucfirst($type) . " updated successfully!";
                    } else {
                        return $this->get_link($type);
                    }
                }


                $message['count'] = 1;
                $message['text'] = $text;

                set_transient('message', $message, 60 * 60 * 12);

                return $this->get_link($type);
            }
        }
        return $this->get_link($type);
    }

    public function hanlde_training_data($exerciseId, $data = array())
    {
        global $wpdb;

        $table = $wpdb->prefix . 'exercise_training_type_option';

        $sql_check = $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE  exercise_id = %s ", $exerciseId);

        $where = array('exercise_id' => $exerciseId);

        if ($wpdb->get_var($sql_check) > 0) {
            $wpdb->delete($table, $where);
        }
        foreach ($data as $id) {
            if ($id) {
                $wpdb->insert(
                    $table,
                    array(
                        'exercise_id' => $exerciseId,
                        'training_id' => $id,
                    ),
                    array(
                        '%d',
                        '%d',
                    )
                );
            }

        }
    }

    public function handle_content($exerciseId, $data = array())
    {
        global $wpdb;

        $table = $wpdb->prefix . 'exercise_content';

        $sql_check = $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE  exercise_id = %s ", $exerciseId);

        $where = array('exercise_id' => $exerciseId);

        if ($wpdb->get_var($sql_check) > 0) {
            $wpdb->delete($table, $where);
        }
        foreach ($data as $item) {
            if (!empty($item['content'])) {
                $item['content'] = wp_unslash($item['content']);
            }

            $item['exercise_id'] = $exerciseId;
            $wpdb->insert(
                $table,
                $item
            );

        }
    }
    public function hanlde_option_data($exerciseId, $data = array())
    {
        global $wpdb;

        foreach ($data as $key => $option) {
            if ($option) {
                $table = $wpdb->prefix . 'exercise_' . $key . '_option';

                $recordTitle = 'equipment_id';
                if ($key == 'primary' || $key == 'secondary') {
                    $recordTitle = 'muscle_id';
                }

                if ($key == 'training_type') {
                    $recordTitle = 'training_id';
                }

                $sql_check = $wpdb->prepare("SELECT COUNT(*) FROM $table WHERE  exercise_id = %s ", $exerciseId);

                $where = array('exercise_id' => $exerciseId);

                if ($wpdb->get_var($sql_check) > 0) {
                    $wpdb->delete($table, $where);
                }

                foreach ($option as $id) {

                    $wpdb->insert(
                        $table,
                        array(
                            'exercise_id' => $exerciseId,
                            $recordTitle => $id,
                        ),
                        array(
                            '%d',
                            '%d',
                        )
                    );
                }
            }
        }
    }
    protected function delete_data_by_id($type, $id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->get_table_name($type);

        $result = 0;

        if (is_array($id)) {
            foreach ($id as $item) {
                $sql_check = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE  id = %s", $item);
                if ($wpdb->get_var($sql_check) > 0) {

                    $where = array('id' => $item);

                    $result = $wpdb->delete(
                        $table_name,
                        $where
                    );
                }
            }
        } else {
            $sql_check = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE  id = %s", $id);
            if ($wpdb->get_var($sql_check) > 0) {

                $where = array('id' => $id);

                $result = $wpdb->delete(
                    $table_name,
                    $where
                );
            }
        }

        if ($result) {
            $message['count'] = $result;
            $message['text'] = 'The selected items were successfully deleted';

            set_transient('message', $message, 60 * 60 * 12);
        }
        $this->redirect_page($this->get_link($type));
    }

    protected function publish_data_by_id($type, $id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->get_table_name($type);

        $result = 0;

        if (is_array($id)) {
            foreach ($id as $item) {
                $data = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $item");
                if (!empty($data[0])) {

                    $arr = get_object_vars($data[0]);

                    $arr['active'] = 1;

                    $where = array('id' => $item);

                    $result = $wpdb->update(
                        $table_name,
                        $arr,
                        $where
                    );
                }
            }
        } else {
            $data = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $id");
            if (!empty($data[0])) {

                $arr = get_object_vars($data[0]);

                $arr['active'] = 1;

                $where = array('id' => $id);

                $result = $wpdb->update(
                    $table_name,
                    $arr,
                    $where
                );
            }
        }

        if ($result) {
            $message['count'] = $result;
            $message['text'] = 'The selected items were successfully published';

            set_transient('message', $message, 60 * 60 * 12);
        }
        $this->redirect_page($this->get_link($type));
    }

    protected function unpublish_data_by_id($type, $id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->get_table_name($type);

        $result = 0;

        if (is_array($id)) {
            foreach ($id as $item) {
                $data = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $item");
                if (!empty($data[0])) {

                    $arr = get_object_vars($data[0]);

                    $arr['active'] = 0;

                    $where = array('id' => $item);

                    $result = $wpdb->update(
                        $table_name,
                        $arr,
                        $where
                    );
                }
            }
        } else {
            $data = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $id");
            if (!empty($data[0])) {

                $arr = get_object_vars($data);

                $arr['active'] = 0;

                $where = array('id' => $id);

                $result = $wpdb->update(
                    $table_name,
                    $arr,
                    $where
                );
            }
        }

        if ($result) {
            $message['count'] = $result;
            $message['text'] = 'The selected items were successfully unpublished';

            set_transient('message', $message, 60 * 60 * 12);
        }

        $this->redirect_page($this->get_link($type));
    }

    protected function get_table_name($type)
    {
        switch ($type) {
            case "exercise":
                $table_name = "exercise";
                break;
            case "equipment":
                $table_name = "exercise_equipment";
                break;
            case "muscle_type":
                $table_name = "exercise_muscle_type";
                break;
            case "training_type":
                $table_name = "exercise_training_type";
                break;
            default:
                $table_name = "exercise_muscle_anatomy";
        }

        return $table_name;
    }

    protected function get_link($type)
    {
        switch ($type) {
            case "exercise":
                $link = admin_url('admin.php?page=exerciseui_manage_exercise');
                break;
            case "equipment":
                $link = admin_url('admin.php?page=exerciseui_manage_equipment');
                break;
            case "muscle_type":
                $link = admin_url('admin.php?page=exerciseui_manage_muscle_type');
                break;
            case "training_type":
                $link = admin_url('admin.php?page=exerciseui_manage_training_type');
                break;
            default:
                $link = admin_url('admin.php?page=exerciseui_manage_muscle');
        }

        return $link;
    }

    protected function redirect_page($url)
    {
        ob_start();
        if (!headers_sent()) {
            wp_redirect($url);
            exit;
        } else {
            echo "<script>location.href='" . esc_url($url) . "';</script>";
            exit;
        }
    }

}
