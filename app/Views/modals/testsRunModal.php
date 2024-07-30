<?php
use App\Core\Application;
?>

<section id="testsRunModal" class="modal hidden" style="width: 700px;">
    <form id="testsRun">
        <div class="modal-header">
            <div class="font-semibold">Run Requests</div>
            <button type="button" name="testsRunCloseButton" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="modal-body h-[400px] overflow-y-auto">
            <div id="modalContent"></div>
        </div>

        <div class="modal-footer">
            <div class="flex">
                <button type="button" name="testsRunStopButton" class="primary">Stop</button>
            </div>
            <div>
                <button type="button" name="testsRunCancelButton" class="secondary">Close</button>
            </div>
        </div>

        <input type="hidden" name="id" value="<?= Application::app()->session()->get('tests/left/groupId') ?>">
        <input type="hidden" name="modalName" value="testsRunModal">
    </form>
</section>
