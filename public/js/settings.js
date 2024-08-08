"use strict";

function tableDragDrop() {
    $("table#globalVariablesInputs").tableDnD({
        dragHandle: ".dragHandle", onDragClass: "dragRow"
    });
}

$(window).on("load", function () {
    tableDragDrop();
});

$("ul.tabs li").on("click", function() {
    let tabId = $(this).attr("id");
    let tabGroup = $(this).closest("ul").attr("id");
    let ajaxKey = "settings/selectedTab";

    //console.log(tabId + " / " + tabGroup);

    // Unselect all tabs and hide all content
    $("ul.tabs#" + tabGroup + " li").removeClass("current");
    $("div#" + tabGroup + "-content div.tab-content").removeClass("current");

    // Select tab and show content
    $("ul.tabs#" + tabGroup + " li#" + tabId).addClass("current");
    $("div#" + tabGroup + "-content div#" + tabId + "-content").addClass("current");

    // Set hidden element
    $("form#settings input[name='selectedTab']").val(tabId);

    // Do AJAX request
    $.ajax({ method: "POST", url: "/ajax.php", data: { token: ajaxToken, key: ajaxKey, value: tabId } });
});

$("table#globalVariablesInputs").on("input", "input", function() {
    let tableId = $(this).closest("table").attr("id");
    let rowCurrent = $(this).closest("tr");
    let rowLast = rowCurrent.is("table#" + tableId + " tr:last");
    if (tableId === "globalVariablesInputs") {
        var rowNew = '<tr class="h-8 nodrop"><td class="pt-0 text-center border border-zinc-300 dark:border-zinc-650"><input type="checkbox" name="globalVariableCheckbox[]" class="bg-transparent dark:bg-transparent" disabled><input type="hidden" name="globalVariableEnabled[]"></td><td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650"><input type="text" name="globalVariableName[]" autocomplete="one-time-code" placeholder="Name" spellcheck="false" class="w-full px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"></td><td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650"><input type="text" name="globalVariableValue[]" autocomplete="one-time-code" placeholder="Value" spellcheck="false" class="w-full px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"></td><td class="w-8 py-1 text-center border border-l-0 border-r-0 border-zinc-300 dark:border-zinc-650"><button type="button" name="deleteButton" class="hidden px-1 text-base"><i class="fa-solid fa-trash-can"></i></button></td><td id="tempCell" class="w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"></td><td id="dragCell" class="hidden dragHandle cursor-move w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"><i class="fa-solid fa-grip-vertical"></i></td></tr>';
        var rowCheckboxElement = "globalVariableCheckbox[]";
        var rowEnabledElement = "globalVariableEnabled[]";
        var rowNameElement = "globalVariableName[]";
        var rowValueElement = "globalVariableValue[]";
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

$("table#globalVariablesInputs").on("click", "button[name='deleteButton']", function () {
    let tableId = $(this).closest("table").attr("id");
    let rowCurrent = $(this).closest("tr");
    let rowLast = rowCurrent.is("table#" + tableId + " tr:last");

    if (!rowLast) {
        //console.log("table: " + tableId + " | delete button");
        rowCurrent.remove();
    }
});

// Detect CTRL+S
$(document).on("keydown",function(e) {
    if ((e.ctrlKey || e.metaKey) && e.which === 83) {
        // Prevent default browser action
        e.preventDefault();

        // Submit the form
        $("form#settings").trigger("submit");
    }
});
