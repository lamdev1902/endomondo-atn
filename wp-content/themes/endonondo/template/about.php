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
</main>
<?php get_footer(); ?>