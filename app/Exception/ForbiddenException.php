<?php

namespace App\Exception;

use Exception;

class ForbiddenException extends Exception
{
    protected $message = "You do not have authorisation to view this page.";
    protected $code = 403;
}
