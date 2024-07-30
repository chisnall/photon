<?php
/**  @var int $groupId **/

use App\Functions\Output;

// Check for results
$resultsData = null;
if ($groupId) {
    $resultsFile = "/var/lib/photon/output/groupTests-$groupId-results";
    if (file_exists($resultsFile)) {
        $resultsData = unserialize(file_get_contents($resultsFile));
        $resultsTime = $resultsData['time'];
        $resultsRequestsRun = $resultsData['requestsRun'];
        $resultsRequestsValid = $resultsData['requestsValid'];
        $resultsRequestsInvalid = $resultsData['requestsInvalid'];
        $resultsTestsRun = $resultsData['testsRun'];
        $resultsTestsPassed = $resultsData['testsPassed'];
        $resultsTestsFailed = $resultsData['testsFailed'];
        $resultsTestsSkipped = $resultsData['testsSkipped'];

        // Format tests age
        $resultsAgeDisplay = Output::ageFormat($resultsTime);

        // CSS classes
        $cssPassed = 'text-white bg-green-700 dark:bg-lime-700';
        $cssFailed = 'text-white bg-red-600 dark:bg-red-700';
        $cssSkipped = 'text-white bg-zinc-500 dark:bg-zinc-600';

        // Determine cell CSS classes
        $resultsRequestsValid == $resultsRequestsRun ? $resultsRequestsValidCss = $cssPassed : $resultsRequestsValidCss = $cssFailed;
        $resultsRequestsInvalid == 0 ? $resultsRequestsInvalidCss = $cssPassed : $resultsRequestsInvalidCss = $cssFailed;
        $resultsTestsPassed == $resultsTestsRun ? $resultsTestsPassedCss = $cssPassed : $resultsTestsPassedCss = $cssFailed;
        $resultsTestsFailed == 0 ? $resultsTestsFailedCss = $cssPassed : $resultsTestsFailedCss = $cssFailed;
        $resultsTestsSkipped == 0 ? $resultsTestsSkippedCss = $cssPassed : $resultsTestsSkippedCss = $cssSkipped;
    }
}

//dump($resultsData);
?>

<?php if ($resultsData): ?>
    <div class="mr-5 mb-5">
        <table class="table-auto text-left text-sm">
            <tr>
                <th class="table-heading">Run</th>
                <td class="table-cell"><?= $resultsAgeDisplay ?></td>
            </tr>
            <tr>
                <th class="table-heading">Requests run</th>
                <td class="table-cell text-right"><?= $resultsRequestsRun ?></td>
            </tr>
            <tr>
                <th class="table-heading">Tests run</th>
                <td class="table-cell text-right"><?= $resultsTestsRun ?></td>
            </tr>
            <tr>
                <th class="table-heading">Requests valid</th>
                <td class="table-cell text-right <?= $resultsRequestsValidCss ?>"><?= $resultsRequestsValid ?></td>
            </tr>
            <tr>
                <th class="table-heading">Requests invalid</th>
                <td class="table-cell text-right <?= $resultsRequestsInvalidCss ?>"><?= $resultsRequestsInvalid ?></td>
            </tr>
            <tr>
                <th class="table-heading">Tests passed</th>
                <td class="table-cell text-right <?= $resultsTestsPassedCss ?>"><?= $resultsTestsPassed ?></td>
            </tr>
            <tr>
                <th class="table-heading">Tests failed</th>
                <td class="table-cell text-right <?= $resultsTestsFailedCss ?>"><?= $resultsTestsFailed ?></td>
            </tr>
            <tr>
                <th class="table-heading">Tests skipped</th>
                <td class="table-cell text-right <?= $resultsTestsSkippedCss ?>"><?= $resultsTestsSkipped ?></td>
            </tr>

        </table>
    </div>
<?php elseif ($groupId): ?>
    <div>Tests have not run yet.</div>
<?php else: ?>
    <div>Select a group to run tests.</div>
<?php endif; ?>
