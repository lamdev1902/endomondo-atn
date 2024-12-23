<?php
$content = array(
    0 => 'How To Do',
    1 => 'Tips From Expert',
    2 => 'Optimal Sets and Reps',
    3 => 'How to Put in Your Workout Split',
    4 => 'Primary Content',
    5 => 'Secondary Content',
    6 => 'Equipment Content'
);

$description = array();

for ($i = 0; $i <= count($content); $i++) {
    $description[$i] = '';
    if (!empty($content_meta[$i]) && $content_meta[$i]['content_title'] == $content[$i] && $content_meta[$i]['content']) {
        $description[$i] = $content_meta[$i]['content'];
    }
}
?>
<div class="field">
    <div class="field-label attention">
        <label for="type">Exercise Content Meta</label>
    </div>
    <div class="field-item content-meta">
        <?php foreach ($content as $key => $text): ?>
            <div class="item">
                <div class="title">
                    <label for=""><?=$text?></label>
                    <input type="hidden" value="<?=$key?>" name="exercise[exercise_content][<?=$key?>][content_type]">
                    <input type="hidden" value="<?=$text?>" name="exercise[exercise_content][<?=$key?>][content_title]">
                </div>
                <div class="content">
                    <?php
                    $textEditor = $description[$key];
                    $editor_id = 'content_'.$key;
                    $settings = array(
                        'textarea_name' => 'exercise[exercise_content]['.$key.'][content]',
                        'textarea_rows' => 4,
                        'media_buttons' => false,
                        'quicktags' => false,
                        'editor_class' => 'my-wp-editor'
                    );
                    wp_editor($textEditor, $editor_id, $settings);
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>