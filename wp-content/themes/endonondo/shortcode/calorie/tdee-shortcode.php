<?php
function tdee_call_shortcode($info)
{
    $curl = curl_init();
    $info = json_encode($info);
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://wordpress-1312425-4788967.cloudwaysapps.com/wp-json/api/v1/tdee-calculate/',
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
function create_shortcode_tool_tdee($args, $content)
{
    ob_start();
    ?>
    <div id="spinner"></div>
    <div id="calculate">
        <div class="container">
            <div class="wrapper">
                <div class="wrapper__content">
                    <div class="content-top">
                        <form action="#" class="form lean-body-calculate" id="tdeeCalculate">
                            <div class="column">
                                <div class="label-wrapper img">
                                    <label for="male" class="label">Gender</label>
                                </div>
                                <div class="radio-wrapper">
                                    <div class="radio-wrapper__item">
                                        <input type="radio" checked class="radio-wrapper__btn" value="1" name="info[gender]"
                                            id="male">
                                        <label for="male" class="radio-wrapper__label">
                                            <span class="radio-visibility"></span>
                                            Male
                                        </label>
                                    </div>
                                    <div class="radio-wrapper__item">
                                        <input type="radio" class="radio-wrapper__btn" value="2" name="info[gender]"
                                            id="female">
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
                                        <input type="text" class="" value="" name="info[age]" id="age">
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
                                        <input type="text" class="" value="" name="info[weight]" id="weight">
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
                                        <div class="pr height-ft">
                                            <input type="text" class="radio-wrapper__btn" value="" name="info[height][feet]"
                                                id="heightFt">
                                            <div class="place-holder">
                                                <span>ft</span>
                                            </div>
                                        </div>
                                        <div class="pr height-in">
                                            <input type="text" class="radio-wrapper__btn" value=""
                                                name="info[height][inches]" id="heightIn">
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
                                    <label for="" class="label">Level of Activity</label>
                                </div>
                                <select name="info[activity]" id="levelofActivity" class="select-wrapper">
                                    <option value="1" class="select-wrapper__option">Basal Metabolic Rate (BMR)</option>
                                    <option value="2" class="select-wrapper__option">Sedentary: little or no exercise
                                    </option>
                                    <option value="3" class="select-wrapper__option">Light: exercise 1-3 times/week</option>
                                    <option value="4" class="select-wrapper__option">Moderate: exercise 4-5 times/week
                                    </option>
                                    <option value="5" class="select-wrapper__option">Active: daily exercise or intense
                                        exercise 3-4 times/week</option>
                                    <option value="6" class="select-wrapper__option">Very Active: intense exercise 6-7
                                        times/week</option>
                                    <option value="7" class="select-wrapper__option">Extra Active: very intense exercise
                                        daily, or physical job</option>
                                </select>
                            </div>
                            <div class="column">
                                <div class="label-wrapper">
                                    <label for="" class="label">Result Unit</label>
                                </div>
                                <select name="unit" id="resultUnit" class="select-wrapper">
                                    <option value="1" class="select-wrapper__option">Calories</option>
                                    <option value="2" class="select-wrapper__option">Kilojoules</option>
                                </select>
                            </div>
                            <div class="column">
                                <div class="label-wrapper">
                                    <label for="" class="label">BMR Estimation Formular</label>
                                </div>
                                <select name="receip" id="bmrReceip" class="select-wrapper">
                                    <option value="1" class="select-wrapper__option">Mifflin St Jeor</option>
                                    <option value="2" class="select-wrapper__option"> Revised Harris-Benedict</option>
                                    <option value="3" class="select-wrapper__option"> Katch-McArdle</option>
                                </select>
                            </div>
                            <div class="action">
                                <button id="btnLeanBody" class="btn-primary" type="submit">
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
    wp_enqueue_style('tool-css', get_template_directory_uri() . '/shortcode/calorie/assets/css/tool.css', '', '1.0.0');
    wp_enqueue_script('tdee-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/tdee-tool.js', '', '1.0.0');
    wp_enqueue_script('validate-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/jquery.validate.min.js', '', '1.0.1');
    return $rt;
}
add_shortcode('hc_tool_tdee', 'create_shortcode_tool_tdee');
/* call ajax tool */
function is_ajax_tdee_tool()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
add_action('init', 'get_tdee_tool');
function get_tdee_tool()
{
    if (isset($_GET['get_tdee_tool']) && is_ajax_tdee_tool()) {
        $info = $_GET["jsonData"];
        $tool_result = tdee_call_shortcode($info);
        $bmi = $tool_result->bmi;
        $calorie = $tool_result->calorie;

        ob_start();
        ?>
        <div class="title">
            <h2>Result</h2>
        </div>
        <div class="result">
            <div class="main-result">
                <?php if (isset($calorie->gain)): ?>
                    <p>The estimated TDEE or body weight maintenance energy requirement is <span
                            class="primary-title"><?= $calorie->maintain->calorie ?></span> Calories per day.</p>
                    <p>BMI Score: <?= $bmi->bmi ?> kg/m2 <span
                            class="primary-title"><?= " ( " . $bmi->description . " ), " ?></span><?= $bmi->healthy_range ?></p>
                <?php else: ?>
                    <p>Basal Metabolic Rate (BMR): <?= $calorie->maintain->calorie ?> Calories/day</p>
                <?php endif; ?>
            </div>
            <div class="loss">
                <?php foreach ($calorie->loss as $loss): ?>
                    <p><?= $loss->name ?><span class="primary-title"> <?= $loss->calorie ?></span> Calories/day</p>
                <?php endforeach; ?>
            </div>
            <div class="gain">
                <?php foreach ($calorie->gain as $gain): ?>
                    <p><?= $gain->name ?><span class="primary-title"> <?= $gain->calorie ?> </span> Calories/day</p>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        $result_get = ob_get_clean();
        echo json_encode($result_get);
        exit;
    }
}