<?php

declare(strict_types=1);

use App\Core\Application;

$formElements = Application::app()->model('TestModel')->formElements('create');
?>

<section id="testCreateModal" class="<?= $formElements['modalClass'] ?>">
    <form id="testCreate" action="/" method="POST">
        <div class="modal-header">
            <div class="font-semibold">Create Unit Test</div>
            <button type="button" name="testCreateCloseButton" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
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
                <button type="submit" class="primary">Create</button>
            </div>
            <div>
                <button type="button" name="testCreateCancelButton" class="secondary">Cancel</button>
            </div>
        </div>

        <input type="hidden" name="requestId" value="<?= Application::app()->session()->get('home/left/requestId') ?>">
        <input type="hidden" name="modalName" value="testCreateModal">
        <input type="hidden" name="modelClassName" value="TestModel">
        <input type="hidden" name="formAction" value="create">
    </form>
</section>
