var modalOpenName;
var modalClose = true;

function openModal(modalName, inputFocus) {
    // Save modal name
    modalOpenName = modalName;

    // Show modal and overlay
    $("section#" + modalName).removeClass("hidden");
    $("div#modalOverlay").removeClass("hidden");

    // Handle focus
    if (inputFocus) {
        $("input[name='" + inputFocus + "']").focus();
    }
}

function closeModal(modalName) {
    // Get form ID
    let formId = modalName.replace(/Modal$/, "");

    // Get clear checkboxes value
    let clearCheckboxes = $("form#" + formId + " input[name='clearCheckboxes']").val() ?? "false";

    // Process form elements
    $("form#" + formId + " input").each(function() {
        // Check CSS class for error - this determines the input border
        let elementCss = $(this).attr("class");
        if (elementCss && elementCss.includes("input-error")) {
            // Remove error class and add normal class
            $(this).removeClass("input-error");
            $(this).addClass("input-normal");
        }
    });

    // Process form error text
    $("form#" + formId + " div").each(function() {
        // Check CSS class for error - this determines the input border
        let elementCss = $(this).attr("class");
        if (elementCss && elementCss.includes("form-error")) {
            // Remove HTML from the element
            $(this).html("");
        }
    });

    // Process form checkboxes
    if (clearCheckboxes === "true") {
        $("form#" + formId + " input[type='checkbox'").each(function () {
            // Uncheck checkbox
            $(this).prop("checked", false);
        });
    }

    // Remove general error text
    $("div#modalError").html("");

    // Hide modal and overlay
    $("section#" + modalName).addClass("hidden");
    $("div#modalOverlay").addClass("hidden");
}

$(document).ready(function() {
    modalOpenName = $("input[name='modalOpenName']").val();
});

$("button[name$=OpenButton]").on("click", function() {
    // Modal
    let modalName = $(this).attr("data-modal");

    // Focus - optional
    let inputFocus = $(this).attr("data-focus") ?? null;

    // Process "data-" elements in the button
    $.each($(this).data(), function(inputId, inputValue) {
        if (inputId.match("^input_")) {
            // Remove "input_" from the start of the input ID
            inputId = inputId.replace(/^input_/g, "");

            // Set input value in the form
            $("section#" + modalName).find("input[name='" + inputId + "']").val(inputValue);
        }
        else if (inputId.match("^textarea_")) {
            // Remove "textarea_" from the start of the textarea ID
            inputId = inputId.replace(/^textarea_/g, "");

            // Convert input value into string so we can handle JSON
            if (typeof inputValue === "object") {
                inputValue = JSON.stringify(inputValue);
            }

            // Set textarea value in the form
            $("section#" + modalName).find("textarea[name='" + inputId + "']").val(inputValue);
        }
        else if (inputId.match("^div_")) {
            // Remove "div_" from the start of the div ID
            inputId = inputId.replace(/^div_/g, "");

            // Set textarea value in the form
            $("section#" + modalName).find("div#" + inputId).html(inputValue);
        }

    });

    openModal(modalName, inputFocus);
});

$("button[name$=CloseButton], button[name$=CancelButton]").on("click", function() {
    // Get modal name
    let modalName = $(this).closest("section").attr("id");

    // Check for function to be run on close
    let modalCloseFunction = $(this).closest("section").attr("data-closefunction");
    if (modalCloseFunction) window[modalCloseFunction]();

    closeModal(modalName);
});

$(document).on("keyup",function(e) {
    // Escape key - only if overlay is visible
    if (e.keyCode === 27 && !$("div#modalOverlay").hasClass("hidden")) {
        let modalCloseFunction = $("section#" + modalOpenName).attr("data-closefunction");
        if (modalCloseFunction) window[modalCloseFunction]();

        if (modalClose) closeModal(modalOpenName);
    }
});
