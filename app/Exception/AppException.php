<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class AppException extends Exception
{
    protected $message = "Application error.";
    protected $code = 500;
}
