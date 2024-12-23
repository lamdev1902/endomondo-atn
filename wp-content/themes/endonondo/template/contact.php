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
</main>
<?php get_footer(); ?>