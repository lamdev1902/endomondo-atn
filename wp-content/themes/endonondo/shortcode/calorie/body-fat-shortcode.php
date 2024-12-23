<?php
function body_fat_call_shortcode($info)
{
	$curl = curl_init();
	$info = json_encode($info);
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://wordpress-1312425-4788967.cloudwaysapps.com/wp-json/api/v1/body-fat-calculate/',
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
function create_shortcode_tool_body_fat($args, $content)
{
	$postid = get_the_ID();

	$des = get_field('tool_des', $postid);
	ob_start();
	?>
	<div class="calories-box" class>
		<div id="spinner"></div>
		<div class="calories-form">
			<div class="content-top">
				<h2>Input Your Information Below </h2>
				<form action="#" class="form lean-body-calculate" id="bodyFat">
					<div class="form-col">
						<label for="">Gender <img src="assets/images/calories-note.svg" alt=""></label>
						<div class="form-radio-list form-radio gender-radio two-grid">
							<div class="form-input checked">
								<label for="genderMale">
									<input type="radio" name="info[gender]" value="1" id="genderMale" checked>
									<span class="radio"></span>
									Male
								</label>
							</div>
							<div class="form-input">
								<label for="genderFemale">
									<input type="radio" name="info[gender]" value="2" id="genderFemale">
									<span class="radio"></span>
									Female
								</label>
							</div>
						</div>
					</div>
					<div class="form-col">
						<div class="form-col">
							<label for="">Age</label>
							<div class="form-input">
								<input class="input-it" type="text" name="info[age]">
								<p>years</p>
							</div>
						</div>
					</div>
					<div class="form-col">
						<label for="">Weight</label>
						<div class="form-input">
							<input class="input-it" type="text" name="info[weight]">
							<p class="">pounds</p>
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
						<label for="" class="label">Neck</label>
						<div class="form-input-list two-grid">
							<div class="form-input">
								<input type="text" class="radio-wrapper__btn" value="" name="info[neck][feet]" id="neckFt">
								<p>ft</p>
							</div>
							<div class="form-input">
								<input type="text" class="radio-wrapper__btn" value="" name="info[neck][inches]"
									id="neckIn">
								<p>in</p>
							</div>
						</div>
						<span class="neck-error error"></span>
					</div>
					<div class="form-col">
						<label for="" class="label">Waist</label>
						<div class="form-input-list two-grid">
							<div class="form-input">
								<input type="text" class="radio-wrapper__btn" value="" name="info[waist][feet]"
									id="waistFt">
								<p>ft</p>
							</div>
							<div class="form-input">
								<input type="text" class="radio-wrapper__btn" value="" name="info[waist][inches]"
									id="waistIn">
								<p>in</p>
							</div>
						</div>
						<span class="waist-error error"></span>
					</div>
					<div class="form-col hip inactive">
						<label for="" class="label">Hip</label>
						<div class="form-input-list two-grid">
							<div class="form-input">
								<input type="text" class="radio-wrapper__btn" value="" name="info[hip][feet]" id="hipFt">
								<p>ft</p>
							</div>
							<div class="form-input">
								<input type="text" class="radio-wrapper__btn" value="" name="info[hip][inches]" id="hipIn">
								<p>in</p>
							</div>
						</div>
						<span class="hip-error error"></span>
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
			<div class="fillResult content-bottom <?= $des ? 'bdbottom' : ''?> ">
				<?php if ($des): ?>
					<div class="empty-result">
						<img src="<?= get_template_directory_uri() . '/shortcode/calorie/assets/images/empty-result-2.svg' ?>"
							alt="Empty Result">
						<p class="mr-top-16"><?=$des?></p>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
	$rt = ob_get_clean();
	ob_start();
	get_template_part('template-parts/content', 'enfit');
	$rt .= ob_get_clean();
	wp_enqueue_script('body-fat-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/body-fat-tool.js', '', '1.0.5');
	wp_enqueue_script('validate-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/jquery.validate.min.js', '', '1.0.0');
	return $rt;
}
add_shortcode('hc_tool_body_fat', 'create_shortcode_tool_body_fat');
/* call ajax tool */
function is_ajax_body_fat_tool()
{
	return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
add_action('init', 'get_body_fat_tool');
function get_body_fat_tool()
{
	if (isset($_GET['get_body_fat_tool']) && is_ajax_body_fat_tool()) {
		$info = $_GET["jsonData"];
		$tool_result = body_fat_call_shortcode($info);
		$result_bfp = $tool_result->bfp;
		$item = 1;
		ob_start();
		?>
		<div class="title">
			<h2>Result</h2>
		</div>
		<div class="result">
			<div class="result-left">
				<div class="main-result">
					<p class="has-large-font-size">
						Body Fat: <?= $result_bfp->navy_method->percent ?> %
					</p>
				</div>
				<div class="range" style="position: relative">
					<img src="<?= get_template_directory_uri() . '/shortcode/calorie/assets/images/rule.svg' ?> " alt=""
						class="" style="width: 100%;">
					<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="80%" height="100%"
						viewBox="0 0 300 163">
						<g transform="translate(9,20)" style="font-family:arial,helvetica,sans-serif;font-size: 12px;">
							<defs>
								<marker id="arrowhead" markerWidth="10" markerHeight="7" refX="0" refY="3.5" orient="auto">
									<polygon points="0 0, 10 3.5, 0 7"></polygon>
								</marker>
								<path id="curvetxt1" d="M67 104 A140 140, 0, 0, 1, 320 130" style="fill: none;"></path>
								<path id="curvetxt2" d="M114 61 A140 140, 0, 0, 1, 277 140" style="fill: #none;"></path>
								<path id="curvetxt3" d="M147 57 A140 140, 0, 0, 1, 245 142" style="fill: #none;"></path>
								<path id="curvetxt4" d="M197 81 A140 140, 0, 0, 1, 225 140" style="fill: #none;"></path>
							</defs>
							<path d="M0 140 A140 140, 0, 0, 1, 77.9 15 L140 140 Z" fill="#9F9F9F"></path>
							<path d="M88 10.2 A140 140, 0, 0, 1, 77.9 14.9 L140 140 Z" fill="#D5E1C3"></path>
							<!-- <path d="M6.9 96.7 A140 140, 0, 0, 1, 12.1 83.1 L140 140 Z" fill="#d38888"></path> -->
							<path d="M92.7 8.4 A140 140, 0, 0, 1, 88 10.2 L140 140 Z" fill="#E8FF99"></path>
							<!-- <path d="M12.1 83.1 A140 140, 0, 0, 1, 22.6 63.8 L140 140 Z" fill="#ffe400"></path> -->
							<!-- <path d="M22.6 63.8 A140 140, 0, 0, 1, 96.7 6.9 L140 140 Z" fill="#008137"></path> -->
							<path d="M92.2 8.7 A140 140, 0, 0, 1, 165.5 2.4 L140 140 Z" fill="#C5F621"></path>
							<!-- <path d="M96.7 6.9 A140 140, 0, 0, 1, 169.1 3.1 L140 140 Z" fill="#ffe400"></path> -->
							<path d="M165.5 2.3 A140 140, 0, 0, 1, 210.7 19 L140 140 Z" fill="#A9D41A"></path>
							<path d="M210.7 19 A140 140, 0, 0, 1, 248.1 50 L140 140 Z" fill="#87AA14"></path>
							<path d="M248 50 A140 140, 0, 0, 1, 272.1 92.7 L140 140 Z" fill="#3E4D0B"></path>
							<!-- <path d="M273.1 96.7 A140 140, 0, 0, 1, 280 140 L140 140 Z" fill="#8a0101"></path> -->
							<path d="M271.5 91 A140 140, 0, 0, 1, 280 140 L140 140 Z" fill="#404040" class=""></path>
							<path d="M45 140 A90 90, 0, 0, 1, 230 140 Z" fill="#fff" class=""></path>
							<circle cx="140" cy="140" r="5" fill="#666"></circle>
							<g style="paint-order: stroke;stroke: #fff;stroke-width: 2px; font-size: 10px;">
								<text x="108" y="90" transform="rotate(-27, 24, 108)">16</text>
								<text x="114" y="71" transform="rotate(-27, 30, 96)">17</text>
								<text x="97" y="20" transform="rotate(-18, 97, 29)">18.5</text>
								<text x="157" y="20" transform="rotate(12, 157, 20)">25</text>
								<text x="204" y="20" transform="rotate(35, 181, 17)">30</text>
								<text x="236" y="45" transform="rotate(42, 214, 45)">35</text>
								<text x="252" y="95" transform="rotate(72, 252, 95)">40</text>
							</g>
							<line x1="140" y1="140" x2="65" y2="140" stroke="#666" stroke-width="2"
								marker-end="url(#arrowhead)">
								<animateTransform attributeName="transform" attributeType="XML" type="rotate" from="0 140 140"
									to="<?= $result_bfp->navy_method->percent * 3.7 ?> 140 140" dur="1s" fill="freeze"
									repeatCount="1"></animateTransform>
							</line>
						</g>
					</svg>
				</div>
			</div>
			<div class="result-right">
				<ul>
					<?php foreach ($result_bfp as $bfp): ?>
						<?php
						if (isset($bfp->percent)) {
							$value = $bfp->percent . " %";
						} else if (isset($bfp->pounds)) {
							$value = $bfp->pounds . " lbs";
						} else {
							$value = $bfp->type;
						}
						?>
						<?php $itemClass = ($item % 2 == 0) ? 'item-1' : 'item-2'; ?>
						<li>
							<p><?= $bfp->title ?></p>
							<p class="has-medium-font-size pri-color-1"><?= $value ?></p>
						</li>
						<?php $item++; endforeach; ?>
				</ul>
			</div>
		</div>
		<?php
		$result_get = ob_get_clean();
		echo json_encode($result_get);
		exit;
	}
}