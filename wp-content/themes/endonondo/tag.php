<?php
get_header();
the_post();
$tag_id = get_queried_object_id();
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

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
	<div class="blog-main category-main">
		<div class="blog-top category-top position-relative">
			<div class="top-box list-flex">
				<div class="info">
					<h1 class="ed-title text-uppercase color-white"><?php single_tag_title(); ?></h1>
				</div>
				<div class="featured list-flex">
					<?php
					$banner = get_field('tag_banner', 'post_tag_' . $tag_id);
					if ($banner) {
						?>
						<img src="<?php echo $banner; ?>" alt="">
					<?php } else { ?>
						<img src="<?php echo get_field('bannercat_default', 'option'); ?>" alt="">
					<?php } ?>
				</div>
			</div>
		</div>
		<section class="home-lastest cate-section pd-main">
			<div class="container mr-bottom-20">
				<div class="lastest-list">
					<?php
					$args = array(
						'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide', 'exercise'),
						'posts_per_page' => 6,
						'paged' => $paged,
						'tag__in' => array($tag_id),
					);
					$the_query = new WP_Query($args);
					$i = 0;
					$notIn = array();
					while ($the_query->have_posts()):
						$the_query->the_post();
						if(!empty($post->ID)){
							array_push($notIn, $post->ID);
						}
						$post_author_id = get_post_field('post_author', $post->ID);
						$post_display_name = get_the_author_meta('nickname', $post_author_id);
						$post_author_url = get_author_posts_url($post_author_id);
						?>
						<div class="lastest-it">
							<div class="lastest-box list-flex position-relative">
								<?php if ($i == 0): ?>
									<div class="featured mr-bottom-20 image-fit hover-scale">
										<?php $image_featured = wp_get_attachment_url(get_post_thumbnail_id($post->ID)); ?>
										<a href="<?php the_permalink(); ?>">
											<?php if ($image_featured): ?>
												<img src="<?php echo $image_featured; ?>" alt="">
											<?php else: ?>
												<img src="<?php echo get_field('fimg_default', 'option'); ?>" alt="">
											<?php endif; ?>
										</a>
									</div>
								<?php endif; ?>
								<div class="info">
									<?php $category = get_the_category($post->ID); ?>
									<?php if ($i == 0 && !empty($category) && count($category) > 0): ?>
										<div class="tag mr-bottom-16">
											<?php
											foreach ($category as $cat) { ?>
												<span><a
														href="<?php echo get_term_link($cat->term_id); ?>"><?php echo $cat->name; ?></a></span>
											<?php } ?>
										</div>
									<?php endif; ?>
									<p class="has-medium-font-size text-special clamp-2 ellipsis pri-color-2"><a
											class="pri-color-2" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</p>
									<p class="has-small-font-size author"><a target="_blank" class="sec-color-4"
											href="<?php echo $post_author_url; ?>">By <?php echo $post_display_name; ?></a>
									</p>
									<?php if ($i != 0): ?>
										<a href="<?php the_permalink(); ?>" class="news-link author position-absolute">
											<img src="<?php echo get_template_directory_uri(); ?>/assets/images/right.svg"
												alt="">
										</a>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<?php
						$i++;
					endwhile;
					wp_reset_query();
					?>
					<div class="clear"></div>
				</div>
			</div>
			<div class="container">
				<h2>All post</h2>
				<div class="news-list tag-list grid grid-feature">
					<?php
					$args2 = array(
						'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide'),
						'posts_per_page' => 9,
						'tag__in' => array($tag_id),
						'post__not_in' => $notIn
					);
					$the_query2 = new WP_Query($args2);
					while ($the_query2->have_posts()):
						$the_query2->the_post();
						if(!empty($post->ID)){
							array_push($notIn, $post->ID);
						}
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
									<p class="has-small-font-size"><a target="_blank" class="sec-color-3"
											href="<?php echo $post_author_url; ?>">By <?php echo $post_display_name; ?></a>
									</p>
								</div>
							</div>
						</div>
						<?php
					endwhile;
					wp_localize_script('infinite-scroll-tag', 'infinite_scroll_tag_params', array(
						'ajaxurl' => admin_url('admin-ajax.php'),
						'query_vars' => array(
							'tag__in' => array($tag_id),
							'post__not_in' => $notIn
						),
						'current_page' => max(1, get_query_var('paged')),
						'max_page' => $the_query->max_num_pages
					));
					?>
				</div>
			</div>
		</section>

	</div>
</main>
<?php get_footer(); ?>