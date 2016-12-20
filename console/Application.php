<?php

namespace rii\console;

class Application extends \yii\console\Application
{
    /**
     * @inheritdoc
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
            'request' => ['class' => 'rii\console\Request'],
            'user'    => ['class' => 'rii\console\User'],
        ]);
    }
}