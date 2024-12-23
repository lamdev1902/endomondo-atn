<?php
$terms_current = get_queried_object();
get_header();
$term_id = $terms_current->term_id;
$image = get_field('featured_image', $terms_current);
$term_parent = $terms_current->category_parent;
$term_name = $terms_current->name;
$taxonomy_name = 'category';
$term_children = get_term_children($term_id, $taxonomy_name);
$term_parent_custom = get_term_by('id', $term_parent, 'category');
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
	<?php if ($term_parent == 0) { ?>
		<div class="blog-main category-main">
			<div class="container">
				<div class="category-link-box">
					<div class="category-link list-flex">
						<?php
						$args = array(
							'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide','coupon','exercise'),
							'taxonomy' => 'category',
							'hide_empty' => 0,
							'parent' => 0,
						);
						$categories = get_categories($args);
						foreach ($categories as $category) {
							$term_id_item = $category->term_id;
							?>
							<p class="has-small-font-size"><a class="<?php if ($term_id_item == $term_id)
								echo 'active'; ?>" href="<?php echo get_term_link($category->term_id); ?>"><?php echo $category->name; ?></a>
							</p>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="category-top blog-top position-relative">
				<div class="top-box list-flex">
					<div class="info">
						<h1 class="text-uppercase pri-color-3"><?php echo $terms_current->name; ?></h1>
						<p class="on-pc pri-color-2"><?php echo $terms_current->description; ?></p>
					</div>
					<?php if ($image) { ?>
						<div class="featured list-flex">
							<img src="<?php echo $image; ?>" alt="">
						</div>
					<?php } else { ?>
						<div class="featured list-flex">
							<img src="<?php echo get_field('bannercat_default', 'option'); ?>" alt="">
						</div>
					<?php } ?>
					<p class="on-sp pri-color-2"><?php echo $terms_current->description; ?></p>
				</div>
			</div>

			<?php if ($term_children) { ?>
				<?php
				$terms = get_terms(
					array(
						'taxonomy' => 'category',
						'hide_empty' => false,
						'parent' => $term_id,
					)
				);
				if ($terms) {
					foreach ($terms as $term) {
						$term_link = get_term_link($term, 'category');
						$term_children_id = $term->term_id;
						$count = $term->count;
						if ($count > 1) {
							?>
							<section class="home-lastest cate-section pd-main">
								<div class="container mr-bottom-20">
									<h2 class="pri-color-2">Latest Post</h2>
									<div class="lastest-list">
										<?php
										$args = array(
											'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide','coupon','exercise'),
											'posts_per_page' => 6,
											'cat' => $term_children_id
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
									<div class="news-list grid grid-feature">
										<?php
										$args = array(
											'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide','coupon','exercise'),
											'posts_per_page' => 6,
											'cat' => $term_id,
											'post__not_in' => $notIn
										);
										$the_query = null;
										$the_query = new WP_Query($args);
										while ($the_query->have_posts()):
											$the_query->the_post();
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
										wp_localize_script('infinite-scroll', 'infinite_scroll_params', array(
											'ajaxurl' => admin_url('admin-ajax.php'),
											'query_vars' => array(
												'cat' => $term_id,
												'post__not_in' => $notIn
											),
											'current_page' => max(1, get_query_var('paged')),
											'max_page' => $the_query->max_num_pages
										));
										?>
									</div>
								</div>
							</section>
						<?php }
					}
				} ?>
			<?php } else { ?>
				<section class="home-lastest cate-section pd-main">
					<div class="container mr-bottom-20">
						<h2 class="pri-color-2">Latest Post</h2>
						<div class="lastest-list">
							<?php
							$args = array(
								'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide','coupon','exercise'),
								'posts_per_page' => 6,
								'cat' => $term_id,
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
											<p class="has-small-font-size author"><a class="sec-color-4"
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
						<div class="news-list cate-list grid grid-feature">
							<?php
							$args2 = array(
								'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide','coupon','exercise'),
								'posts_per_page' => 6,
								'cat' => $term_id,
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
							wp_reset_query();
							wp_localize_script('infinite-scroll', 'infinite_scroll_params', array(
								'ajaxurl' => admin_url('admin-ajax.php'),
								'query_vars' => array(
									'cat' => $term_id,
									'post__not_in' => $notIn
								),
								'current_page' => max(1, get_query_var('paged')),
								'max_page' => $the_query->max_num_pages
							));
							?>
						</div>
					</div>
				</section>
			<?php } ?>
		</div>
	<?php } else { ?>
		<!-- <div class="blog-main">
			<div class="blog-top position-relative">
				<div class="container">
					<div class="top-box list-flex">
						<div class="info">
							<h1 class="text-uppercase pri-color-3"><?php echo $terms_current->name; ?></h1>
							<p class="on-pc"><?php echo $terms_current->description; ?></p>
						</div>
						<?php if ($image) { ?>
							<div class="featured list-flex">
								<img src="<?php echo $image; ?>" alt="">
							</div>
						<?php } ?>
						<p class="on-sp pri-color-2"><?php echo $terms_current->description; ?></p>
					</div>
				</div>
			</div>
			<div class="container">
				<div class="blog-select">
					<div class="news-list list-flex">
						<?php
						$args = array(
							'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide'),
							'posts_per_page' => 3,
							'cat' => $term_id,
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
										<a href="<?php the_permalink(); ?>">
											<?php $image_featured = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
											;
											if ($image_featured) {
												?>
												<div class="image-fit">
													<img src="<?php echo $image_featured; ?>" alt="">
												</div>
											<?php } else { ?>
												<div class="image-fit">
													<img src="<?php echo get_field('fimg_default', 'option'); ?>" alt="">
												</div>
											<?php } ?>
										</a>
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
										<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
										<h5 class="author"><a href="<?php echo $post_author_url; ?>">By
												<?php echo $post_display_name; ?></a></h5>
									</div>
								</div>
							</div>
							<?php
						endwhile;
						wp_reset_query();
						?>
					</div>
				</div>
				<div class="blog-all">
					<?php
					$args = array(
						'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide'),
						'cat' => $term_id,
					);
					$the_query = new WP_Query($args);
					$count = $the_query->post_count;
					if ($count > 3) {
						?>
						<h2 class="ed-title">All post</h2>
					<?php } ?>
					<div class="news-list list-flex">
						<?php
						$args = array(
							'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide'),
							'posts_per_page' => 12,
							'cat' => $term_id,
							'offset' => 3,
							'paged' => $paged,
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
										<a href="<?php the_permalink(); ?>">
											<?php $image_featured = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
											;
											if ($image_featured) {
												?>
												<div class="image-fit">
													<img src="<?php echo $image_featured; ?>" alt="">
												</div>
											<?php } else { ?>
												<div class="image-fit">
													<img src="<?php echo get_field('fimg_default', 'option'); ?>" alt="">
												</div>
											<?php } ?>
										</a>
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
										<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
										<h5 class="author"><a href="<?php echo $post_author_url; ?>">By
												<?php echo $post_display_name; ?></a></h5>
									</div>
								</div>
							</div>
							<?php
						endwhile;
						wp_reset_query();
						?>
					</div>
				</div>
				<?php
				$big = 999999999;
				$mcs_paginate_links = paginate_links(
					array(
						'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
						'format' => '?paged=%#%',
						'current' => max(1, get_query_var('paged')),
						'total' => $the_query->max_num_pages,
						'prev_text' => __('<i class="fal fa-angle-left"></i>', 'yup'),
						'next_text' => __('<i class="fal fa-angle-right"></i>', 'yup')
					)
				);
				if ($mcs_paginate_links):
					?>
					<div class="pagination">
						<?php echo $mcs_paginate_links ?>
					</div>
				<?php endif; ?>
			</div>
		</div> -->
	<?php } ?>
</main>

<?php get_footer(); ?>
