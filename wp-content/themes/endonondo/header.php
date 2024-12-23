<!DOCTYPE HTML>
<html lang="en-US">

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="p:domain_verify" content="28c2a5883d9783b56cfa793df37dcd1a" />
	<title><?php
	global $page, $paged;
	wp_title('|', true, 'right');
	$site_description = get_bloginfo('description', 'display');
	if ($site_description && (is_home() || is_front_page()))
		echo " | $site_description";
	if ($paged >= 2 || $page >= 2)
		echo ' | ' . sprintf(__('Page %s', 'twentyeleven'), max($paged, $page));
	?></title>
	<?php
	if (is_singular() && get_option('thread_comments'))
		wp_enqueue_script('comment-reply');
	wp_head();
	?>

	<link rel="preload" href="<?= get_template_directory_uri() . '/assets/images/enfit/first-mb.png' ?>" as="image">

	<!-- Canon Link -->
	<link rel="canonical" href="<?= the_permalink() ?>" />

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo get_field('favicon', 'option'); ?>" />
	<link rel="icon" href="<?= (get_field('favicon_16', 'option')) ? get_field('favicon_16', 'option') : "#"; ?>"
		sizes="16x16" />
	<link rel="icon" href="<?= (get_field('favicon_32', 'option')) ? get_field('favicon_32', 'option') : "#"; ?>"
		sizes="32x32" />
	<link rel="icon" href="<?= (get_field('favicon_96', 'option')) ? get_field('favicon_96', 'option') : "#"; ?>"
		sizes="96x96" />
	<link rel="apple-touch-icon"
		href="<?= (get_field('favicon_180', 'option')) ? get_field('favicon_180', 'option') : "#"; ?>"
		sizes="180x180" />
	<link rel="icon" href="<?= (get_field('favicon_256', 'option')) ? get_field('favicon_256', 'option') : "#"; ?>"
		sizes="256x256" />
	<meta name="msapplication-TileImage" content="<?php echo get_field('favicon_512', 'option'); ?>" />

	<script src="https://www.youtube.com/iframe_api"></script>
	<script type="text/javascript"
		src="<?php echo get_template_directory_uri(); ?>/assets/js/jquery-3.5.0.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<meta name="msapplication-TileImage" content="<?php echo get_field('favicon_size_270', 'option'); ?>" />
	<?php
	$pty = get_post_type();
	$args = array(
		'post_type' => 'gp_elements',
		'posts_per_page' => 5,
		'meta_query' => array(
			array(
				'key' => 'emposition',
				'value' => 'in_head_tag'
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
				'value' => 'in_head_tag'
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
					'value' => 'in_head_tag'
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
	?>
</head>

<body <?php body_class(); ?>>

	<div id="wapper" class="<?php if (is_front_page()) {
		echo 'home-main color-white';
	} else {
		echo '';
	} ?>">
		<header id="header" class="position-relative">
			<div class="container">
				<div class="list-flex flex-middle flex-center">
					<div>
						<div class="toogle-menu">
							<span></span>
						</div>
						<div class="hd-search">
							<a class="position-relative" href="#"></a>
						</div>
					</div>
					<div class="logo"><a href="<?php echo home_url(); ?>"><img
								src="<?php echo get_field('logo', 'option') ?>" alt=""></a>
					</div>
					<div class="btn-header">
						<a href="<?php echo get_field('subscribe_link', 'option') ?>"
							class="ed-btn btn-popup"><?php echo get_field('subscribe_title', 'option') ?></a>
					</div>
				</div>
			</div>
			<nav class="menu-main">
				<div class="hd-search-form">
					<div class="container">
						<form action="<?php echo get_home_url(); ?>/" method="get">
							<input type="text" id="s" name="s" class="form-control" value=""
								placeholder="Type here ...">
						</form>
					</div>
				</div>
				<div class="list-menu">
					<div class="container">
						<nav class="menu-box">
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'menu_main',
								)
							);
							?>
						</nav>
					</div>
				</div>
			</nav>
			<div class="popup" id="popup-email">
				<div class="popup-click"></div>
				<div class="popup-bg">
					<div class="popup-box list-flex">
						<div class="featured image-fit">
							<img class="on-pc" src="<?php echo get_template_directory_uri(); ?>/assets/images/popup.jpg"
								alt="">
							<img class="on-sp"
								src="<?php echo get_template_directory_uri(); ?>/assets/images/sp-popup.jpg" alt="">
						</div>
						<div class="info">
							<img class="close" src="<?php echo get_template_directory_uri(); ?>/assets/images/close.svg"
								alt="">
							<div class="box">
								<div class="feature">
									<img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo-popup.svg"
										alt="">
								</div>
								<div class="klaviyo-form-UPY2r8"></div>
								<div class="social">
									<p class="has-small-font-size">Follow us: </p>
									<?php
									$socials = get_field('social', 'option');
									if ($socials) {
										foreach ($socials as $social) {
											?>
											<a target="_blank" href="<?php echo $social['link']; ?>"><img
													src="<?= $social['icon']['url']; ?>"
													alt="<?= $social['icon']['alt']; ?>" /></a>
										<?php }
									} ?>
								</div>

								<p class="note has-small-font-size"><i>* <a href="https://www.endomondo.com/privacy-policy">Your privacy</a> is important to
										us</i></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</header>
		<?php
		$pty = get_post_type();
		$args = array(
			'post_type' => 'gp_elements',
			'posts_per_page' => 5,
			'meta_query' => array(
				array(
					'key' => 'emposition',
					'value' => 'after_header'
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
			echo get_field('emcode', $post->ID);
		endwhile;
		wp_reset_query();
		$args = array(
			'post_type' => 'gp_elements',
			'posts_per_page' => 5,
			'meta_query' => array(
				array(
					'key' => 'emposition',
					'value' => 'after_header'
				),
				array(
					'key' => 'display_with_id',
					'value' => sprintf('"%s"', get_the_ID()),
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
		if (is_front_page()) {
			$args = array(
				'post_type' => 'gp_elements',
				'posts_per_page' => 5,
				'meta_query' => array(
					array(
						'key' => 'emposition',
						'value' => 'after_header'
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
		?>