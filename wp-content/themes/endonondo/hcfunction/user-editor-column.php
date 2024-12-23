<?php
add_action('manage_users_columns','yoursite_manage_users_columns');
function yoursite_manage_users_columns($column_headers) {
    unset($column_headers['posts']);
    $column_headers['custom_posts'] = 'CPT static';
    $column_headers['custom_posts_total'] = 'Posts total';
    return $column_headers;
}

add_action('manage_users_custom_column','yoursite_manage_users_custom_column',10,3);
function yoursite_manage_users_custom_column($custom_column,$column_name,$user_id) {
    if ($column_name=='custom_posts') {
        $counts = _yoursite_get_author_post_type_counts();
        $custom_column = array();
        if (isset($counts[$user_id]) && is_array($counts[$user_id]))
            foreach($counts[$user_id] as $count) {
                $link = admin_url() . "edit.php?post_type=" . $count['type']. "&author=".$user_id;
                // admin_url() . "edit.php?author=" . $user->ID;
                $custom_column[] = "\t<tr><th><a href={$link}>{$count['label']}</a></th><td>{$count['count']}</td></tr>";
            }
        $custom_column = implode("\n",$custom_column);
        if (empty($custom_column))
            $custom_column = "<th>0</th>";
        $custom_column = "<table>\n{$custom_column}\n</table>";
    }
    if ($column_name=='custom_posts_total') {
        $counts = _yoursite_get_author_post_type_counts();
        $totalp = 0;
        if (isset($counts[$user_id]) && is_array($counts[$user_id]))
            foreach($counts[$user_id] as $count) {
                $totalp += $count['count'];
            }
        $custom_column = "<b>".$totalp."</b>";
    }
    return $custom_column;
}

function _yoursite_get_author_post_type_counts() {
    static $counts;
    if (!isset($counts)) {
        global $wpdb;
        global $wp_post_types;
        $sql = <<<SQL
        SELECT
        post_type,
        post_author,
        COUNT(*) AS post_count
        FROM
        {$wpdb->posts}
        WHERE 1=1
        AND post_type IN ('post','single_reviews','round_up','informational_posts','coupon')
        AND post_status NOT IN ('trash')
        GROUP BY
        post_type,
        post_author
SQL;
        $posts = $wpdb->get_results($sql);
        foreach($posts as $post) {
            $post_type_object = $wp_post_types[$post_type = $post->post_type];
            if (!empty($post_type_object->label))
                $label = $post_type_object->label;
            else if (!empty($post_type_object->labels->name))
                $label = $post_type_object->labels->name;
            else
                $label = ucfirst(str_replace(array('-','_'),' ',$post_type));
            if (!isset($counts[$post_author = $post->post_author]))
                $counts[$post_author] = array();
            $counts[$post_author][] = array(
                'label' => $label,
                'count' => $post->post_count,
                'type' => $post->post_type,
                );
        }
    }
    return $counts;
}