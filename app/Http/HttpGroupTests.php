<?php

declare(strict_types=1);

namespace App\Http;

use App\Models\RequestModel;
use App\Models\SettingsModel;

class HttpGroupTests
{
    private int $groupId;
    private string $groupTestsProgress;
    private string $groupTestsResults;
    private string $groupTestsStatus;
    private string $groupTestsStop;

    public function __construct(?array $parameters)
    {
        // Get group Id
        $groupId = (int)$parameters['groupId'] ?? null;

        // Set properties
        $this->groupId = $groupId;
        $this->groupTestsProgress = "/var/lib/photon/output/groupTests-" . $this->groupId . "-progress";
        $this->groupTestsResults = "/var/lib/photon/output/groupTests-" . $this->groupId . "-results";
        $this->groupTestsStatus = "/var/lib/photon/output/groupTests-" . $this->groupId . "-status";
        $this->groupTestsStop = "/var/lib/photon/output/groupTests-" . $this->groupId . "-stop";
    }

    public function runGroupTests(): void
    {
        // Get group requests from the session
        $groupRequests = session()->get('tests/upper/groupRequests') ?? [];

        // Get settings
        $stopOnResponseFail = SettingsModel::getSetting('groups/stopOnResponseFail');

        // Write session data and close session so the tests do not block other scripts from running
        session_write_close();

        // Update status
        file_put_contents($this->groupTestsStatus, 'running');

        // Delete existing files
        deleteFile($this->groupTestsProgress);
        deleteFile($this->groupTestsResults);
        deleteFile($this->groupTestsStop);

        // Debug log
        //logger()->logDebug('http.log', [" from: runGroupTests()", "group: " . $this->groupId, " stop: " . (int)$stopOnResponseFail]);

        // Count enabled requests first
        $groupRequestsCount = 0;
        foreach ($groupRequests as $request) {
            $requestEnabled = $request['enabled'];
            if ($requestEnabled == 'on') $groupRequestsCount++;
        }

        // Check requests
        if ($groupRequestsCount > 0) {
            // Array for output
            $output = [];
            $output['time'] = time();
            $output['requestsRun'] = null;
            $output['requestsValid'] = null;
            $output['requestsInvalid'] = null;
            $output['testsRun'] = null;
            $output['testsPassed'] = null;
            $output['testsFailed'] = null;
            $output['testsSkipped'] = null;

            // Init counts
            $requestsRun = 0;
            $requestsValid = 0;
            $requestsInvalid = 0;
            $testsRun = 0;
            $testsPassed = 0;
            $testsFailed = 0;
            $testsSkipped = 0;

            // Process requests
            foreach ($groupRequests as $request) {
                // Get request details
                $requestId = $request['id'];
                $requestEnabled = $request['enabled'];

                if ($requestEnabled == 'on') {
                    // Get request details
                    $model = RequestModel::getSingleRecord(['id' => $requestId]);

                    // Confirm record still exists
                    if ($model->getProperty('id')) {
                        $requestsRun++;

                        // Get request details
                        $requestMethod = strtoupper($model->getProperty('requestMethod'));
                        $requestName = $model->getProperty('requestName');

                        // Create new client
                        $client = new HttpClient($model);
                        $client->request();

                        // Get client details
                        $clientResponseValid = $client->getProperty('responseValid');
                        $clientErrorMessage = $client->getProperty('errorMessage');

                        // Run tests
                        if ($clientResponseValid) {
                            $requestsValid++;
                            $model->handleTests($client);
                        } else {
                            $requestsInvalid++;
                        }

                        // Get tests results
                        $testsResults = $model->getProperty('testsResults');
                        $testsResults ? $testsResultsCount = count($testsResults) : $testsResultsCount = 0;

                        // Count tests
                        if ($testsResultsCount > 0) {
                            foreach ($testsResults as $testsResult) {
                                $testsRun++;
                                $testResult = $testsResult['result'];
                                if ($testResult == 'passed') $testsPassed++;
                                if ($testResult == 'failed') $testsFailed++;
                                if ($testResult == 'skipped') $testsSkipped++;
                            }
                        }

                        // Output progress
                        file_put_contents($this->groupTestsProgress, (int)$clientResponseValid . ",$requestId,$testsResultsCount,$requestMethod,$requestName\n", FILE_APPEND);

                        // Add to output array
                        $output['requests'][] = [
                            'id' => $requestId,
                            'name' => $requestName,
                            'valid' => $clientResponseValid,
                            'error' => $clientErrorMessage,
                            'testsResults' => $testsResults,
                        ];
                    }

                    // Detect stop button
                    if (file_exists($this->groupTestsStop)) {
                        // Update status
                        file_put_contents($this->groupTestsStatus, 'stopped');

                        // Delete files
                        deleteFile($this->groupTestsStop);

                        // Stop method
                        return;
                    }

                    // If response is not valid and stop on fail is on, stop now
                    if (!$clientResponseValid && $stopOnResponseFail) {
                        // Update status
                        file_put_contents($this->groupTestsStatus, 'stopped');

                        // Break loop
                        break;
                    }
                }
            }

            // Add counts
            $output['requestsRun'] = $requestsRun;
            $output['requestsValid'] = $requestsValid;
            $output['requestsInvalid'] = $requestsInvalid;
            $output['testsRun'] = $testsRun;
            $output['testsPassed'] = $testsPassed;
            $output['testsFailed'] = $testsFailed;
            $output['testsSkipped'] = $testsSkipped;

            // Output results
            file_put_contents($this->groupTestsResults, serialize($output));
        } else {
            // No requests to run
            file_put_contents($this->groupTestsProgress, "none", FILE_APPEND);
        }

        // Update status
        file_put_contents($this->groupTestsStatus, 'complete');
    }

    public function stopGroupTests(): void
    {
        // Create stop file
        touch($this->groupTestsStop);
    }

    public function statusGroupTests(): void
    {
        // Check for status file
        if (file_exists($this->groupTestsStatus)) {
            echo file_get_contents($this->groupTestsStatus);
        } else {
            echo "unknown";
        }
    }

    public function cleanupGroupTests(): void
    {
        // Do not remove the status file, it can prevent the modal from obtaining the status after the tests finish

        // Remove progress
        deleteFile($this->groupTestsProgress);
    }
}
