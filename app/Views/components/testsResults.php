<?php
/**  @var int $groupId **/

declare(strict_types=1);

// Check for results
$resultsData = null;
if ($groupId) {
    $resultsFile = "/var/lib/photon/output/groupTests-$groupId-results";
    if (file_exists($resultsFile)) {
        $resultsData = unserialize(file_get_contents($resultsFile));
        $resultsRequests = $resultsData['requests'];
    }
}
?>

<?php if ($resultsData): ?>
    <div class="mr-5 mb-5">
        <table class="table-auto text-left text-sm">
            <tr>
                <th class="table-heading">ID</th>
                <th class="table-heading">Request</th>
                <th class="table-heading">Valid</th>
                <th class="table-heading">Tests</th>
                <th class="table-heading">Test Name</th>
                <th class="table-heading">Test Type</th>
                <th class="table-heading">Test Assertion</th>
                <th class="table-heading">Test Value</th>
                <th class="table-heading">Result</th>
            </tr>

            <?php foreach($resultsRequests as $resultsRequest): ?>
                <?php
                $requestId = $resultsRequest['id'];
                $requestName = $resultsRequest['name'];
                $requestValid = $resultsRequest['valid'];
                $requestError = $resultsRequest['error'];
                $testsResults = $resultsRequest['testsResults'] ?? [];

                // Results count
                $testsResultsCount = count($testsResults);

                // Valid display
                $requestValid ? $requestValidDisplay = '<i class="text-green-700 dark:text-lime-700 fa-solid fa-circle-check"></i>' : $requestValidDisplay = '<i class="text-red-600 dark:text-red-700 fa-solid fa-circle-xmark"></i>';

                // Notice for when no tests have run
                if ($requestError) {
                    $requestNotice = $requestError;
                } elseif ($testsResultsCount === 0) {
                    $requestNotice = "No tests are defined for this response.";
                } else {
                    $requestNotice = null;
                }
                ?>
                <?php if ($testsResultsCount > 0): ?>
                    <?php foreach ($testsResults as $testsResult): ?>
                        <?php
                        $testId = $testsResult['id'];
                        $testName = $testsResult['name'];
                        $testType = $testsResult['type'];
                        $testAssertion = $testsResult['assertion'];
                        $testValue = $testsResult['value'];
                        $testResult = $testsResult['result'];

                        if ($testResult == 'passed') {
                            $testResultClass = 'bg-green-700 dark:bg-lime-700';
                        } elseif ($testResult == 'failed') {
                            $testResultClass = 'bg-red-600 dark:bg-red-700';
                        } elseif ($testResult == 'skipped') {
                            $testResultClass = 'bg-zinc-500 dark:bg-zinc-600';
                        }
                        ?>
                        <tr class="h-8 align-top">
                            <?php if ($requestId): ?>
                                <td rowspan="<?= $testsResultsCount ?>" class="min-w-10 text-right table-cell"><?= $requestId ?></td>
                                <td rowspan="<?= $testsResultsCount ?>" class="min-w-60 table-cell"><?= $requestName ?></td>
                                <td rowspan="<?= $testsResultsCount ?>" class="text-center table-cell"><?= $requestValidDisplay ?></td>
                                <td rowspan="<?= $testsResultsCount ?>" class="text-right table-cell"><?= $testsResultsCount ?></td>
                            <?php endif; ?>
                            <td class="min-w-40 table-cell"><?= $testName ?></td>
                            <td class="min-w-40 table-cell"><?= $testType ?></td>
                            <td class="min-w-40 table-cell"><?= $testAssertion ?></td>
                            <td class="min-w-40 table-cell"><?= $testValue ?></td>
                            <td class="min-w-20 text-white table-cell <?= $testResultClass ?>"><?= ucfirst($testResult) ?></td>
                        </tr>
                        <?php
                        // Nullify request ID for the rowspan to work
                        $requestId = null;
                        ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr class="h-8 align-top">
                        <td class="min-w-10 text-right table-cell"><?= $requestId ?></td>
                        <td class="min-w-60 table-cell"><?= $requestName ?></td>
                        <td class="text-center table-cell"><?= $requestValidDisplay ?></td>
                        <td class="text-right table-cell"><?= $testsResultsCount ?></td>
                        <td colspan="5" class="table-cell"><?= $requestNotice ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
    </div>
<?php elseif ($groupId): ?>
    <div>Tests have not run yet.</div>
<?php else: ?>
    <div>Select a group to run tests.</div>
<?php endif; ?>
