<?php

global $wpdb;

$postid = get_the_ID();

$exerciseId = get_post_meta($post->ID, 'exercise_name', true);
get_header();

$post_type = $post->post_type;

?>
<main id="content">
    <?php

    $exData = $wpdb->get_results(
        "SELECT * FROM {$wpdb->prefix}exercise WHERE id = " . $exerciseId,
        ARRAY_A
    );

    $arrVideo = array();
    if ($exData) {
        $arrVideo = array(
            $exData[0]['video_white_male'],
            $exData[0]['video_green'],
            $exData[0]['video_transparent'],
        );

    }

    $video = '';
    $checkPath = false;
    $isYoutube = true;
    foreach ($arrVideo as $vid) {
        if ($vid) {
            $video = $vid;
        }
    }

    if ($video) {
        $youtubeMatch = preg_match(
            '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
            $video,
            $matches
        );

        if ($youtubeMatch) {
            $videoId = $matches[1];
            $video = 'https://www.youtube.com/embed/' . $videoId;
            $isYoutube = true;
        }

        $videoPath = parse_url($video, PHP_URL_PATH);
        $extension = pathinfo($videoPath, PATHINFO_EXTENSION);

        if ($extension == 'mp4') {
            $checkPath = true;
            $elemlemnt = "<video autoplay='autoplay' loop='loop' muted playsinline  oncontextmenu='return false;'  preload='auto' src='$video'>Your browser does not support the video tag.</video>";
        }
    }
    if ($exData):
        ?>
        <section class="single-top">
            <div class="container">
                <div class="list-flex flex-center flex-middle">
                    <?php
                    if (function_exists('yoast_breadcrumb')) {
                        yoast_breadcrumb('<div id="breadcrumbs" class="breacrump">', '</div>');
                    }
                    ?>
                </div>
            </div>
        </section>
        <section class="exc-hero-section">
            <div class="container">
                <div class="exc-container">
                    <?php if ($checkPath): ?>
                        <div class="exc-video">
                            <?= $elemlemnt ?>
                        </div>
                    <?php else: ?>
                        <div id="exc-container" style="padding:56.25% 0 0 0;position:relative;">
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    var player;

                                    function onYouTubeIframeAPIReady() {
                                        document.getElementById('exc-container').innerHTML = '<iframe id="player" marginwidth="0" marginheight="0" align="top" scrolling="No" frameborder="0" hspace="0" vspace="0" src="https://www.youtube.com/embed/<?= $videoId ?>?rel=0&amp;fs=0&amp;autoplay=1&mute=1&loop=1&color=white&controls=0&modestbranding=1&playsinline=1&enablejsapi=1&playlist=<?= $videoId ?>" frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" style="position:absolute;top:0;left:0;width:100%;height:100%;border:none"></iframe>';
                                        player = new YT.Player('player', {
                                            events: {
                                                'onReady': onPlayerReady,
                                                'onStateChange': onPlayerStateChange
                                            }
                                        });
                                    }

                                    function onPlayerReady(event) {
                                        var YTP = event.target;
                                        YTP.playVideo();
                                        // Đảm bảo controls và info bị ẩn
                                        setTimeout(function () {
                                            YTP.setOption("controls", 0);
                                            YTP.setOption("modestbranding", 1);
                                            YTP.setOption("rel", 0);
                                            YTP.setOption("showinfo", 0);
                                        }, 1000);
                                    }

                                    function onPlayerStateChange(event) {
                                        var YTP = event.target;
                                        if (event.data === 1) {
                                            var remains = YTP.getDuration() - YTP.getCurrentTime();
                                            if (this.rewindTO)
                                                clearTimeout(this.rewindTO);
                                            this.rewindTO = setTimeout(function () {
                                                YTP.seekTo(0);
                                            }, (remains - 0.1) * 1000);
                                        }
                                    }

                                    onYouTubeIframeAPIReady();
                                });
                            </script>
                        </div>
                    <?php endif; ?>
                    <div class="exc-title">
                        <h1><?= $exData[0]['name'] ?></h1>
                    </div>
                    <div class="social mr-bottom-20">
                        <p class="has-small-font-size pri-color-2" style="margin-bottom: 0">Follow us: </p>
                        <?php
                        $socials = get_field('follow_social', 'option');
                        if ($socials) {
                            foreach ($socials as $social) {
                                ?>
                                <a target="_blank" href="<?php echo $social['link']; ?>"><img alt="<?= $social['icon']['alt']; ?>"
                                        src="<?= $social['icon']['url']; ?>" /></a>
                            <?php }
                        } ?>
                    </div>
                    <div class="exc-description">
                        <?= $exData[0]['description'] ?>
                    </div>
                </div>
            </div>
        </section>

        <?php
        $contents = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}exercise_content WHERE exercise_id = " . $exerciseId,
            ARRAY_A
        );
        $contentPrimary = "";
        $contentSecondary = "";
        $contentEquipment = "";
        ?>
        <section class="exc-content">
            <div class="container">
                <div class="exc-container">
                    <?php foreach ($contents as $content): ?>
                        <?php if ($content['content_type'] != 4 && $content['content_type'] != 5 && $content['content_type'] != 6): ?>
                            <?php if (!empty($content['content'])): ?>
                                <div class="content-item bd-bot exercise-list-start">
                                    <h2 class="title-content"><?= $content['content_title']; ?></h2>
                                    <?= $content['content'] ?>
                                    <?php
                                    if ($content['content_type'] == 2):
                                        $optimals = get_field('optimal_sets_and_reps', $postid);
                                        if ($optimals):
                                            ?>
                                            <div style="overflow: auto">
                                                <figure class="wp-block-table">
                                                    <table>
                                                        <thead>
                                                            <tr>
                                                                <th>Training Type</th>
                                                                <th>Sets</th>
                                                                <?php if (!empty($optimals[0]['exc_training_reps'])): ?>
                                                                    <th>Reps</th>
                                                                <?php else: ?>
                                                                    <th>Duration</th>
                                                                <?php endif; ?>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($optimals as $optimal): ?>
                                                                <tr>
                                                                    <?php foreach ($optimal as $key => $item): ?>
                                                                        <?php if (!empty($item)): ?>
                                                                            <td><?= $item ?></td>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                    <figcaption class="wp-element-caption">Optimal Sets & Reps of <?= $exData[0]['name'] ?>
                                                    </figcaption>
                                                </figure>
                                            </div>
                                        <?php endif; endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php else:
                            if ($content['content_type'] == 4) {
                                $contentPrimary = $content['content'];
                            } elseif ($content['content_type'] == 5) {
                                $contentSecondary = $content['content'];
                            } elseif ($content['content_type'] == 6) {
                                $contentEquipment = $content['content'];
                            }
                            ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php get_template_part('template-parts/content', 'enfit'); ?>
                </div>
            </div>
        </section>
        <?php
        $primaryIds = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}exercise_primary_option WHERE exercise_id = " . $exerciseId,
            ARRAY_A
        );

        $arrPrimaryId = array();
        foreach ($primaryIds as $primaryId) {
            $arrPrimaryId[] = $primaryId['muscle_id'];
        }

        $primaryDatas = [];
        $ids = '';
        if ($arrPrimaryId) {
            $ids = implode(',', $arrPrimaryId);

            $primaryDatas = $wpdb->get_results(
                "SELECT * FROM {$wpdb->prefix}exercise_muscle_anatomy WHERE id IN ({$ids}) AND active = 1",
                ARRAY_A
            );

            $muscle_ids = $arrPrimaryId;
            $exclude_exercise_id = $exerciseId;
            $placeholders = implode(',', array_fill(0, count($muscle_ids), '%d'));

            $query = "
                SELECT exercise_id
                FROM {$wpdb->prefix}exercise_primary_option
                WHERE muscle_id IN ($placeholders)
                AND exercise_id != %d
                GROUP BY exercise_id
            ";

            $prepared_query = $wpdb->prepare(
                $query,
                array_merge($muscle_ids, [$exclude_exercise_id])
            );

            $samePrimary = $wpdb->get_results($prepared_query, ARRAY_A);

            foreach ($samePrimary as $key => $idPri) {

                $preparePri = $wpdb->prepare(
                    "SELECT muscle_id as muscle_id
                    FROM {$wpdb->prefix}exercise_primary_option
                    WHERE exercise_id = %d 
                    ",
                    $idPri
                );

                $resultPrimary = $wpdb->get_col($preparePri);

                if (!array_intersect($resultPrimary, $muscle_ids)) {
                    unset($samePrimary[$key]);
                }
            }
        }



        if ($primaryDatas):
            ?>
            <section class="exc-primary">
                <div class="container">
                    <div class="exc-container bd-bot">
                        <div class="muscle-title">
                            <h2>Primary Muscle Groups</h2>
                        </div>
                        <div class="muscle-list primary-muscle">
                            <?php foreach ($primaryDatas as $primaryData): ?>
                                <div class="mucle">
                                    <div class="muscle-item">
                                        <div class="muscle-img">
                                            <img src="<?= $primaryData['image'] ?>" alt="">
                                        </div>
                                        <p class="has-medium-font-size"><?= $primaryData['name'] ?></p>
                                        <p class="has-small-font-size"><?= $primaryData['description'] ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </section>
            <?php if (!empty($contentPrimary)): ?>
                <div class="container">
                    <div class="exc-container">
                        <div class="muscle-text bd-bot exercise-list-start">
                            <?= $contentPrimary ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php
        $secondaryIds = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}exercise_secondary_option WHERE exercise_id = " . $exerciseId,
            ARRAY_A
        );

        $arrSecondaryId = array();
        foreach ($secondaryIds as $secondaryId) {
            $arrSecondaryId[] = $secondaryId['muscle_id'];
        }

        $secondaryDatas = [];
        $ids = '';
        if ($arrSecondaryId) {
            $ids = implode(',', $arrSecondaryId);

            $secondaryDatas = $wpdb->get_results(
                "SELECT * FROM {$wpdb->prefix}exercise_muscle_anatomy WHERE id IN ({$ids}) AND active = 1",
                ARRAY_A
            );
        }
        if ($secondaryDatas):
            ?>
            <section class="exc-secondary">
                <div class="container">
                    <div class="exc-container bd-bot">
                        <div class="muscle-title">
                            <h2>Secondary Muscle Groups</h2>
                        </div>
                        <div class="muscle-list secondary-muscle">
                            <?php foreach ($secondaryDatas as $secondaryData): ?>
                                <div class="secondary-item muscle-item">
                                    <div class="muscle-img">
                                        <img src="<?= $secondaryData['image'] ?>" alt="">
                                    </div>
                                    <p class="has-medium-font-size"><?= $secondaryData['name'] ?></p>
                                    <p class="has-small-font-size"><?= $secondaryData['description'] ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </section>
            <?php if (!empty($contentSecondary)): ?>
                <div class="container">
                    <div class="exc-container">
                        <div class="muscle-text bd-bot exercise-list-start">
                            <?= $contentSecondary ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php
        $equipmentIds = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}exercise_equipment_option WHERE exercise_id = " . $exerciseId,
            ARRAY_A
        );

        $arrEquipmentId = array();
        foreach ($equipmentIds as $equipmentId) {
            $arrEquipmentId[] = $equipmentId['equipment_id'];
        }

        $equipmentDatas = [];

        $ids = '';
        $sameEquipment = [];

        if ($arrEquipmentId) {
            $ids = implode(',', $arrEquipmentId);

            $equipmentDatas = $wpdb->get_results(
                "SELECT * FROM {$wpdb->prefix}exercise_equipment WHERE id IN ({$ids}) AND active = 1",
                ARRAY_A
            );

            $placeholderss = implode(',', array_fill(0, count($arrEquipmentId), '%d'));

            $var = $wpdb->prepare(
                "SELECT exercise_id
                FROM {$wpdb->prefix}exercise_equipment_option
                WHERE equipment_id IN ($placeholderss)
                AND exercise_id != %d
                GROUP BY exercise_id",
                array_merge($arrEquipmentId, [$exerciseId])
            );

            $sameEquipment = $wpdb->get_results($var, ARRAY_A);
        }


        if ($equipmentDatas):
            ?>
            <section class="exc-equipment">
                <div class="container">
                    <div class="exc-container bd-bot">
                        <div class="muscle-title">
                            <h2>Equipment</h2>
                        </div>
                        <div class="equipment-container">
                            <div class="muscle-list equipment-list">
                                <?php foreach ($equipmentDatas as $equipmentData): ?>
                                    <?php if (!empty($equipmentData['image'])): ?>
                                        <div class="equipment-item muscle-item">
                                            <div class="muscle-img">
                                                <img src="<?= $equipmentData['image'] ?>" alt="">
                                            </div>
                                            <p class="has-medium-font-size"><?= $equipmentData['name'] ?></p>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <div class="muscle-text exercise-equipment-start">
                                <?= $contentEquipment ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php

        $primaryIds = array_column($samePrimary, 'exercise_id');
        $equipmentIds = array_column($sameEquipment, 'exercise_id');


        $variations = array_intersect($primaryIds, $equipmentIds);

        $i = 0;
        $variationsDatas = array();

        if ($variations) {
            $idsVar = implode(',', $variations);

            $variationsDatas = $wpdb->get_results(
                "SELECT * FROM {$wpdb->prefix}exercise WHERE id IN ({$idsVar}) AND active = 1 LIMIT 10",
                ARRAY_A
            );
        }

        if ($variationsDatas):
            ?>
            <section class="exc-variations">
                <div class="container">
                    <div class="exc-container bd-bot">
                        <div class="muscle-title">
                            <h2>Variations</h2>
                            <p class="">Exercises that target the same primary muscle groups and require the same equipment.</p>
                        </div>
                        <div class="muscle-list variations-list">
                            <?php
                            if ($i <= 10):
                                foreach ($variationsDatas as $variationsData): ?>
                                    <div class="equipment-item muscle-item">
                                        <div class="muscle-img">
                                            <img src="<?= $variationsData['image_male'] ? $variationsData['image_male'] : $variationsData['image_female'] ?>"
                                                alt="">
                                        </div>
                                        <?php if ($variationsData['slug']): ?>
                                            <a href="<?= home_url('/exercise/' . $variationsData['slug']); ?>">
                                                <p class="has-medium-font-size"><?= $variationsData['name'] ?></p>
                                            </a>
                                        <?php else: ?>
                                            <p class="has-medium-font-size"><?= $variationsData['name'] ?>
                                            <?php endif; ?>
                                    </div>
                                    <?php
                                    $i++;
                                endforeach;
                            endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php

        $alternative = array_diff($primaryIds, $equipmentIds);

        $i = 0;
        $alternativeDatas = array();

        if ($alternative) {
            $idsVar = implode(',', $alternative);

            $alternativeDatas = $wpdb->get_results(
                "SELECT * FROM {$wpdb->prefix}exercise WHERE id IN ({$idsVar}) AND active = 1 LIMIT 10",
                ARRAY_A
            );
        }

        if ($alternativeDatas):
            ?>
            <section class="exc-alternatives">
                <div class="container">
                    <div class="exc-container bd-bot">
                        <div class="muscle-title">
                            <h2>Alternatives</h2>
                            <p class="">Exercises that target the same primary muscle groups and require the different
                                equipment.</p>
                        </div>
                        <div class="muscle-list alternatives-list">
                            <?php
                            if ($i <= 10):
                                foreach ($alternativeDatas as $alternativeData): ?>
                                    <div class="equipment-item muscle-item">
                                        <div class="muscle-img">
                                            <img src="<?= $alternativeData['image_male'] ? $alternativeData['image_male'] : $alternativeData['image_male'] ?>"
                                                alt="">
                                        </div>
                                        <?php if ($alternativeData['slug']): ?>
                                            <a href="<?= home_url('/exercise/' . $alternativeData['slug']); ?>">
                                                <p class="has-medium-font-size"><?= $alternativeData['name'] ?></p>
                                            </a>
                                        <?php else: ?>
                                            <p class="has-medium-font-size"><?= $alternativeData['name'] ?>
                                            <?php endif; ?>
                                    </div>
                                    <?php
                                    $i++;
                                endforeach;
                            endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>
    <section class="exc-section-content single-main">
        <div class="container">
            <div class="exc-container bd-bot">
                <?php the_content(); ?>
                <?php
                if (get_field('enable_source', 'option') == true) {
                    ?>
                    <div class="sg-resources mr-bottom-20 pd-main">
                        <h3>Resources</h3>
                        <div class="intro">
                            <?= get_field('source_intro', 'option'); ?>
                        </div>
                        <?php $source_content = get_field('source_content', $postid);
                        if ($source_content)
                            echo $source_content;
                        ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <?php
    $author_id = get_post_field('post_author', $postid);

    $author_name = get_the_author_meta('nickname', $author_id);
    $author_url = get_author_posts_url($author_id);

    $avt = '';

    if (get_field('new_avata', 'user_' . $author_id)) {
        $avt = get_field('new_avata', 'user_' . $author_id);
    } elseif (get_field('avata', 'user_' . $author_id)) {
        $avt = get_field('avata', 'user_' . $author_id);
    }

    $user_description = '';

    if (get_field('new_story', 'user_' . $author_id)) {
        $user_description = get_field('new_story', 'user_' . $author_id);
    } elseif (get_field('story', 'user_' . $author_id)) {
        $user_description = get_field('story', 'user_' . $author_id);
    }

    $userPosition = get_field('position', 'user_' . $author_id);

    if (get_field('new_position', 'user_' . $author_id)) {
        $userPosition = get_field('new_position', 'user_' . $author_id);
    } elseif (get_field('position', 'user_' . $author_id)) {
        $userPosition = get_field('position', 'user_' . $author_id);
    }
    ?>
    <div class="single-main exc-author">
        <aside class="single-sidebar ">
            <div class="container">
                <div class="author-about exc-container">
                    <h3>About the Author</h3>
                    <div class="author-write">
                        <div class="author-link">
                            <?php
                            if ($avt) {
                                ?>
                                <a target="_blank" href="<?php echo $author_url; ?>"><img src="<?php echo $avt; ?>"
                                        alt=""></a>
                            <?php } else { ?>
                                <a target="_blank" href="<?php echo $author_url; ?>"><img
                                        src="<?php echo get_field('avatar_default', 'option'); ?>" alt="">
                                <?php } ?>
                                <p class="has-medium-font-size"><a target="_blank"
                                        style="color: var(--pri-color-2) !important;"
                                        href="<?php echo $author_url; ?>"><?php the_author(); ?>
                                    </a>
                                    <?php if ($userPosition): ?>
                                        <span>
                                            <?= $userPosition; ?>
                                            </sp>
                                        <?php endif; ?>
                                </p>
                        </div>
                        <?php if ($user_description) { ?>
                            <div class="author-info">
                                <p><?php echo wp_trim_words($user_description, 50, '') . '.. '; ?><a
                                        href="<?php echo $author_url; ?>"> See more</a></p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </aside>
    </div>
    <?php if (comments_open()): ?>
        <div class="container">
            <?php comments_template(); ?>
        </div>
    <?php endif; ?>
</main>
<script>
    jQuery(function ($) {
        $('.wp-caption-small').parent().addClass('wp-caption-small');
    });

    var player;

    function onYouTubeIframeAPIReady() {
        document.getElementById('exc-container').innerHTML = '<iframe id="player" marginwidth="0" marginheight="0" align="top" scrolling="No" frameborder="0" hspace="0" vspace="0" src="https://www.youtube.com/embed/<?= $videoId ?>?rel=0&amp;fs=0&amp;autoplay=1&mute=1&loop=1&color=white&controls=0&modestbranding=1&playsinline=1&enablejsapi=1&playlist=<?= $videoId ?>" frameBorder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" style="position:absolute;top:0;left:0;width:100%;height:100%;border:none"></iframe>';
        player = new YT.Player('player', {
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });
    }

    function onPlayerReady(event) {
        event.target.playVideo();
    }
    function onPlayerStateChange(event) {
        var YTP = event.target;
        if (event.data === 1) {
            var remains = YTP.getDuration() - YTP.getCurrentTime();
            if (this.rewindTO)
                clearTimeout(this.rewindTO);
            this.rewindTO = setTimeout(function () {
                YTP.seekTo(0);
            }, (remains - 0.1) * 1000);
        }
    }
</script>
<?php get_footer(); ?>