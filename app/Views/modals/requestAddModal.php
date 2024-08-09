<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Functions;

// Get form elements
$formElements = Application::app()->model('GroupModel')->formElements('addRequests');

// Get group ID - from form elements if form has been submitted, else from session on page load
$formElements['id'] ? $groupId = $formElements['id'] : $groupId = Application::app()->session()->get('tests/left/groupId');
?>

<section id="requestAddModal" class="<?= $formElements['modalClass'] ?>" style="width: 700px;">
    <form id="requestAdd" action="/tests" method="POST">
        <div class="modal-header">
            <div class="font-semibold">Add Request</div>
            <button type="button" name="requestAddCloseButton" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="modal-body h-[400px] overflow-y-auto">
            <div id="modalContent">
                <?php Functions::includeFile(file: '/app/Views/components/requestAdd.php'); ?>
            </div>
        </div>

        <div class="modal-footer">
            <div class="flex">
                <button type="submit" class="primary">Add</button>
                <div id="modalError" class="ml-4 pt-1 font-bold text-red-600 dark:text-red-700"><?php if ($formElements['groupRequestsAddError']) echo 'select a request' ?></div>
            </div>
            <div>
                <button type="button" name="requestAddCancelButton" class="secondary">Cancel</button>
            </div>
        </div>

        <input type="hidden" name="id" value="<?= $groupId ?>">
        <input type="hidden" name="modalName" value="requestAddModal">
        <input type="hidden" name="modelClassName" value="GroupModel">
        <input type="hidden" name="clearCheckboxes" value="true">
        <input type="hidden" name="formAction" value="addRequests">
    </form>
</section>

<script>
$("button[name='requestAddOpenButton']").on("click", function() {
    $("section#requestAddModal div#modalContent").load("/ajax.php", {token: ajaxToken, file: "/app/Views/components/requestAdd.php"});
});
</script>
