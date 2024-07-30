<?php

// Allowed keys
const KEYS_ALLOWED = [
    'page/theme' => null,
    #---
    'home/layout' => null,
    'home/left/selectedTab' => null,
    'home/upper/selectedTab' => null,
    'home/lower/selectedTab' => null,
    'home/upper/requestMethod' => 'home/upper/requestModified',
    'home/upper/requestUrl' => 'home/upper/requestModified',
    'home/upper/requestName' => 'home/upper/requestModified',
    'home/upper/requestParamsInputs' => 'home/upper/requestModified',
    'home/upper/requestHeadersInputs' => 'home/upper/requestModified',
    'home/upper/requestAuth' => 'home/upper/requestModified',
    'home/upper/requestAuthBasicUsername' => 'home/upper/requestModified',
    'home/upper/requestAuthBasicPassword' => 'home/upper/requestModified',
    'home/upper/requestAuthTokenValue' => 'home/upper/requestModified',
    'home/upper/requestAuthHeaderName' => 'home/upper/requestModified',
    'home/upper/requestAuthHeaderValue' => 'home/upper/requestModified',
    'home/upper/requestBody' => 'home/upper/requestModified',
    'home/upper/requestBodyTextValue' => 'home/upper/requestModified',
    'home/upper/requestBodyTextType' => 'home/upper/requestModified',
    'home/upper/requestBodyFormInputs' => 'home/upper/requestModified',
    'home/upper/requestBodyFileExisting' => 'home/upper/requestModified',
    'home/upper/requestBodyFileDelete' => 'home/upper/requestModified',
    'home/upper/requestVariablesInputs' => 'home/upper/requestModified',
    #---
    'settings/selectedTab' => null,
    #---
    'tests/layout' => null,
    'tests/left/selectedTab' => null,
    'tests/upper/selectedTab' => null,
    'tests/lower/selectedTab' => null,
    'tests/upper/groupName' => 'tests/upper/groupModified',
    'tests/upper/groupRequests' => 'tests/upper/groupModified',
    #---
    'variables/clear' => null,
];

// Allowed files
const FILES_ALLOWED = [
    '/app/Views/components/requestAdd.php',
    '/app/Views/components/testsRun.php',
    '/app/Views/components/testsResults.php',
    '/app/Views/components/testsSummary.php',
];

// Allowed methods
const METHODS_ALLOWED = [
    'App\Http\HttpGroupTests::runGroupTests()',
    'App\Http\HttpGroupTests::statusGroupTests()',
    'App\Http\HttpGroupTests::stopGroupTests()',
    'App\Http\HttpGroupTests::cleanupGroupTests()',
];

// Session keys for selected record
const RECORD_KEYS = [
    'home/upper/requestModified' => 'home/left/requestId',
    'tests/upper/groupModified' => 'tests/left/groupId',
];

// Settings to update
const SETTINGS_UPDATE = [
    'home/layout',
    'home/left/selectedTab',
    'home/upper/selectedTab',
    'home/lower/selectedTab',
    #---
    'tests/layout',
    'tests/left/selectedTab',
    'tests/upper/selectedTab',
    'tests/lower/selectedTab',
];
