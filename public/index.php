<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Functions;

function exceptionHandler(Throwable $exception): never {
    http_response_code(500);
    echo "<div style=\"font-size: 120%;\">\n";
    echo "<pre style=\"font-weight: bold;\">Error</pre>\n";
    echo "<pre>Unable to start framework.</pre>\n";
    echo "<pre>" . $exception->getMessage() . "</pre>\n";
    echo "<pre>" . $exception->getTraceAsString() . "</pre>\n";
    echo "</div>\n";
    exit;
}

set_exception_handler('exceptionHandler');

session_save_path('/var/lib/photon/sessions');

define('APP_START', microtime(true));
define('APP_DEBUG', false);
define('APP_VERSION', '2024.8.1');
define('APP_RELEASE', '2024-08-17');
define('APP_SUPPORT', 'https://www.chisnall.net/support');
define('APP_DOCKER', 'https://hub.docker.com/r/chisnall/photon  ');
define('APP_GITHUB', 'https://github.com/chisnall/photon');
define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_PATH', '/tmp/body-files');

require_once BASE_PATH . '/vendor/autoload.php';

#------------------------------------------------------------------------------------------------

// Boot application
$app = new Application();

// Run application
$app->run();

// Calculate application time
Functions::time();
