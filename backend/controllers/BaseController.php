<?php
namespace backend\controllers;

use yii\db\BaseActiveRecord;
/**
 *
 * @author Yong
 *
 */
class BaseController extends \yii\web\Controller
{
    public $layout='app';

    /**
     * 搜索列表设置safe安全方法,并验证参数是否合适
     * @param BaseActiveRecord $model
     * @param array $addAttribute 模型自定义字段
     * @return boolean
     */
    protected static function safeValidate(BaseActiveRecord $model, array $customerAttributes = [])
    {
        $attribute = $model->attributes();
        $model->addRules([
             [array_merge($attribute, $customerAttributes), 'safe'],
        ]);
        return $model->load(\Yii::$app->getRequest()->get()) && $model->validate();
    }
}