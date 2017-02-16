<?php
namespace common\lib\helpers;

use yii\db\BaseActiveRecord;
use yii\web\ServerErrorHttpException;
/**
 * 
 * @author Yong
 *
 */
class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * 对数组每个非对象元素进行trim处理去除左右空格 , 可以对特定的元素进行intval处理.
     * 
     * ```php
     * $array = [
     *     'a' => ' aa', 'b' => 'bb ', 'c' => ' cc ', 'd' => '5dfdf',
     *     'e' => [
     *        'a' => ' aa', 'b' => 'bb ', 'c' => ' cc ', 'd' => '5dfdf',
     *     ],
     * ];
     * $array = Common::arrayTrim($array, ['d', 'e' => ['d']]);
     * //处理后数组结果
     * [
     *     'a' => 'aa', 'b' => 'bb', 'c' => 'cc', 'd' => 5,
     *     'e' => [
     *        'a' => 'aa', 'b' => 'bb', 'c' => 'cc', 'd' => 5,
     *     ],
     * ];
     * ````
     * 
     * @param array $array
     * @param array $intvalHandler
     * @return array
     */
    public static function trim(array &$array, array $intvalHandler = []) 
    {
        if (!$array) {
            return [];
        }elseif (!$intvalHandler && !array_filter($array, 'is_array')) {
            $array = array_map('trim', $array);
        }
         
        foreach ($array as $k => $v){
            if (is_object($v)){ 
                throw new ServerErrorHttpException('This function can\'t process object.');
            } elseif (is_array($v)) {
                static::trim($array[$k], isset($intvalHandler[$k]) ? $intvalHandler[$k] : []);
            } elseif (in_array($k, $intvalHandler, true)) {
                $array[$k] = intval($v);
            } else {
                $array[$k] = trim($v);
            }
        }
    }

    /**
     * 销毁数组多个键对应的值 , 并返回对应的键值
     * @param array $array
     * @param array $indexs
     * @return array
     */
    public static function unsetIndexsValue(array &$array, array $indexs) : array
    {
        if (!$array || !$indexs) {
            return [];
        }
        $unsets = [];
        foreach ($indexs as $v){
            $unsets[$v] = $array[$v];
            unset($array[$v]);
        }
        return $unsets;
    }
    
    /**
     * 获取数组最后一个键名
     * @param array $array
     * @return mixed
     */
    public static function getLastKey(array &$array)
    {
        end($array);
        return key($array);
    }
     
    /**
     * 获取数组第一个键名
     * @param array $array
     * @return mixed
     */
    public static function getFirstKey(array &$array)
    {
        reset($array);
        return key($array);
    }
    
    /**
     * 获取AR数组某属性的值
     * @param array $arArray
     * @param string $attribute
     * @param bool|string $attribute2key 把ar对象某属性作为键(如果为true,则$attribute为键,可以设置其它属性)
     * @return array
     */
    public static function arArrayColumn(array $arArray, string $attribute, $attribute2key = false) :array
    {
        $firstModel = current($arArray);
        if (empty($firstModel) || empty($attribute) || 
        !$firstModel instanceof BaseActiveRecord || 
        (!in_array($attribute, $firstModel->attributes()) &&
        !$firstModel->hasProperty($attribute))) {
             return [];
        }
        
        $attributeList = [];
        foreach ($arArray as $model) {
            if ($attribute2key && !isset($attributeList[$model->$attribute])) {
                $attributeList[is_bool($attribute2key) ? $model->$attribute : $model->$attribute2key] = $model->$attribute;                       
            } elseif (!$attribute2key) {
                $attributeList[] = $model->$attribute;
            }
        }
        return $attributeList;
    }
    
}