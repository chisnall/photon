<section id="testDeleteModal" class="modal hidden">
    <form id="testDelete" action="/" method="POST">
        <div class="modal-header">
            <div class="font-semibold">Delete Unit Test</div>
            <button type="button" name="testDeleteCloseButton" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="modal-body">
            <div class="mb-6">
                <div class="mb-2">This test will be deleted:</div>
                <input type="text" name="testName" value="" autocomplete="one-time-code" spellcheck="false" class="p-0 w-full border-transparent bg-white dark:bg-black cursor-default" style="pointer-events: none;" disabled/>
            </div>
        </div>

        <div class="modal-footer">
            <div>
                <button type="submit" class="primary">Delete</button>
            </div>
            <div>
                <button type="button" name="testDeleteCancelButton" class="secondary">Cancel</button>
            </div>
        </div>

        <input type="hidden" name="id" value="">
        <input type="hidden" name="modalName" value="testDeleteModal">
        <input type="hidden" name="modelClassName" value="TestModel">
        <input type="hidden" name="formAction" value="delete">
    </form>
</section>
