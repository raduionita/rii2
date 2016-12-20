<?php

namespace rii\web;

class Application extends \yii\web\Application
{
    /**
     * @inheritdoc
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
            'request' => ['class' => 'rii\web\Request'],
        ]);
    }
}