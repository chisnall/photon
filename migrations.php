<?php

use App\Core\Application;
use App\Core\Database\Migrations;

session_save_path('/var/lib/photon/sessions');

define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/vendor/autoload.php';

// Get argument
if ($argc > 1) {
    // Get the first argument
    $argument = strtolower($argv[1]);
} else {
    $argument = "up";
}

// Check arguments
if ($argument != "up" && $argument != "down") {
    echo "Argument not supported: $argument\n\n";
    exit;
}

// Run migrations
// We need a try/catch block here to make the error output terminal friendly
try {
    // Create application instance
    $app = new Application();

    // Create migrations instance
    $migrations = new Migrations();

    // Apply or remove migrations
    $migrations->handleMigrations($argument);
} catch (Throwable $exception) {
    // Get exception details
    $className = get_class($exception);
    $exceptionMessage = $exception->getMessage();
    $exceptionCode = $exception->getCode();
    $exceptionFile = $exception->getFile();
    $exceptionLine = $exception->getLine();
    $exceptionTrace = $exception->getTraceAsString();

    // Check for database error
    if ($className == 'PDOException') {
        $className = 'Database Error';
    }

    // Output
    echo "\n$className\n\n";
    echo "File: $exceptionFile\n";
    echo "Line: $exceptionLine\n\n";
    echo "$exceptionMessage\n\n";
    if ($className != 'Database Error') {
        echo "$exceptionTrace\n\n";
    }

    // Exit
    exit;
}

// Cleanup session
$sessionFile = session_save_path() . '/sess_' . session_id();
deleteFile($sessionFile);
