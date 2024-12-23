<?php
function healthy_weight_call_shortcode($info)
{
	$curl = curl_init();
	$info = json_encode($info);
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://wordpress-1312425-4788967.cloudwaysapps.com/wp-json/api/v1/healthy-weight-calculate/',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => $info,
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
function create_shortcode_tool_healthy_weight($args, $content)
{
	ob_start();
	?>
	<div class="calories-box">
		<div id="spinner"></div>
		<div class="calories-form">
			<div class="content-top">
				<h2>Input Your Information Below </h2>
				<form action="#" class="form lean-body-calculate" id="healthyWeight">
					<div class="form-col">
						<label for="">Height</label>
						<div class="form-input-list two-grid">
							<div class="form-input">
								<input class="input-it" type="text" name="info[height][feet]">
								<p>ft</p>
							</div>
							<div class="form-input">
								<input class="input-it" type="text" name="info[height][inches]">
								<p>in</p>
							</div>
						</div>
					</div>
					<div class="form-col action odd">
						<button id="btn" class="calories-submit btn-primary has-medium-font-size" type="submit">
							Calculate
						</button>
						<button type="btnClear" id="btnClear"
							class="calories-clear calories-submit has-medium-font-size">Clear</button>
					</div>
				</form>
			</div>
			<div class="content-bottom">
			</div>
		</div>
	</div>
	<?php
	$rt = ob_get_clean();
	ob_start();
	get_template_part('template-parts/content', 'enfit');
	$rt .= ob_get_clean();
	wp_enqueue_style('tool-css', get_template_directory_uri() . '/shortcode/calorie/assets/css/tool.css', '', '1.0.0');
	wp_enqueue_script('healthy-weight-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/healthy-weight-tool.js', '', '1.1.9');
	wp_enqueue_script('validate-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/jquery.validate.min.js', '', '1.0.1');
	return $rt;
}
add_shortcode('hc_tool_healthy_weight', 'create_shortcode_tool_healthy_weight');
/* call ajax tool */
function is_ajax_healthy_weight_tool()
{
	return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
add_action('init', 'get_healthy_weight_tool');
function get_healthy_weight_tool()
{
	if (isset($_GET['get_healthy_weight_tool']) && is_ajax_healthy_weight_tool()) {
		$info = $_GET["jsonData"];
		$tool_result = healthy_weight_call_shortcode($info);
		$result = $tool_result->healthy_weight;
		ob_start();
		?>
		<div class="title">
			<h2>Result</h2>
		</div>
		<div class="result">
			<p><?= $result ?></p>
		</div>
		<?php
		$result_get = ob_get_clean();
		echo json_encode($result_get);
		exit;
	}
}