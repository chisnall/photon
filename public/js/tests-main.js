"use strict";

function ajaxRequest(key, value) {
    // Indicate group has been modified
    $("i#groupModified").removeClass("hidden");

    // Do AJAX request
    $.ajax({ method: "POST", url: "/ajax.php", data: {token: ajaxToken, key: key, value: value} });
}

function ajaxTable(tableId) {
    if (tableId === "groupRequests") {
        var rowIdElement = "groupRequestId[]";
        var rowEnabledElement = "groupRequestEnabled[]";
    }

    //console.log("table: " + tableId + " | ajax request");

    let dataArray = [];

    $("table#" + tableId + " > tbody > tr").each(function () {
        let rowIdVal = $(this).find("input[name='" + rowIdElement + "']").val();
        let rowEnabledVal = $(this).find("input[name='" + rowEnabledElement + "']").val();
        dataArray.push({"id": rowIdVal, "enabled": rowEnabledVal})
    });

    // Post requests cannot handle empty arrays (they are missing from $_POST)
    // so use placeholder for empty arrays
    if ( dataArray.length === 0 ) {
        dataArray = '{{emptyArray}}'
    }

    // Set AJAX key
    let ajaxKey = "tests/upper/" + tableId;

    // Do AJAX request
    ajaxRequest(ajaxKey, dataArray);
}

function ajaxInput() {
    // Get input name
    let inputName = this.name;

    // Get input value
    let inputVal = $(this).val();

    // Set AJAX key
    let ajaxKey = "tests/upper/" + inputName;

    // Do AJAX request
    ajaxRequest(ajaxKey, inputVal);
}

function tableDragDrop() {
    $("table#groupRequests").tableDnD({
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

$("table#groupsList tr").on("click", function () {
    let rowId = $(this).attr("id");
    window.location.href = "/tests?select=group&id=" + rowId;
});

$("button#saveSubmitButton").on("click", function () {
    // Get form action
    let formAction = $(this).val();

    // Set hidden element
    $("form#groupManage input[name='formAction']").val(formAction);

    // Submit form
    $("form#groupManage").trigger("submit");
});

$("table#groupRequests").on("input", "input", function() {
    let tableId = $(this).closest("table").attr("id");
    let rowCurrent = $(this).closest("tr");
    if (tableId === "groupRequests") {
        var rowEnabledElement = "groupRequestEnabled[]";
    }
    let rowEnabled = $(rowCurrent).find("input[name='" + rowEnabledElement + "']");

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
});

$("table#groupRequests").on("change", "input", function () {
    let tableId = $(this).closest("table").attr("id");
    //console.log("table: " + tableId + " | data has changed");
    ajaxTable(tableId);
});

$("table#groupRequests").on("click", "button[name='deleteButton']", function () {
    let tableId = $(this).closest("table").attr("id");
    let rowCurrent = $(this).closest("tr");

    rowCurrent.remove();

    // Remove table if all rows have been deleted
    let rowCount = $("table#" + tableId + " tbody tr").length;
    if (rowCount === 0) {
        $("table#" + tableId).remove();
        $("div#" + tableId).html("No requests have been added yet");
    }

    ajaxTable(tableId);
});

$("form#groupManage input[name='groupName']").on("change", ajaxInput);

// Enter pressed on group name input
$("input#groupName").on("keydown",function(e) {
    if (e.which === 13) {
        // Prevent default browser action
        e.preventDefault();

        // Get form action
        let formAction = $(this).attr("data-action");

        // Set form action and submit the form
        $("form#groupManage input[name='formAction']").val(formAction);
        $("form#groupManage").trigger("submit");
    }
});

// Detect CTRL+S
$(document).on("keydown",function(e) {
    // Detect CTRL+S
    if ((e.ctrlKey || e.metaKey) && e.which === 83) {
        // Prevent default browser action
        e.preventDefault();

        // Get selected group ID
        let groupId = $("form#groupManage input[name='id']").val();

        // Only run this if a group is selected and the modal is hidden
        if (groupId && $("div#modalOverlay").hasClass("hidden")) {
            // Set form action to "save" and submit the form
            $("form#groupManage input[name='formAction']").val("save");
            $("form#groupManage").trigger("submit");
        }
    }
});
