<?php

namespace App\Exception;

use Exception;

class FrameworkException extends Exception
{
    protected $message = "Framework error.";
    protected $code = 500;
}
