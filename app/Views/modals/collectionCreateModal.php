<?php
use App\Core\Application;

$formElements = Application::app()->model('CollectionModel')->formElements('create');
?>

<section id="collectionCreateModal" class="<?= $formElements['modalClass'] ?>">
    <form id="collectionCreate" action="/" method="POST">
        <div class="modal-header">
            <div class="font-semibold">Create Collection</div>
            <button type="button" name="collectionCreateCloseButton" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="modal-body">
            <div class="mb-6">
                <div class="mb-2">Collection name</div>
                <div><input type="text" name="collectionName" value="<?= $formElements['collectionNameValue'] ?>" autocomplete="one-time-code" spellcheck="false" class="<?= $formElements['collectionNameClass'] ?>"/></div>
                <div class="form-error"><?= $formElements['collectionNameError'] ?></div>
            </div>
        </div>

        <div class="modal-footer">
            <div>
                <button type="submit" class="primary">Create</button>
            </div>
            <div>
                <button type="button" name="collectionCreateCancelButton" class="secondary">Cancel</button>
            </div>
        </div>

        <input type="hidden" name="userId" value="<?= Application::app()->user()->id() ?>">
        <input type="hidden" name="modalName" value="collectionCreateModal">
        <input type="hidden" name="modelClassName" value="CollectionModel">
        <input type="hidden" name="formAction" value="create">
    </form>
</section>
