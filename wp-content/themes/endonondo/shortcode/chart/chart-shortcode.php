<?php
function create_shortcode_chart($args, $content) {
	ob_start();
	$pname = $args['name'];
 	$sargs = array(
        'post_type'         => 'static_short',
        'posts_per_page'     => '1',
        'title' => $pname,
        'post_status' => 'publish',
    );
	$sposts = get_posts($sargs);
	if($sposts && count($sposts) > 0) {
		$pid = $sposts[0]->ID;
		$type = get_field('sc_type',$pid);
		$vertical_bar_data = get_field('vertical_bar_data',$pid);
		$max_value = $vertical_bar_data['max_value'];
		$xlabel = $vertical_bar_data['x_label'];
		$datas = $vertical_bar_data['data_list'];
		if($type == 'vertical bar') INCLUDE(TEMPLATEPATH .'/shortcode/chart/chart-vertical-bar.php');
		if($type == 'horizontal bar') INCLUDE(TEMPLATEPATH .'/shortcode/chart/chart-horizontal-bar.php');
		if($type == 'pie') INCLUDE(TEMPLATEPATH .'/shortcode/chart/chart-pie.php');
		if($type == 'line') INCLUDE(TEMPLATEPATH .'/shortcode/chart/chart-line.php');
	} 
	$rt = ob_get_clean();
	wp_enqueue_style( 'chart-css', get_template_directory_uri() . '/shortcode/chart/chart-custom.css','','1.4.7');
	//wp_enqueue_script( 'chart-js', get_template_directory_uri() . '/shortcode/chart/chart-custom.js','','1.1.1');
	wp_enqueue_script( 'chart-library-js', get_template_directory_uri() . '/shortcode/chart/chartjs/chart.js','','1.0.1');
	wp_enqueue_script( 'chart-database-library-js', get_template_directory_uri() . '/shortcode/chart/chartjs/chartjs-plugin-datalabels@2.js','','1.0.1');
	return $rt;
}
add_shortcode( 'chart', 'create_shortcode_chart' );