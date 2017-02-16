<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * @author Yong
 */
class ApplicationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $jsOptions = ['position' => View::POS_HEAD];
    public $css = [
        'css/style.css',
        'css/bootstrap-responsive.css',
        'date/css/bootstrap-datetimepicker.min.css',
        '//cdn.bootcss.com/select2/4.0.3/css/select2.min.css',
    ];
    public $js = [
        '//cdn.bootcss.com/select2/4.0.3/js/select2.min.js',
        'js/bootstrap-treeview.min.js',
        'date/js/bootstrap-datetimepicker.min.js',
        'js/customer.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    
    public function init() 
    {
        parent::init();
        //unset($this->js[0], $this->js[1], $this->js[4]);
    }
}
