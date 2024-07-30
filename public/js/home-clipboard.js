"use strict";

function showCopied() {
    $("div#clipboard-icon").addClass("hidden");
    $("div#clipboard-copied").removeClass("hidden");
}

function resetToDefault() {
    $("div#clipboard-icon").removeClass("hidden");
    $("div#clipboard-copied").addClass("hidden");
}

$("button#clipboard-button").on("click", function () {
    var clipboardTarget = $("textarea#lower-tab-clipboard");
    var clipboardText = clipboardTarget.val();

    // Check for secure connection
    if (window.isSecureContext && navigator.clipboard) {
        //console.log("Secure");

        // HTTPS connections
        navigator.clipboard.writeText(clipboardText);
    } else {
        //console.log("Not secure");

        // HTTP connections
        clipboardTarget.removeClass("hidden");
        clipboardTarget.select();
        document.execCommand("copy");
        clipboardTarget.addClass("hidden");
    }

    // Show copied icon
    showCopied();

    // Reset to default state
    setTimeout(() => {
        resetToDefault();
    }, 1000);
});
