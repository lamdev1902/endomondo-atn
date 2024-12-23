<?php
function ideal_weight_call_shortcode($info)
{
    $curl = curl_init();
    $info = json_encode($info);
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://wordpress-1312425-4788967.cloudwaysapps.com/wp-json/api/v1/ideal-weight-calculate/',
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
function create_shortcode_tool_ideal_weight($args, $content)
{
    ob_start();
    ?>
    <div class="calories-box">
        <div id="spinner"></div>
        <div class="calories-form">
            <div class="content-top">
                <h2>Input Your Information Below </h2>
                <form action="#" class="form" id="idealWeight">
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
                    <div class="action odd special-col">
                        <button id="btn" class="btn-primary calories-submit has-medium-font-size" type="submit">
                            Calculate
                        </button>
                        <button type="button" id="btnClear"
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
    wp_enqueue_script('ideal-weight-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/ideal-weight-tool.js', '', '1.1.9');
    wp_enqueue_script('validate-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/jquery.validate.min.js', '', '1.0.1');
    return $rt;
}
add_shortcode('hc_tool_ideal_weight', 'create_shortcode_tool_ideal_weight');
/* call ajax tool */
function is_ajax_ideal_weight_tool()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
add_action('init', 'get_ideal_weight_tool');
function get_ideal_weight_tool()
{
    if (isset($_GET['get_ideal_weight_tool']) && is_ajax_ideal_weight_tool()) {
        $info = $_GET["jsonData"];
        $tool_result = ideal_weight_call_shortcode($info);
        $result = $tool_result->ideal_weight;
        ob_start();
        ?>
        <h2>Result</h2>
        <div class="result-one">
            <p>The ideal weight based on popular formulas:</p>
            <figure class="wp-block-table calories-table">
                <table>
                    <thead>
                        <tr>
                            <th>Formula</th>
                            <th>Ideal Weight</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result as $item): ?>
                            <tr>
                                <td><?= $item->title ?></td>
                                <td><?= $item->pounds . " lbs" ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </figure>
        </div>
        <?php
        $result_get = ob_get_clean();
        echo json_encode($result_get);
        exit;
    }
}