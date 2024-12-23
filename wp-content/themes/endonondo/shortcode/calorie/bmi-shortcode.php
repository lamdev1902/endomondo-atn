<?php
function bmi_call_shortcode($info)
{
    $curl = curl_init();
    $info = json_encode($info);
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://wordpress-1312425-4788967.cloudwaysapps.com/wp-json/api/v1/bmi-calculate/',
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
function create_shortcode_tool_bmi($args, $content)
{
    ob_start();
    ?>
    <div class="calories-box">
        <div id="spinner"></div>
        <div class="calories-form">
            <div class="content-top">
                <h2>Input Your Information Below </h2>
                <form action="#" class="form" id="bmiCalculate">
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
                        <label for="">Weight</label>
                        <div class="form-input">
                            <input class="input-it" type="text" name="info[weight]">
                            <p class="">pounds</p>
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
                    <div class="action odd">
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
    wp_enqueue_script('bmi-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/bmi-tool.js', '', '1.0.2');
    wp_enqueue_script('validate-js', get_template_directory_uri() . '/shortcode/calorie/assets/js/jquery.validate.min.js', '', '1.0.0');
    return $rt;
}
add_shortcode('hc_tool_bmi', 'create_shortcode_tool_bmi');
/* call ajax tool */
function is_ajax_bmi_tool()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}
add_action('init', 'get_bmi_tool');
function get_bmi_tool()
{
    if (isset($_GET['get_bmi_tool']) && is_ajax_bmi_tool()) {
        $info = $_GET["jsonData"];
        $tool_result = bmi_call_shortcode($info);
        $result_bmi = $tool_result->bmi;


        $bmi = $result_bmi->bmi;


        if ($bmi < 16) {
            $angel = (16 - $bmi) * 6;
        }

        if ($bmi < 16) {
            $color = "#9F9F9F";
        } else if ($bmi < 17) {
            $color = "#D5E1C3";
        } else if ($bmi < 18.5) {
            $color = "#E8FF99";
        } else if ($bmi >= 18.5 && $bmi < 25) {
            $color = "#C5F621";
        } else if ($bmi >= 25 && $bmi < 30) {
            $color = "#A9D41A";
        } else if ($bmi >= 30 && $bmi < 35) {
            $color = "#87AA14";
        } else if ($bmi >= 35 && $bmi < 40) {
            $color = "#3E4D0B";
        } else if ($bmi >= 40) {
            $color = "#404040";
        }

        $i = $bmi / 5 * 0.6 + $bmi / 5;

        $angle = $bmi * 3.7 + $i;

        ob_start();
        ?>
        <div class="title">
            <h2>Result</h2>
        </div>
        <div class="result flex-row full-width">
            <div class="result-left">
                <div class="main-result">
                    <p class="has-large-font-size">BMI:
                        <?= $result_bmi->bmi . " kg/m2 " . " <strong style='color: $color'>[" . $result_bmi->description . "]</strong>" ?>
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
                            <g style="font-size: 8px; color: #404040;">
                                <text>
                                    <textPath xlink:href="#curvetxt1">Underweight</textPath>
                                </text>
                                <text>
                                    <textPath xlink:href="#curvetxt2">Normal</textPath>
                                </text>
                                <text>
                                    <textPath xlink:href="#curvetxt3">Overweight</textPath>
                                </text>
                                <text>
                                    <textPath xlink:href="#curvetxt4">Obesity</textPath>
                                </text>
                            </g>
                            <line x1="140" y1="140" x2="65" y2="140" stroke="#666" stroke-width="2"
                                marker-end="url(#arrowhead)">
                                <animateTransform attributeName="transform" attributeType="XML" type="rotate" from="0 140 140"
                                    to="<?= $angle ?> 140 140" dur="1s" fill="freeze" repeatCount="1"></animateTransform>
                            </line>
                        </g>
                    </svg>
                </div>
                <div class="description-range">
                    <div class="severe">
                        <div style="background: #9F9F9F" class=""></div>
                        <div class="">
                            <p class="number has-small-font-size">
                                < 16</p>
                        </div>
                    </div>
                    <div class="moderate">
                        <div style="background: #D5E1C3" class=""></div>
                        <div class="">
                            <p class="number has-small-font-size">16-17</p>
                        </div>
                    </div>
                    <div class="mild">
                        <div style="background: #E8FF99" class=""></div>
                        <div class="">
                            <p class="number has-small-font-size">17-18.5</p>
                        </div>
                    </div>
                    <div class="normal">
                        <div style="background: #C5F621" class=""></div>
                        <div class="">
                            <p class="number has-small-font-size">18.5-25</p>
                        </div>
                    </div>
                    <div class="overweight">
                        <div style="background: #A9D41A" class=""></div>
                        <div class="">
                            <p class="number has-small-font-size">25-30</p>
                        </div>
                    </div>
                    <div class="obese-1">
                        <div style="background: #87AA14" class=""></div>
                        <div class="">
                            <p class="number has-small-font-size">30-35</p>
                        </div>
                    </div>
                    <div class="obese-2">
                        <div style="background: #3E4D0B" class=""></div>
                        <div class="">
                            <p class="number has-small-font-size">35-40</p>
                        </div>
                    </div>
                    <div class="obese-2">
                        <div style="background: #404040" class=""></div>
                        <div class="">
                            <p class="number has-small-font-size">> 40</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="result-right">
                <ul>
                    <li>
                        <p>Healthy BMI range:</p>
                        <p class="has-medium-font-size"><strong><?= $result_bmi->healthy_range ?></strong></p>
                    </li>
                    <li>
                        <p>Healthy weight for the height:
                        </p>
                        <p class="has-medium-font-size"><strong><?= $result_bmi->ideal_weight ?></strong></p>
                    </li>
                    <?php
                    if ($result_bmi->propose) {
                        echo "<li>" . $result_bmi->propose->title . "<strong class='has-medium-font-size'>" . $result_bmi->propose->result . " lbs </strong>" . " to reach a BMI of <strong class='has-medium-font-size'>25 kg/m2</strong>" . "</li>";
                    }
                    ?>
                    <li>
                        <p>BMI Prime: </p>
                        <p class="has-medium-font-size"><strong><?= $result_bmi->prime ?></strong>
                        <p>
                    </li>
                    <li>
                        <p>Ponderal Index: </p>
                        <p class="has-medium-font-size"><strong><?= $result_bmi->ponderal . " kg/m3" ?></strong></p>
                    </li>
                </ul>
            </div>
        </div>
        <?php
        $result_get = ob_get_clean();
        echo json_encode($result_get);
        exit;
    }
}