<?php
function rm_call_shortcode($info)
{
	$curl = curl_init();
	$info = json_encode($info);
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://wordpress-1312425-4788967.cloudwaysapps.com/wp-json/api/v1/rep-max-calculate/',
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
function create_shortcode_tool_rm($args, $content)
{
	ob_start();
	?>
	<div class="calories-box">
		<div id="spinner"></div>
		<div class="calories-form">
			<h2>Input Your Information Below</h2>
			<div class="content-top">
				<form action="" id="rmCalculate">
					<div class="form-col">
						<label for="">Weight Lifted</label>
						<div class="form-input rep">
							<input class="input-it" type="text" name="info[weight]">
							<select name="unit">
								<option value="2">Pound</option>
								<option value="1">Kilogram</option>
							</select>
						</div>
						<br />
						<label class="age-label" for="">Repeated</label>
						<div class="form-input rep">
							<input class="input-it" type="text" name="info[rep]">
							<p>times</p>
							<span>( 1-10 )</span>
						</div>
						<br />

						<button type="submit" class="calories-submit has-medium-font-size">Calculate</button>
					</div>
					<div class="form-col">
						<h3>Setting</h3>
						<label for="">Results unit:</label>
						<div class="form-radio-list mb-17 two-grid">
							<div class="form-radio checked">
								<label for="unitCalo">
									<input id="unitCalo" type="radio" name="rsunit" value="2" checked>
									<span class="radio"></span>
									Pound
								</label>
							</div>
							<div class="form-radio">
								<label for="unitKilo">
									<input id="unitKilo" type="radio" name="rsunit" value="1">
									<span class="radio"></span>
									Kilogram
								</label>
							</div>
						</div>
						<div class="form-radio-list form-radio form-radio-list-full">
							<label class="form-radio" for="">1RM estimation formula to use: <img
									src="<?= get_template_directory_uri() . '/shortcode/calorie/assets/images/calories-question.svg' ?>"
									alt=""></label>
							<div class="form-radio checked">
								<label for="bmr1">
									<input id="bmr1" type="radio" name="receip" value="1" checked>
									<span class="radio"></span>
									Epley
								</label>
							</div>
							<div class="form-radio">
								<label for="bmr2">
									<input id="bmr2" type="radio" name="receip" value="2">
									<span class="radio"></span>
									Brzycki
								</label>
							</div>
							<div class="form-radio">
								<label for="bmr3">
									<input id="bmr3" type="radio" name="receip" value="3">
									<span class="radio"></span>
									Lombardi
								</label>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="content-bottom fillResult">
			</div>
		</div>
	</div>
	<?php
	$rt = ob_get_clean();
	ob_start();
	get_template_part('template-parts/content', 'enfit');
	$rt .= ob_get_clean();
	wp_enqueue_style('bmr-css', get_template_directory_uri() . '/shortcode/calorie/assets/css/calorie-tool.css', '', '1.0.1');
	wp_enqueue_script('bmr-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/repmax-tool.js', '', '1.0.1');
	wp_enqueue_script('validate-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/jquery.validate.min.js', '', '1.0.0');
	return $rt;
}
add_shortcode('hc_tool_rm', 'create_shortcode_tool_rm');
/* call ajax tool */
function is_ajax_rm_tool()
{
	return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
add_action('init', 'get_repmax_tool');
function get_repmax_tool()
{
	if (isset($_GET['get_repmax_tool']) && is_ajax_rm_tool()) {
		$info = $_GET["jsonData"];
		$tool_result = rm_call_shortcode($info);
		$result = $tool_result;
		$unit = ($info['rsunit'] == 1) ? "kg" : "lb";
		$rs = '';
		if ($result->repmax) {
			$rs = $result->repmax->{'1'}->w;
		}
		ob_start();
		if ($result):
			?>
			<h2>Result</h2>
			<div class="result-one">
				<div class="main-result">
					<p class="has-large-font-size">Estimated one rep max: <span class
							style="color: #87AA14"><?= $rs . " " . $unit ?></span>
				</div>
				<?php if ($result->repmax): ?>
					<figure class="wp-block-table" style="font-size:16px;font-style:normal;font-weight:500">
						<table class="has-fixed-layout calories-table">
							<thead>
								<tr>
									<th>Repetitions</th>
									<th>Lift Weight</th>
									<th>% of 1RM</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($result->repmax as $keyRm => $item) {
									?>
									<tr>
										<td><?= $keyRm ?></td>
										<td><?= $item->w . " " . $unit ?></td>
										<td><?= $item->pr ?> </td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
						<figcaption class="wp-element-caption">1RM Percentage Chart for Repetitions</figcaption>
					</figure>
				<?php endif; ?>
				<?php if ($result->per): ?>
					<figure class="wp-block-table" style="font-size:16px;font-style:normal;font-weight:500">
						<table class="has-fixed-layout calories-table">
							<thead>
								<tr>
									<th>% of 1RM</th>
									<th>Lift Weight</th>
									<th>Repetitions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($result->per as $keyRm => $itemPer) {
									?>
									<tr>
										<td><?= $keyRm ?></td>
										<td><?= $itemPer->w . " " . $unit ?></td>
										<td><?= $itemPer->rm ?> </td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
						<figcaption class="wp-element-caption">Repetitions at Percentage of 1RM</figcaption>
					</figure>
				<?php endif; ?>
			</div>
			<?php
		endif;
		$result_get = ob_get_clean();
		echo json_encode($result_get);
		exit;
	}
}