<?php
/* Template Name: Exercise Page */

$pageid = get_the_ID();
get_header();
the_post();

$queryMS = "
    SELECT DISTINCT mt.id, mt.name
    FROM {$wpdb->prefix}exercise_muscle_type AS mt
    WHERE EXISTS (
        SELECT 1
        FROM {$wpdb->prefix}exercise_muscle_anatomy AS ma
        INNER JOIN {$wpdb->prefix}exercise_primary_option AS epo ON epo.muscle_id = ma.id
        INNER JOIN {$wpdb->prefix}exercise AS e ON e.id = epo.exercise_id
        WHERE ma.type_id = mt.id
        AND e.slug IS NOT NULL
        AND e.slug != ''
        AND e.active = 1
    );
";

$muscle_types = $wpdb->get_results($queryMS);

$queryE = "
    SELECT DISTINCT eq.id, eq.name
    FROM wp_exercise_equipment AS eq
    WHERE EXISTS (
        SELECT 1
        FROM wp_exercise_equipment_option AS eo
        INNER JOIN {$wpdb->prefix}exercise AS e ON e.id = eo.exercise_id
        WHERE eo.equipment_id = eq.id
        AND e.slug IS NOT NULL
        AND e.slug != ''
        AND e.active = 1
    );
";

$equipments = $wpdb->get_results($queryE);

$exEquip = "
    SELECT eq.name
    FROM {$wpdb->prefix}exercise_equipment AS eq
    INNER JOIN {$wpdb->prefix}exercise_equipment_option AS eo 
        ON eq.id = eo.equipment_id
    WHERE eo.exercise_id = %d
";

$exTag = "
    SELECT DISTINCT mt.id, mt.name
    FROM {$wpdb->prefix}exercise_muscle_type AS mt
    INNER JOIN {$wpdb->prefix}exercise_muscle_anatomy AS ma ON ma.type_id = mt.id
    INNER JOIN {$wpdb->prefix}exercise_primary_option AS epo ON epo.muscle_id = ma.id
    WHERE epo.exercise_id = %d
";
?>
<main id="content">
    <?php
    $heroCalculator = get_field('hero_description', $pageid);
    ?>
    <section class="hero-exercise-section mb">
        <div class="page-top-white mb-top-black">
            <div class="container">
                <?php
                if (function_exists('yoast_breadcrumb')) {
                    yoast_breadcrumb('<div id="breadcrumbs" class="breacrump">', '</div>');
                }
                ?>
            </div>
        </div>
        <div class="container">
            <div class="content special-width">
                <h1 class="pri-color-3"><?= the_title() ?></h1>
                <p class="sec-color-2"><?= $heroCalculator ?></p>
                <?php
                $socials = get_field('follow_social', 'option');
                if ($socials):
                    ?>
                    <div class="social flex mr-bottom-20">
                        <p class="has-small-font-size pri-color-3" style="margin-bottom: 0">Follow us: </p>
                        <?php foreach ($socials as $social): ?>
                            <a target="_blank" href="<?php echo $social['link']; ?>"><img alt="<?= $social['icon']['alt']; ?>"
                                    src="<?= $social['icon']['url']; ?>" /></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <div class="mr-bottom-20 search ex-search">
                    <input type="text" name="" id="exerciseSearch">
                    <span class="exIcon"></span>
                </div>
            </div>
        </div>
    </section>
    <?php
    ?>
    <div class="filter-section">
        <div class="container">
            <div class="flex">
                <div class="left"></div>
                <div class="right flex">
                    <div class="flex">
                        <?php if (!empty($muscle_types)): ?>
                            <div class="muscle flex">
                                <p>Target Muscle: </p>
                                <select name="mt" id="mt" multiple>
                                    <?php foreach ($muscle_types as $mt): ?>
                                        <option value="<?= $mt->id ?>"><?= $mt->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($equipments)): ?>
                            <div class="eq flex">
                                <p>Equipment: </p>
                                <select name="eq" id="eq" multiple>
                                    <?php foreach ($equipments as $eq): ?>
                                        <option value="<?= $eq->id ?>"><?= $eq->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="action flex">
                                <div class="clear flex">
                                    <a href="javascript:void(0)" class="clearAction">
                                        <p><strong>Clear All</strong></p>
                                    </a>
                                    <div class="filter-icon">
                                        <img src="<?= get_template_directory_uri() . '/assets/images/close.svg' ?>" alt="">
                                    </div>
                                </div>
                                <div class="apply flex">
                                    <a href="javascript:void(0)" class="applyAction">
                                        <p><strong>Apply</strong></p>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="loader">
        <div class="loader-rs mr-top-20 text-center">
            <h2>No results found!!</h2>
            <p>Try adjusting your search or filter to find what you're looking for.</p>
            <img src="<?= get_template_directory_uri() . '/assets/images/empty.svg' ?>" alt="">
        </div>
        <div class="ex">
            <div id="loader"></div>
        </div>
    </div>
    <section class="result-section">
        <div class="container">
            <?php if (!empty($muscle_types)): ?>
                <div class="grid ex-section">
                    <?php
                    foreach ($muscle_types as $mtIt):
                        $query = "
                            SELECT DISTINCT e.id, e.name, e.description, e.image_male, e.image_female, e.slug
                            FROM {$wpdb->prefix}exercise AS e
                            INNER JOIN {$wpdb->prefix}exercise_primary_option AS epo ON epo.exercise_id = e.id
                            INNER JOIN {$wpdb->prefix}exercise_muscle_anatomy AS ma ON ma.id = epo.muscle_id
                            WHERE ma.type_id = %d
                            AND e.slug IS NOT NULL
                            AND e.slug != ''
                            AND e.active = 1
                            ORDER BY e.created_at DESC
                            LIMIT 4
                        ";

                        $latest_exercises = $wpdb->get_results($wpdb->prepare($query, $mtIt->id));
                        if ($latest_exercises):
                            ?>
                            <h2><?= $mtIt->name ?></h2>
                            <div class="grid grid-ex">
                                <?php
                                foreach ($latest_exercises as $ex):
                                    $exID = $ex->id;
                                    $equipment = $wpdb->get_results($wpdb->prepare($exEquip, $exID), ARRAY_A);
                                    if (!empty($equipment)) {
                                        $equipment_names = array_column($equipment, 'name');

                                        $equipment = implode(', ', $equipment_names);
                                    } else {
                                        $equipment = '';
                                    }
                                    $mtExercises = $wpdb->get_results($wpdb->prepare($exTag, $exID), ARRAY_A);

                                    ?>
                                    <div class="mt flex">
                                        <div class="ex-img">
                                            <a target="_blank" href="<?= home_url('/exercise/' . $ex->slug); ?>">
                                                <img src=" <?= $ex->image_male ?: $ex->image_female ?>" alt="">
                                            </a>
                                        </div>
                                        <div class="ex-content">
                                            <a target="_blank" href="<?= home_url('/exercise/' . $ex->slug); ?>">
                                                <p><strong><?= $ex->name ?></strong></p>
                                            </a>
                                            <p><strong>Equipment:</strong> <?= $equipment ?></p>
                                            <div class="flex">
                                                <?php foreach ($mtExercises as $mtex): ?>
                                                    <span class="has-ssmall-font-size"><?= esc_html($mtex['name']) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($equipments)): ?>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php get_footer(); ?>