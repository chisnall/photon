<?php

declare(strict_types=1);

use App\Core\Application;
?>
<section id="collectionDeleteModal" class="modal hidden">
    <form id="collectionDelete" action="/" method="POST">
        <div class="modal-header">
            <div class="font-semibold">Delete Collection</div>
            <button type="button" name="collectionDeleteCloseButton" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="modal-body">
            <div class="mb-6">
                <div>This collection will be deleted:</div>
                <div><?= Application::app()->session()->get('home/left/collectionName') ?></div>
            </div>
            <div>
                <div class="text-red-600 dark:text-red-700 font-bold">WARNING</div>
                <div>This will also delete all requests which belong to the collection.</div>
            </div>
        </div>

        <div class="modal-footer">
            <div>
                <button type="submit" class="primary">Delete</button>
            </div>
            <div>
                <button type="button" name="collectionDeleteCancelButton" class="secondary">Cancel</button>
            </div>
        </div>

        <input type="hidden" name="id" value="<?= Application::app()->session()->get('home/left/collectionId') ?>">
        <input type="hidden" name="modalName" value="collectionDeleteModal">
        <input type="hidden" name="modelClassName" value="CollectionModel">
        <input type="hidden" name="formAction" value="delete">
    </form>
</section>
