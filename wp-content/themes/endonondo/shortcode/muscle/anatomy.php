<?php
function anatomy_data($nameMuscle)
{
    global $wpdb;

    $table = $wpdb->prefix . 'exercise_muscle_anatomy';

    $nameMuscle = sanitize_text_field($nameMuscle);

    $prepare = $wpdb->prepare("Select * From $table Where name = %s AND active = 1", $nameMuscle);

    $result = $wpdb->get_results($prepare, ARRAY_A);

    return $result;
}
function anatomy_short_code($atts)
{
    $attributes = shortcode_atts(
        array(
            'value' => '', // Default value if none is provided
        ),
        $atts
    );

    $value = esc_html($attributes['value']);

    if ($value) {
        $arrMuscle = explode(',', $value);
    }
    ob_start();
    ?>
    <section class="exc-primary">
        <div class="exc-container bd-bot">
            <div class="muscle-list primary-muscle">
                <?php foreach ($arrMuscle as $nameMuscle): ?>
                    <?php
                    $primaryData = anatomy_data($nameMuscle);
                    if (!empty($primaryData)):
                        ?>
                        <div class="mucle">
                            <div class="muscle-item">
                                <div class="muscle-img">
                                    <img src="<?= $primaryData[0]['image'] ?>" alt="">
                                </div>
                                <p class="has-medium-font-size"><?= $primaryData[0]['name'] ?></p>
                                <p class="has-small-font-size muslce-ds"><?= $primaryData[0]['description'] ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
    $rt = ob_get_clean();
    return $rt;
}
add_shortcode('anatomir', 'anatomy_short_code');
