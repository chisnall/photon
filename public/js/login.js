"use strict";

function passwordIcons() {
    // Get display values
    let passwordDisplay = $("form#login input[name='passwordDisplay").val();

    // Set password inputs
    if (passwordDisplay === "show") $("input#password").attr("type", "text");
    if (passwordDisplay === "hide") $("input#password").attr("type", "password");
}

$(window).on("load", function () {
    passwordIcons();
});

$("span#passwordShow, span#passwordHide").on("click", function () {
    // Get input
    let inputId = this.id.slice(0, -4);

    // Get action
    let action = this.id.slice(-4).toLowerCase();

    // Show
    if (action === "show") {
        $("span#" + inputId + "Show").addClass("hidden");
        $("span#" + inputId + "Hide").removeClass("hidden");
        $("input#" + inputId).attr("type", "text");
        $("form#login input[name='" + inputId + "Display']").val("show");
    }
    // Hide
    else if (action === "hide") {
        $("span#" + inputId + "Show").removeClass("hidden");
        $("span#" + inputId + "Hide").addClass("hidden");
        $("input#" + inputId).attr("type", "password");
        $("form#login input[name='" + inputId + "Display']").val("hide");
    }
});
