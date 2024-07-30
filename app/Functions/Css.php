<?php

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
