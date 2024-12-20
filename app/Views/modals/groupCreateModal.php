<?php

declare(strict_types=1);

$formElements = model('GroupModel')->formElements('create');
?>

<section id="groupCreateModal" class="<?= $formElements['modalClass'] ?>">
    <form id="groupCreate" action="/tests" method="POST">
        <div class="modal-header">
            <div class="font-semibold">Create Group</div>
            <button type="button" name="groupCreateCloseButton" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
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
                <button type="submit" class="primary">Create</button>
            </div>
            <div>
                <button type="button" name="groupCreateCancelButton" class="secondary">Cancel</button>
            </div>
        </div>

        <input type="hidden" name="userId" value="<?= user()->id() ?>">
        <input type="hidden" name="modalName" value="groupCreateModal">
        <input type="hidden" name="modelClassName" value="GroupModel">
        <input type="hidden" name="formAction" value="create">
    </form>
</section>
