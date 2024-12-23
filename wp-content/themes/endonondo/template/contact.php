<?php
/* Template Name: Contact */
$pageid = get_the_ID();
get_header();
the_post();
?>
<main id="content">
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
		<div class="contact-main">
			<h1 class=""><?php the_title(); ?></h1>
			<div class="contact-box flex flex-two">
				<div class="contact-left item-flex">
					<?php the_content(); ?>
					<p class="has-small-font-size"><?php echo get_field('form_title', $pageid); ?></p>
					<div class="contact-form">
						<?php echo do_shortcode(get_field('form_contact', $pageid)); ?>
					</div>
				</div>
				<div class="contact-right item-flex">
					<div class="contact-right-box">
						<h3><?php echo get_field('office_title', $pageid); ?></h3>
						<address class="ct-info mr-bottom-20">
							<?php echo get_field('office__description', $pageid); ?>
						</address>
						<h4 class="mr-bottom-20"><?php echo get_field('follow_title', $pageid); ?></h4>
						<div class="follow-list list-flex">
							<?php $follow_social = get_field('follow_social', 'option');
							if ($follow_social) {
								foreach ($follow_social as $follow) {
									?>
									<a href="<?php echo $follow['link']; ?>" target="_blank"><img src="<?php echo $follow['icon']['url']; ?>" alt="<?php echo $follow['icon']['alt']; ?>" /><?php echo $follow['title']; ?> </a>
									</a>
								<?php }
							} ?>
						</div>
					</div>
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