<?php

namespace App\Exception;

use Exception;

class AppException extends Exception
{
    protected $message = "Application error.";
    protected $code = 500;
}
