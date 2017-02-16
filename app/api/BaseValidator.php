<?php
namespace app\api;


use yii\base\Model;
use app\lib\helpers\ApiException;
use common\lib\helpers\Error;
use common\lib\helpers\Common;
/**
 * 基础验证服务类 (此类子类主要完成的操作为模型数据填充，还有规则验证)
 * @author Yong
 *
 */
abstract class BaseValidator
{
    /**
     * 检验数据是否符合一个模型验证规则
     * @param Model $model
     * @return boolean
     */
    public static function validate($model, $data)
    {
        $model->load($data, '');
        if (!$model->validate()) {
            throw new ApiException(Error::PARAMS_ERROR, Common::getModelFirstError($model));
        }
        return true;
    }
    
    /**
     * 检验数据是否符合多个模型验证规则
     * @param Model[] $models
     * @param array $data
     * @return boolean
     */
    public static function validateMultiple(array $models, array $data)
    {
        foreach ($models as $model) {
            if (!is_object($model) || !$model instanceof Model) {
                throw new ApiException(Error::CLASS_ERROR, '$models数组元素必须是Model类或者子类对象');
            } 
            
            $model->load($data, '');
            if (!$model->validate()) {
                throw new ApiException(Error::PARAMS_ERROR, Common::getModelFirstError($model));
            }
        }
        return true;
    }
}