<?php

declare(strict_types=1);

use App\Http\AjaxRequest;

session_save_path('/var/lib/photon/sessions');

define('APP_DEBUG', false);
define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_PATH', '/tmp/body-files');

require_once BASE_PATH . '/vendor/autoload.php';

#------------------------------------------------------------------------------------------------

// Handle Ajax request
new AjaxRequest();
