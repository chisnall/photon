<?php
use App\Core\Application;

// Get form elements
$formElements = Application::app()->model('CollectionModel')->formElements('update');

// Get collection ID - from form elements if form has been submitted, else from session on page load
$formElements['id'] ? $collectionId = $formElements['id'] : $collectionId = Application::app()->session()->get('home/left/collectionId');

// Get collection variables - from form elements if form has been submitted, else from session on page load
$formElements['collectionVariablesValue'] ? $collectionVariablesInputs = $formElements['collectionVariablesValue'] : $collectionVariablesInputs = Application::app()->session()->get('home/left/collectionVariables') ?? [];
?>

<section id="collectionUpdateModal" data-closefunction="closeCollectionUpdateModal" class="<?= $formElements['modalClass'] ?>">
    <form id="collectionUpdate" action="/" method="POST">
        <div class="modal-header">
            <div class="font-semibold">Edit Collection</div>
            <button type="button" name="collectionUpdateCloseButton" class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="modal-body min-w-[800px] min-h-[350px] max-h-[450px] overflow-y-auto">
            <div class="mb-6">
                <div class="mb-2">Collection name</div>
                <div>
                    <input type="text" name="collectionName" value="<?= $formElements['collectionNameValue'] ?>" autocomplete="one-time-code" spellcheck="false" class="w-[500px] <?= $formElements['collectionNameClass'] ?>"/>
                </div>
                <div class="form-error"><?= $formElements['collectionNameError'] ?></div>
            </div>

            <div>
                <div class="mb-2">Collection variables</div>
                <div>
                    <table id="collectionVariablesInputs" class="table-auto text-left text-sm">
                        <thead>
                        <tr class="h-8">
                            <th class="w-10 px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                            <th class="px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Name</th>
                            <th class="px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Value</th>
                            <th colspan="2" class="px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($collectionVariablesInputs as $collectionVariablesInput): ?>
                            <?php
                            $collectionVariableName = $collectionVariablesInput['name'];
                            $collectionVariableValue = $collectionVariablesInput['value'];
                            $collectionVariableEnabled = $collectionVariablesInput['enabled'];
                            $collectionVariableEnabled == 'on' ? $collectionVariableEnabledCheckbox = ' checked' : $collectionVariableEnabledCheckbox = null;
                            $collectionVariableName = htmlspecialchars($collectionVariableName);
                            $collectionVariableValue = htmlspecialchars($collectionVariableValue);
                            ?>
                            <!--Existing row-->
                            <tr class="h-8">
                                <td class="pt-0 text-center border border-zinc-300 dark:border-zinc-650">
                                    <input type="checkbox" name="collectionVariableCheckbox[]" class="bg-transparent dark:bg-transparent"<?= $collectionVariableEnabledCheckbox ?>>
                                    <input type="hidden" name="collectionVariableEnabled[]" value="<?= $collectionVariableEnabled ?>">
                                </td>
                                <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                    <input type="text" name="collectionVariableName[]" autocomplete="one-time-code" placeholder="Name" spellcheck="false" value="<?= $collectionVariableName ?>" class="w-[200px] px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                </td>
                                <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                    <input type="text" name="collectionVariableValue[]" autocomplete="one-time-code" placeholder="Value" spellcheck="false" value="<?= $collectionVariableValue ?>" class="w-[400px] px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                </td>
                                <td class="w-8 py-1 text-center border border-l-0 border-r-0 border-zinc-300 dark:border-zinc-650">
                                    <button type="button" name="deleteButton" class="px-1 text-base"><i class="fa-solid fa-trash-can"></i></button>
                                </td>
                                <td class="dragHandle cursor-move w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650">
                                    <div id="dragHandle">
                                        <i class="fa-solid fa-grip-vertical"></i>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <!--New row-->
                        <tr class="h-8 nodrop">
                            <td class="pt-0 text-center border border-zinc-300 dark:border-zinc-650">
                                <input type="checkbox" name="collectionVariableCheckbox[]" class="bg-transparent dark:bg-transparent" disabled>
                                <input type="hidden" name="collectionVariableEnabled[]">
                            </td>
                            <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                <input type="text" name="collectionVariableName[]" autocomplete="one-time-code" placeholder="Name" spellcheck="false" class="w-[200px] px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                            </td>
                            <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                <input type="text" name="collectionVariableValue[]" autocomplete="one-time-code" placeholder="Value" spellcheck="false" class="w-[400px] px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                            </td>
                            <td class="w-8 py-1 text-center border border-l-0 border-r-0 border-zinc-300 dark:border-zinc-650">
                                <button type="button" name="deleteButton" class="hidden px-1 text-base"><i class="fa-solid fa-trash-can"></i></button>
                            </td>
                            <td id="tempCell" class="w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"></td>
                            <td id="dragCell" class="hidden dragHandle cursor-move w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650">
                                <i class="fa-solid fa-grip-vertical"></i>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <div>
                <button type="submit" class="primary">Update</button>
            </div>
            <div>
                <button type="button" name="collectionUpdateCancelButton" class="secondary">Cancel</button>
            </div>
        </div>

        <input type="hidden" name="id" value="<?= $collectionId ?>">
        <input type="hidden" name="userId" value="<?= Application::app()->user()->id() ?>">
        <input type="hidden" name="modalName" value="collectionUpdateModal">
        <input type="hidden" name="modelClassName" value="CollectionModel">
        <input type="hidden" name="formAction" value="update">
    </form>
</section>

<script>
// We need to reload the variables table on close
// The html() method below will not work if the modal has been submitted (and has a submit error)
// and changes have been made to the variables table
// The only real way of guaranteeing the table is correct is to reload the page on modal close
// Otherwise we would need to create a duplicate (hidden) table and capture that into JS variable

// var collectionVariablesInputsHtml = $("table#collectionVariablesInputs").html();
// function closeCollectionUpdateModal() {
//     $("table#collectionVariablesInputs").html(collectionVariablesInputsHtml);
// }

function closeCollectionUpdateModal() {
    window.location.href = "/";
}
</script>
