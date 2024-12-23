<?php
function chinese_gender_call_shortcode($info) {
	$curl = curl_init();
	$info = json_encode($info);
	curl_setopt_array($curl, array(
	  CURLOPT_URL => 'https://wordpress-1312425-4788967.cloudwaysapps.com/wp-json/api/v1/chinese-gender-calculate/',
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
function create_shortcode_tool_chinese_gender($args, $content) {
	ob_start();
	?>
    <div id="calculate">
        <div class="container">
			<div id="spinner"></div>
            <div class="wrapper">
                <div class="wrapper__content">
				<div class="content-top">
					<h4>Input Your Information Below </h4>
                    <form action="#" class="form" id="chineseGender">
                        <div class="form-row">
							<div class="label">Date of Conception (Western)</div>
                            <div class="date">
                                <input name="dd" id="dueDatepicker">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="label">Mother's Date of Birth (Western)</div>
                            <div class="date">
                                <input name="dob" readonly id="dobDatepicker">
                            </div>
                        </div>
                        <div class="action"><button id="btnGender" class="btn-primary"  type="submit">See Prediction</button>
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
	wp_enqueue_style( 'tool-css', get_template_directory_uri() . '/shortcode/calorie/assets/css/tool.css', '', '1.4.0');
	wp_enqueue_script( 'chinese-gender-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/chinese-gender-tool.js', '', '1.1.0');
	wp_enqueue_script( 'validate-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/jquery.validate.min.js','','1.0.1');
	return $rt;
}
add_shortcode( 'hc_tool_chinese_gender', 'create_shortcode_tool_chinese_gender' );
/* call ajax tool */
function is_ajax_chinese_gender_tool(){
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
add_action('init', 'get_chinese_gender_tool');
function get_chinese_gender_tool() {
		if(isset($_GET['get_chinese_gender_tool']) && is_ajax_chinese_gender_tool()){
		$info = $_GET["jsonData"];
		$tool_result = chinese_gender_call_shortcode($info);
		$age = ($tool_result->age) ? $tool_result->age : "";
		$lunar = ($tool_result->lunar) ? $tool_result->lunar : "";
		$dob = ($tool_result->dob) ? $tool_result->dob : "";
		$due = ($tool_result->due) ? $tool_result->due : "";
		ob_start();
		?>
		<div class="title result-row">
            <h2>Result</h2>
        </div>
		<div class="chinese-convert">
			<?php if($age) :?>
				<p class="chinese-title">Lunar Age of Mother: <?= $age ?></p>
			<?php endif; ?>
			<?php if($due) :?>
				<div class="lunar">
					<p class="lunar-title">Date of Conception (Lunar)</p>
					<p class="exact-age"><?=$due->month.'st'. ' Lunar Month, ' . $due->day.'th'.' Day, ' . $due->year . ' Year'?></p>
				</div>
			<?php endif; ?>
			<?php if($dob) :?>
				<div class="lunar">
					<p class="lunar-title">Mother's Date of Birth (Lunar)</p>
					<p class="exact-age"><?=$dob->month.'st'. ' Lunar Month, ' . $dob->day.'th'.' Day, ' . $dob->year . ' Year'?></p>
				</div>
			<?php endif; ?>
		</div>
		<div class="result chinese-gender flex-column">
			<p class="label">It's a <?= $tool_result->gender ?></p>
			<p>Congratulations! According to the legendary Chinese Gender Chart, you're having a <?= $tool_result->gender ?></p>

			<div class="img-gender">
				<?php if($tool_result->gender == "Boy"):?>
					<img style="width:100%;" src="<?=get_template_directory_uri() . '/shortcode/calorie/assets/images/male.svg'?>" alt="" class="">
				<?php else: ?>
					<img style="width:100%;" src="<?=get_template_directory_uri() . '/shortcode/calorie/assets/images/female.svg'?>" alt="" class="">
				<?php endif ?>
			</div>
		</div>
		<?php
		$result_get = ob_get_clean();
		echo json_encode($result_get);
		exit;
	}
}