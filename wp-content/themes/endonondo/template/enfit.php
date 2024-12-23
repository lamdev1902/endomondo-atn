<?php
/* Template Name: Enfit */
$pageid = get_the_ID();
get_header();
the_post();

$app = get_field('intro_app', 'option');

?>
<main id="content" class="coming">
    <section class="first__coming pd-secondary">
        <div class="first__coming--content">
            <div class="container special-width text-center">
                <h2 class="tag-coming flex">
                    <img src="<?= get_template_directory_uri() . '/assets/images/enfit/workout.svg' ?>" alt="">
                    <strong>#1 Fitness and Sport App!</strong>
                </h2>
                <h1>No Gym? No Problem. Enfit Has You Covered!</h1>
            </div>
            <div class="first__coming--img">
                <div class="container">
                    <div class="store">
                        <?php if (!empty($app[0]['store'])): ?>
                            <a target="_blank" href="<?= $app[0]['store'] ?>">
                                <img src="<?= get_template_directory_uri() . '/assets/images/enfit/store.svg' ?>" alt="">
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="first-img mb">
                        <div class="main">
                            <img loading="eager"
                                src="<?= get_template_directory_uri() . '/assets/images/enfit/first-mb.png' ?>" alt="">
                        </div>
                        <div class="chart">
                            <img src="<?= get_template_directory_uri() . '/assets/images/enfit/first-chart.svg' ?>"
                                alt="">
                        </div>
                        <div class="training">
                            <img src="<?= get_template_directory_uri() . '/assets/images/enfit/first-training.svg' ?>"
                                alt="">
                        </div>
                        <div class="workout">
                            <img src="<?= get_template_directory_uri() . '/assets/images/enfit/first-wk.svg' ?>" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
    $brand_list = get_field('feature_on', 'option');
    if ($brand_list) {
        ?>
        <section class="home-feature-on">
            <div class="container">
                <h2 class="text-center">Featured On</h2>
                <ul>
                    <?php foreach ($brand_list as $hl) {
                        $logo = $hl['logo'];
                        ?>
                        <li><img src="<?php echo $logo['url']; ?>" alt="<?php echo $logo['alt']; ?>"></li>
                    <?php } ?>
                </ul>
            </div>
        </section>
    <?php } ?>
    <section class="second__coming pd-secondary">
        <div class="container flex">
            <div class="second__coming--left">
                <div class="content">
                    <h2 class="tag-coming">Our Features</h2>
                    <p class="title">Every workout, every meal, every goal—designed for your success.</p>
                    <p>Key features that propel your fitness journey.</p>
                </div>
                <div class="second__coming--item grid">
                    <div class="it">
                        <img src="<?= get_template_directory_uri() . '/assets/images/enfit/it-1.svg' ?>" alt="">
                        <h3 class="">Your Personal Fitness Coach</h3>
                        <p class="has-small-font-size">Get tailored workout plans designed to fit your goals, schedule,
                            and home setup.</p>
                    </div>
                    <div class="it">
                        <img src="<?= get_template_directory_uri() . '/assets/images/enfit/it-2.svg' ?>" alt="">
                        <h3 class="">Nutrition Made Simple</h3>
                        <p class="has-small-font-size">Discover various recipes designed to meet your dietary needs and
                            support your fitness journey.</p>
                    </div>
                    <div class="it">
                        <img src="<?= get_template_directory_uri() . '/assets/images/enfit/it-3.svg' ?>" alt="">
                        <h3 class="">High-Quality Exercise Guides</h3>
                        <p class="has-small-font-size">Access expertly performed exercise videos that guide you
                            step-by-step to perfect your form.</p>
                    </div>
                    <div class="it">
                        <img src="<?= get_template_directory_uri() . '/assets/images/enfit/it-4.svg' ?>" alt="">
                        <h3 class="">Track Your Transformation</h3>
                        <p class="has-small-font-size">Monitor your workouts, calorie burn, and achievements with
                            detailed, real-time insights.</p>
                    </div>
                </div>
            </div>
            <?php $comingSlick = get_field('slider_image', $pageid); ?>
            <?php if (!empty($comingSlick)): ?>
                <div class="second__coming-right" id="rightSlick">
                    <?php foreach ($comingSlick as $it): ?>
                        <img src="<?= $it['img']['url'] ?>" alt="<?= $it['img']['alt'] ?>">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <section class="third__coming pd-secondary">
        <div class="container">
            <div class="third__coming--top text-center">
                <h2 class="tag-coming">HOW TO START</h2>
                <p class="title">Start Your Fitness Journey In Just Minutes</p>
                <p>Download, set up, and start tracking your progress effortlessly.</p>
            </div>
            <div class="third__coming--bottom flex">
                <div class="it">
                    <div class="third_download">
                        <img class="" src="<?= get_template_directory_uri() . '/assets/images/enfit/third-1.jpg' ?>"
                            alt="Download App">
                    </div>
                    <h3>Download App</h3>
                    <p class="has-small-font-size">Download the app easily from the App Store and get started.</p>
                </div>
                <div class="it">
                    <div class="third_create">
                        <img class="" src="<?= get_template_directory_uri() . '/assets/images/enfit/third-2.png' ?>"
                            alt="Create and Personalized">
                    </div>
                    <h3>Create and Personalize</h3>
                    <p class="has-small-font-size">Set up your account and complete a quick survey to receive a
                        personalized workout plan tailored to your needs.</p>
                </div>
                <div class="it">
                    <div>
                        <img class=""
                            src="<?= get_template_directory_uri() . '/assets/images/enfit/third-3.jpg' ?>"
                            alt="Start Your Workout!">
                    </div>
                    <h3>Start Your Workout!</h3>
                    <p class="has-small-font-size">Follow expert-led exercises and begin your fitness journey today.</p>
                </div>
                <div class="it">
                    <div class="">
                        <img class=""
                            src="<?= get_template_directory_uri() . '/assets/images/enfit/third-4.jpg' ?>"
                            alt="Analyze and Repeat">
                    </div>
                    <h3>Track and Achieve</h3>
                    <p class="has-small-font-size">Monitor your progress, celebrate milestones, and stay motivated</p>
                </div>
            </div>
        </div>
    </section>
    <?php
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
        ?>
        <section class="fourth__coming">
            <div class="flex">
                <div class="four__coming--left">
                    <img src="<?= get_template_directory_uri() . '/assets/images/enfit/fouth-coming.svg' ?>" alt="">
                </div>
                <div class="four__coming--right">
                    <div class="content app-content">
                        <?php if (!empty($app['title'])): ?>
                            <p class="has-x-large-font-size"><?= $app['title'] ?></p>
                        <?php endif; ?>
                        <?php if (!empty($app['description'])): ?>
                            <p><?= $app['description'] ?></p>
                        <?php endif; ?>
                        <?php if (!empty($app['store'])): ?>
                            <div class="mr-bottom-20">
                                <a target="_blank" class="mr-bottom-20" href="<?= $app['store'] ?>">
                                    <img src="<?= get_template_directory_uri() . '/assets/images/enfit/store.svg' ?>" alt="">
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($app['discount'])): ?>
                            <p class="discount"><?= $app['discount'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <section class="fifth__coming pd-secondary">
        <div class="fifth__coming--top text-center">
            <div class="container">
                <h2 class="tag-coming">App Preview</h2>
                <p class="title">See Your Fitness Journey in Action</p>
                <p>Explore our app's intuitive interface and powerful features. These screenshots highlight how Enfit
                    simplifies fitness and makes every step of your journey enjoyable.</p>
            </div>
        </div>
        <div class="fifth__coming--bottom">
            <?php $previewSlick = get_field('slider_preview', $pageid); ?>
            <?php if (!empty($previewSlick)): ?>
                <div class="fifth__coming--bottom--img" id="previewSlick">
                    <?php foreach ($previewSlick as $it): ?>
                        <img src="<?= $it['img']['url'] ?>" alt="<?= $it['img']['alt'] ?>">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="single-main pd-secondary">
        <div class="container">
            <div class="sg-editor">
                <h2 class="tag-coming">FAQs</h2>
                <?php the_content(); ?>
            </div>
        </div>
    </div>
    <?php
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