<?php
get_header();
the_post();
$author = get_queried_object();
$author_id = $author->ID;
$author_name = get_the_author_meta('nicename', $author_id);
$userdata = get_userdata($author_id);
$userid = $userdata->ID;
$author_display_name = get_the_author_meta('display_name', $author_id);
$disible_ap = get_field('disible_ap', 'user_' . $userid);
if ($disible_ap == true)
	wp_redirect(get_permalink(20));
$check = false;
?>
<main id="content">
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
	<div class="author-main">
		<div class="bg-author"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/bg-author-3.png"
				alt="">
		</div>
		<section class="author-top position-relative special-width">
			<div class="container">
				<div class="author-box">
					<div class="featured image-fit">
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
					</div>
					<div class="info">
						<div class="top">
							<div class="social">
								<?php
								$social = '';
								$type = true;
								if (get_field('new_social', 'user_' . $userid)) {
									$social = get_field('new_social', 'user_' . $userid);
								} elseif (get_field('social', 'user_' . $userid)) {
									$social = get_field('social', 'user_' . $userid);
									$type = false;
								}

								if ($social) {
									if ($type) {
										foreach ($social as $social_item) {
											$url = get_template_directory_uri() . '/assets/images/usericon/' . $social_item['icon'] . '.svg';

											?>
											<a target="_blank" href="<?php echo $social_item['link']; ?>"><img
													src="<?php echo $url; ?>" alt="<?php echo $social_item['icon'] ?>" /></a>
										<?php }
									} else {
										foreach ($social as $social_item) {
											?>
											<a target="_blank" href="<?php echo $social_item['link']; ?>"><img
													src="<?php echo $social_item['icon']['url']; ?>"
													alt="<?php echo $social_item['icon']['alt']; ?>" /></a>
										<?php }
									}
								} ?>
							</div>
							<h1><?php echo $author_display_name; ?></h1>
						</div>
						<h3><?= get_field('new_position', 'user_' . $userid) ?? get_field('position', 'user_' . $userid) ?>
						</h3>
						<div class="tag">
							<?php

							$skills = '';

							if (get_field('new_skills', 'user_' . $userid)) {
								$skills = get_field('new_skills', 'user_' . $userid);
							} elseif (get_field('skills', 'user_' . $userid)) {
								$skills = get_field('skills', 'user_' . $userid);
								;
							}

							if ($skills) {
								foreach ($skills as $skills) {
									?>
									<span><?php echo $skills['skill_item']; ?></span>
								<?php }
							} ?>
						</div>
						<p class="pri-color-2">
							<?= get_field('new_story', 'user_' . $userid) ?? get_field('story', 'user_' . $userid) ?>
						</p>
					</div>
				</div>
				<div class="author-content">
					<?php
					$author_content = get_field('user_content_about', 'user_' . $userid);
					if ($author_content):
						foreach ($author_content as $author_ct):
							?>
							<h2><?php echo $author_ct['title']; ?></h2>
							<ul>
								<?php
								$content_list = $author_ct['content_list'];
								foreach ($content_list as $list):
									?>
									<li class="content_author_list">
										<p class="has-medium-font-size"><?php echo $list['it_title']; ?></p>
										<p><?php echo $list['it_description']; ?></p>
										<p class="has-small-font-size sec-color-4"><?php echo $list['it_date']; ?></p>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endforeach; ?>
					<?php else: ?>
						<div class="author-custom">
							<div class="author-it">
								<h2>Experience</h2>
								<?php echo get_field('experience', 'user_' . $userid) ?>
							</div>
							<div class="author-it">
								<h2>EDUCATION</h2>
								<?php echo get_field('educator', 'user_' . $userid) ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<section class="author-other sg-other">
			<div class="container">
				<h2 class="text-center"><?php echo get_field('other_author_page', 'option'); ?>
				</h2>
				<?php
				$array_merge = array();
				$args = array(
					'post_type' => array('exercise', 'post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide'),
					'posts_per_page' => 9,
					'author' => $userid
				);
				$the_query = new WP_Query($args);
				if ($the_query->have_posts()) {
					while ($the_query->have_posts()):
						$the_query->the_post();
						$array_merge[] = $post;
					endwhile;
					wp_reset_query();
				}
				$num_left = 9 - count($array_merge);
				if ($num_left > 0) {
					$args = array(
						'post_type' => array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide'),
						'posts_per_page' => $num_left,
						'meta_query' => array(
							array(
								'key' => 'select_author',
								'value' => $userid,
								'compare' => 'LIKE'
							)
						)
					);
					$the_query = new WP_Query($args);
					if ($the_query->have_posts()) {
						while ($the_query->have_posts()):
							$the_query->the_post();
							$array_merge[] = $post;
						endwhile;
						wp_reset_query();
					}
				}
				if (count($array_merge) > 0) {
					?>
					<div class="news-list grid grid-feature">
						<?php
						foreach ($array_merge as $post) {
							$medically_reviewed = get_field('medically_reviewed', $post->ID);
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
										<div class="tag">
											<?php $cat = get_the_category($post->ID);
											if (!empty($cat) && count($cat) > 0): ?>
												<span><a
														href="<?php echo get_term_link($cat[0]->term_id); ?>"><?php echo $cat[0]->name; ?></a></span>
											<?php endif; ?>
										</div>
										<p class="has-large-font-size text-special clamp-2"><a class="pri-color-2"
												href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
										<p class="has-small-font-size"><a class="sec-color-3"
												href="<?php echo $post_author_url; ?>">By
												<?php echo $post_display_name; ?></a>
										</p>
										<?php
										$yoast_meta = get_post_meta($post->ID, '_yoast_wpseo_metadesc', true);
										if ($yoast_meta) {
											$current_year = date('Y');
											$yoast_meta = str_replace('%%currentyear%%', $current_year, $yoast_meta);
											?>
										<?php } ?>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
					<?php
				}
				?>
			</div>
		</section>
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