"use strict";

function ajaxBodyFiles(ajaxKey, ajaxValue) {
    // Indicate request has been modified
    $("i#requestModified").removeClass("hidden");

    // Do AJAX request
    $.ajax({ method: "POST", url: "/ajax.php", data: { token: ajaxToken, key: ajaxKey, value: ajaxValue } });
}

function uncheckALlFiles() {
    $("table#bodyFiles").find("input[type='checkbox']").each(function () {
        $(this).prop("checked", false);
    });
}

$("table#bodyFiles").on("click", "input[type='checkbox']", function (e) {
    // If checked
    if ($(this).prop( "checked")) {
        // Get checkbox value
        var requestBodyFileExisting = $(this).val();

        // Uncheck all files
        uncheckALlFiles();

        // Check checkbox
        $(this).prop("checked", true);

        // Set new file upload to none
        $("input#requestBodyFile").val("");

        // Set label next to upload button
        $("div#requestBodyFileValue").text("");

        // Hide clear icon
        $("div#requestBodyFileClear").addClass("hidden");
    } else {
        var requestBodyFileExisting = null;
    }

    // Do AJAX request
    ajaxBodyFiles("home/upper/requestBodyFileExisting", requestBodyFileExisting);
});

$("table#bodyFiles").on("click", "button[name='deleteButton']", function () {
    // Get current row
    let rowCurrent = $(this).closest("tr");
    let requestBodyFilePath = $(rowCurrent).find("input[name='requestBodyFileExisting']").val();

    // Delete row from the table
    rowCurrent.remove();

    // Hide table if last file is deleted
    let rowCount = $("table#bodyFiles tr").length;
    if (rowCount === 1) {
        $("table#bodyFiles").addClass("hidden");
    }

    // Do AJAX request
    ajaxBodyFiles("home/upper/requestBodyFileDelete", requestBodyFilePath);
});

$("input#requestBodyFile").on("change", function () {
    // Get filename without the "fakepath"
    let fileName = $("input#requestBodyFile").get(0).files.item(0).name;

    // Set label next to upload button
    $("div#requestBodyFileValue").text(fileName);

    // Show clear icon
    $("div#requestBodyFileClear").removeClass("hidden");

    // Uncheck all files
    uncheckALlFiles();

    // Do AJAX request
    ajaxBodyFiles("home/upper/requestBodyFileExisting", null);
});

$("div#requestBodyFileClear").on("click", function () {
    // Set new file upload to none
    $("input#requestBodyFile").val("");

    // Set label next to upload button
    $("div#requestBodyFileValue").text("");

    // Hide clear icon
    $("div#requestBodyFileClear").addClass("hidden");
});
