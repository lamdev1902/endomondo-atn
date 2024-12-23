<div class="primary table-section">
    <div class="table-title attention">
        <a href="#exercise-primary-modal" data-id="<?= $id ?>" class="add-primary-option insert-btn">Insert primary
            option</a>
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
            <?php foreach ($primary_data as $primary): ?>
                <tr>
                    <td></td>
                    <td><?= $primary['muscle_id'] ?></td>
                    <td><?= $primary['name'] ?></td>
                    <td>
                        <input type="hidden" name="exercise[primary][]" value="<?= $primary['muscle_id'] ?>"
                            id="<?= $primary['muscle_id'] ?>">
                        <a href="<?= admin_url('admin.php?page=exerciseui_manage_muscle&action=edit&muscle=' . $primary['muscle_id']) ?>"
                            class="action-item exercise-muscle-edit">Edit</a>
                        <a href="javascript:void(0)" data-type="primary" data-exercise="<?= $id ?>"
                            data-id="<?= $primary['muscle_id'] ?>" class="action-item exercise-muscle-delete">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($primary_data)): ?>
                <tr class="single-tr">
                    <td colspan="7"><a href="#exercise-primary-modal" data-id="<?= $id ?>"
                            class="add-primary-option insert-btn">Insert primary option</a></h3>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php

?>
<div id="exercise-primary-modal" class="modal">

    <div class="form-search">
        <label>Search: </label>
        <input type="text" class="search-option" data-id="<?= $id ?>" data-type="primary" name="search" value="" />
    </div>

    <div class="primary-modal">

    </div>
    <a href="javascript:void(0)" class="add-muscle-primary button-primary">Insert primary muscle</a>
</div>