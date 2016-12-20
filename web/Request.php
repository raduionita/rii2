<?php

namespace rii\web;

use yii\base\UnknownPropertyException;

class Request extends \yii\web\Request
{
    public function __set($name, $value)
    {
        try {
            return parent::__set($name, $value);
        } catch (UnknownPropertyException $e) { /* stfu */ }
    }
}