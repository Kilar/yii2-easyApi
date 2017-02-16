<?php

namespace common\models;

use Yii;
use common\helpers\Common;
/**
 * This is the model class for table "{{%app_version_record}}".
 *
 * @property integer $id
 * @property string $version_num
 * @property integer $platform
 * @property string $local_key
 * @property string $fix_question
 * @property string $update_content
 * @property integer $created_at
 * @property integer $updated_at
 */
class AppVersionRecord extends \common\models\BaseActiveRecord
{
    const ANDROID = 1;
    const IOS = 2;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%app_version_record}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeRules()
    {
        return [
            [$this->attributes(), 'trim'],
            [['update_content', 'fix_question'], 'string'],
            [['api_version', 'app_version'], 'string', 'max' => 30],
            [['local_key'], 'string', 'max' => 64],
            [['platform', 'created_at', 'updated_at'], 'integer', 'min' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'api_version' => '接口版本',
            'app_version' => '应用版本',
            'platform' => '平台',
            'local_key' => '客户端本地密钥',
            'fix_question' => '修复问题',
            'update_content' => '升级内容',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
    
    /**
     * 随机生成客户端本地密钥
     */
    public function generateLocalKey()
    {
        $this->local_key = \Yii::$app->getSecurity()->generateRandomString(16);
    }
    
    /**
     * 根据应用版本号获取版本记录信息
     * @param string $ver
     * @param string $pf
     * @param bool $cached
     */
    public static function getByPfVer (string $ver, string $pf, bool $cached = true) 
    {
        $key = \Yii::$app->params['cache_prefix']['app_ver'] . '_' . $pf . '_'  . $ver;
        $cache = \Yii::$app->getCache();
        if ($cached && ($data = $cache->get($key))) {
            return $data;
        }
        
        $data = self::findOne(['app_version' => $ver, 'platform' => $pf]);
        if ($cached && $data) {
            $data = $data->toArray(['api_version', 'local_key']);
            $cache->set($key, $data);
        }
        return $data;
    }
    
}
