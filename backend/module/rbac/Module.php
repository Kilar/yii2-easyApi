<?php
namespace backend\module\rbac;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'backend\module\rbac\controllers';

    public $defaultRoute = 'menu';

    public $layout='app';

    public function init()
    {
        parent::init();
        // custom initialization code goes here
        $this->layoutPath = '@backend/views/layouts';
    }
}
