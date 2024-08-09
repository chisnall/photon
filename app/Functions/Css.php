<?php

declare(strict_types=1);

namespace App\Functions;

class Css
{
    public static function getOverlayClass()
    {
        if (array_key_exists('modalName', $_POST)) {
            return 'overlay';
        } else {
            return 'overlay hidden';
        }
    }
}
