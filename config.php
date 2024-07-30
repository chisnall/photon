<?php

$config = [
    # Database
    'database' => [
        'driver' => 'sqlite',
        'mysql' => [
            'class' => 'App\Database\MysqlDatabase',
            'host' => 'host',
            'port' => '3306',
            'schema' => 'schema',
            'username' => 'username',
            'password' => 'password',
        ],
        'pgsql' => [
            'class' => 'App\Database\PostgresDatabase',
            'host' => 'host',
            'port' => '5432',
            'schema' => 'schema',
            'username' => 'username',
            'password' => 'password',
        ],
        'sqlite' => [
            'class' => 'App\Database\SqliteDatabase',
            'path' => '/var/lib/photon/database/photon.db',
        ],
    ],

    # Application
    'app' => [
        'httpUser' => 'www-data',
    ],

    # Page
    'page' => [
        # Defaults
        'default' => [
            'layout' => 'default',
            'title' => 'Our App',
        ],
        # Error
        'error' => [
            'client' => [
                'layout' => 'default',
                'view' => 'error/client',
            ],
            'framework' => [
                'layout' => 'error',
                'view' => 'error/framework',
            ],
        ],
        # Footer
        'footer' => [
            'databaseIcons' => [
                'show' => true,
                'colour' => true,
            ],
        ],
        # Flash message classes
        'flash' => [
            'success' => 'flash-success',
            'info' => 'flash-info',
            'warning' => 'flash-warning',
        ],
    ],

    # Classes
    'class' => [
        'user' => 'App\Models\UserModel',
        'exception' => [
            'controller' => 'App\Controllers\SiteController',
            'framework' => 'App\Exception\FrameworkException',
            'notFound' => 'App\Exception\NotFoundException',
        ],
    ],

    # Controllers
    'controller' => [
        'genericView' => [
            'controller' => 'App\Controllers\SiteController',
            'method' => 'genericView',
        ]
    ],

    # Ignore referer on these pages
    'no-referer' => [
        '/login',
        '/logout',
        '/register',
    ]
];

return $config;
