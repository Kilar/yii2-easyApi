<?php

namespace common\models;

use Yii;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;
use common\lib\helpers\Common;
use common\lib\helpers\Status;
use common\redis\Member as User;
/**
 * This is the model class for table "{{%member}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $real_name
 * @property string $mobile
 * @property string $email
 * @property string $auth_key
 * @property string $access_token
 * @property string $password_reset_token
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Member extends BaseActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = Status::DELETE;
    const STATUS_ACTIVE = Status::ACTIVE;
    const STATUS_STOP = Status::STOP;
    
    /**
     * 用户明文密码
     */
    public $password;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeRules()
    {
        return [
            [$this->attributes(), 'trim'],
            ['email', 'email'],
            [['email'], 'string', 'max' => 100],
            [['status', 'created_at', 'updated_at', 'id'], 'integer', 'min' => 0],
            [['username', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key', 'password_hash', 'real_name'], 'string', 'max' => 64],
            [['mobile'], 'string', 'max' => 20],
            [['password', 'access_token'], 'string', 'max' => 32],
            [['username', 'access_token'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户账号',
            'password' => '密码',
            'password_hash' => '加密密码',
            'real_name' => '真是姓名',
            'mobile' => '手机号',
            'email' => '邮件',
            'auth_key' => '验证密钥',
            'password_reset_token' => '密码重置token',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
      
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }
    
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }
    
    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
    
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }
    
    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
    
        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    
    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }
    
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    
    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
    
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password = '')
    {
        $password = empty($password) ? $this->password : $password;
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }
    
    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString(64);
    }
    
    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    
    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    /**
     * Generates "remember me" Access Token
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString(32);
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findOne(['access_token' => $token, 'status' => Status::ACTIVE]);
    }
    
}


