<?php

namespace rii\rest;

class UrlRule extends \yii\rest\UrlRule
{
    /**
     * @var array
     */
    public $ruleConfig = ['class' => '\rii\web\UrlRule'];

    /**
     * {@inheritdoc}
     */
    protected function createRule($pattern, $prefix, $action)
    {
        $rule = parent::createRule($pattern, str_replace($this->prefix, '', $prefix), $action);
        $rule->pattern = '#(^|[\d]+\/)' . substr($rule->pattern, 2);
        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();
        if (!empty($this->prefix)) {
            // check if prefix matches
            if (strpos($pathInfo, $this->prefix) === false) {
                return false;
            }
            // remove prefix from pathInfo
            $request->setPathInfo(str_replace($this->prefix, '', $pathInfo));
        }
        // continue parsing the request
        $result = parent::parseRequest($manager, $request);
        // put it back :)
        $request->setPathInfo($pathInfo);
        return $result;
    }
}
