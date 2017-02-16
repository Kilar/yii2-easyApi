<?php
namespace common\redis;

use Yii;
use yii\web\IdentityInterface;
use common\lib\helpers\Common;
use common\lib\helpers\Status;
/**
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
    public function rules()
    {
        //parent::rules();
        
        return [
            [$this->attributes(), 'trim'],
            ['email', 'email'],
            [['email'], 'string', 'max' => 100],
            [['status', 'created_at', 'updated_at', 'id'], 'integer', 'min' => 0],
            [['username', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key', 'password_hash', 'real_name'], 'string', 'max' => 64],
            [['mobile'], 'string', 'max' => 20],
            [['password', 'access_token'], 'string', 'max' => 32],
            [['username', 'access_token', 'id'], 'unique'],
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \yii\db\ActiveRecord::attributes()
     */
    public function attributes()
    {
        return [
            'id' , 'username', 'password_hash','real_name', 'mobile', 'email', 'auth_key', 
            'password_reset_token', 'status', 'created_at', 'updated_at', 'access_token'
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
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = self::findOne(['access_token' => $token, 'status' => Status::ACTIVE]);
        if (!$user) {
            $user = \common\models\Member::findIdentityByAccessToken($token);
            if ($user) {
                self::createUser($user);
            }
        }
        return $user;
    }
    
    /**
     * 创建一个用户
     * @param \common\models\Member $user
     * @return boolean
     */
    public static function createUser(\common\models\Member $user)
    {
        $model = new static();
        $model->load($user->toArray(), '');
        return $model->save();
    }
    
    /**
     * 修改用户
     * @param \common\models\Member $user
     * @return boolean
     */
    public static function updateUser(\common\models\Member $muser)
    {
        $ruser = self::findOne($muser->id);
        if (!$ruser) {
            return self::createUser($muser);
        } else {
            $ruser->load($muser->toArray(), '');
            return $ruser->save();
        }
    
    }
    
}


