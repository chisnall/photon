<?php
use App\Core\Application;
?>
<section id="groupDeleteModal" class="modal hidden">
    <form id="groupDelete" action="/tests" method="POST">
        <div class="modal-header">
            <div class="font-semibold">Delete Group</div>
            <button type="button" name="groupDeleteCloseButton" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="modal-body">
            <div class="mb-6">
                <div>This group will be deleted:</div>
                <div><?= Application::app()->session()->get('tests/left/groupName') ?></div>
            </div>
            <div>
                <div>This will not affect any requests assigned to this group.</div>
            </div>
        </div>

        <div class="modal-footer">
            <div>
                <button type="submit" class="primary">Delete</button>
            </div>
            <div>
                <button type="button" name="groupDeleteCancelButton" class="secondary">Cancel</button>
            </div>
        </div>

        <input type="hidden" name="id" value="<?= Application::app()->session()->get('tests/left/groupId') ?>">
        <input type="hidden" name="modalName" value="groupDeleteModal">
        <input type="hidden" name="modelClassName" value="GroupModel">
        <input type="hidden" name="formAction" value="delete">
    </form>
</section>
