<?php
$postid = get_the_ID();
$post_terms = wp_get_post_terms($postid, 'category');
$author_id = get_post_field('post_author', $postid);
$upid = get_post_field('post_author', $postid);
$author_name = get_the_author_meta('nickname', $author_id);
$author_url = get_author_posts_url($author_id);
$user_info = get_userdata($author_id);

$avt = '';
if (get_field('new_avata', 'user_' . $upid)) {
	$avt = get_field('new_avata', 'user_' . $upid);
} elseif (get_field('avata', 'user_' . $upid)) {
	$avt = get_field('avata', 'user_' . $upid);
}


$user_description = '';

if (get_field('new_story', 'user_' . $upid)) {
	$user_description = get_field('new_story', 'user_' . $upid);
} elseif (get_field('story', 'user_' . $upid)) {
	$user_description = get_field('story', 'user_' . $upid);
}

$userPosition = get_field('position', 'user_' . $upid);

if (get_field('new_position', 'user_' . $upid)) {
	$userPosition = get_field('new_position', 'user_' . $upid);
} elseif (get_field('position', 'user_' . $upid)) {
	$userPosition = get_field('position', 'user_' . $upid);
}

get_header();
the_post();
$post_type = $post->post_type;



$checktime = '';

$disableFeature = get_field('disable_featured_image', $postid);

$advertiser_disclosure = get_field('enable_tooltip1', $postid);

$enable_fat_checked = get_field('enable_fat_checked', $postid);

$enable_fcgroup = get_field('enable_fcgroup', $postid);

?>
<main id="content">
	<div class="container">
		<div class="single-top">
			<div class="list-flex flex-center flex-middle">
				<?php
				if (function_exists('yoast_breadcrumb')) {
					yoast_breadcrumb('<div id="breadcrumbs" class="breacrump">', '</div>');
				}
				?>
			</div>
		</div>
		<div class="single-main list-flex">
			<?php get_sidebar(); ?>
			<div class="sg-right">
				<h1 class="mr-bottom-20"><?php the_title(); ?></h1>
				<?php $aname = get_field('user_nshort', 'user_' . $upid);
				if (!$aname || $aname == '')
					$aname = get_the_author();
				?>
				<div class="single-author mr-bottom-20">
					<div class="name-author">
						<div class="info">
							<div class="author-by" itemscope>
								<time class="updated has-small-font-size" datetime="<?php the_modified_date('c'); ?>"
									itemprop="dateModified"><?php
									if (get_the_modified_date('U') !== get_the_date('U')) {
										echo __('Updated on', 'hc_theme');
									} else {
										echo __('Published', 'hc_theme');
									}
									?>
									<?php the_modified_date('F d, Y'); ?></time>
								<span class="has-small-font-size">- Writen by: </span>
								<span class="has-small-font-size" itemprop="author" itemscope
									itemtype="https://schema.org/Person"><a class="pri-color-2" target="_blank"
										href="<?php echo $author_url; ?>"
										title="<?php echo __('View all posts by', 'hc_theme'); ?> <?php the_author(); ?>"
										rel="author" itemprop="url"><span class="ncustom has-small-font-size"
											itemprop="name"><?php echo $aname; ?></span></a></span>
								<?php
								$medically_reviewed = get_field('select_author', $postid);
								if ($medically_reviewed) { ?>
									<span class="has-small-font-size"> - Reviewed by</span>
									<span class="has-small-font-size">
										<?php foreach ($medically_reviewed as $m => $mr) {
											$anamer = get_field('user_nshort', 'user_' . $mr['ID']);
											if (!$anamer || $anamer == '')
												$anamer = $mr['display_name'];
											?>
											<a target="_blank" class="pri-color-2" style="text-decoration: underline"
												href="<?php echo get_author_posts_url($mr['ID']); ?>"><?php if ($m > 0)
													   echo ' ,'; ?><?php echo $anamer; ?></a>
										<?php } ?>
									</span>
								<?php } ?>
								<?php
								if ($enable_fcgroup): ?>
									<?php if ($enable_fcgroup == '1') { ?>
										<span id="at-box"><img
												src="<?php echo get_template_directory_uri(); ?>/assets/images/author.svg"
												alt="Fact checked"></span>
									<?php } elseif ($enable_fcgroup == '2') { ?>
										<span id="eb-box"><img
												src="<?php echo get_template_directory_uri(); ?>/assets/images/eb.svg"
												alt="Fact checked"></span>
									<?php } ?>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				<?php
				if ($enable_fcgroup) {
					if ($enable_fcgroup == '1') { ?>
						<div class="fact-check ">
							<div class="fact-label at">
								<p class="has-large-font-size"><?php echo __("Author's opinion", 'hc_theme'); ?></p>
								<span class="fact-close"></span>
								<?php the_field('fccontent', 'option'); ?>
							</div>
						</div>
					<?php } elseif ($enable_fcgroup == '2') { ?>
						<div class="fact-check">
							<div class="fact-label eb">
								<p class="has-large-font-size"><?php echo __("Evidence Based", 'hc_theme'); ?></p>
								<span class="fact-close"></span>
								<?php the_field('evidence_based', 'option'); ?>
							</div>
						</div>
					<?php }
				}
				?>
				<div class="social mr-bottom-20">
					<p class="has-small-font-size pri-color-2" style="margin-bottom: 0">Follow us: </p>
					<?php
					$socials = get_field('follow_social', 'option');
					if ($socials) {
						foreach ($socials as $social) {
							?>
							<a target="_blank" href="<?php echo $social['link']; ?>"><img alt="<?= $social['icon']['alt']; ?>"
									src="<?= $social['icon']['url']; ?>" /></a>
						<?php }
					} ?>
				</div>
				<article class="sg-custom">
					<?php if (!$disableFeature): ?>
						<div class="single-featured">
							<figure class="wp-block-image size-full">
								<?php $image_featured = wp_get_attachment_url(get_post_thumbnail_id($postid));

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
								<?php $post_thumbnail_id = get_post_thumbnail_id($postid);
								$caption = wp_get_attachment_caption($post_thumbnail_id) ?>
								<figcaption class="wp-element-caption text-center"><?php echo $caption; ?></figcaption>
							</figure>
						</div>
					<?php endif; ?>
					<?php if ($advertiser_disclosure): ?>
						<div class="box-e mr-bottom-20">
							<?php the_field('adcontent', 'option'); ?>
						</div>
					<?php endif; ?>
					<?php get_template_part('template-parts/content', 'enfit'); ?>
					<div class="sg-editor">
						<?php the_content(); ?>
					</div>
					<?php
					if (get_field('enable_source', 'option') == true) {
						?>
						<div class="sg-resources mr-bottom-20 pd-main">
							<h3>Resources</h3>
							<div class="intro">
								<?= get_field('source_intro', 'option'); ?>
							</div>
							<?php $source_content = get_field('source_content', $postid);
							if ($source_content)
								echo $source_content;
							?>
						</div>
					<?php } ?>
					<div class="author-about">
						<h3>About the Author</h3>
						<div class="author-write">
							<div class="author-link">
								<?php
								if ($avt) {
									?>
									<a target="_blank" href="<?php echo $author_url; ?>"><img src="<?php echo $avt; ?>"
											alt=""></a>
								<?php } else { ?>
									<a target="_blank" href="<?php echo $author_url; ?>"><img
											src="<?php echo get_field('avatar_default', 'option'); ?>" alt="">
									<?php } ?>
									<p class="has-medium-font-size"><a target="_blank"
											style="color: var(--pri-color-2) !important;"
											href="<?php echo $author_url; ?>"><?php the_author(); ?>
										</a>
										<?php if ($userPosition): ?>
											<span>
												<?= $userPosition; ?>
											</span>
										<?php endif; ?>
									</p>
							</div>
							<?php if ($user_description) { ?>
								<div class="author-info">
									<p><?php echo wp_trim_words($user_description, 50, '') . '.. '; ?><a
											href="<?php echo $author_url; ?>"> See more</a></p>
								</div>
							<?php } ?>
						</div>
					</div>
				</article>
			</div>
		</div>
		<aside class="sg-other">
			<h2 class="text-center">Read More</h2>
			<div class="news-list grid grid-feature">
				<?php
				$args = array(
					'posts_per_page' => 3,
					'post__not_in' => array($postid),
					'post_type' => $post_type,
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
							<div class="featured image-fit hover-scale mr-bottom-16">
								<a href="<?php the_permalink(); ?>">
									<?php
									$image_featured = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
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
									<div class="tag mr-bottom-16">
										<?php
										foreach ($category as $cat) { ?>
											<span><a
													href="<?php echo get_term_link($cat->term_id); ?>"><?php echo $cat->name; ?></a></span>
										<?php } ?>
									</div>
								<?php endif; ?>
								<p class="has-medium-font-size text-special mr-bottom-16 clamp-2">
									<a class="pri-color-2" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</p>
								<p class="has-small-font-size">
									<a class="sec-color-3" href="<?php echo $post_author_url; ?>">By
										<?php echo $post_display_name; ?></a>
								</p>
							</div>
						</div>
					</div>
					<?php
				endwhile;
				wp_reset_query();
				?>
			</div>
		</aside>
	</div>
	<div class="container">
        <?php comments_template(); ?>
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