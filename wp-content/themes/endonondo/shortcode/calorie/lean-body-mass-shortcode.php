<?php
function lean_body_mass_call_shortcode($info)
{
    $curl = curl_init();
    $info = json_encode($info);
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://wordpress-1312425-4788967.cloudwaysapps.com/wp-json/api/v1/lean-body-mass-calculate/',
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
function create_shortcode_tool_lean_body_mass($args, $content)
{
    ob_start();
    ?>
    <div class="calories-box">
        <div id="spinner"></div>
        <div class="calories-form">
            <div class="content-top">
                <h2>Input Your Information Below </h2>
                <form action="#" class="form lean-body-calculate" id="leanBodyMass">
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
                        <label for="">Age 14 or younger</label>
                        <div class="form-radio-list form-radio gender-radio two-grid">
                            <div class="form-input checked">
                                <label for="age1">
                                    <input type="radio" name="info[age]" value="1" id="age1" checked>
                                    <span class="radio"></span>
                                    Yes
                                </label>
                            </div>
                            <div class="form-input">
                                <label for="age2">
                                    <input type="radio" name="info[age]" value="2" id="age2">
                                    <span class="radio"></span>
                                    No
                                </label>
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
                    <div class="form-col action odd">
                        <button id="btn" class="calories-submit btn-primary has-medium-font-size" type="submit">
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
    wp_enqueue_style('tool-css', get_template_directory_uri() . '/shortcode/calorie/assets/css/tool.css', '', '1.0.0');
    wp_enqueue_script('lean-body-mass-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/lean-body-mass-tool.js', '', '1.0.1');
    wp_enqueue_script('validate-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/jquery.validate.min.js', '', '1.0.0');
    return $rt;
}
add_shortcode('hc_tool_lean_body_mass', 'create_shortcode_tool_lean_body_mass');
/* call ajax tool */
function is_ajax_lean_body_mass_tool()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
add_action('init', 'get_lean_body_mass_tool');
function get_lean_body_mass_tool()
{
    if (isset($_GET['get_lean_body_mass_tool']) && is_ajax_lean_body_mass_tool()) {
        $info = $_GET["jsonData"];
        $tool_result = lean_body_mass_call_shortcode($info);
        $result = $tool_result->lean_body_mass;
        ob_start();
        ?>
        <h2>Result</h2>
        <div class="result-one">
            <figure class="wp-block-table calories-table">
                <table>
                    <thead>
                        <tr>
                            <th>Formular</th>
                            <th>Lean Body</th>
                            <th>Body Fat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result as $item): ?>
                            <tr>
                                <td><?= $item->title ?></td>
                                <td><?= $item->score . " ( " . $item->percent . "% )" ?></td>
                                <td><?= $item->body_fat." %"?></td>
                            </tr>
                        <?php endforeach; ?>
                </table>
            </figure>
        </div>
        <?php
        $result_get = ob_get_clean();
        echo json_encode($result_get);
        exit;
    }
}