"use strict";

function ajaxRequest(key, value, modified = true) {
    // Indicate request has been modified
    if (modified) $("i#requestModified").removeClass("hidden");

    // Do AJAX request
    $.ajax({ method: "POST", url: "/ajax.php", data: {token: ajaxToken, key: key, value: value} });
}

function ajaxTable(tableId) {
    if (tableId === "requestParamsInputs") {
        var rowEnabledElement = "requestParamEnabled[]";
        var rowNameElement = "requestParamName[]";
        var rowValueElement = "requestParamValue[]";
    } else if (tableId === "requestHeadersInputs") {
        var rowEnabledElement = "requestHeaderEnabled[]";
        var rowNameElement = "requestHeaderName[]";
        var rowValueElement = "requestHeaderValue[]";
    } else if (tableId === "requestBodyFormInputs") {
        var rowEnabledElement = "requestBodyFormInputEnabled[]";
        var rowNameElement = "requestBodyFormInputName[]";
        var rowValueElement = "requestBodyFormInputValue[]";
    } else if (tableId === "requestVariablesInputs") {
        var rowEnabledElement = "requestVariableEnabled[]";
        var rowNameElement = "requestVariableKey[]";
        var rowValueElement = "requestVariableName[]";
    } else {
        return;
    }

    //console.log("table: " + tableId + " | ajax request");

    let dataArray = [];

    $("table#" + tableId + " > tbody > tr").each(function () {
        let rowEnabledVal = $(this).find("input[name='" + rowEnabledElement + "']").val();
        let rowNameVal = $(this).find("input[name='" + rowNameElement + "']").val();
        let rowValueVal = $(this).find("input[name='" + rowValueElement + "']").val();

        // Check for empty rows
        // This covers both the final empty line, and rows that were deleted due
        // to removing both the key and the value
        if (rowNameVal !== "" || rowValueVal !== "") {
            if (tableId === "requestVariablesInputs") {
                dataArray.push({"enabled": rowEnabledVal, "key": rowNameVal, "name": rowValueVal})
            } else {
                dataArray.push({"enabled": rowEnabledVal, "name": rowNameVal, "value": rowValueVal})
            }
        }
        //else {
        //    console.log("EMPTY | " + rowEnabledVal + ' | ' + rowNameVal + ' | ' + rowValueVal);
        //}
    });

    // Post requests cannot handle empty arrays (they are missing from $_POST)
    // so use placeholder for empty arrays
    if ( dataArray.length === 0 ) {
        dataArray = '{{emptyArray}}'
    }

    // Set AJAX key
    let ajaxKey = "home/upper/" + tableId;

    // Do AJAX request
    ajaxRequest(ajaxKey, dataArray);
}

function ajaxInput() {
    // Get input name
    let inputName = this.name;

    // Get input value
    let inputVal = $(this).val();

    // Set AJAX key
    let ajaxKey = "home/upper/" + inputName;

    // Do AJAX request
    ajaxRequest(ajaxKey, inputVal);
}

function tableDragDrop() {
    $("table#requestParamsInputs, table#requestHeadersInputs, table#requestBodyFormInputs, table#requestVariablesInputs, table#collectionVariablesInputs").tableDnD({
        onDragStop: function (table) {
            //console.log("table: " + table.id + " | dropped row");
            ajaxTable(table.id);
        },

        dragHandle: ".dragHandle", onDragClass: "dragRow"
    });

    //console.log("init tableDnD");
}

$(document).ready(function(){
    window.history.replaceState( null, null, window.location.href );
});

$(window).on("load", function () {
    tableDragDrop();
});

$("table#collectionsList tr").on("click", function () {
    let rowId = $(this).attr("id");
    window.location.href = "/?select=collection&id=" + rowId;
});

$("table#requestsList tr").on("click", function () {
    let rowId = $(this).attr("id");
    window.location.href = "/?select=request&id=" + rowId;
});

$("table#variablesList tr button[name='variableClearButton']").on("click", function () {
    let collectionId = $("form#requestManage input[name='collectionId").val();
    let variableName = $(this).val();
    window.location.href = "/?select=variable&collection=" + collectionId + "&variable=" + variableName;
});

$("button#sendSubmitButton, button#saveSubmitButton, button#cloneSubmitButton, button#deleteSubmitButton").on("click", function () {
    // Get form action
    let formAction = $(this).val();

    // Set hidden element
    $("form#requestManage input[name='formAction']").val(formAction);

    // Submit form
    $("form#requestManage").trigger("submit");
});

$("form#requestManage").submit(function() {
    // Get form action
    let formAction = $("form#requestManage input[name='formAction']").val();

    // Check for send action
    if (formAction === "send") {
        // Hide URL input error
        $("div#requestUrlError").addClass("hidden");

        // Only show icon for slower requests
        setTimeout(function () {
            // Hide response status
            $("div#responseStatus").addClass("hidden");

            // Show response progress
            $("div#responseStatusProgress").removeClass("hidden");
        }, 250);
    }
});

$("table#requestParamsInputs, table#requestHeadersInputs, table#requestBodyFormInputs, table#requestVariablesInputs, table#collectionVariablesInputs").on("input", "input", function() {
    let tableId = $(this).closest("table").attr("id");
    let rowCurrent = $(this).closest("tr");
    let rowLast = rowCurrent.is("table#" + tableId + " tr:last");
    if (tableId === "requestParamsInputs") {
        var rowNew = '<tr class="h-8 nodrop"><td class="w-10 pt-0 text-center border border-zinc-300 dark:border-zinc-650"><input type="checkbox" name="requestParamCheckbox[]" class="bg-transparent dark:bg-transparent" disabled><input type="hidden" name="requestParamEnabled[]"></td><td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650"><input type="text" name="requestParamName[]" autocomplete="one-time-code" placeholder="Name" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"></td><td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650"><input type="text" name="requestParamValue[]" autocomplete="one-time-code" placeholder="Value" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"></td><td class="w-8 py-1 text-center border border-r-0 border-zinc-300 dark:border-zinc-650"><button type="button" name="deleteButton" class="hidden px-1 text-base"><i class="fa-solid fa-trash-can"></i></button></td><td id="tempCell" class="w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"></td><td id="dragCell" class="hidden dragHandle cursor-move w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"><i class="fa-solid fa-grip-vertical"></i></td></tr>';
        var rowCheckboxElement = "requestParamCheckbox[]";
        var rowEnabledElement = "requestParamEnabled[]";
        var rowNameElement = "requestParamName[]";
        var rowValueElement = "requestParamValue[]";
    } else if (tableId === "requestHeadersInputs") {
        var rowNew = '<tr class="h-8 nodrop"><td class="w-10 pt-0 text-center border border-zinc-300 dark:border-zinc-650"><input type="checkbox" name="requestHeaderCheckbox[]" class="bg-transparent dark:bg-transparent" disabled><input type="hidden" name="requestHeaderEnabled[]"></td><td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650"><input type="text" name="requestHeaderName[]" autocomplete="one-time-code" placeholder="Name" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"></td><td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650"><input type="text" name="requestHeaderValue[]" autocomplete="one-time-code" placeholder="Value" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"></td><td class="w-8 py-1 text-center border border-r-0 border-zinc-300 dark:border-zinc-650"><button type="button" name="deleteButton" class="hidden px-1 text-base"><i class="fa-solid fa-trash-can"></i></button></td><td id="tempCell" class="w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"></td><td id="dragCell" class="hidden dragHandle cursor-move w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"><i class="fa-solid fa-grip-vertical"></i></td></tr>';
        var rowCheckboxElement = "requestHeaderCheckbox[]";
        var rowEnabledElement = "requestHeaderEnabled[]";
        var rowNameElement = "requestHeaderName[]";
        var rowValueElement = "requestHeaderValue[]";
    } else if (tableId === "requestBodyFormInputs") {
        var rowNew = '<tr class="h-8 nodrop"><td class="w-10 pt-0 text-center border border-zinc-300 dark:border-zinc-650"><input type="checkbox" name="requestBodyFormInputCheckbox[]" class="bg-transparent dark:bg-transparent" disabled><input type="hidden" name="requestBodyFormInputEnabled[]"></td><td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650"><input type="text" name="requestBodyFormInputName[]" autocomplete="one-time-code" placeholder="Name" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"></td><td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650"><input type="text" name="requestBodyFormInputValue[]" autocomplete="one-time-code" placeholder="Value" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"></td><td class="w-8 py-1 text-center border border-r-0 border-zinc-300 dark:border-zinc-650"><button type="button" name="deleteButton" class="hidden px-1 text-base"><i class="fa-solid fa-trash-can"></i></button></td><td id="tempCell" class="w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"></td><td id="dragCell" class="hidden dragHandle cursor-move w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"><i class="fa-solid fa-grip-vertical"></i></td></tr>';
        var rowCheckboxElement = "requestBodyFormInputCheckbox[]";
        var rowEnabledElement = "requestBodyFormInputEnabled[]";
        var rowNameElement = "requestBodyFormInputName[]";
        var rowValueElement = "requestBodyFormInputValue[]";
    } else if (tableId === "requestVariablesInputs") {
        var rowNew = '<tr class="h-8 nodrop"><td class="pt-0 text-center border border-zinc-300 dark:border-zinc-650"><input type="checkbox" name="requestVariableCheckbox[]" class="bg-transparent dark:bg-transparent" disabled><input type="hidden" name="requestVariableEnabled[]"></td><td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650"><input type="text" name="requestVariableKey[]" autocomplete="one-time-code" placeholder="JSON key" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"></td><td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650"><input type="text" name="requestVariableName[]" autocomplete="one-time-code" placeholder="Variable name" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"></td><td class="px-2 py-1 font-mono border border-zinc-300 dark:border-zinc-650"></td><td class="w-8 py-1 text-center border border-r-0 border-zinc-300 dark:border-zinc-650"><button type="button" name="showButton" class="hidden px-1 text-base" disabled><i class="fa-solid fa-magnifying-glass text-zinc-300 dark:text-zinc-500"></i></button></td><td class="w-8 py-1 text-center border border-l-0 border-r-0 border-zinc-300 dark:border-zinc-650"><button type="button" name="clearButton" class="hidden px-1 text-base" disabled><i class="fa-solid fa-arrow-rotate-left text-zinc-300 dark:text-zinc-500"></i></button></td><td class="w-8 py-1 text-center border border-l-0 border-r-0 border-zinc-300 dark:border-zinc-650"><button type="button" name="deleteButton" class="hidden px-1 text-base"><i class="fa-solid fa-trash-can"></i></button></td><td id="tempCell" class="w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"></td><td id="dragCell" class="hidden dragHandle cursor-move w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"><i class="fa-solid fa-grip-vertical"></i></td></tr>';
        var rowCheckboxElement = "requestVariableCheckbox[]";
        var rowEnabledElement = "requestVariableEnabled[]";
        var rowNameElement = "requestVariableKey[]";
        var rowValueElement = "requestVariableName[]";
    } else if (tableId === "collectionVariablesInputs") {
        var rowNew = '<tr class="h-8 nodrop"><td class="pt-0 text-center border border-zinc-300 dark:border-zinc-650"><input type="checkbox" name="collectionVariableCheckbox[]" class="bg-transparent dark:bg-transparent" disabled><input type="hidden" name="collectionVariableEnabled[]"></td><td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650"><input type="text" name="collectionVariableName[]" autocomplete="one-time-code" placeholder="Name" spellcheck="false" class="w-[200px] px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"></td><td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650"><input type="text" name="collectionVariableValue[]" autocomplete="one-time-code" placeholder="Value" spellcheck="false" class="w-[400px] px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"></td><td class="w-8 py-1 text-center border border-l-0 border-r-0 border-zinc-300 dark:border-zinc-650"><button type="button" name="deleteButton" class="hidden px-1 text-base"><i class="fa-solid fa-trash-can"></i></button></td><td id="tempCell" class="w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"></td><td id="dragCell" class="hidden dragHandle cursor-move w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"><i class="fa-solid fa-grip-vertical"></i></td></tr>';
        var rowCheckboxElement = "collectionVariableCheckbox[]";
        var rowEnabledElement = "collectionVariableEnabled[]";
        var rowNameElement = "collectionVariableName[]";
        var rowValueElement = "collectionVariableValue[]";
    }

    let rowEnabled = $(rowCurrent).find("input[name='" + rowEnabledElement + "']");
    let rowNameVal = $(rowCurrent).find("input[name='" + rowNameElement + "']").val();
    let rowValueVal = $(rowCurrent).find("input[name='" + rowValueElement + "']").val();

    //console.log("table: " + tableId + " | input detected");

    // Handle checkboxes
    if ($(this).attr("type") === "checkbox") {
        //console.log("table: " + tableId + " | clicked on checkbox");
        if ($(this).is(":checked")) {
            $(rowEnabled).val("on");
            //console.log(tableId + " | checkbox | on");
        } else {
            $(rowEnabled).val("off");
            //console.log(tableId + " | checkbox | off");
        }
    }

    // If this is the last row and any of the inputs are not empty, add a new row
    if (rowLast && (rowNameVal !== "" || rowValueVal !== "")) {
        let tempCell = $(rowCurrent).find("td[id='tempCell']"); // cell
        let dragCell = $(rowCurrent).find("td[id='dragCell']"); // cell
        let rowCheckbox = $(rowCurrent).find("input[name='" + rowCheckboxElement + "']");
        let deleteButton = $(rowCurrent).find("button[name='deleteButton']");
        let showButton = $(rowCurrent).find("button[name='showButton']");
        let clearButton = $(rowCurrent).find("button[name='clearButton']");

        $(rowCheckbox).prop("disabled", false);
        $(rowCheckbox).prop("checked", true);
        $(rowEnabled).val("on");
        $(deleteButton).removeClass("hidden");
        $(tempCell).addClass("hidden");
        $(dragCell).removeClass("hidden");
        $(rowCurrent).removeClass("nodrop");
        $(showButton).removeClass("hidden");
        $(clearButton).removeClass("hidden");

        $("table#" + tableId + " tbody").append(rowNew);

        //console.log(tableId + " | add row");

        tableDragDrop();
    }
    // If both name and value are empty, delete the row, unless it is the last row
    else if (rowNameVal === "" && rowValueVal === "" && !rowLast) {
        // This will cause the row to lose focus, automatically triggering the on("change") event below
        //console.log(tableId + " | delete row");
        rowCurrent.remove();
    }
});

$("table#requestParamsInputs, table#requestHeadersInputs, table#requestBodyFormInputs, table#requestVariablesInputs").on("change", "input", function () {
    let tableId = $(this).closest("table").attr("id");
    //console.log("table: " + tableId + " | data has changed");
    ajaxTable(tableId);
});

$("table#requestParamsInputs, table#requestHeadersInputs, table#requestBodyFormInputs, table#requestVariablesInputs, table#collectionVariablesInputs").on("click", "button[name='deleteButton']", function () {
    let tableId = $(this).closest("table").attr("id");
    let rowCurrent = $(this).closest("tr");
    let rowLast = rowCurrent.is("table#" + tableId + " tr:last");

    if (!rowLast) {
        //console.log("table: " + tableId + " | delete button");
        rowCurrent.remove();
        ajaxTable(tableId);
    }
});

$("table#requestVariablesInputs").on("click", "button[name='clearButton']", function () {
    let collectionId = $(this).attr("data-collection");
    let variableName = $(this).attr("data-variable");
    window.location.href = "/?select=variable&collection=" + collectionId + "&variable=" + variableName;
});

$("input#requestUrl, input#requestName").on("change", ajaxInput);
$("table#requestAuthBasic, table#requestAuthToken, table#requestAuthHeader").on("change", "input, textarea", ajaxInput);
$("textarea#requestBodyTextValue").on("change", ajaxInput);

$("td#requestAuthBasicPasswordShow, td#requestAuthBasicPasswordHide, td#requestAuthTokenValueShow, td#requestAuthTokenValueHide, td#requestAuthHeaderValueShow, td#requestAuthHeaderValueHide").on("click", function () {
    // Get input
    let inputId = this.id.slice(0, -4);

    // Get action
    let action = this.id.slice(-4).toLowerCase();

    // Show
    if (action === "show") {
        $("td#" + inputId + "Show").addClass("hidden");
        $("td#" + inputId + "Hide").removeClass("hidden");

        if (inputId === "requestAuthBasicPassword" || inputId === "requestAuthTokenValue") {
            $("input#" + inputId).attr("type", "text");
        }
        else if (inputId === "requestAuthHeaderValue") {
            $("textarea#" + inputId).removeClass("textarea-password");
        }
    }
    // Hide
    else if (action === "hide") {
        $("td#" + inputId + "Show").removeClass("hidden");
        $("td#" + inputId + "Hide").addClass("hidden");

        if (inputId === "requestAuthBasicPassword" || inputId === "requestAuthTokenValue") {
            $("input#" + inputId).attr("type", "password");
        }
        else if (inputId === "requestAuthHeaderValue") {
            $("textarea#" + inputId).addClass("textarea-password");
        }
    }
});

// Enter pressed on URL or request name inputs
$("input#requestUrl, input#requestName").on("keydown",function(e) {
    if (e.which === 13) {
        // Prevent default browser action
        e.preventDefault();

        // Get form action
        let formAction = $(this).attr("data-action");

        // Set form action and submit the form
        $("form#requestManage input[name='formAction']").val(formAction);
        $("form#requestManage").trigger("submit");
    }
});

// Detect CTRL+S
$(document).on("keydown",function(e) {
    if ((e.ctrlKey || e.metaKey) && e.which === 83) {
        // Prevent default browser action
        e.preventDefault();

        // Only run this if the modal is hidden
        if ($("div#modalOverlay").hasClass("hidden")) {
            // Set form action to "save" and submit the form
            $("form#requestManage input[name='formAction']").val("save");
            $("form#requestManage").trigger("submit");
        }
    }
});
