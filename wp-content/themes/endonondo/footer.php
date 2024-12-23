<footer id="footer">
	<div class="ft-top">
		<div class="container list-flex flex-two">
			<div class="ft-info item-flex">
				<div class="ft-logo"><a href="<?php echo home_url(); ?>"><img
							src="<?php echo get_field('logo', 'option') ?>" alt=""></a></div>
				<div class="social mr-bottom-40">
					<?php
					$social = get_field('social', 'option');
					if ($social) {
						foreach ($social as $social) {
							?>
							<a target="_blank" href="<?php echo $social['link']; ?>"><img src="<?= $social['icon']['url']; ?>"
									alt="<?= $social['icon']['alt']; ?>" /></a>
						<?php }
					} ?>
				</div>
				<div class="ft-form">
					<div class="title mr-bottom-20">
						<p class="has-large-font-size">
							<?= get_field('news_title', 'option') ?>
						</p>
					</div>
					<div class="description mr-bottom-20">
						<p class="has-small-font-size">
							<?= get_field('news_des', 'option') ?>
						</p>
					</div>
					<div class="klaviyo-form-TcfuNL mr-bottom-20"></div>
				</div>
			</div>
			<nav class="ft-menu item-flex">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu_cat',
					)
				);
				?>
				<div class="disclaimer">
					<p class="has-small-font-size pri-color-3"><?php the_field('disclaimer', 'option'); ?></p>
				</div>
			</nav>
		</div>
	</div>
	<div class="ft-bottom">
		<div class="container list-flex flex-two">
			<div class="item-flex">
				<?php
				$copyright = get_field('footer_bottom', 'option');
				$yearc = date('Y');
				$text= str_replace("%year%", $yearc, $copyright);?>
				<p class="has-small-font-size pri-color-3"><?=$text;?></p>
			</div>
			<div class="item-flex">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu_footer',
					)
				);
				?>
			</div>
		</div>
	</div>
</footer>
</div>
<div class="modalcn-bg" style="display: none;"></div>
<?php 
	$post_type = get_field('cra_with_cpt','option');
	if(in_array( get_post_type(), $post_type) == true) include "hcfunction/customer-feedback.php";
?>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/slick/slick.js"></script>
<script type="text/javascript"
	src="<?php echo get_template_directory_uri(); ?>/assets/js/swiper/swiper-bundle.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/custom.js?v=1.2.3"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/rating.js?ver=1.0.0"></script>
<script async type="text/javascript" src="https://static.klaviyo.com/onsite/js/klaviyo.js?company_id=RG9krj"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
<?php
$pty = get_post_type();
$args = array(
	'post_type' => 'gp_elements',
	'posts_per_page' => 5,
	'meta_query' => array(
		array(
			'key' => 'emposition',
			'value' => 'before_close_body'
		),
		array(
			'key' => 'emdisplay',
			'value' => sprintf('"%s"', $pty),
			'compare' => 'LIKE'
		)
	)
);
$the_query = new WP_Query($args);
while ($the_query->have_posts()):
	$the_query->the_post();
	echo get_field('emcode', $post->ID, false, false);
endwhile;
wp_reset_query();
$args = array(
	'post_type' => 'gp_elements',
	'posts_per_page' => 5,
	'meta_query' => array(
		array(
			'key' => 'emposition',
			'value' => 'before_close_body'
		),
		array(
			'key' => 'display_with_id_$_pid',
			'value' => get_the_ID(),
			'compare' => '='
		)
	)
);
$the_query = new WP_Query($args);
while ($the_query->have_posts()):
	$the_query->the_post();
	echo get_field('emcode', $post->ID, false, false);
endwhile;
wp_reset_query();
if (is_front_page()) {
	$args = array(
		'post_type' => 'gp_elements',
		'posts_per_page' => 5,
		'meta_query' => array(
			array(
				'key' => 'emposition',
				'value' => 'before_close_body'
			),
			array(
				'key' => 'emdisplay',
				'value' => 'home_page',
				'compare' => 'LIKE'
			)
		)
	);
	$the_query = new WP_Query($args);
	while ($the_query->have_posts()):
		$the_query->the_post();
		echo get_field('emcode', $post->ID, false, false);
	endwhile;
	wp_reset_query();
}
$args = array(
	'post_type' => 'gp_elements',
	'posts_per_page' => 5,
	'meta_query' => array(
		array(
			'key' => 'emposition',
			'value' => 'ads_footer'
		),
		array(
			'key' => 'emdisplay',
			'value' => sprintf('"%s"', $pty),
			'compare' => 'LIKE'
		)
	)
);
$the_query = new WP_Query($args);
while ($the_query->have_posts()):
	$the_query->the_post();
	?>
	<div class="ads-script"><?php echo get_field('emcode', $post->ID, false, false); ?></div>
	<?php
endwhile;
wp_reset_query();
?>
<?php wp_footer(); ?>
</body>

</html>