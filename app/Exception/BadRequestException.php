<?php

namespace App\Exception;

use Exception;

class BadRequestException extends Exception
{
    protected $message = "Bad request. Unable to process the URL.";
    protected $code = 400;
}
