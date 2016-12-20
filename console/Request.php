<?php

namespace rii\console;

use yii\base\UnknownPropertyException;

class Request extends \yii\console\Request
{
    public function __set($name, $value)
    {
        try {
            return parent::__set($name, $value);
        } catch (UnknownPropertyException $e) { /* stfu */ }
    }
}