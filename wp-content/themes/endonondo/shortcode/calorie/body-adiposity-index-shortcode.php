<?php
function body_adiposity_index_call_shortcode($info) {
	$curl = curl_init();
	$info = json_encode($info);
	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://wordpress-1312425-4788967.cloudwaysapps.com/wp-json/api/v1/body-adiposity-index-calculate/',
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => '',
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => 'POST',
	  CURLOPT_POSTFIELDS =>$info,
	  CURLOPT_HTTPHEADER => array(
	    'Content-Type: application/json'
	  ),
	));
	$response = curl_exec($curl);
	$response = json_decode($response);
	$response = $response->result;
	curl_close($curl);
	return $response;
}
function create_shortcode_tool_body_adiposity_index($args, $content) {
	ob_start();
	?>
	<div id="spinner"></div>
    <div id="calculate">
        <div class="container">
            <div class="wrapper">
                <div class="wrapper__content">
                    <div class="content-top">
                        <form action="#" class="form" id="baiCalculate">
                            <div class="column">
                                <div class="label-wrapper">
                                    <label for="" class="label">Your height</label>
                                </div>
                                <div class="text-wrapper">
                                    <div class="text-wrapper__item us">
                                        <div class="place height-ft">
                                            <input type="text" class="radio-wrapper__btn" value="" name="info[height][feet]" id="heightFt">
                                            <div class="place-holder">
                                                <span>ft</span>
                                            </div>
                                        </div>
                                        <div class="place height-in">
                                            <input type="text" class="radio-wrapper__btn" value="" name="info[height][inches]" id="heightIn">
                                            <div class="place-holder">
                                                <span>in</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="height-error error"></span>
                                </div>
                            </div>
                            <div class="column">
                                <div class="label-wrapper">
                                    <label for="" class="label">Your hip circumference</label>
                                </div>
                                <div class="text-wrapper">
                                    <div class="text-wrapper__item us">
                                        <div class="place hip-ft">
                                            <input type="text" class="radio-wrapper__btn" value="" name="info[hip][feet]" id="hipFt">
                                            <div class="place-holder">
                                                <span>ft</span>
                                            </div>
                                        </div>
                                        <div class="place hip-in">
                                            <input type="text" class="radio-wrapper__btn" value="" name="info[hip][inches]" id="hipIn">
                                            <div class="place-holder">
                                                <span>in</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="hip-error error"></span>
                                </div>
                            </div>
                            <div class="action">
                                <button id="btnBAI"  class="btn-primary" type="submit">
                                    Calculate
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="content-bottom">
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php 
	$rt = ob_get_clean();
	ob_start();
	get_template_part('template-parts/content', 'enfit');
	$rt .= ob_get_clean();
	wp_enqueue_style( 'tool-css', get_template_directory_uri() . '/shortcode/calorie/assets/css/tool.css','','1.0.0');
	wp_enqueue_script( 'body-adiposity-index-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/body-adiposity-index-tool.js','','1.0.0');
	wp_enqueue_script( 'validate-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/jquery.validate.min.js','','1.0.1');
	return $rt;
}
add_shortcode( 'hc_tool_body_adiposity_index', 'create_shortcode_tool_body_adiposity_index' );
/* call ajax tool */
function is_ajax_body_adiposity_index_tool(){
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
add_action('init', 'get_body_adiposity_index_tool');
function get_body_adiposity_index_tool() {
		if(isset($_GET['get_body_adiposity_index_tool']) && is_ajax_body_adiposity_index_tool()){
		$info = $_GET["jsonData"];
		$tool_result = body_adiposity_index_call_shortcode($info);
		$result = $tool_result->bai;
		ob_start();
		?>
            <div class="title">
				<h2>Result</h2>
			</div>
			<div class="result">
                <div class="main-result">
                    <p>Your body adiposity index: <span class="primary-title"><?=$result->percent?></span> %</p>
                </div>
            </div>
        <?php
		$result_get = ob_get_clean();
		echo json_encode($result_get);
		exit;
	}
}