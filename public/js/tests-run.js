"use strict";

$("button[name='testsRunOpenButton']").on("click", function() {
    modalClose = false; // disable escape key
    let groupId = $("form#testsRun input[name='id']").val();

    //console.log("Tests modal | group: " + groupId);

    // Remove content from last run
    $("section#testsRunModal div#modalContent").html("");

    // Run ajax request to run the tests
    $.ajax({ method: "POST", url: "/ajax.php", data: { token: ajaxToken, class: "App\\Http\\HttpGroupTests", method: "runGroupTests", classParameters: {groupId: groupId} } });

    // Loop to view the output of the tests
    let counter = 1;
    let testsStatus = "started";

    let reload = function() {
        // Reload view
        $("section#testsRunModal div#modalContent").load("/ajax.php", {token: ajaxToken, file: "/app/Views/components/testsRun.php", variables: {groupId: groupId}});

        // Get tests status from counter 3
        // This prevents us receiving the "complete" status from the previous run
        if (counter >= 3) {
            $.ajax({
                method: "POST",
                url: "/ajax.php",
                async: false,
                success: function(response) {
                    testsStatus = response;
                },
                data: { token: ajaxToken, class: "App\\Http\\HttpGroupTests", method: "statusGroupTests", classParameters: {groupId: groupId} }
            });
        } else {
            // Set to running for the first iterations
            testsStatus = "running";
        }

        // Enable/disable buttons
        if (testsStatus === 'running') {
            $("form#testsRun button[name='testsRunCloseButton']").prop("disabled", true);
            $("form#testsRun button[name='testsRunCancelButton']").prop("disabled", true);
            $("form#testsRun button[name='testsRunStopButton']").prop("disabled", false);
        }
        else if (testsStatus === 'complete' || testsStatus === 'stopped') {
            modalClose = true; // enable escape key
            $("form#testsRun button[name='testsRunCloseButton']").prop("disabled", false);
            $("form#testsRun button[name='testsRunCancelButton']").prop("disabled", false);
            $("form#testsRun button[name='testsRunStopButton']").prop("disabled", true);
        }

        //console.log("Counter: " + counter + " | " + $("section#testsRunModal").hasClass("hidden") + " | status: " + testsStatus);

        // Determine loop
        //if (counter <= 3 || (!$("section#testsRunModal").hasClass("hidden") && testsStatus === 'running')) {
        if (counter <= 3 || testsStatus === 'running') {
            // Run reload again
            setTimeout(reload, 250);
        } else {
            // Do cleanup
            $.ajax({ method: "POST", url: "/ajax.php", data: { token: ajaxToken, class: "App\\Http\\HttpGroupTests", method: "cleanupGroupTests", classParameters: {groupId: groupId} } });

            // Reload tabs on the tests page
            $("div#lower-content div#tab1-content").load("/ajax.php", {token: ajaxToken, file: "/app/Views/components/testsSummary.php", variables: {groupId: groupId}});
            $("div#lower-content div#tab2-content").load("/ajax.php", {token: ajaxToken, file: "/app/Views/components/testsResults.php", variables: {groupId: groupId}});
        }

        counter += 1;
    }

    // Start loop
    reload();
});

$("button[name='testsRunStopButton']").on("click", function() {
    let groupId = $("form#testsRun input[name='id']").val();

    //console.log("Stop button pressed | groupId: " + groupId);

    // Run ajax request to stop the tests
    $.ajax({ method: "POST", url: "/ajax.php", data: { token: ajaxToken, class: "App\\Http\\HttpGroupTests", method: "stopGroupTests", classParameters: {groupId: groupId} } });
});
