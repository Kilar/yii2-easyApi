<?php
namespace common\lib\traits;

use common\lib\helpers\DbHelper;
/**
 * ar通用trait
 * @author Hym
 */
trait ActiveRecordTrait
{
    /**
     * @var string 自定义类表单名字
     */
    public $formName;
    /**
     * @var string 在组件中数据库连接的id , 这里只起缓存作用 , 可以通过
     * 在子类实现init方法初始化时赋值到这个变量然后赋值静态变量$db ,
     * 从而在实例化对象时选择数据库的连接.
     */
    public $tempDb;
    /**
     * @var array 特定行为验证规则
     */
    private $_actionRules = [];
    
    
    /**
     * @inheritdoc
     */
    public final function rules()
    {
        return !empty($this->_actionRules) ? array_merge($this->attributeRules(), $this->_actionRules) : $this->attributeRules() ;
    }
    
    /**
     * 子类属性验证规则设置接口方法
     * @return array
     */
    public function attributeRules()
    { 
        return [];
    }
    
    /**
     * 添加特定行为验证规则
     * @param array $rules
     */
    public function addRules(array $rules)
    {
        $this->_actionRules = empty($this->_actionRules) ? $rules : array_merge($this->_actionRules, $rules);
    }
    
    /**
     * {@inheritDoc}
     * @see \yii\base\Model::formName()
     */
    public final function formName()
    {
        if (is_string($this->formName) && !empty($this->formName)) {
            return $this->formName;
        }
        return parent::formName();
    }
    
    /**
     * 自定义数据库连接方法, 子类定义一个静态变量db,
     * 然后赋值(组件配置中数据库连接的id), 即可连接
     * 特定数据库连接 . (注：只可以在子类模型定义静态变量$db)
     * eg:
     * class Model extends common\models\ActiveRecord
     * {
     *     //db值为组件配置中数据库连接id(以下为给予默认非db连接)
     *     static $db = 'db2';
     * }
     *
     * 如果在实例对象中, 需要切换连接, 以下即可
     * (注：需要切换操作数据库前, 必须先给$db赋值)
     * $model = new Model;
     * $model::$db = 'db';
     * 如果db值为空，默认连接db连接库
     * @return \yii\db\Connection
     */
    public static function getDb()
    {
        if(!empty(static::$db)){
            return DbHelper::getConnection(static::$db);
        }
        return parent::getDb();
    }
}
