"use strict";

// Declare variables
var verticalResizing;
var horizontalResizing;
var containerWidth;
var containerHeight;
var verticalMin;
var verticalMax;
var horizontalMin;
var horizontalMax;
var containerVerticalOffset;

// Set page
if (window.location.pathname === "/") {
    var page = "home";
} else if (window.location.pathname === "/tests") {
    var page = "tests";
}

// Get sections
var leftSection = $("#leftSection");
var rightSection = $("#rightSection");
var topSection = $("#topSection");
var bottomSection = $("#bottomSection");

// Init
init();

function init() {
    //console.log("Init values");

    // Set resizing
    verticalResizing = false;
    horizontalResizing = false;

    // Get container dimensions
    containerWidth = $("#pageSection").width();
    containerHeight = $("#pageSection").height();

    // Definite min/max
    verticalMin = containerWidth * 0.175;
    verticalMax = containerWidth * 0.40;
    horizontalMin = containerHeight * 0.30;
    horizontalMax = containerHeight * 0.70;

    // Get vertical offset
    containerVerticalOffset = $("#pageSection").css("padding-top");
    containerVerticalOffset = containerVerticalOffset.replace("px", "");
    containerVerticalOffset = Number(containerVerticalOffset);
}

function resize() {
    //console.log("Resize");

    // Calculate dimensions
    let leftSectionWidth = leftSection.width();
    let rightSectionWidth = containerWidth - leftSectionWidth - 17;
    let topSectionHeight = topSection.height();
    let bottomSectionHeight = containerHeight - topSectionHeight - 17;

    // Set right and bottom sections
    rightSection.css("width", rightSectionWidth + "px");
    bottomSection.css("height", bottomSectionHeight + "px");

    // Get layout
    let leftWidth = leftSection.css("width");
    let rightWidth = rightSection.css("width");
    let topHeight = topSection.css("height");
    let bottomHeight = bottomSection.css("height");

    // Do AJAX requests
    $.ajax({ method: "POST", url: "/ajax.php", data: { token: ajaxToken, key: page + "/layout", value: {leftSection: leftWidth, rightSection: rightWidth}, process: true } });
    $.ajax({ method: "POST", url: "/ajax.php", data: { token: ajaxToken, key: page + "/layout", value: {topSection: topHeight, bottomSection: bottomHeight}, process: true } });
}

// Convert % based layout to pixels for guest users
if (initGuest) {
    //console.log("Init guest");
    resize();
}

// Init layout for user who has just logged in
if (initUser) {
    //console.log("Init user");
    resize();
}

$(window).on("resize", function() {
    init();
    resize();
});

$("div#borderVertical").on("mousedown", function() {
    //console.log("Mouse clicked: vertical");
    verticalResizing = true;
});

$("div#borderHorizontal").on("mousedown", function() {
    //console.log("Mouse clicked: horizontal");
    horizontalResizing = true;
});

$(document).on("mousemove", function(e) {
    // Return if not resizing
    if (!verticalResizing && !horizontalResizing) {
        return;
    }

    // Vertical resizing
    if (verticalResizing) {
        // Set cursor
        $("body").css("cursor", "col-resize");

        // Set class on border
        $("div#borderVerticalLeft").addClass("borderVerticalLeftDrag");
        $("div#borderVerticalRight").addClass("borderVerticalRightDrag");

        // Remove hover class from other border
        $("div#borderHorizontal").removeClass("borderHorizontalHover");

        // Change width if within min/max
        if (e.clientX > verticalMin && e.clientX < verticalMax) {
            let leftWidth = e.clientX - 8.5;
            let rightWidth = containerWidth - e.clientX - 8.5;
            leftSection.css("width", leftWidth);
            rightSection.css("width", rightWidth);
        }
    }
    // Horizontal resizing
    else if (horizontalResizing) {
        // Set cursor
        $("body").css("cursor", "row-resize");

        // Set class on border
        $("div#borderHorizontalTop").addClass("borderHorizontalTopDrag");
        $("div#borderHorizontalBottom").addClass("borderHorizontalBottomDrag");

        // Remove hover class from other border
        $("div#borderVertical").removeClass("borderVerticalHover");

        // Change height if within min/max
        if (e.clientY > (horizontalMin + containerVerticalOffset) && e.clientY < (horizontalMax + containerVerticalOffset)) {
            let topHeight = e.clientY - containerVerticalOffset - 8.5;
            let bottomHeight = containerHeight - e.clientY + containerVerticalOffset - 8.5;
            topSection.css("height", topHeight);
            bottomSection.css("height", bottomHeight);
        }
    }
}).on("mouseup", function() {
    // Check for resizing
    if (verticalResizing) {
        //console.log("Stop resizing: vertical");
        //console.log("Mouse up: vertical");

        // Stop resizing
        verticalResizing = false;

        // Set cursor
        $("body").css("cursor", "default");

        // Remove class on border
        $("div#borderVerticalLeft").removeClass("borderVerticalLeftDrag");
        $("div#borderVerticalRight").removeClass("borderVerticalRightDrag");

        // Add hover class back to other border
        $("div#borderHorizontal").addClass("borderHorizontalHover");

        // Get layout width
        let leftWidth = leftSection.css("width");
        let rightWidth = rightSection.css("width");

        // Do AJAX request
        $.ajax({ method: "POST", url: "/ajax.php", data: { token: ajaxToken, key: page + "/layout", value: {leftSection: leftWidth, rightSection: rightWidth}, process: true } });
    }
    else if (horizontalResizing) {
        //console.log("Stop resizing: horizontal");
        //console.log("Mouse up: horizontal");

        // Stop resizing
        horizontalResizing = false;

        // Set cursor
        $("body").css("cursor", "default");

        // Remove class on border
        $("div#borderHorizontalTop").removeClass("borderHorizontalTopDrag");
        $("div#borderHorizontalBottom").removeClass("borderHorizontalBottomDrag");

        // Add hover class back to other border
        $("div#borderVertical").addClass("borderVerticalHover");

        // Get layout height
        let topHeight = topSection.css("height");
        let bottomHeight = bottomSection.css("height");

        // Do AJAX request
        $.ajax({ method: "POST", url: "/ajax.php", data: { token: ajaxToken, key: page + "/layout", value: {topSection: topHeight, bottomSection: bottomHeight}, process: true } });
    }
});
