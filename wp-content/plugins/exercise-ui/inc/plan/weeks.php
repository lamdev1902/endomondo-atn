<?php
$weeksName = array(
    'Initial Assessment Week',
    'Main Part',
    'Build-up Week',
    'Progress Assessment Week'
);

$display = 'none';
?>
<div class="primary table-section">
    <button type="button" id="add-week">Add Week</button>
    <table class="exercise-option-table" id="week-table">
        <thead>
            <tr>
                <th>Week</th>
                <th>Week Number</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($weeks)): ?>
                <?php $display = 'block'; ?>
                <?php foreach ($weeks as $key => $week): ?>
                    <?php
                    $name = $week['week_name'];
                    ?>
                    <tr class="week-count">
                        <td>
                            <select name="plan[week][<?= $key ?>][week_name]" id="">
                                <?php foreach ($weeksName as $weekName): ?>
                                    <option <?= $name == $weekName ? "selected" : "" ?> value="<?= $weekName ?>"><?= $weekName ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="number" data-number="<?= $week['week_number'] ?>" value="<?= $week['week_number'] ?>"
                                name="plan[week][<?= $key ?>][week_number]" class="week-number"></td>
                        <td><button type="button" class="remove-week">Remove</button></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr class="week-count">
                    <td>
                        <select name="plan[week][0][week_name]" id="">
                            <?php foreach ($weeksName as $weekName): ?>
                                <option value="<?= $weekName ?>"><?= $weekName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="number" data-number="1" value="1" name="plan[week][0][week_number]"></td>
                    <!-- <td>
                        <div class="item">
                            <div class="wrapper">
                                <button type="button" class="form-control toggle-next ellipsis">Select training
                                    type
                                    <span class="dropdown"></span>
                                </button>

                                <div class="checkboxes">
                                    <label class="apply-selection">
                                        <input type="checkbox" value="" class="ajax-link" />
                                        &#x2714; apply selection
                                    </label>

                                    <div class="inner-wrap">

                                        <?php
                                        foreach ($training_type_arr as $item): ?>
                                            <?php $checked = in_array($item['id'], $arr_training) ? 'checked' : '' ?>
                                            <label>
                                                <input type="hidden" class="val-<?= $item['id'] ?>"
                                                    name="plan[week][0][training][]">
                                                <input type="checkbox" <?= $checked ?> value="<?= $item['id'] ?>"
                                                    class="ckkBox val" />
                                                <span><?= $item['name'] ?></span>
                                            </label><br>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td> -->
                    <td><button type="button" class="remove-week">Remove</button></td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>

    <?php
    $sections = array(
        'Warm Up',
        'Main Workout',
        'Cool Down'
    )
        ?>

    <table class="exercise-option-table" style="margin-top: 30px;" id="day-table">
        <thead>
            <tr>
                <th>Week Number</th>
                <th>Day Number</th>
                <th>Training Method</th>
                <th>Section</th>
                <th>Round</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($weeks)): ?>
                <?php foreach ($weeks as $key => $week): ?>
                    <?php
                    $week_number = $week['week_number'];
                    $weekPos = 0;
                    $days = $this->get_training_method_by_plan($id, $week['id']);
                    ?>
                    <?php if ($days): ?>
                        <?php foreach ($days as $keyy => $day): ?>
                            <tr data-key="<?= $key + 1 ?>">
                                <td>
                                    <p class="weekNumber">
                                        <?= $week_number ?>
                                    </p>
                                </td>
                                <td><input type="number" value="<?= $day['num_days'] ?>"
                                        name="plan[week][<?= $key ?>][days][<?= $keyy ?>][num_days]">
                                </td>
                                <td>
                                    <select class="training" name="plan[week][<?= $key ?>][days][<?= $keyy ?>][training_method_id]">
                                        <?php
                                        foreach ($training_methods as $item): ?>
                                            <?php $checked = $item['id'] == $day['id'] ? 'selected' : ''; ?>
                                            <option <?= $checked ?> value="<?= $item['id'] ?>"><?= $item['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <div class="section">
                                        <?php foreach ($sections as $keySection => $section): ?>
                                            <?php
                                            $exercise = $this->get_exercise_by_section($day['day_id'], $keySection);
                                            $exStr = '';
                                            $durStr = '';
                                            $repsStr = '';
                                            $noteStr = '';
                                            $section_id = '';
                                            if (!empty($exercise)) {
                                                $exercise_ids = array_column($exercise, 'exercise_id');
                                                $duration = array_column($exercise, 'duration');
                                                $reps = array_column($exercise, 'reps');
                                                $note = array_column($exercise, 'note');


                                                $exStr = implode(',', $exercise_ids);
                                                $durStr = implode(',', $duration);
                                                $repsStr = implode(',', $reps);
                                                $noteStr = implode(',', $note);
                                                $section_id = $exercise[0]['section_id'];
                                            }
                                            ?>
                                            <div class="section-item">
                                                <input type="hidden" value="<?= $section_id ?>"
                                                    name="plan[week][0][days][<?= $keyy ?>][sections][section-<?= $keySection ?>][section_id]">
                                                <input type="hidden" value="<?= $durStr ?>"
                                                    name="plan[week][0][days][<?= $keyy ?>][sections][section-<?= $keySection ?>][duration]">
                                                <input type="hidden" value="<?= $repsStr ?>"
                                                    name="plan[week][0][days][<?= $keyy ?>][sections][section-<?= $keySection ?>][reps]">
                                                <input type="hidden" value="<?= $noteStr ?>"
                                                    name="plan[week][0][days][<?= $keyy ?>][sections][section-<?= $keySection ?>][note]">
                                                <input type="hidden" value="<?= $exStr ?>"
                                                    name="plan[week][<?= $key ?>][days][<?= $keyy ?>][sections][section-<?= $keySection ?>][exercise]">
                                                <a class="section-btn" data-section-id="<?= $section_id ?>" data-exercise="<?= $exStr ?>"
                                                    data-duration="<?= $durStr ?>" data-reps="<?= $repsStr ?>" data-note="<?= $noteStr ?>"
                                                    href="javascript:void(0)" id=""><?= $section ?></a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="round">
                                        <?php foreach ($sections as $keySection => $section): ?>
                                            <?php
                                            $roundSection = $this->get_round($day['day_id'], $keySection);
                                            $round = '';
                                            if ($roundSection) {
                                                $round = $roundSection[0]['round'];
                                            }
                                            ?>
                                            <div class="rount-item" style="margin-bottom: 5px;">
                                                <input type="number" value="<?= $round ?>"
                                                    name="plan[week][0][days][<?= $keyy ?>][sections][section-<?= $keySection ?>][round]">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td><button type="button" class="remove-day">Remove</button></td>
                            </tr>
                            <?php $week_number = ''; endforeach; ?>
                    <?php else: ?>
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <tr data-key="<?=$week_number?>">
                                <td>
                                    <p class="week-number">
                                        <?= $week_number != $weekPos ? $week_number : "" ?>
                                    </p>
                                </td>
                                <td>
                                    <input type="number" value="<?= $i ?>" name="plan[week][<?=$key?>][days][<?= $i - 1 ?>][num_days]">
                                </td>
                                <td>
                                    <select class="training" name="plan[week][<?=$key?>][days][<?= $i - 1 ?>][training_method_id]">
                                        <?php
                                        foreach ($training_methods as $item): ?>
                                            <option value="<?= $item['id'] ?>"><?= $item['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <div class="section">
                                        <?php foreach ($sections as $keySection => $section): ?>
                                            <div class="section-item">
                                                <input type="hidden" value=""
                                                    name="plan[week][<?=$key?>][days][<?= $i - 1 ?>][sections][section-<?= $keySection ?>][section_id]">
                                                <input type="hidden" value=""
                                                    name="plan[week][<?=$key?>][days][<?= $i - 1 ?>][sections][section-<?= $keySection ?>][duration]">
                                                <input type="hidden" value=""
                                                    name="plan[week][<?=$key?>][days][<?= $i - 1 ?>][sections][section-<?= $keySection ?>][reps]">
                                                <input type="hidden" value=""
                                                    name="plan[week][<?=$key?>][days][<?= $i - 1 ?>][sections][section-<?= $keySection ?>][note]">
                                                <input type="hidden" value=""
                                                    name="plan[week][<?=$key?>][days][<?= $i - 1 ?>][sections][section-<?= $keySection ?>][exercise]">
                                                <a class="section-btn" data-section-id="" data-exercise="" data-duration="" data-reps=""
                                                    data-note="" href="javascript:void(0)" id=""><?= $section ?></a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="round">
                                        <?php foreach ($sections as $keySection => $section): ?>
                                            <div class="rount-item" style="margin-bottom: 5px;">
                                                <input type="number"
                                                    name="plan[week][<?=$key?>][days][<?= $i - 1 ?>][sections][section-<?= $keySection ?>][round]">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            <td><button type="button" class="remove-day">Remove</button></td>
                            </tr>
                            <?php $weekPos = $week_number; ?>
                        <?php endfor; ?>
                    <?php endif; ?>

                <?php endforeach; ?>
            <?php else: ?>
                <?php $weekPosition = 1; ?>
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <tr data-key="<?=$i?>">
                        <td>
                            <p class="week-number">
                                <?= $weekPosition > 0 ? $weekPosition : "" ?>
                            </p>
                        </td>
                        <td>
                            <input type="number" value="<?= $i ?>" name="plan[week][0][days][<?= $i - 1 ?>][num_days]">
                        </td>
                        <td>
                            <select class="training" name="plan[week][0][days][<?= $i - 1 ?>][training_method_id]">
                                <?php
                                foreach ($training_methods as $item): ?>
                                    <option value="<?= $item['id'] ?>"><?= $item['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <div class="section">
                                <?php foreach ($sections as $keySection => $section): ?>
                                    <div class="section-item">
                                        <input type="hidden" value=""
                                            name="plan[week][0][days][<?= $i - 1 ?>][sections][section-<?= $keySection ?>][section_id]">
                                        <input type="hidden" value=""
                                            name="plan[week][0][days][<?= $i - 1 ?>][sections][section-<?= $keySection ?>][duration]">
                                        <input type="hidden" value=""
                                            name="plan[week][0][days][<?= $i - 1 ?>][sections][section-<?= $keySection ?>][reps]">
                                        <input type="hidden" value=""
                                            name="plan[week][0][days][<?= $i - 1 ?>][sections][section-<?= $keySection ?>][note]">
                                        <input type="hidden" value=""
                                            name="plan[week][0][days][<?= $i - 1 ?>][sections][section-<?= $keySection ?>][exercise]">
                                        <a class="section-btn" data-section-id="" data-exercise="" data-duration="" data-reps=""
                                            data-note="" href="javascript:void(0)" id=""><?= $section ?></a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td>
                            <div class="round">
                                <?php foreach ($sections as $keySection => $section): ?>
                                    <div class="rount-item" style="margin-bottom: 5px;">
                                        <input type="number"
                                            name="plan[week][0][days][<?= $i - 1 ?>][sections][section-<?= $keySection ?>][round]">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td><button type="button" class="remove-day">Remove</button></td>
                    </tr>
                    <?php $weekPosition--; ?>
                <?php endfor; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="exercise-section-modal" class="modal">
    <div class="form-search">
        <label>Search: </label>
        <input type="text" class="search-option-section" data-type="section" name="search-section" value="" />
    </div>
    <div class="section-modal">

    </div>
</div>