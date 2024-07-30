<?php

namespace App\Core\Traits;

use App\Core\Functions;

trait GetSetProperty
{
    public function getProperty($property): mixed
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new (Functions::getConfig("class/exception/framework"))("Property does not exist: $property");
        }
    }

    public function setProperty($property, $value): void
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            throw new (Functions::getConfig("class/exception/framework"))("Property does not exist: $property");
        }
    }

    public function getConstant($constant): mixed
    {
        if (defined("self::$constant")) {
            return self::{$constant};
        } else {
            throw new (Functions::getConfig("class/exception/framework"))("Constant does not exist: $constant");
        }
    }
}
