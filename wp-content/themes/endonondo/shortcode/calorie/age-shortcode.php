<?php
function age_call_shortcode($info)
{
    $curl = curl_init();
    $info = json_encode($info);
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://wordpress-1312425-4788967.cloudwaysapps.com/wp-json/api/v1/age-calculate/',
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
function create_shortcode_tool_age($args, $content)
{
    ob_start();
    ?>
    <div id="calculate">
        <div class="container">
            <div id="spinner"></div>
            <div class="wrapper">
                <div class="wrapper__content">
                    <div class="content-top">
                        <h4>Input Your Information Below </h4>
                        <form action="#" class="form" id="ageCalculate">
                            <div class="form-row">
                                <div class="label">Date of Birth</div>
                                <div class="date date-option">
                                    <input type="hidden" class="" value="1990-01-01" name="dob" id="dayOfBirth">
                                    <div class="options">
                                        <div class="day-option age-option">
                                            <select class="dateMonthInput" name="mon-birth">
                                                <option value="1" selected>Jan</option>
                                                <option value="2">Feb</option>
                                                <option value="3">Mar</option>
                                                <option value="4">Apr</option>
                                                <option value="5">May</option>
                                                <option value="6">Jun</option>
                                                <option value="7">Jul</option>
                                                <option value="8">Aug</option>
                                                <option value="9">Sep</option>
                                                <option value="10">Oct</option>
                                                <option value="11">Nov</option>
                                                <option value="12">Dec</option>
                                            </select>
                                        </div>
                                        <div class="mon-option age-option">
                                            <select class="dayDateInput" name="date-birth">
                                            </select>
                                        </div>
                                        <div class="year-option age-option">
                                            <input type="text" value="1990" class="" name="year-birth">
                                        </div>
                                    </div>
                                    <span class="birth-error error"></span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="label">Age at the Date</div>
                                <div class="date date-option">
                                    <input type="hidden" class="" value="" name="ageat" id="birth">
                                    <div class="options">
                                        <div class="day-option age-option">
                                            <select class="ageMonthInput" name="mon-age">
                                                <option value="1">Jan</option>
                                                <option value="2">Feb</option>
                                                <option value="3">Mar</option>
                                                <option value="4">Apr</option>
                                                <option value="5">May</option>
                                                <option value="6">Jun</option>
                                                <option value="7">Jul</option>
                                                <option value="8">Aug</option>
                                                <option value="9">Sep</option>
                                                <option value="10">Oct</option>
                                                <option value="11">Nov</option>
                                                <option value="12">Dec</option>
                                            </select>
                                        </div>
                                        <div class="mon-option age-option">
                                            <select class="ageDateInput" name="date-age">
                                            </select>
                                        </div>
                                        <div class="year-option age-option">
                                            <input type="text" class="" name="year-age">
                                        </div>
                                    </div>
                                    <span class="ageof-error error"></span>
                                </div>
                            </div>
                            <div class="action">
                                <button id="btnAge" class="btn-primary" type="submit">
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
    wp_enqueue_style('tool-css', get_template_directory_uri() . '/shortcode/calorie/assets/css/tool.css');
    wp_enqueue_script('age-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/age-tool.js');
    wp_enqueue_script('validate-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/jquery.validate.min.js', '', '1.0.1');
    return $rt;
}
add_shortcode('hc_tool_age', 'create_shortcode_tool_age');
/* call ajax tool */
function is_ajax_age_tool()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
add_action('init', 'get_age_tool');
function get_age_tool()
{
    if (isset($_GET['get_age_tool']) && is_ajax_age_tool()) {
        $info = $_GET["jsonData"];
        $tool_result = age_call_shortcode($info);
        $result = $tool_result->age;
        ob_start();
        ?>
        <div class="title">
            <h2>Result</h2>
        </div>
        <div class="result flex-column">
            <?php foreach ($result as $key => $item): ?>
                <p><?= $key . ": " . $item ?></p>
            <?php endforeach; ?>
        </div>
        <?php
        $result_get = ob_get_clean();
        echo json_encode($result_get);
        exit;
    }
}