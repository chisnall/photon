"use strict";

function passwordIcons() {
    // Get display values
    let passwordDisplay = $("form#register input[name='passwordDisplay").val();
    let confirmPasswordDisplay = $("form#register input[name='confirmPasswordDisplay").val();

    // Set password inputs
    if (passwordDisplay === "show") $("input#password").attr("type", "text");
    if (passwordDisplay === "hide") $("input#password").attr("type", "password");
    if (confirmPasswordDisplay === "show") $("input#confirmPassword").attr("type", "text");
    if (confirmPasswordDisplay === "hide") $("input#confirmPassword").attr("type", "password");
}

$(window).on("load", function () {
    passwordIcons();
});

$("span#passwordShow, span#passwordHide, span#confirmPasswordShow, span#confirmPasswordHide").on("click", function () {
    // Get input
    let inputId = this.id.slice(0, -4);

    // Get action
    let action = this.id.slice(-4).toLowerCase();

    // Show
    if (action === "show") {
        $("span#" + inputId + "Show").addClass("hidden");
        $("span#" + inputId + "Hide").removeClass("hidden");
        $("input#" + inputId).attr("type", "text");
        $("form#register input[name='" + inputId + "Display']").val("show");
    }
    // Hide
    else if (action === "hide") {
        $("span#" + inputId + "Show").removeClass("hidden");
        $("span#" + inputId + "Hide").addClass("hidden");
        $("input#" + inputId).attr("type", "password");
        $("form#register input[name='" + inputId + "Display']").val("hide");
    }
});

$('input#createCollectionCheckbox').change(function() {
    if (this.checked) {
        $("input#createCollection").val("on");
    } else {
        $("input#createCollection").val("off");
    }
});
