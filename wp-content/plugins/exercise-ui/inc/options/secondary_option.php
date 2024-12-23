<div class="secondary table-section">
    <div class="table-title attention">
        <a href="#exercise-secondary-modal" data-id="<?= $id ?>" class="add-secondary-option insert-btn">Insert
            secondary option</a>
    </div>
    <table class="exercise-option-table">
        <thead>
            <tr>
                <th>Ordering</th>
                <th>ID</th>
                <th>Muscle</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($secondary_data as $secondary): ?>
                <tr>
                    <td></td>
                    <td><?= $secondary['muscle_id'] ?></td>
                    <td><?= $secondary['name'] ?></td>
                    <td>
                        <input type="hidden" name="exercise[secondary][]" value="<?= $secondary['muscle_id'] ?>"
                            id="<?= $secondary['muscle_id'] ?>">
                        <a href="<?= admin_url('admin.php?page=exerciseui_manage_muscle&action=edit&muscle=' . $primary['muscle_id']) ?>"
                            class="action-item exercise-muscle-edit">Edit</a>
                        <a href="javascript:void(0)" data-type="secondary" data-exercise="<?= $id ?>"
                            data-id="<?= $secondary['muscle_id'] ?>" class="action-item exercise-muscle-delete">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($secondary_data)): ?>
                <tr class="single-tr">
                    <td colspan="7"><a href="#exercise-primary-modal" data-id="<?= $id ?>"
                            class="add-secondary-option insert-btn">Insert secondary option</a></h3>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php

?>
<div id="exercise-secondary-modal" class="modal">
    <div class="form-search">
        <label>Search: </label>
        <input type="text" class="search-option" data-id="<?= $id ?>" data-type="secondary" name="search" value="" />
    </div>
    <div class="secondary-modal">

    </div>
    <a href="javascript:void(0)" class="add-muscle-secondary button-primary">Insert secondary option</a>
</div>