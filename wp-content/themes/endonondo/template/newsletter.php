<?php 
/* Template Name: Newsletter */
get_header(); ?>
<main id="content">
	<div class="news-letter">
		<div class="news-container">
			<div class="container">
				<div class="klaviyo-form-VfHycs"></div>
				<div class="news-image">
					<img src="<?=  get_template_directory_uri(); ?>/assets/images/news-img.svg?>" alt="newsletter-icon">
				</div>
			</div>
		</div>
	</div>
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
