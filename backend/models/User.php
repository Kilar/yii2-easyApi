<?php
namespace backend\models;

use Yii;
use common\lib\helpers\Status;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use common\models\BaseActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password password
 * @property string $realname
 * @property string $mobile
 */
class User extends BaseActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_STOP = 2;

    /**
     * @var string 用户角色name值
     */
    public $role;
    /**
     * @var string 用户密码
     */
    public $password;
    /**
     * @var string 用户旧密码
     */
    public $oldPassword;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();
        //array_push($fields, 'role');
        return $fields;
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
    public function attributeRules()
    {
        $attributes = $this->attributes();
        array_push($attributes, 'password', 'role', 'oldPassword', 'mobile');
        $integers = ['id', 'status', 'created_at', 'updated_at'];
        return [
            [ $attributes, 'trim'],
            ['email', 'email'],
            [ $integers, 'integer'],
            ['status', 'in', 'range' => [Status::ACTIVE, Status::DELETE, Status::STOP]],
            [['username', 'email', 'realname', 'password', 'oldPassword'], 'string', 'min' => 6, 'max' => 64],
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
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
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
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
                'username' => '用户名',
                'realname' => '真实姓名',
                'email' => '邮件',
                'status' => '状态',
                'created_at' => '创建时间',
                'updated_at' => '更改时间',
                'password' => '密码',
                'role' => '用户角色',
                'mobile' => '手机号',
                'oldPassword' => '旧密码',
        ];
    }

    /**
     * 重置密码
     * @param string $runValidation
     * @param string $attributeNames
     * @return boolean
     */
    public function resetPassword($runValidation = true, $attributeNames = null)
    {
        $this->setPassword($this->password);
        return $this->save($runValidation, $attributeNames);
    }

    /**
     * 根据角色描述关键词获取用户id
     * @param string $keyword
     * @return array
     */
    public static function getUidsByDesKword($keyword)
    {
        $return = [];
        if (!$keyword) {
            return $return;
        }
        return self::find()->select('a.user_id')
            ->from(['a' => '{{%auth_assignment}}', 'b' => '{{%auth_item}}'])
            ->where('{{a}}.[[item_name]]={{b}}.[[name]] AND {{b}}.[[type]]=1')
            ->andWhere(['like', 'b.description', $keyword])
            ->groupBy('a.user_id')->asArray()->column();
    }

    /**
     * 根据id列表获取用户信息列表
     * @param array $idList
     * @param array $columns
     * @return \yii\db\ActiveRecord[]
     */
    public static function getBYIds(array $idList, array $columns = ['id', 'username', 'realname'])
    {
        return self::find()->select($columns)->where(['id' => $idList])->indexBy('id')->asArray()->all();
    }

}
