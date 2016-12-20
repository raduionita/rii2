<?php

namespace rii\web;

class Controller extends \yii\web\Controller
{
    public function init()
    {
        // @todo What if there's no module!?
        $this->layout = '@'.$this->module->id.'/views/layout';
        parent::init();
    }
}
