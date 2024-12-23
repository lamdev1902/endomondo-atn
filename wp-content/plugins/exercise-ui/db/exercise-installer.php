<?php
if (!defined('ABSPATH')) {
    exit;
}

class Exercise_Installer
{

    public static function activate()
    {
        self::create_tables();
        self::update_tables();
        // Save the current plugin version in the database
        add_option('exercise_version', EXERCISE_VERSION);
    }

    public static function update()
    {
        $installed_version = get_option('exercise_version');

        if ($installed_version != EXERCISE_VERSION) {
            self::create_tables();
            // Update the plugin version in the database
            update_option('exercise_version', EXERCISE_VERSION);
        }
    }

    private static function create_tables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $tables = [
            $wpdb->prefix . 'exercise_muscle_type' => "
                CREATE TABLE {$wpdb->prefix}exercise_muscle_type (
                    id smallint NOT NULL AUTO_INCREMENT,
                    name varchar(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_muscle_anatomy' => "
                CREATE TABLE {$wpdb->prefix}exercise_muscle_anatomy (
                    id smallint NOT NULL AUTO_INCREMENT,
                    name varchar(255) NOT NULL,
                    type_id smallint NOT NULL,
                    other_name varchar(255),
                    image varchar(255),
                    description text,
                    active tinyint NOT NULL DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    FOREIGN KEY (type_id) REFERENCES {$wpdb->prefix}exercise_muscle_type(id) ON DELETE CASCADE
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_training_type' => "
                CREATE TABLE {$wpdb->prefix}exercise_training_type (
                    id smallint NOT NULL AUTO_INCREMENT,
                    name varchar(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_equipment' => "
                CREATE TABLE {$wpdb->prefix}exercise_equipment (
                    id smallint NOT NULL AUTO_INCREMENT,
                    name varchar(255) NOT NULL,
                    image varchar(255),
                    description text,
                    active tinyint NOT NULL DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise' => "
                CREATE TABLE {$wpdb->prefix}exercise (
                    id smallint NOT NULL AUTO_INCREMENT,
                    name varchar(255) NOT NULL,
                    other_name varchar(255),
                    description text,
                    video_white_male varchar(255),
                    video_green varchar(255),
                    video_transparent varchar(255),
                    video_performer varchar(255),
                    image_male varchar(255),
                    image_female varchar(255),
					slug varchar(255),
					met_value tinyint DEFAULT 1,
                    active tinyint NOT NULL DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_primary_option' => "
                CREATE TABLE {$wpdb->prefix}exercise_primary_option (
                    id smallint NOT NULL AUTO_INCREMENT,
                    exercise_id smallint NOT NULL,
                    muscle_id smallint NOT NULL,
                    FOREIGN KEY (exercise_id) REFERENCES {$wpdb->prefix}exercise(id) ON DELETE CASCADE,
                    FOREIGN KEY (muscle_id) REFERENCES {$wpdb->prefix}exercise_muscle_anatomy(id) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_secondary_option' => "
                CREATE TABLE {$wpdb->prefix}exercise_secondary_option (
                    id smallint NOT NULL AUTO_INCREMENT,
                    exercise_id smallint NOT NULL,
                    muscle_id smallint NOT NULL,
                    FOREIGN KEY (exercise_id) REFERENCES {$wpdb->prefix}exercise(id) ON DELETE CASCADE,
                    FOREIGN KEY (muscle_id) REFERENCES {$wpdb->prefix}exercise_muscle_anatomy(id) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_equipment_option' => "
                CREATE TABLE {$wpdb->prefix}exercise_equipment_option (
                    id smallint NOT NULL AUTO_INCREMENT,
                    exercise_id smallint NOT NULL,
                    equipment_id smallint NOT NULL,
                    FOREIGN KEY (exercise_id) REFERENCES {$wpdb->prefix}exercise(id) ON DELETE CASCADE,
                    FOREIGN KEY (equipment_id) REFERENCES {$wpdb->prefix}exercise_equipment(id) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_training_type_option' => "
                CREATE TABLE {$wpdb->prefix}exercise_training_type_option (
                    id smallint NOT NULL AUTO_INCREMENT,
                    exercise_id smallint NOT NULL,
                    training_id smallint NOT NULL,
                    FOREIGN KEY (exercise_id) REFERENCES {$wpdb->prefix}exercise(id) ON DELETE CASCADE,
                    FOREIGN KEY (training_id) REFERENCES {$wpdb->prefix}exercise_training_type(id) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_content_meta' => "
                CREATE TABLE {$wpdb->prefix}exercise_content (
                    id smallint NOT NULL AUTO_INCREMENT,
                    exercise_id smallint NOT NULL,
                    content_type tinyint NOT NULL default 0,
                    content_title varchar(255),
                    content text,
                    FOREIGN KEY (exercise_id) REFERENCES {$wpdb->prefix}exercise(id) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
			$wpdb->prefix . 'exercise_goal' => "
                CREATE TABLE {$wpdb->prefix}exercise_goal (
                    id smallint NOT NULL AUTO_INCREMENT,
                    name varchar(255) NOT NULL,
                    active tinyint NOT NULL DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_plan' => "
                CREATE TABLE {$wpdb->prefix}exercise_plan (
                    id int  NOT NULL AUTO_INCREMENT,
                    name varchar(255) NOT NULL,
                    cardio tinyint NOT NULL DEFAULT 1,
                    strength tinyint NOT NULL DEFAULT 1,
                    description text,
                    muscle_type_id varchar(255) NOT NULL,
					image varchar(255),
					vertical_image varchar(255),
                    active tinyint NOT NULL DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_plan_goal' => "
                CREATE TABLE {$wpdb->prefix}exercise_plan_goal (
                    id smallint UNSIGNED NOT NULL AUTO_INCREMENT,
                    plan_id int  NOT NULL,
                    goal_id smallint  NOT NULL,
                    FOREIGN KEY (plan_id) REFERENCES {$wpdb->prefix}exercise_plan(id) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_plan_muscle' => "
                CREATE TABLE {$wpdb->prefix}exercise_plan_muscle (
                    id smallint UNSIGNED NOT NULL AUTO_INCREMENT,
                    plan_id int  NOT NULL,
                    muscle_type_id smallint NOT NULL,
                    FOREIGN KEY (muscle_type_id) REFERENCES {$wpdb->prefix}exercise_muscle_type(id) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_training_method' => "
                CREATE TABLE {$wpdb->prefix}exercise_training_method (
                    id smallint UNSIGNED NOT NULL AUTO_INCREMENT,
                    name varchar(255) NOT NULL,
                    description varchar(255),
                    description_app varchar(255),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_weeks' => "
                CREATE TABLE {$wpdb->prefix}exercise_weeks (
                    id int UNSIGNED NOT NULL AUTO_INCREMENT,
                    plan_id int  NOT NULL,
                    week_name varchar(255),  
                    week_description varchar(255),  
                    week_number INT NOT NULL,  
                    FOREIGN KEY (plan_id) REFERENCES {$wpdb->prefix}exercise_plan(id) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_days' => "
                CREATE TABLE {$wpdb->prefix}exercise_days (
                    id int UNSIGNED NOT NULL AUTO_INCREMENT,
                    week_id int UNSIGNED NOT NULL,
                    training_method_id smallint UNSIGNED NOT NULL,
                    num_days INT NOT NULL,                    
                    priority tinyint DEFAULT 1, 
                    FOREIGN KEY (week_id) REFERENCES {$wpdb->prefix}exercise_weeks(id) ON DELETE CASCADE,
                    FOREIGN KEY (training_method_id) REFERENCES {$wpdb->prefix}exercise_training_method(id) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_section' => "
                CREATE TABLE {$wpdb->prefix}exercise_section (
                    id int UNSIGNED NOT NULL AUTO_INCREMENT,
                    day_id int UNSIGNED NOT NULL,
                    type tinyint NOT NULL DEFAULT 0,
                    round tinyint NOT NULL DEFAULT 1,
                    finish tinyint NOT NULL DEFAULT 0,
                    FOREIGN KEY (day_id) REFERENCES {$wpdb->prefix}exercise_days(id) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_schedule' => "
                CREATE TABLE {$wpdb->prefix}exercise_schedule (
                    id int UNSIGNED NOT NULL AUTO_INCREMENT,
                    section_id int UNSIGNED NOT NULL,
                    exercise_id smallint NOT NULL,
                    duration INT,                    
                    reps INT,                    
                    note varchar(255),       
                    finish tinyint NOT NULL DEFAULT 0,
                    FOREIGN KEY (section_id) REFERENCES {$wpdb->prefix}exercise_section(id) ON DELETE CASCADE,
                    FOREIGN KEY (exercise_id) REFERENCES {$wpdb->prefix}exercise(id) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_user_plan' => "
                CREATE TABLE {$wpdb->prefix}exercise_user_plan (
                    id bigint NOT NULL AUTO_INCREMENT,
                    user_id bigint UNSIGNED NOT NULL,
                    plan_id int NOT NULL,
                    FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE,
                    FOREIGN KEY (plan_id) REFERENCES {$wpdb->prefix}exercise_plan(id) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_usermeta' => "
                CREATE TABLE {$wpdb->prefix}exercise_usermeta (
                    id bigint NOT NULL AUTO_INCREMENT,
                    user_id bigint UNSIGNED NOT NULL,
                    goal_id smallint NOT NULL,
                    muscle_type_id varchar(255) NOT NULL,
					experience smallint NOT NULL,
                    additional_goal smallint NOT NULL,
					gender tinyint NOT NULL DEFAULT 1,
                    duration smallint DEFAULT 1,
                    height varchar(255) NOT NULL,
                    weight varchar(255) NOT NULL,
                    appearance_option smallint NOT NULL,
                    page_plan_option smallint NOT NULL,
                    training_date smallint NOT NULL,
					current_week tinyint DEFAULT 0,
					avt varchar(255),
					unit smallint DEFAULT 1,
					created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE,
                    FOREIGN KEY (goal_id) REFERENCES {$wpdb->prefix}exercise_goal(id) ON DELETE CASCADE,
                    FOREIGN KEY (muscle_type_id) REFERENCES {$wpdb->prefix}exercise_muscle_type(id) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
			$wpdb->prefix . 'exercise_finish_section' => "
                CREATE TABLE {$wpdb->prefix}exercise_finish_section (
                    id int UNSIGNED NOT NULL AUTO_INCREMENT,
                    section_id int UNSIGNED NOT NULL,
                    user_id bigint UNSIGNED NOT NULL,
                    finish tinyint NOT NULL DEFAULT 0,
					round tinyint NOT NULL DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (section_id) REFERENCES {$wpdb->prefix}exercise_section(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_met' => "
                CREATE TABLE {$wpdb->prefix}exercise_met (
                    id int UNSIGNED NOT NULL AUTO_INCREMENT,
                    special_activity smallint UNSIGNED NOT NULL,
                    value smallint UNSIGNED NOT NULL,
                    descroption text,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) $charset_collate;",
			$wpdb->prefix . 'exercise_report_weight' => "
                CREATE TABLE {$wpdb->prefix}exercise_report_weight (
                    id int UNSIGNED NOT NULL AUTO_INCREMENT,
                    user_id bigint UNSIGNED NOT NULL,
                    weight smallint NOT NULL DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
            $wpdb->prefix . 'exercise_report_body' => "
                CREATE TABLE {$wpdb->prefix}exercise_report_body (
                    id int UNSIGNED NOT NULL AUTO_INCREMENT,
                    user_id bigint UNSIGNED NOT NULL,
                    image varchar(255),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE,
                    PRIMARY KEY (id)
                ) $charset_collate;",
        ];

        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

        foreach ($tables as $table => $sql) {
            dbDelta($sql);
        }
    }

    private static function update_tables()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'exercise_secondary_option';
        $db_name = DB_NAME;

        // Tìm tên khóa ngoại
        $foreign_key_query = $wpdb->prepare("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = %s 
        AND TABLE_NAME = %s 
        AND COLUMN_NAME = 'exercise_id' 
        AND REFERENCED_COLUMN_NAME IS NOT NULL
    ", $db_name, $table_name);

        $foreign_keys = $wpdb->get_results($foreign_key_query);

        // Xóa khóa ngoại hiện tại
        foreach ($foreign_keys as $key) {
            $constraint_name = $key->CONSTRAINT_NAME;
            $wpdb->query("ALTER TABLE {$table_name} DROP FOREIGN KEY {$constraint_name};");
        }

        // Thêm lại khóa ngoại với ON DELETE CASCADE
        $wpdb->query("ALTER TABLE {$table_name} ADD CONSTRAINT fk_exercise_id FOREIGN KEY (exercise_id) REFERENCES {$wpdb->prefix}exercise(id) ON DELETE CASCADE;");
    }
}
