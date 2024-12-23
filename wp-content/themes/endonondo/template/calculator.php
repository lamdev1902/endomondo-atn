<?php
/* Template Name: Calculator Page */

$pageid = get_the_ID();
get_header();
the_post();
?>
<main id="content">
    <?php
    $heroCalculator = get_field('hero_description', $pageid);
    ?>
    <section class="hero-calculator-section mb">
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
                <h1><?= the_title() ?></h1>
                <p><?= $heroCalculator ?></p>
                <?php
                $socials = get_field('follow_social', 'option');
                if ($socials):
                    ?>
                    <div class="social flex mr-bottom-20">
                        <p class="has-small-font-size" style="margin-bottom: 0">Follow us: </p>
                        <?php foreach ($socials as $social): ?>
                            <a target="_blank" href="<?php echo $social['link']; ?>"><img alt="<?= $social['icon']['alt']; ?>"
                                    src="<?= $social['icon']['url']; ?>" /></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        $calculator = get_field('calculator', $pageid);
        if (!empty($calculator)):
            ?>
            <div class="container cal">
                <div class="cal-list grid grid-feature">
                    <?php foreach ($calculator as $it): ?>
                        <div class="cal-it">
                            <?php if ($it['icon']): ?>
                                <div class="cal-icon">
                                    <img src="<?= $it['icon'] ?>" alt="">
                                </div>
                            <?php endif; ?>

                            <?php if ($it['calculator-post']): ?>
                                <p class="has-medium-font-size"><a target="_blank"
                                        href="<?= get_permalink($it['calculator-post']->ID) ?>" class="pri-color-2">
                                        <?= $it['title'] ?>
                                    </a></p>
                                <?php
                                $author_id = get_post_field('post_author', $it['calculator-post']->ID);
                                $author_name = get_the_author_meta('nickname', $author_id);
                                $author_url = get_author_posts_url($author_id);
                                if ($author_name):
                                    ?>
                                    <p class="has-small-font-size"><a target="_blank" href="<?= $author_url ?>" class="sec-color-3">
                                            <?= $author_name ?>
                                        </a></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <?php
    $app = get_field('intro_app', 'option');
    if (!empty($app[0])):
        $app = $app[0];
        if (!empty($app['title'])) {
            if (strpos($app['title'], 'Enfit') !== false) {
                $app['title'] = str_replace('Enfit', '<strong>Enfit</strong>', $app['title']);
            }
        }

        if (!empty($app['discount'])) {
            $app['discount'] = preg_replace('/(\d+%)/', '<strong>$1</strong>', $app['discount']);
        }

        $explore = !empty($app['explore']) ? $app['explore'] : '';
		$store = !empty($app['store']) ? $app['store'] : '';
        ?>

        <section class="app-section mb">
            <div class="container">
                <div class="content app-content">
                    <?php if (!empty($app['title'])): ?>
                        <p class="has-x-large-font-size"><?= $app['title'] ?></p>
                    <?php endif; ?>
                    <?php if (!empty($app['description'])): ?>
                        <p><?= $app['description'] ?></p>
                    <?php endif; ?>
                    <div class="enfit-action mr-bottom-20 flex">
						<?php if ($explore): ?>
							<a href="<?= $explore ?>" id="">Explore Now</a>
						<?php endif; ?>
						<?php if ($store): ?>
							<a target="_blank" href="<?= $store ?>" class="home-store">
								<img src="<?= get_template_directory_uri() . '/assets/images/enfit/store.svg' ?>" alt="">
							</a>
						<?php endif; ?>
					</div>
                    <?php if (!empty($app['discount'])): ?>
                        <p class="discount"><?= $app['discount'] ?></p>
                    <?php endif; ?>
                    <?php
                    $socials = get_field('follow_social', 'option');
                    if ($socials):
                        ?>
                        <div class="social flex">
                            <p class="has-small-font-size pri-color-2" style="margin-bottom: 0">Follow us: </p>
                            <?php foreach ($socials as $social): ?>
                                <a target="_blank" href="<?php echo $social['link']; ?>"><img alt="<?= $social['icon']['alt']; ?>"
                                        src="<?= $social['icon']['url']; ?>" /></a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>
<?php get_footer(); ?>