"use strict";

// Reset ticks and add tick to selected value
function dropdownTicks(dropdownType, dropdownClass, dropdownValue) {
    // Uppercase first character
    let dropdownTypeUpper = dropdownType.charAt(0).toUpperCase() + dropdownType.slice(1);

    // Set list
    let dropdownList = "dropdown" + dropdownTypeUpper + "List";

    // Remove classes from all <li> elements and set to default class
    $("ul#" + dropdownList).children("li").each(function() {
        $("ul#" + dropdownList + " div#" + this.id + "-tick").removeClass();
        $("ul#" + dropdownList + " div#" + this.id + "-tick").addClass(dropdownClass + "-option-tick " + dropdownClass + "-option-tickoff");
        $("ul#" + dropdownList + " li#" + this.id).removeClass();
        $("ul#" + dropdownList + " li#" + this.id).addClass(dropdownClass + "-" + this.id);
    });

    // Remove class from clicked <li> element and set to selected
    $("ul#" + dropdownList + " div#" + dropdownValue + "-tick").removeClass();
    $("ul#" + dropdownList + " div#" + dropdownValue + "-tick").addClass(dropdownClass + "-option-tick " + dropdownClass + "-option-tickon");
    $("ul#" + dropdownList + " li#" + dropdownValue).removeClass();
    $("ul#" + dropdownList + " li#" + dropdownValue).addClass(dropdownClass + "-" + dropdownValue + " " + dropdownClass + "-option-on");
}

// Show/hide body text type button
function bodyTextTypeButton(dropdownValue) {
    if (dropdownValue === "text") {
        // Show text type button
        $("div#dropdownBodyTextTypeButton").removeClass("hidden");

        // Unable to get this to work - see note at the bottom
        //// Set required attribute on text input
        // $("textarea#requestBodyTextValue").prop("required", true);
        // console.log($("textarea#requestBodyTextValue").prop("required"));
    } else {
        // Hide text type button
        $("div#dropdownBodyTextTypeButton").addClass("hidden");
    }
}

$(window).on("load", function() {
    // Get dropdown values
    let requestMethod = $("form#requestManage input[name='requestMethod").val();
    let requestAuth = $("form#requestManage input[name='requestAuth").val();
    let requestBody = $("form#requestManage input[name='requestBody").val();
    let requestBodyTextType = $("form#requestManage input[name='requestBodyTextType").val();

    // Init ticks for each dropdown
    dropdownTicks("method", "dropdown-method", requestMethod);
    dropdownTicks("auth", "dropdown-general", requestAuth);
    dropdownTicks("body", "dropdown-general", requestBody);
    dropdownTicks("bodyTextType", "dropdown-general", requestBodyTextType);
});

// Dropdown
$("div#dropdownMethodButton, div#dropdownAuthButton, div#dropdownBodyButton, div#dropdownBodyTextTypeButton").on("click", function() {
    // Get element button and list ID
    let dropdownButton = $(this).attr("id");
    let dropdownList = dropdownButton.replace(/Button$/, "List");

    // Handle clicking outside
    $(this).attr("tabindex", 1).focus();

    // Set active class
    $(this).toggleClass("active");

    // Show menu with transition effect
    $(this).children("ul#" + dropdownList).slideToggle(150);
});

$("div#dropdownMethodButton, div#dropdownAuthButton, div#dropdownBodyButton, div#dropdownBodyTextTypeButton").on("focusout", function() {
    // Get element button and list ID
    let dropdownButton = $(this).attr("id");
    let dropdownList = dropdownButton.replace(/Button$/, "List");

    // Remove active class when clicking outside
    $(this).removeClass("active");

    // Hide menu with transition effect
    $(this).children("ul#" + dropdownList).slideUp(150);
});

$("div#dropdownMethodButton ul#dropdownMethodList li, div#dropdownAuthButton ul#dropdownAuthList li, div#dropdownBodyButton ul#dropdownBodyList li, div#dropdownBodyTextTypeButton ul#dropdownBodyTextTypeList li").on("click", function() {
    // Get element list and button ID
    let dropdownType = $(this).closest("ul").attr("data-type");

    // Uppercase first character
    let dropdownTypeUpper = dropdownType.charAt(0).toUpperCase() + dropdownType.slice(1);

    // Set variables
    let dropdownButton = "dropdown" + dropdownTypeUpper + "Button";
    let dropdownValueInput = "request" + dropdownTypeUpper;
    let dropdownValueDiv = "dropdown" + dropdownTypeUpper + "Value";
    let dropdownSession = dropdownValueInput;

    // Get dropdown value
    let dropdownValue = $(this).attr("id");

    // Set hidden form element value
    $("form#requestManage input[name='" + dropdownValueInput + "']").val(dropdownValue);

    // Method type
    if (dropdownType === "method") {
        // Set class for ticks
        var dropdownClass= "dropdown-" + dropdownType;

        // Remove class from button and set to the class of the clicked option
        $("div#" + dropdownValueDiv).removeClass();
        $("div#" + dropdownValueDiv).addClass("dropdown-" + dropdownType + "-value");

        // Get primary class from the selected option
        // We don't want the secondary class
        // Otherwise this adds a background to the dropdown value if the same option is selected twice
        let dropdownSelectedClass = $(this).attr("class").split(" ")[0];
        $("div#" + dropdownValueDiv).addClass(dropdownSelectedClass);

        // Get text of clicked option
        let dropdownText = $(this).attr("data-label");

        // Set button text to the label of the clicked option
        $("div#" + dropdownValueDiv).text(dropdownText);
    }
    // General type
    else if (dropdownType === "auth" || dropdownType === "body" || dropdownType === "bodyTextType") {
        // Set class for ticks
        var dropdownClass= "dropdown-general";

        // Get HTML of clicked option
        let dropdownText = $(this).children("div#" + dropdownValue + "-text").html();

        // Set button icon and text to the label of the clicked option
        $("div#" + dropdownValueDiv).html(dropdownText);

        // Handle sub-content for auth and body
        if (dropdownType === "auth" || dropdownType === "body") {
            // Get tab ID
            let tabId = $("div#" + dropdownButton).attr("data-tab");

            // Hide all sub-content
            $("div#upper-content div#" + tabId + "-content").children("div").each(function () {
                $("div#upper-content div#" + tabId + "-content div#" + this.id).removeClass("current");
            });

            // Show sub-content
            $("div#upper-content div#" + tabId + "-content div#" + tabId + "-subcontent-" + dropdownValue).addClass("current");

            // Show/hide body text type dropdown
            if (dropdownType === "body") {
                bodyTextTypeButton(dropdownValue);
            }
        }

        // Unable to get this to work
        // To re-create the issue:
        // 1) open the console
        // 2) select body type as text
        // 3) submit nothing and get the "Enter the text" popup
        // 4) select body type as none
        // 5) submit again
        // ---
        //// Set required attribute on text input
        // if (dropdownType === "body" && dropdownValue === "text") {
        //     console.log("body: require text");
        //     $("textarea#requestBodyTextValue").prop("required", true);
        //     console.log($("textarea#requestBodyTextValue").prop("required"));
        // }
        // else if (dropdownType === "body") {
        //     console.log("body: do not require text");
        //     $("textarea#requestBodyTextValue").prop("required", false);
        //     console.log($("textarea#requestBodyTextValue").prop("required"));
        // }
    }

    // Reset ticks
    dropdownTicks(dropdownType, dropdownClass, dropdownValue);

    // Indicate request has been modified
    $("i#requestModified").removeClass("hidden");

    // Do AJAX request
    $.ajax({ method: "POST", url: "/ajax.php", data: { token: ajaxToken, key: "home/upper/" + dropdownSession, value: dropdownValue } });
});
