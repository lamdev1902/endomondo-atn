<?php
include(TEMPLATEPATH . '/shortcode/chart/chart-shortcode.php');
include(TEMPLATEPATH . '/shortcode/muscle/anatomy.php');
include(TEMPLATEPATH . '/shortcode/calorie/calorie-shortcode.php');
include(TEMPLATEPATH . '/shortcode/calorie/bmi-shortcode.php');
// include(TEMPLATEPATH.'/shortcode/calorie/chinese-gender-shortcode.php');
include(TEMPLATEPATH . '/shortcode/calorie/body-fat-shortcode.php');
include(TEMPLATEPATH . '/shortcode/calorie/ideal-weight-shortcode.php');
include(TEMPLATEPATH . '/shortcode/calorie/lean-body-mass-shortcode.php');
// include(TEMPLATEPATH.'/shortcode/calorie/healthy-weight-shortcode.php');
// include(TEMPLATEPATH.'/shortcode/calorie/age-shortcode.php');
// include(TEMPLATEPATH.'/shortcode/calorie/tdee-shortcode.php');
// include(TEMPLATEPATH.'/shortcode/calorie/army-body-fat-shortcode.php');
// include(TEMPLATEPATH.'/shortcode/calorie/absi-shortcode.php');
// include(TEMPLATEPATH.'/shortcode/calorie/adjusted-body-weight-shortcode.php');
// include(TEMPLATEPATH.'/shortcode/calorie/body-adiposity-index-shortcode.php');
include(TEMPLATEPATH . '/shortcode/calorie/bmr-shortcode.php');
include(TEMPLATEPATH . '/shortcode/calorie/repmax-shortcode.php');

// Override Hook Breadcrum
add_filter('wpseo_breadcrumb_links', 'customize_yoast_breadcrumb_ex_links', 20);
function customize_yoast_breadcrumb_ex_links($links)
{
	global $template;

	if (basename($template) == 'single-tool_post.php') {
		$calculator_page = get_pages(array(
			'post_type' => 'page',
			'meta_key' => '_wp_page_template',
			'meta_value' => 'template/calculator.php',
			'number' => 1
		));

		if (!empty($calculator_page)) {
			$page = $calculator_page[0];

			if (isset($links[1])) {
				$clone = $links[1];
				$links[1]['url'] = get_permalink($page->ID);
				$links[1]['text'] = "Tool";
				$links[1]['id'] = $page->ID;

				$links[2] = $clone;
			}
		}
	} else if (basename($template) == 'single-exercise.php') {
		$calculator_page = get_pages(array(
			'post_type' => 'page',
			'meta_key' => '_wp_page_template',
			'meta_value' => 'template/exercise.php',
			'number' => 1
		));

		if (!empty($calculator_page)) {
			$page = $calculator_page[0];

			if (isset($links[1])) {
				$links[1]['url'] = get_permalink($page->ID);
				$links[1]['id'] = $page->ID;
			}
		}
	}



	return $links;
}
function enqueue_searchable_option_list_assets()
{
	wp_enqueue_script(
		'searchable-option-list-js',
		get_template_directory_uri() . '/assets/searchable/jquery.multiselect.js',
		array('jquery'),
		null,
		true
	);

	wp_enqueue_style(
		'searchable-option-list-css',
		get_template_directory_uri() . '/assets/searchable/jquery.multiselect.css',
		array(),
		null
	);
}

add_action('wp_enqueue_scripts', 'enqueue_searchable_option_list_assets');
function enqueue_exercise_search_script()
{
	if (is_page_template('template/exercise.php')) {
		wp_enqueue_script('ajax-search-script', get_template_directory_uri() . '/assets/js/ajax-search.js', array('jquery'), '1.0.3', true);

		wp_localize_script('ajax-search-script', 'exerciseSearch', array(
			'nonce' => wp_create_nonce('search_exercise_nonce'),
			'ajaxurl' => admin_url('admin-ajax.php')
		));
	}
}
add_action('wp_enqueue_scripts', 'enqueue_exercise_search_script');

function search_exercise()
{
	global $wpdb;

	$search_term = !empty($_POST['data']['name']) ? $_POST['data']['name'] : '';

	$additional_condition = '';

	if (!empty($_POST['data']['mt']) && is_array($_POST['data']['mt'])) {
		$mt_conditions = array_map(function ($muscleid) use ($wpdb) {
			return $wpdb->prepare("mt.id = %d", $muscleid);
		}, $_POST['data']['mt']);
		$additional_condition .= ' AND (' . implode(' OR ', $mt_conditions) . ')';
	}

	if (!empty($_POST['data']['eq']) && is_array($_POST['data']['eq'])) {
		$eq_conditions = array_map(function ($equipment_id) use ($wpdb) {
			return $wpdb->prepare("eq.id = %d", $equipment_id);
		}, $_POST['data']['eq']);
		$additional_condition .= ' AND (' . implode(' OR ', $eq_conditions) . ')';
	}

	$query = $wpdb->prepare(
		"
    SELECT DISTINCT e.id AS exercise_id, e.name AS exercise_name, e.image_male AS exercise_image, e.image_female AS exercise_image_female, e.slug AS exercise_slug
    FROM {$wpdb->prefix}exercise AS e
    LEFT JOIN {$wpdb->prefix}exercise_primary_option AS epo ON epo.exercise_id = e.id
    LEFT JOIN {$wpdb->prefix}exercise_muscle_anatomy AS ma ON ma.id = epo.muscle_id
    LEFT JOIN {$wpdb->prefix}exercise_muscle_type AS mt ON mt.id = ma.type_id
    LEFT JOIN {$wpdb->prefix}exercise_equipment_option AS eo ON eo.exercise_id = e.id
    LEFT JOIN {$wpdb->prefix}exercise_equipment AS eq ON eq.id = eo.equipment_id
    WHERE (e.name LIKE %s 
       OR ma.name LIKE %s 
       OR mt.name LIKE %s 
       OR eq.name LIKE %s)
       $additional_condition
	   AND e.slug IS NOT NULL
		AND e.slug != ''
		AND e.active = 1
    ",
		'%' . $search_term . '%',
		'%' . $search_term . '%',
		'%' . $search_term . '%',
		'%' . $search_term . '%'
	);

	$exEquip = "
    SELECT eq.name
    FROM {$wpdb->prefix}exercise_equipment AS eq
    INNER JOIN {$wpdb->prefix}exercise_equipment_option AS eo 
        ON eq.id = eo.equipment_id
    WHERE eo.exercise_id = %d
";

	$exTag = "
    SELECT DISTINCT mt.id, mt.name
    FROM {$wpdb->prefix}exercise_muscle_type AS mt
    INNER JOIN {$wpdb->prefix}exercise_muscle_anatomy AS ma ON ma.type_id = mt.id
    INNER JOIN {$wpdb->prefix}exercise_primary_option AS epo ON epo.muscle_id = ma.id
    WHERE epo.exercise_id = %d
";

	$results = $wpdb->get_results($query, ARRAY_A);

	if (!empty($results)) {
		ob_start();

		foreach ($results as $ex):
			$exID = $ex['exercise_id'];

			$equipment = $wpdb->get_results($wpdb->prepare($exEquip, $exID), ARRAY_A);
			if (!empty($equipment)) {
				$equipment_names = array_column($equipment, 'name');

				$equipment = implode(', ', $equipment_names);
			} else {
				$equipment = '';
			}
			$mtExercises = $wpdb->get_results($wpdb->prepare($exTag, $exID), ARRAY_A);
			?>
			<div class="mt flex">
				<div class="ex-img">
					<a target="_blank" href="<?= home_url('/exercise/' . $ex['exercise_slug']); ?>">
						<img src=" <?= $ex['exercise_image'] ?: $ex['exercise_image_female'] ?>" alt="">
					</a>
				</div>
				<div class="ex-content">
					<a target="_blank" href="<?= home_url('/exercise/' . $ex['exercise_slug']); ?>">
						<p><strong><?= $ex['exercise_name'] ?></strong></p>
					</a>

					<p><strong>Equipment:</strong> <?= $equipment ?></p>
					<div class="flex">
						<?php foreach ($mtExercises as $mtex): ?>
							<span class="has-ssmall-font-size"><?= esc_html($mtex['name']) ?></span>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<?php
		endforeach;
		$output = ob_get_clean();
		wp_send_json_success(
			$output
		);
	} else {
		wp_send_json_success(

		);
	}

}
add_action('wp_ajax_search_exercise', 'search_exercise');
add_action('wp_ajax_nopriv_search_exercise', 'search_exercise');
function enqueue_infinite_scroll_script()
{
	if (is_category()) {
		global $wp_query;

		wp_enqueue_script(
			'infinite-scroll',
			get_template_directory_uri() . '/assets/js/infinite-scroll.js',
			array('jquery'),
			'1.0.0',
			true
		);
	} elseif (is_tag()) {
		global $wp_query;

		wp_enqueue_script(
			'infinite-scroll-tag',
			get_template_directory_uri() . '/assets/js/infinite-tag.js',
			array('jquery'),
			'1.0.0',
			true
		);
	}
}
add_action('wp_enqueue_scripts', 'enqueue_infinite_scroll_script');

function load_more_posts()
{

	$paged = 6;
	$args = $_POST['query_vars'];
	$args['post_type'] = array('post', 'informational_posts', 'round_up', 'single_reviews', 'step_guide');
	$args['posts_per_page'] = 3;

	$paged = $_POST['page'] + 1;
	$args['paged'] = $paged;


	$the_query = new WP_Query($args);

	if ($the_query->have_posts()):
		while ($the_query->have_posts()):
			$the_query->the_post();
			$post_author_id = get_post_field('post_author', get_the_ID());
			$post_display_name = get_the_author_meta('nickname', $post_author_id);
			$post_author_url = get_author_posts_url($post_author_id);
			?>
			<div class="news-it">
				<div class="news-box">
					<div class="featured image-fit hover-scale">
						<?php $image_featured = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())); ?>
						<a href="<?php the_permalink(); ?>">
							<?php if ($image_featured): ?>
								<img src="<?php echo $image_featured; ?>" alt="">
							<?php else: ?>
								<img src="<?php echo get_field('fimg_default', 'option'); ?>" alt="">
							<?php endif; ?>
						</a>
					</div>
					<div class="info">
						<?php $category = get_the_category(get_the_ID()); ?>
						<?php if (!empty($category) && count($category) > 0): ?>
							<div class="tag mr-bottom-16">
								<?php
								foreach ($category as $cat) { ?>
									<span><a href="<?php echo get_term_link($cat->term_id); ?>"><?php echo $cat->name; ?></a></span>
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
	endif;
	die();
}

add_action('wp_ajax_load_more_posts', 'load_more_posts');
add_action('wp_ajax_nopriv_load_more_posts', 'load_more_posts');

function custom_image_sizes_choose($sizes)
{
	unset($sizes['thumbnail']);
	unset($sizes['medium']);
	unset($sizes['large']);

	return array('full' => __('Full Size'));

}

add_filter('image_size_names_choose', 'custom_image_sizes_choose');

/* Replace Year current */
function year_shortcode()
{
	$year = date('Y');
	return $year;
}

add_filter('single_post_title', 'my_shortcode_title');
add_filter('the_title', 'my_shortcode_title');
add_filter('wp_title', 'my_shortcode_title');
function my_shortcode_title($title)
{
	$title = strip_tags($title);
	return do_shortcode($title);
}
add_filter('pre_get_document_title', function ($title) {
	// Make any changes here
	return do_shortcode($title);
}, 999, 1);

add_shortcode('Year', 'year_shortcode');
add_shortcode('year', 'year_shortcode');
/* year seo */
include(TEMPLATEPATH . '/sitemap/sitemap-loader.php');
include(TEMPLATEPATH . '/include/menus.php');
include(TEMPLATEPATH . '/hcfunction/update-modifile-be.php');
add_theme_support('post-thumbnails', array('post', 'page', 'informational_posts', 'round_up', 'single_reviews', 'step_guide'));
/* Script Admin */
function my_script()
{ ?>
	<style type="text/css">
		#dashboard_primary,
		#icl_dashboard_widget,
		#dashboard_right_now #wp-version-message,
		#wpfooter {
			display: none;
		}
	</style>
<?php }
add_action('admin_footer', 'my_script');
function custom_style_login()
{
	?>
	<style type="text/css">
		.login h1 a {
			background-image: url("<?php echo get_template_directory_uri(); ?>/assets/images/endomondo-1.svg");
			background-size: 100% auto;
			height: 35px;
			width: 200px;
		}

		.wp-social-login-provider-list img {
			max-width: 100%;
		}
	</style>
<?php }
add_action('login_head', 'custom_style_login');
/* add css, jquery */
function theme_mcs_scripts()
{
	/* general css */
	wp_enqueue_style('style-slick', get_template_directory_uri() . '/assets/js/slick/slick.css');
	wp_enqueue_style('style-slick-theme', get_template_directory_uri() . '/assets/js/slick/slick-theme.css');
	wp_enqueue_style('style-swiper', get_template_directory_uri() . '/assets/js/swiper/swiper-bundle.min.css');
	wp_enqueue_style('style-main', get_template_directory_uri() . '/assets/css/main.css', '', '1.7.2');
	wp_enqueue_style('style-custom', get_template_directory_uri() . '/assets/css/custom.css', '', '1.4.6');
	wp_enqueue_style('style-base', get_template_directory_uri() . '/assets/css/base.css', '', '1.3.5');
	wp_enqueue_style('tool-css', get_template_directory_uri() . '/shortcode/calorie/assets/css/tool.css', '', '1.0.5');
	wp_enqueue_style('style-element', get_template_directory_uri() . '/assets/css/element.css', '', '1.7.7');
	wp_enqueue_style('style-responsive', get_template_directory_uri() . '/assets/css/responsive.css', '', '1.8.6');
	wp_enqueue_style('style-awesome', get_template_directory_uri() . '/assets/fonts/css/fontawesome.css');
	wp_enqueue_style('style-solid', get_template_directory_uri() . '/assets/fonts/css/solid.css');
	wp_enqueue_style('style-regular', get_template_directory_uri() . '/assets/fonts/css/regular.css');
}
add_action('wp_enqueue_scripts', 'theme_mcs_scripts');

/* register page option ACF */
if (function_exists('acf_add_options_page')) {
	$parent = acf_add_options_page(array(
		'page_title' => 'Website Option',
		'menu_title' => 'Website Option',
		'icon_url' => 'dashicons-image-filter',
	));
	acf_add_options_sub_page(array(
		'page_title' => 'Option',
		'menu_title' => 'Option',
		'parent_slug' => $parent['menu_slug'],
	));
	acf_add_options_sub_page(array(
		'page_title' => 'Sitemap',
		'menu_title' => 'Sitemap',
		'parent_slug' => $parent['menu_slug'],
	));
}
//add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar()
{
	show_admin_bar(false);
}
/* Hide editor not use */
//add_action( 'admin_init', 'hide_editor_not_use' );
function hide_editor_not_use()
{
	if (isset($_GET['post']) && $_POST['post_ID']) {
		$post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'];
		if (!isset($post_id))
			return;

		$template_file = get_post_meta($post_id, '_wp_page_template', true);

		if ($template_file == 'template/home.php') {
			remove_post_type_support('page', 'editor');
		}
	}
}
/* Update date when publish post */
function post_unpublished($new_status, $old_status, $post)
{
	if ($old_status == 'future' && $new_status == 'publish') {
		$update_post = array(
			'ID' => $post->ID,
			'post_modified' => $post->post_date
		);
		wp_update_post($update_post);
	}
}
add_action('transition_post_status', 'post_unpublished', 10, 3);

add_filter('request', 'my_tag_nav');
function my_tag_nav($request)
{
	if (isset($request['post_tag'])) {
		$request['posts_per_page'] = 1;
	}
	return $request;
}

function custom_social_share_buttons_shortcode()
{
	ob_start(); ?>

	<div class="addtoany_share_buttons">
		<?php if (function_exists('ADDTOANY_SHARE_SAVE_KIT')) {
			ADDTOANY_SHARE_SAVE_KIT();
		} ?>
	</div>

	<?php
	return ob_get_clean();
}

function mytheme_comment($comment, $args, $depth)
{
	if ('div' === $args['style']) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	} ?>
	<<?php echo $tag; ?> 	<?php comment_class(empty($args['has_children']) ? '' : 'parent'); ?>
		id="comment-<?php comment_ID() ?>"><?php
		  if ('div' != $args['style']) { ?>
			<div id="div-comment-<?php comment_ID() ?>" class="comment-body"><?php
		  } ?>
			<div class="flex section-header">
				<div class="comment-author vcard"><?php
				if ($args['avatar_size'] != 0) {
					echo get_avatar($comment, $args['avatar_size']);
				}
				printf(__('<cite class="fn">%s</cite>'), get_comment_author_link()); ?>
				</div><?php
				if ($comment->comment_approved == '0') { ?>
					<em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.'); ?></em><br /><?php
				} ?>
				<div class="comment-meta commentmetadata">
					<a href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)); ?>">
						<p class="has-ssmall-font-size"><?php
						/* translators: 1: date, 2: time */
						printf(
							__('%1$s at %2$s'),
							get_comment_date(),
							get_comment_time()
						); ?></p>
					</a><?php
					edit_comment_link(__('(Edit)'), '  ', ''); ?>
				</div>
			</div>

			<div class="cmt-box">
				<?php comment_text(); ?>
			</div>
		</div>
		<div class="reply"><?php
		comment_reply_link(
			array_merge(
				$args,
				array(
					'add_below' => $add_below,
					'depth' => $depth,
					'max_depth' => $args['max_depth']
				)
			)
		); ?>
			<?php
			if ('div' != $args['style']): ?>
			</div>
			<?php
			endif;
}
?>