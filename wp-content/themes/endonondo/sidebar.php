<?php
$postid = get_the_ID();
$author_id = get_post_field('post_author', $postid);
$author_name = get_the_author_meta('nickname', $author_id);
$author_url = get_author_posts_url($author_id);
$post_type = $post->post_type;
?>
<aside class="single-sidebar">
	<section class="sg-lastest on-pc">
		<p class="has-large-font-size mr-bottom-20 pri-color-2">Recommended Post</p>
		<div class="sg-lastest-list">
			<?php
			$args = array(
				'posts_per_page' => 1,
				'post_type' => $post_type,
			);
			$the_query = new WP_Query($args);
			while ($the_query->have_posts()):
				$the_query->the_post();
				$post_author_id = get_post_field('post_author', $post->ID);
				$post_display_name = get_the_author_meta('nickname', $post_author_id);
				$post_author_url = get_author_posts_url($post_author_id);
				?>
				<div class="sg-lastest-it">
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
									<span><a href="<?php echo get_term_link($cat->term_id); ?>"><?php echo $cat->name; ?></a></span>
								<?php } ?>
							</div>
						<?php endif; ?>
						<p class="has-medium-font-size mr-bottom-20 text-special clamp-1"><a class="pri-color-2"
								href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
						<p class="has-small-font-size"><a class="sec-color-3" target="_blank" href="<?php echo $post_author_url; ?>">By
								<?php echo $post_display_name; ?></a></p>
						<?php
						$yoast_meta = get_post_meta($post->ID, '_yoast_wpseo_metadesc', true);
						if ($yoast_meta) {
							$current_year = date('Y');
							$yoast_meta = str_replace('%%currentyear%%', $current_year, $yoast_meta);
							?>
							<div class="">
								<p class="text-special clamp-2 pri-color-2"><?php echo $yoast_meta; ?></p>
							</div>
						<?php } ?>
					</div>
				</div>
				<?php
			endwhile;
			wp_reset_query();
			?>
			<?php
			$args = array(
				'posts_per_page' => 4,
				'post_type' => $post_type,
				'offset' => 1,
			);
			$the_query = new WP_Query($args);
			while ($the_query->have_posts()):
				$the_query->the_post();
				$post_author_id = get_post_field('post_author', $post->ID);
				$post_display_name = get_the_author_meta('nickname', $post_author_id);
				$post_author_url = get_author_posts_url($post_author_id);
				?>
				<div class="sg-lastest-it position-relative">
					<p class="has-medium-font-size mr-bottom-20 text-special clamp-2"><a class="pri-color-2"
							href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
					<p class="has-small-font-size mr-bottom-20"><a class="sec-color-2"
							href="<?php echo $post_author_url; ?>">By <?php echo $post_display_name; ?></a></p>
					<a href="<?php the_permalink(); ?>" class="news-link position-absolute"><img
							src="<?php echo get_template_directory_uri(); ?>/assets/images/right-black.svg" alt=""></a>
				</div>
				<?php
			endwhile;
			wp_reset_query();
			?>
		</div>
	</section>
</aside>