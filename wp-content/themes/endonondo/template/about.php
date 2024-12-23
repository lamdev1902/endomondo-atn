<?php
/* Template Name: About*/
$pageid = get_the_ID();
get_header();
the_post();
?>
<main id="content">
	<div class="page-top-white">
		<div class="container">
			<?php
			if (function_exists('yoast_breadcrumb')) {
				yoast_breadcrumb('<div id="breadcrumbs" class="breacrump">', '</div>');
			}
			?>
		</div>
	</div>
	<div class="container">
		<article class="about-main special-width">
			<div class="container-small">
				<h1 class="text-center"><?php the_title(); ?></h1>
				<div class="about-custom">
					<?php the_content(); ?>
				</div>

				<?php $team = get_field('team', $pageid);
				if ($team) {
					foreach ($team as $team) {
						?>
						<section class="about-author">
							<h2 class="text-center"><?php echo $team['title']; ?></h2>
							<div class="grid grid-item">
								<?php $team_list = $team['select_team'];
								if ($team_list) {
									foreach ($team_list as $team_it) {
										$userid = $team_it['ID'];
										$post_author_url = get_author_posts_url($userid);

										?>
										<div class="it">
											<div class="featured image-fit">
												<a href="<?php echo $post_author_url; ?>">
													<?php
													$avata = '';

													if (get_field('new_avata', 'user_' . $userid)) {
														$avata = get_field('new_avata', 'user_' . $userid);
													} elseif (get_field('avata', 'user_' . $userid)) {
														$avata = get_field('avata', 'user_' . $userid);
													}
													if ($avata) {
														?>
														<img src="<?php echo $avata; ?>" alt="">
													<?php } else { ?>
														<img src="<?php echo get_field('avatar_default', 'option'); ?>" alt="">
													<?php } ?>
												</a>
											</div>
											<div class="info">
												<p class="has-medium-font-size"><a target="_blank" class="pri-color-2"
														href="<?php echo $post_author_url; ?>"><?php echo $team_it['display_name']; ?></a>
												</p>
												<p class="sec-color-3">
													<?= get_field('new_position', 'user_' . $userid) ?? get_field('position', 'user_' . $userid) ?>
												</p>
												<!-- <div class="social">
									<?php $social = get_field('social', 'user_' . $userid);
									if ($social) {
										foreach ($social as $social) {
											?>
									<a target="_blank" href="<?php echo $social['link']; ?>"><i class="<?php echo $social['icon']; ?>"></i></a>
									<?php }
									} ?>
								</div> -->
											</div>
										</div>
									<?php }
								} ?>
							</div>
						</section>
					<?php }
				} ?>
			</div>
		</article>
	</div>
	<?php
	$app = get_field('intro_app', 'option');
	if (!empty($app[0])):
		$app = $app[0];
		$explore = !empty($app['explore']) ? $app['explore'] : '';
		$store = !empty($app['store']) ? $app['store'] : '';

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