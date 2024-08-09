<?php

declare(strict_types=1);

use App\Core\Application;

$formElements = Application::app()->model('TestModel')->formElements('update');
?>

<section id="testUpdateModal" class="<?= $formElements['modalClass'] ?>">
    <form id="testUpdate" action="/" method="POST">
        <div class="modal-header">
            <div class="font-semibold">Edit Unit Test</div>
            <button type="button" name="testUpdateCloseButton" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="modal-body">
            <div class="mb-6">
                <div class="mb-2">Test name</div>
                <div><input type="text" name="testName" value="<?= $formElements['testNameValue'] ?>" autocomplete="one-time-code" spellcheck="false" class="<?= $formElements['testNameClass'] ?>"/></div>
                <div class="form-error"><?= $formElements['testNameError'] ?></div>
            </div>

            <div class="mb-6">
                <div class="mb-2">Test type</div>
                <div><input type="text" name="testType" value="<?= $formElements['testTypeValue'] ?>" autocomplete="one-time-code" spellcheck="false" class="<?= $formElements['testTypeClass'] ?>"/></div>
                <div class="form-error"><?= $formElements['testTypeError'] ?></div>
            </div>

            <div class="mb-6">
                <div class="mb-2">Test assertion</div>
                <div><input type="text" name="testAssertion" value="<?= $formElements['testAssertionValue'] ?>" autocomplete="one-time-code" spellcheck="false" class="<?= $formElements['testAssertionClass'] ?>"/></div>
                <div class="form-error"><?= $formElements['testAssertionError'] ?></div>
            </div>
        </div>

        <div class="modal-footer">
            <div>
                <button type="submit" class="primary">Update</button>
            </div>
            <div>
                <button type="button" name="testUpdateCancelButton" class="secondary">Cancel</button>
            </div>
        </div>

        <input type="hidden" name="id" value="<?= $formElements['id'] ?>">
        <input type="hidden" name="requestId" value="<?= Application::app()->session()->get('home/left/requestId') ?>">
        <input type="hidden" name="modalName" value="testUpdateModal">
        <input type="hidden" name="modelClassName" value="TestModel">
        <input type="hidden" name="formAction" value="update">
    </form>
</section>
