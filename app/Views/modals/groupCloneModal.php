<?php
use App\Core\Application;

$formElements = Application::app()->model('GroupModel')->formElements('clone');
?>

<section id="groupCloneModal" class="<?= $formElements['modalClass'] ?>">
    <form id="groupClone" action="/tests" method="POST">
        <div class="modal-header">
            <div class="font-semibold">Clone Group</div>
            <button type="button" name="groupCloneCloseButton" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="modal-body">
            <div class="mb-6">
                <div class="mb-2">Group name</div>
                <div><input type="text" name="groupName" value="<?= $formElements['groupNameValue'] ?>" autocomplete="one-time-code" spellcheck="false" class="<?= $formElements['groupNameClass'] ?>"/></div>
                <div class="form-error"><?= $formElements['groupNameError'] ?></div>
            </div>
        </div>

        <div class="modal-footer">
            <div>
                <button type="submit" class="primary">Clone</button>
            </div>
            <div>
                <button type="button" name="groupCloneCancelButton" class="secondary">Cancel</button>
            </div>
        </div>

        <input type="hidden" name="id" value="<?= Application::app()->session()->get('tests/left/groupId') ?>">
        <input type="hidden" name="userId" value="<?= Application::app()->user()->id() ?>">
        <input type="hidden" name="modalName" value="groupCloneModal">
        <input type="hidden" name="modelClassName" value="GroupModel">
        <input type="hidden" name="formAction" value="clone">
    </form>
</section>
