<?php

namespace rii\rest;

use \yii\web\Response;

abstract class Controller extends \yii\rest\Controller
{
    /**
     * Parent controller response
     * @note Protected agains direct access!
     * @var array|null
     */
    private $_parent = null;

    abstract public function actionIndex();

    abstract public function actionView(int $id);

    abstract public function actionCreate();

    abstract public function actionUpdate(int $id);

    abstract public function actionDelete(int $id);

    abstract public function actionOptions();

    /** {@inheritdoc} */
    public function asJson($data)
    {
        if (!($data instanceof Output)) {
            throw new \InvalidArgumentException("The data MUST be an \\rii\\rest\\Output instance!");
        }
        return parent::asJson($data);
    }

    /** {@inheritdoc} */
    public function asXml($data)
    {
        if (!($data instanceof Output)) {
            throw new \InvalidArgumentException("The data MUST be an \\rii\\rest\\Output instance!");
        }
        return parent::asXml($data);
    }

    /**
     * Check if this resource controller has a parrent
     * @note parent "/users/1337/orders/66/products"..."orders/66" is the parent of "products"
     * @return bool
     */
    public function hasParent() : bool
    {
        if (is_null($this->_parent)) {
            // /users/1337/orders/66/products
            $pathInfo = \Yii::$app->request->getPathInfo();
            // /users/1337/orders/66           // remove this resource reference from request
            $pathInfo = substr($pathInfo, 0, strrpos($pathInfo, $this->id) - 1); // last occurence
            foreach (\Yii::$app->urlManager->rules as $rule) {
                if ($rule instanceof \yii\rest\UrlRule) {
                    foreach ($rule->controller as $id => $value) {
                        // find rule matching new request
                        if(preg_match('#'. $id .'(\/[\d]+)?$#', $pathInfo, $matches)) {
                            $this->_parent = $id;
                            break 2; // break both loops
                        }
                    }
                }
            }
            $this->_parent = $this->_parent ?? false; // protection against re-running the code
        }
        return !!($this->_parent); // force to bool
    }

    /**
     * Find and create aprent controller
     * @return null|Output
     */
    public function getParent() : ?Output
    {
        if ($this->hasParent()) {
            $request  = clone \Yii::$app->request;
            $pathInfo = $request->getPathInfo();
            // once again, remove this resource reference from request
            $request->setPathInfo(substr($pathInfo, 0, strrpos($pathInfo, $this->id) - 1));
            $this->_parent = \Yii::$app->handleRequest($request)->data;
            $this->_parent = $this->_parent ?? false; // protection against re-running the code
        }
        return $this->_parent === false ? null : $this->_parent; // DO NOT return false!
    }

    /**
     * Wanna set parent by yourself, please go head!
     * @param array $parent
     */
    public function setParent(array $parent) : void
    {
        $this->_parent = $parent;
    }
}
