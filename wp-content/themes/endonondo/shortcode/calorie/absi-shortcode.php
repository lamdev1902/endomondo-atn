<?php
function absi_call_shortcode($info) {
	$curl = curl_init();
	$info = json_encode($info);
	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://wordpress-1312425-4788967.cloudwaysapps.com/wp-json/api/v1/absi-calculate/',
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
function create_shortcode_tool_absi($args, $content) {
	ob_start();
	?>
	<div id="spinner"></div>
    <div id="calculate">
        <div class="container">
            <div class="wrapper">
                <div class="wrapper__content">
                    <div class="content-top">
                        <form action="#" class="form" id="absiCalculate">
                            <div class="column">
                                <div class="label-wrapper img">
                                    <label for="male" class="label">Gender</label>
                                </div>
                                <div class="radio-wrapper">
                                    <div class="radio-wrapper__item">
                                        <input type="radio" checked class="radio-wrapper__btn" value="1" name="info[gender]" id="male">
                                        <label for="male" class="radio-wrapper__label">
                                            <span class="radio-visibility"></span>
                                            Male
                                        </label>
                                    </div>
                                    <div class="radio-wrapper__item">
                                        <input type="radio" class="radio-wrapper__btn" value="2" name="info[gender]" id="female">
                                        <label for="female" class="radio-wrapper__label">
                                            <span class="radio-visibility"></span>
                                            Female
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="column">
                                <div class="label-wrapper">
                                    <label for="male" class="label">Age</label>
                                </div>
                                <div class="text-wrapper">
                                    <div class="text-wrapper__item only-one">
                                        <input type="text"  class="" value="" name="info[age]" id="age">
                                        <div class="place-holder">
                                            <span>Years</span>
                                        </div>
                                    </div>
                                    <span style="" class="age-error error"></span>
                                </div>
                            </div>
                            <div class="column">
                                <div class="label-wrapper">
                                    <label for="male" class="label">Weight</label>
                                </div>
                                <div class="text-wrapper">
                                        <div class="text-wrapper__item only-one">
                                            <input type="text"  class="" value="" name="info[weight]" id="weight">
                                            <div class="place-holder">
                                                <span>Pounds</span>
                                            </div>
                                        </div>
                                        <span style="" class="weight-error error"></span>
                                    </div>
                            </div>
                            <div class="column">
                                <div class="label-wrapper">
                                    <label for="" class="label">Height</label>
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
                                    <label for="" class="label">Waist</label>
                                </div>
                                <div class="text-wrapper">
                                    <div class="text-wrapper__item us">
                                        <div class="place waist-ft">
                                            <input type="text" class="radio-wrapper__btn" value="" name="info[waist][feet]" id="waistFt">
                                            <div class="place-holder">
                                                <span>ft</span>
                                            </div>
                                        </div>
                                        <div class="place waist-in">
                                            <input type="text" class="radio-wrapper__btn" value="" name="info[waist][inches]" id="waistIn">
                                            <div class="place-holder">
                                                <span>in</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="waist-error error"></span>
                                </div>
                            </div>
                            <div class="action">
                                <button id="btnAbsi"  class="btn-primary" type="submit">
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
	wp_enqueue_script( 'absi-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/absi-tool.js','','1.0.0');
	wp_enqueue_script( 'validate-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/jquery.validate.min.js','','1.0.1');
	return $rt;
}
add_shortcode( 'hc_tool_absi', 'create_shortcode_tool_absi' );
/* call ajax tool */
function is_ajax_absi_tool(){
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
add_action('init', 'get_absi_tool');
function get_absi_tool() {
		if(isset($_GET['get_absi_tool']) && is_ajax_absi_tool()){
		$info = $_GET["jsonData"];
		$tool_result = absi_call_shortcode($info);
		$result = $tool_result->absi;
		ob_start();
		?>
            <div class="title">
				<h2>Result</h2>
			</div>
			<div class="result">
                <div class="main-result">
                    <p>ABSI: <span class="primary-title"><?=$result->score?></span></p>
                    <p>ABSI Z-Score: <span class="primary-title"><?=$result->zscore?></span></p>
                    <p>According to your ABSI z score, your premature mortality risk is <span class="primary-title"><?= $result->description ?></span></p>
                </div>
            </div>
        <?php
		$result_get = ob_get_clean();
		echo json_encode($result_get);
		exit;
	}
}