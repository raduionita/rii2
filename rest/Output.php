<?php

namespace rii\rest;

use yii\base\Object;

class Output
{
    /** @var int */
    public $code;
    /** @var array */
    public $meta = [];
    /** @var array|null */
    public $data = null;

    public function __construct($code = 200, $data = null, array $meta = [])
    {
        // setup
        $this->code = $code;
        $this->meta = $meta;
        $this->data = $data;
        // other processing
        \Yii::$app->response->statusCode = $code;
        $this->meta['count'] = !is_array($data) ? 0 : count($data);
    }
}
