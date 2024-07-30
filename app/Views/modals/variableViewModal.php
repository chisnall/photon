<section id="variableViewModal" class="modal hidden">
    <form id="variableView">
        <div class="modal-header">
            <div class="font-semibold">Variable</div>
            <button type="button" name="variableViewCloseButton" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="modal-body">
            <div class="mb-2">
                <div id="variableName"></div>
            </div>

            <div class="mb-6">
                <textarea readonly name="variableValue" autocomplete="one-time-code" spellcheck="false" class="input-normal w-[800px] resize-none font-mono break-all" rows="15"></textarea>
            </div>
        </div>

        <div class="modal-footer">
            <div>
                <button type="button" name="variableViewCancelButton" class="secondary">Close</button>
            </div>
        </div>

        <input type="hidden" name="modalName" value="variableViewModal">
    </form>
</section>
