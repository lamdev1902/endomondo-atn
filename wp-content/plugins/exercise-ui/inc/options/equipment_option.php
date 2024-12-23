<div class="equipment table-section">
    <div class="table-title attention">
        <a href="#exercise-equipment-modal" data-id="<?= $id ?>" class="add-equipment-option insert-btn">Insert
            equipment option</a>
    </div>
    <table class="exercise-option-table">
        <thead>
            <tr>
                <th>Ordering</th>
                <th>ID</th>
                <th>Equipment</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipment_data as $equipment): ?>
                <tr>
                    <td></td>
                    <td><?= $equipment['equipment_id'] ?></td>
                    <td><?= $equipment['name'] ?></td>
                    <td>
                        <input type="hidden" name="exercise[equipment][]" value="<?= $equipment['equipment_id'] ?>"
                            id="<?= $equipment['equipment_id'] ?>">
                        <a href="<?= admin_url('admin.php?page=exerciseui_manage_equipment&action=edit&equipment=' . $equipment['equipment_id']) ?>"
                            class="action-item exercise-equipment-edit">Edit</a>
                        <a href="javascript:void(0)" data-type="equipment" data-exercise="<?= $id ?>"
                            data-id="<?= $equipment['equipment_id'] ?>"
                            class="action-item exercise-muscle-delete">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($equipment_data)): ?>
                <tr class="single-tr">
                    <td colspan="7"><a href="#exercise-equipment-modal" data-type="equipment" data-id="<?= $id ?>"
                            class="add-equipment-option insert-btn">Insert equipment option</a></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php

?>
<div id="exercise-equipment-modal" class="modal">
    <div class="form-search">
        <label>Search: </label>
        <input type="text" class="search-option" data-id="<?= $id ?>" data-type="equipment" name="search" value="" />
    </div>
    <div class="equipment-modal">

    </div>
    <a href="javascript:void(0)" class="add-equipment button-primary">Insert equipment</a>
</div>