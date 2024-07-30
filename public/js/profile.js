"use strict";

function passwordIcons() {
    // Get display values
    let newPasswordDisplay = $("form#profile input[name='newPasswordDisplay").val();
    let confirmNewPasswordDisplay = $("form#profile input[name='confirmNewPasswordDisplay").val();

    // Set password inputs
    if (newPasswordDisplay === "show") $("input#newPassword").attr("type", "text");
    if (newPasswordDisplay === "hide") $("input#newPassword").attr("type", "password");
    if (confirmNewPasswordDisplay === "show") $("input#confirmNewPassword").attr("type", "text");
    if (confirmNewPasswordDisplay === "hide") $("input#confirmNewPassword").attr("type", "password");
}

$(window).on("load", function () {
    passwordIcons();
});

$("span#newPasswordShow, span#newPasswordHide, span#confirmNewPasswordShow, span#confirmNewPasswordHide").on("click", function () {
    // Get input
    let inputId = this.id.slice(0, -4);

    // Get action
    let action = this.id.slice(-4).toLowerCase();

    // Show
    if (action === "show") {
        $("span#" + inputId + "Show").addClass("hidden");
        $("span#" + inputId + "Hide").removeClass("hidden");
        $("input#" + inputId).attr("type", "text");
        $("form#profile input[name='" + inputId + "Display']").val("show");
    }
    // Hide
    else if (action === "hide") {
        $("span#" + inputId + "Show").removeClass("hidden");
        $("span#" + inputId + "Hide").addClass("hidden");
        $("input#" + inputId).attr("type", "password");
        $("form#profile input[name='" + inputId + "Display']").val("hide");
    }
});

// Detect CTRL+S
$(document).on("keydown",function(e) {
    if ((e.ctrlKey || e.metaKey) && e.which === 83) {
        // Prevent default browser action
        e.preventDefault();

        // Submit the form
        $("form#profile").trigger("submit");
    }
});
