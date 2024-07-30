<?php

namespace App\Traits;

trait FormTrait
{
    // Return class for form input
    public function getInputClass($attribute): string
    {
        // Return border class
        if (array_key_exists($attribute, $this->errors)) {
            return "input-error";
        } else {
            return "input-normal";
        }
    }

    public function getPasswordIconClass($attribute, $icon): ?string
    {
        // Return hidden if icon matches current value
        if ($icon == $this->$attribute) {
            return 'hidden ';
        }

        // Return null so the icon is visible
        return null;
    }
}
