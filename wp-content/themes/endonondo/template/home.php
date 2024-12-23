<?php
/* Template Name: Home*/
$pageid = get_the_ID();
get_header();
the_post();
?>
<main id="content">
	<section class="home-top enfit-post color-white pd-main">
		<div class="container">
			<div class="list-flex">
				<div class="top-big list-flex">
					<?php
					$enfit = get_field('enfit_content', $pageid);
					$intro = get_field('intro_app', 'option');
					$explore = !empty($intro[0]['explore']) ? $intro[0]['explore'] : '';
					$store = !empty($intro[0]['store']) ? $intro[0]['store'] : '';
					$enfitLogo = get_field('enfit_logo', 'option');
					if (!empty($enfit)) {
						$enfit = $enfit[0];
						?>
						<div class="info">
							<p class="has-x-large-font-size text-special clamp-2 mr-bottom-20 pri-color-3"><a
									class="pri-color-3" href="<?= $explore ?>"><?= $enfit['title'] ?></a></p>
							<p class="sec-color-2"><?php echo wp_trim_words($enfit['description'], 28); ?></p>
							<div class="enfit-action flex">
								<?php if ($explore): ?>
									<a href="<?= $explore ?>" id="">Explore Now</a>
								<?php endif; ?>
								<?php if ($store): ?>
									<a target="_blank" href="<?= $store ?>" class="home-store">
										<img src="<?= get_template_directory_uri() . '/assets/images/enfit/store-home.svg' ?>"
											alt="">
									</a>
								<?php endif; ?>
							</div>
						</div>
						<div class="featured image-fit hover-scale">
							<?php $image_featured = $enfit['image']; ?>
							<a href="<?= $explore ?>">
								<?php if ($image_featured): ?>
									<img src="<?= $image_featured['url'] ?>" alt="<?= $image_featured['alt'] ?>">
								<?php else: ?>
									<img src="<?php echo get_field('fimg_default', 'option'); ?>" alt="">
								<?php endif; ?>
							</a>
							<?php if ($enfitLogo): ?>
								<div class="enfit-logo">
									<img src="<?= $enfitLogo ?>" alt="">
								</div>
							<?php endif; ?>
						</div>
						<?php
					} else {
						$args = array(
							'posts_per_page' => 1,
							'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide', 'exercise', 'tool_post'),
						);
						$the_query = new WP_Query($args);
						while ($the_query->have_posts()):
							$the_query->the_post();
							$post_author_id = get_post_field('post_author', $post->ID);
							$post_display_name = get_the_author_meta('nickname', $post_author_id);
							$post_author_url = get_author_posts_url($post_author_id);
							?>
							<div class="info">
								<p class="has-x-large-font-size text-special clamp-2 mr-bottom-20 pri-color-3"><a
										class="pri-color-3" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
								<p class="sec-color-2"><?php echo wp_trim_words(get_the_excerpt($post->ID), 28); ?></p>
							</div>
							<div class="featured image-fit hover-scale">
								<?php $image_featured = wp_get_attachment_url(get_post_thumbnail_id($post->ID)); ?>
								<a href="<?php the_permalink(); ?>">
									<?php if ($image_featured): ?>
										<?php the_post_thumbnail(); ?>
									<?php else: ?>
										<img src="<?php echo get_field('fimg_default', 'option'); ?>" alt="">
									<?php endif; ?>
								</a>
							</div>
							<?php
						endwhile;
						wp_reset_query();
					}
					?>
				</div>
				<div class="news-right">
					<p class="sec-color-3 has-large-font-size mr-bottom-20">News</p>
					<div class="top-list">
						<?php
						$args = array(
							'posts_per_page' => 4,
							'offset' => 1,
							'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide', 'exercise', 'tool_post'),
						);
						$the_query = new WP_Query($args);
						while ($the_query->have_posts()):
							$the_query->the_post();
							$post_author_id = get_post_field('post_author', $post->ID);
							$post_display_name = get_the_author_meta('nickname', $post_author_id);
							$post_author_url = get_author_posts_url($post_author_id);
							?>
							<div class="top-it mr-bottom-20 position-relative">
								<p class="has-medium-font-size mr-bottom-16 text-special clamp-2 ellipsis pri-color-3"><a
										class="pri-color-3" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
								<p class="author"><a class="sec-color-3" href="<?php echo $post_author_url; ?>">By
										<?php echo $post_display_name; ?></a></p>
								<a href="<?php the_permalink(); ?>" class="news-link author position-absolute">
									<img src="<?php echo get_template_directory_uri(); ?>/assets/images/right.svg" alt="">
								</a>
							</div>
							<?php
						endwhile;
						wp_reset_query();
						?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
	$brand_list = get_field('feature_on', $pageid);
	if ($brand_list) {
		?>
		<section class="home-feature-on">
			<div class="container">
				<h2 class="pri-color-3 text-center">Featured On</h2>
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
	<section class="home-feature pd-main">
		<div class="list-flex feature-bg">
			<div class="feature-social">
				<?php
				$social = get_field('social', 'option');
				if ($social) {
					foreach ($social as $social) {
						?>
						<a target="_blank" href="<?php echo $social['link']; ?>"><img alt="<?= $social['icon']['alt']; ?>"
								src="<?= $social['icon']['url']; ?>" /></a>
					<?php }
				} ?>
			</div>
			<div class="feature-collections bg-white color-black">
				<h2 class="pri-color-2 mr-bottom-40">Feature Collections</h2>
				<div class="feature-slider swiper">
					<div class="swiper-wrapper">
						<?php
						$args = array(
							'posts_per_page' => 6,
							'offset' => 5,
							'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide', 'exercise', 'tool_post'),
						);
						$the_query = new WP_Query($args);
						while ($the_query->have_posts()):
							$the_query->the_post();
							$post_author_id = get_post_field('post_author', $post->ID);
							$post_display_name = get_the_author_meta('nickname', $post_author_id);
							$post_author_url = get_author_posts_url($post_author_id);
							?>
							<div class="it swiper-slide">
								<div class="feature-box">
									<div class="image image-fit hover-scale">
										<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
									</div>
									<div class="info">
										<?php $category = get_the_category($post->ID); ?>
										<?php if (!empty($category) && count($category) > 0): ?>
											<div class="tag">
												<?php
												foreach ($category as $cat) { ?>
													<span><a
															href="<?php echo get_term_link($cat->term_id); ?>"><?php echo $cat->name; ?></a></span>
												<?php } ?>
											</div>
										<?php endif; ?>
										<p class="has-medium-font-size text-special clamp-2 ellipsis"><a class="pri-color-2"
												href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></p>
										<p class="has-small-font-size"><a class="sec-color-3"
												href="<?php echo $post_author_url; ?>">By
												<?php echo $post_display_name; ?></a></p>
									</div>
								</div>
							</div>
							<?php
						endwhile;
						wp_reset_query();
						?>
					</div>
					<div class="swiper-pagination"></div>
				</div>
			</div>
		</div>
	</section>
	<!-- <section class="home-stories">
		<div class="container">
			<h2 class="ed-title text-uppercase"><?php echo get_field('stories_title', $pageid); ?></h2>
			<div class="stories-list list-flex">
				<?php $stories_list = get_field('stories_list', $pageid);
				if ($stories_list) {
					foreach ($stories_list as $stories) {
						?>
				<div class="stories-it">
					<img src="<?php echo $stories['image']; ?>" alt="" class="featured">
					<img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon-three.svg" alt="" class="icon">
					<div class="stories-info">
						<div class="line list-flex">
						</div>
						<h3><a href="<?php echo $stories['link']; ?>"><?php echo $stories['title']; ?></a></h3>
						<div class="list-flex flex-middle flex-center">
							<p><?php echo $stories['date']; ?></p>
							<a class="stories-link" href="<?php echo $stories['link']; ?>"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon-e.svg" alt=""></a>
						</div>
					</div>
				</div>
				<?php }
				} ?>
			</div>
		</div>
	</section> -->
	<section class="home-lastest bg-section pd-main">
		<div class="container">
			<h2 class="pri-color-3">Latest news</h2>
			<div class="lastest-list">
				<?php
				$args = array(
					'posts_per_page' => 5,
					'offset' => 11,
					'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide'),
				);
				$the_query = new WP_Query($args);
				while ($the_query->have_posts()):
					$the_query->the_post();
					$post_author_id = get_post_field('post_author', $post->ID);
					$post_display_name = get_the_author_meta('nickname', $post_author_id);
					$post_author_url = get_author_posts_url($post_author_id);
					?>
					<div class="lastest-it">
						<div class="lastest-box list-flex">
							<div class="featured image-fit hover-scale">
								<?php $image_featured = wp_get_attachment_url(get_post_thumbnail_id($post->ID)); ?>
								<a href="<?php the_permalink(); ?>">
									<?php if ($image_featured): ?>
										<img src="<?php echo $image_featured; ?>" alt="">
									<?php else: ?>
										<img src="<?php echo get_field('fimg_default', 'option'); ?>" alt="">
									<?php endif; ?>
								</a>
							</div>
							<div class="info">
								<?php $category = get_the_category($post->ID); ?>
								<?php if (!empty($category) && count($category) > 0): ?>
									<div class="tag mr-bottom-16">
										<?php
										foreach ($category as $cat) { ?>
											<span><a
													href="<?php echo get_term_link($cat->term_id); ?>"><?php echo $cat->name; ?></a></span>
										<?php } ?>
									</div>
								<?php endif; ?>
								<p class="has-medium-font-size text-special clamp-2 ellipsis pri-color-3"><a
										class="pri-color-3" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
								<p class="has-small-font-size author"><a class="sec-color-3"
										href="<?php echo $post_author_url; ?>">By <?php echo $post_display_name; ?></a></p>
							</div>
						</div>
					</div>
					<?php
				endwhile;
				wp_reset_query();
				?>
				<div class="clear"></div>
			</div>
		</div>
	</section>
	<!--<section class="home-video position-relative">
		<div class="video-featured image-fit">
			<img src="<?php echo get_field('video_background', $pageid); ?>" alt="">
		</div>
		<a class="video-btn position-absolute" href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/play.svg" alt=""></a>
		<section class="video-source position-absolute">	
			<?php echo get_field('video_source', $pageid); ?>
		</section>	
	</section>-->
	<!-- <section class="soon bg-section">
		<div class="container">
			<div class="soon-content pd-main flex">
				<div class="soon-img">
					<img src="<?php echo get_template_directory_uri(); ?>/assets/images/home/soon-img.svg" alt="">
				</div>
				<div class="soon-item">
					<p class="has-x-large-font-size pri-color-2">We are launching soon</p>
					<p class="special-text">We've have helped <span
							class="has-x-large-font-size pri-color-2">1.542,335</span> people
						get in shape</p>
					<a class="pri-color-3 soon-btn">GET ME THE LIFETIME DEAL</a>
					<div class="social flex">
						<p class="has-small-font-size pri-color-2" style="margin-bottom: 0">Follow us: </p>
						<?php
						$socials = get_field('follow_social', 'option');
						if ($socials) {
							foreach ($socials as $social) {
								?>
								<a target="_blank" href="<?php echo $social['link']; ?>"><img
										alt="<?= $social['icon']['alt']; ?>" src="<?= $social['icon']['url']; ?>" /></a>
							<?php }
						} ?>
					</div>
				</div>
			</div>
		</div>
	</section> -->
	<section class="home-choise bg-white color-black pd-main">
		<div class="container">
			<h2 class="">Recommended Posts</h2>
			<div class="news-list grid grid-feature">
				<?php
				$args = array(
					'posts_per_page' => 12,
					'offset' => 17,
					'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide', 'exercise', 'tool_post'),
				);
				$the_query = new WP_Query($args);
				while ($the_query->have_posts()):
					$the_query->the_post();
					$post_author_id = get_post_field('post_author', $post->ID);
					$post_display_name = get_the_author_meta('nickname', $post_author_id);
					$post_author_url = get_author_posts_url($post_author_id);
					?>
					<div class="news-it">
						<div class="news-box">
							<div class="featured image-fit hover-scale">
								<?php $image_featured = wp_get_attachment_url(get_post_thumbnail_id($post->ID)); ?>
								<a href="<?php the_permalink(); ?>">
									<?php if ($image_featured): ?>
										<img src="<?php echo $image_featured; ?>" alt="">
									<?php else: ?>
										<img src="<?php echo get_field('fimg_default', 'option'); ?>" alt="">
									<?php endif; ?>
								</a>
							</div>
							<div class="info">
								<?php $category = get_the_category($post->ID); ?>
								<?php if (!empty($category) && count($category) > 0): ?>
									<div class="tag mr-bottom-16">
										<?php
										foreach ($category as $cat) { ?>
											<span><a
													href="<?php echo get_term_link($cat->term_id); ?>"><?php echo $cat->name; ?></a></span>
										<?php } ?>
									</div>
								<?php endif; ?>
								<p class="has-medium-font-size text-special clamp-2"><a class="pri-color-2"
										href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></p>
								<p class="has-small-font-size"><a class="sec-color-3"
										href="<?php echo $post_author_url; ?>">By <?php echo $post_display_name; ?></a></p>
							</div>
						</div>
					</div>
					<?php
				endwhile;
				wp_reset_query();
				?>
			</div>
		</div>
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
<script>
	jQuery(function ($) {
		if ($('.feature-slider').length)
			var swiper = new Swiper(".feature-slider", {
				slidesPerView: 1.3,
				spaceBetween: 16,
				autoplay: {
					delay: 5000,
				},
				pagination: {
					el: ".swiper-pagination",
					type: "progressbar",
				},
				breakpoints: {
					768: {
						slidesPerView: 1.9,
						spaceBetween: 16
					},
					991: {
						slidesPerView: 2.1,
						spaceBetween: 16
					},
					1500: {
						slidesPerView: 3.5,
						spaceBetween: 16
					}
				}
			});

		$('.video-btn').click(function () {
			$(this).parent().find('.video-source').fadeIn();
			$(this).parent().find('.video-source iframe')[0].src += "?autoplay=1";
			return false;
		});
		if ($(window).width() < 767) {
			$('.stories-list').slick({
				dots: false,
				infinite: false,
				slidesToShow: 1.1,
				arrows: false,
			});
		};
	});
</script>
<script type="text/javascript"
	src="http://classic.avantlink.com/affiliate_app_confirm.php?mode=js&authResponse=5162225e4379593155ea96202c49b34242f4284e"></script>
<?php get_footer(); ?>