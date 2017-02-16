<?php

namespace backend\module\app;

/**
 * api module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\module\app\controllers';
    
    public $defaultRoute = 'version';
    
    public $layout='app';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
        $this->layoutPath = '@backend/views/layouts';
    }
}
