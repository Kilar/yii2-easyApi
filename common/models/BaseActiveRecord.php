<?php
namespace common\models;


use common\lib\traits\ActiveRecordTrait;
/**
 * 
 * @author Yong
 */
class BaseActiveRecord extends \yii\db\ActiveRecord
{
    use ActiveRecordTrait;
    
    /**
     * 复写insert方法,防止数据库驱动为pgsql,id值设置为null而引起sql报错
     * (non-PHPdoc)
     * @see \yii\db\ActiveRecord::insert()
     */
    public function insert($runValidation = true, $attributes = null)
    {
        $tableSchema = $this->getTableSchema(self::tableName());
        $insertAttributes = array_keys($this->attributes);
        if(self::getDb()->driverName === 'pgsql' && $attributes === null &&
         ($pos = array_search('id', $insertAttributes)) !== false &&
         $tableSchema->columns['id']->autoIncrement &&
         !$this->attributes['id'] && !is_numeric($this->attributes['id'])
         ){
            unset($insertAttributes[$pos]);
            $attributes = $insertAttributes;
        }
        return parent::insert($runValidation, $attributes);
    }
}

















