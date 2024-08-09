<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;

class BadRequestException extends Exception
{
    protected $message = "Bad request. Unable to process the URL.";
    protected $code = 400;
}
