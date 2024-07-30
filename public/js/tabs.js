"use strict";

// Set page
if (window.location.pathname === "/") {
    var page = "home";
} else if (window.location.pathname === "/tests") {
    var page = "tests";
}

function clipboardIcon (tabId) {
    let lower_selectedTabContent = $("textarea#lower-" + tabId + "-clipboard").val();

    $("textarea#lower-tab-clipboard").val(lower_selectedTabContent);

    if (lower_selectedTabContent !== "") {
        //console.log(tabId + ": show");
        $("div#clipboard-button-enabled").show();
        $("div#clipboard-button-disabled").hide();
    } else {
        //console.log(tabId + ": hide");
        $("div#clipboard-button-enabled").hide();
        $("div#clipboard-button-disabled").show();
    }

    //console.log("Running: clipboardIcon() | " + tabId);
}

$(window).on("load", function() {
    // Get lower selected tab
    let lower_selectedTab = $("input[name='lower_selectedTab']").val();

    // Handle clipboard icon
    if (page === "home") {
        clipboardIcon(lower_selectedTab);
    }
});

$("ul.tabs li").on("click", function() {
    let tabId = $(this).attr("id");
    let tabGroup = $(this).closest("ul").attr("id");
    let ajaxKey = page + "/" + tabGroup + "/selectedTab";

    // Unselect all tabs and hide all content
    $("ul.tabs#" + tabGroup + " li").removeClass("current");
    $("div#" + tabGroup + "-content div.tab-content").removeClass("current");

    // Select tab and show content
    $("ul.tabs#" + tabGroup + " li#" + tabId).addClass("current");
    $("div#" + tabGroup + "-content div#" + tabId + "-content").addClass("current");

    // Set hidden element
    $("form#" + formId + " input[name='" + tabGroup + "_selectedTab']").val(tabId);

    // Hide all tab headers
    $("div#" + tabGroup + "-header div.tab-header").removeClass("current");

    // Hide all tab footers
    $("div#" + tabGroup + "-footer div.tab-footer").removeClass("current");

    // Check for tab header
    let tabHeader = $("div#" + tabGroup + "-header div#" + tabId + "-header");
    if (tabHeader.length) {
        //console.log(tabId + " / " + tabGroup + " / header is present");
        $(tabHeader).addClass("current");
    }

    // Check for tab footer
    let tabFooter = $("div#" + tabGroup + "-footer div#" + tabId + "-footer");
    if (tabFooter.length) {
        //console.log(tabId + " / " + tabGroup + " / footer is present");
        $(tabFooter).addClass("current");
    }

    // Handle clipboard icon
    if (page === "home" && tabGroup === "lower") {
        clipboardIcon(tabId)
    }

    // Do AJAX request
    $.ajax({ method: "POST", url: "/ajax.php", data: { token: ajaxToken, key: ajaxKey, value: tabId } });
});
