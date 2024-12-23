<?php
function bmr_call_shortcode($info)
{
	$curl = curl_init();
	$info = json_encode($info);
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://wordpress-1312425-4788967.cloudwaysapps.com/wp-json/api/v1/bmr-calculate/',
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
function create_shortcode_tool_bmr($args, $content)
{
	ob_start();
	?>
	<div class="calories-box">
		<div id="spinner"></div>
		<div class="calories-form">
			<h2>Input Your Information Below </h2>
			<div class="content-top">
				<form action="" id="bmrCalculate">
					<div class="form-col">
						<label for="">Gender <img
								src="<?= get_template_directory_uri() . '/shortcode/calorie/assets/images/calories-note.svg' ?>"
								alt=""></label>
						<div class="form-radio-list form-radio two-grid">
							<div class="form-radio checked">
								<label for="genderMale"><input type="radio" name="info[gender]" value="1" id="genderMale"
										checked>
									<span class="radio"></span>
									Male
								</label>
							</div>
							<div class="form-radio">
								<label for="genderFemale">
									<input type="radio" name="info[gender]" value="2" id="genderFemale">
									<span class="radio"></span>
									Female
								</label>
							</div>
						</div>
					</div>
					<div class="form-col">
						<label for="">Weight</label>
						<div class="form-input">
							<input class="input-it" type="text" name="info[weight]">
							<p>pounds</p>
						</div>
					</div>
					<div class="form-col">
						<label class="age-label" for="">Age</label>
						<div class="form-input">
							<input class="input-it" type="text" name="info[age]">
							<p>years</p>
						</div>
					</div>
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
					<div class="form-col">
						<h3>+ Setting</h3>
						<label for="">Results unit:</label>
						<div class="form-radio-list mb-17 two-grid">
							<div class="form-radio checked">
								<label for="unitCalo">
									<input id="unitCalo" type="radio" name="unit" value="1" checked>
									<span class="radio"></span>
									Calories
								</label>
							</div>
							<div class="form-radio">
								<label for="unitKilo">
									<input id="unitKilo" type="radio" name="unit" value="2">
									<span class="radio"></span>
									Kilojoules
								</label>
							</div>
						</div>
						<div class="form-radio-list form-radio form-radio-list-full">
							<label class="form-radio" for="">BMR estimation formula: <img
									src="<?= get_template_directory_uri() . '/shortcode/calorie/assets/images/calories-question.svg' ?>"
									alt=""></label>
							<div class="form-radio checked">
								<label for="bmr1">
									<input id="bmr1" type="radio" name="receip" value="1" checked>
									<span class="radio"></span>
									Mifflin St Joer
								</label>
							</div>
							<div class="form-radio">
								<label for="bmr2">
									<input id="bmr2" type="radio" name="receip" value="2">
									<span class="radio"></span>
									Revised Harris Benedict
								</label>
							</div>
							<div class="form-radio">
								<label for="bmr3">
									<input id="bmr3" type="radio" name="receip" value="3">
									<span class="radio"></span>
									Katch - McArdle
								</label>
							</div>
							<div class="input-body" style="opacity: 0">
								<label for="">Body Fat: </label>
								<input class="input-it" type="text" placeholder=" %" name="info[body-fat]">
							</div>
						</div>
					</div>
					<div class="form-col action">
						<button type="submit" class="calories-submit has-medium-font-size">Calculate</button>
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
	wp_enqueue_style('bmr-css', get_template_directory_uri() . '/shortcode/calorie/assets/css/calorie-tool.css', '', '1.0.0');
	wp_enqueue_script('bmr-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/bmr-tool.js', '', '1.0.1');
	wp_enqueue_script('validate-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/jquery.validate.min.js', '', '1.0.0');
	return $rt;
}
add_shortcode('hc_tool_bmr', 'create_shortcode_tool_bmr');
/* call ajax tool */
function is_ajax_bmr_tool()
{
	return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
add_action('init', 'get_bmr_tool');
function get_bmr_tool()
{
	if (isset($_GET['get_bmr_tool']) && is_ajax_bmr_tool()) {
		$info = $_GET["jsonData"];
		$tool_result = bmr_call_shortcode($info);
		$result = $tool_result->bmr;
		$unit = ($info['unit'] == 1) ? "Calories/day" : "Kj/day";
		ob_start();
		if ($result->calorie):
			?>
			<h2>Result</h2>
			<div class="result-one">
				<div class="main-result">
					<p class="has-large-font-size">BMR: <span class style="color: #87AA14"><?= $result->calorie ?></span>
						<?= $unit ?></p>
					<p>Daily calorie needs based on activity level.</p>
				</div>
				<figure class="wp-block-table calories-table">
					<table>
						<thead>
							<tr>
								<th>Activity Level</th>
								<th>Calorie</th>
							</tr>
						</thead>
						<?php if ($result->activity) { ?>
							<tbody>
								<?php foreach ($result->activity as $item) {
									?>
									<tr>
										<td><?= $item->name ?></td>
										<td><?= $item->calorie ?> </td>
									</tr>
								<?php } ?>
							</tbody>
						<?php } ?>
					</table>
				</figure>
				<div class="description">
					<ul class="description-list">
						<li><strong>Exercise:</strong> 15-30 minutes of elevated heart rate activity.</li>
						<li><strong>Intense exercise:</strong> 45-120 minutes of elevated heart rate activity.</li>
						<li><strong>Very intense exercise:</strong> 2+ hours of elevated heart rate activity.</li>
					</ul>
				</div>
			</div>
			<?php
		endif;
		$result_get = ob_get_clean();
		echo json_encode($result_get);
		exit;
	}
}