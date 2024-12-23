<?php
function army_body_fat_call_shortcode($info)
{
    $curl = curl_init();
    $info = json_encode($info);
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://wordpress-1312425-4788967.cloudwaysapps.com/wp-json/api/v1/army-body-fat-calculate/',
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
function create_shortcode_tool_army_body_fat($args, $content)
{
    ob_start();
    ?>
    <div class="calories-box">
        <div id="spinner"></div>
        <div class="calories-form">
            <div class="content-top">
                <h2>Input Your Information Below </h2>
                <form action="#" class="form lean-body-calculate" id="armyBodyFat">
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
    wp_enqueue_script('army-body-fat-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/army-body-fat-tool.js', '', '1.0.0');
    wp_enqueue_script('validate-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/jquery.validate.min.js', '', '1.0.1');
    return $rt;
}
add_shortcode('hc_tool_army_body_fat', 'create_shortcode_tool_army_body_fat');
/* call ajax tool */
function is_ajax_army_body_fat_tool()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
add_action('init', 'get_army_body_fat_tool');
function get_army_body_fat_tool()
{
    if (isset($_GET['get_army_body_fat_tool']) && is_ajax_army_body_fat_tool()) {
        $info = $_GET["jsonData"];
        $tool_result = army_body_fat_call_shortcode($info);
        $result = $tool_result->army_bodyfat;

        $score = $result->score;
        $description = $result->description;

        unset($result->score);
        unset($result->description);
        ob_start();
        ?>
        <div class="title">
            <h2>Result</h2>
        </div>
        <div class="result">
            <div class="main-result">
                <h3>Body Fat: <span class="primary-title"><?= $score ?>%</span> <?= $description ?></h3>
            </div>
            <?php if ($info['info']['gender'] == 1 && $result): ?>
                <div class="result">
                    <div class="flex-column lean-body-table">
                        <div class="goals">
                            <table class="lean-body">
                                <tbody>
                                    <tr>
                                        <td>Goal</td>
                                        <td>Body Fat Reduction Needed</td>
                                        <td>Equivalent Body Fat Reduction for a 140-Pound Person</td>
                                    </tr>
                                    <?php foreach ($result as $item): ?>
                                        <tr>
                                            <td><?= $item->title ?></td>
                                            <td><?= $item->percent . " %" ?></td>
                                            <td><?= $item->pounds . " pounds" ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
        $result_get = ob_get_clean();
        echo json_encode($result_get);
        exit;
    }
}