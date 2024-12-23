<?php

$postid = get_the_ID();
$pdes = get_post_meta($postid, '_yoast_wpseo_metadesc', true);
$cuyear = date('Y');
$pdes = str_replace("%%currentyear%%", $cuyear, $pdes);
get_header();
the_post();
?>
<main id="content" class="calories-content">
	<article class="single-main">
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
		<section class="exc-hero-section tool">
			<div class="container">
				<div class="tool-top text-center">
					<h1><?php the_title(); ?></h1>
					<?php if ($pdes) { ?>
						<p><?php echo $pdes; ?></p><?php } ?>
					<div class="social follow mr-bottom-20">
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
					<div class="br-section mr-bottom-20"></div>
					<!-- <div class="choose-list mr-bottom-20">
							<ul class="flex">
								<li>
									<p class="has-meidum-font-size">Input Your Information</p>
								</li>
								<li>
									<p class="has-meidum-font-size">Choose Your Calories</p>
								</li>
								<li>
									<p class="has-meidum-font-size">Plan your diel meal</p>
								</li>
							</ul>
						</div> -->
					<!-- <div class="social">
							<?php
							$social = get_field('social', 'option');
							if ($social) {
								foreach ($social as $social) {
									?>
									<a target="_blank" href="<?php echo $social['link']; ?>"><img
											src="<?= $social['icon']['url']; ?>" alt="<?= $social['icon']['alt']; ?>" /></a>
								<?php }
							} ?>
						</div> -->
				</div>
				<div class="exc-container">
					<?php the_content(); ?>
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
				</div>
			</div>
		</section>
		<?php
		$author_id = get_post_field('post_author', $postid);

		$author_name = get_the_author_meta('nickname', $author_id);
		$author_url = get_author_posts_url($author_id);

		$avt = '';

		if (get_field('new_avata', 'user_' . $author_id)) {
			$avt = get_field('new_avata', 'user_' . $author_id);
		} elseif (get_field('avata', 'user_' . $author_id)) {
			$avt = get_field('avata', 'user_' . $author_id);
		}

		$user_description = '';

		if (get_field('new_story', 'user_' . $author_id)) {
			$user_description = get_field('new_story', 'user_' . $author_id);
		} elseif (get_field('story', 'user_' . $author_id)) {
			$user_description = get_field('story', 'user_' . $author_id);
		}

		$userPosition = get_field('position', 'user_' . $author_id);

		if (get_field('new_position', 'user_' . $author_id)) {
			$userPosition = get_field('new_position', 'user_' . $author_id);
		} elseif (get_field('position', 'user_' . $author_id)) {
			$userPosition = get_field('position', 'user_' . $author_id);
		}
		?>
		<section class="single-main exc-author">
			<aside class="single-sidebar ">
				<div class="container">
					<div class="author-about exc-container">
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
												</sp>
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
				</div>
			</aside>
		</section>
	</article>
</main>
<?php get_footer(); ?>
<script>
</script>