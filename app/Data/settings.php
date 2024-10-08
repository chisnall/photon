<?php

// !!! do not edit this file

$settings = [
    # Groups
    'groups' => [
        'stopOnResponseFail' => false,
    ],
    # HTTP
    'http' => [
        'defaultScheme' => 'http://',
        'sortHeaders' => true,
        'timeout' => 5,
        'version' => 'auto',
        'accept' => 'application/json',
    ],
    # Home
    'home' => [
        'layout' => [
            'leftSection' => '25%',
            'rightSection' => '75%',
            'topSection' => '45%',
            'bottomSection' => '55%',
        ],
        'hidePasswords' => false,
    ],
    # JSON
    'json' => [
        'lineNumbers' => 'right',
        'indent' => 4,
        'linkUrls' => true,
        'trailingCommas' => false,
        'quoteKeys' => true,
    ],
    # Tests
    'tests' => [
        'layout' => [
            'leftSection' => '25%',
            'rightSection' => '75%',
            'topSection' => '45%',
            'bottomSection' => '55%',
        ],
    ],
    # Groups
    'variables' => [
        'showGlobalsHome' => false,
    ],
];

return $settings;
