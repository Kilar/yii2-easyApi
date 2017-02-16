<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use yii\base\Object;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
    public $verifyCode;

    private $_user;

    /**
     * @inheritdoc
     */
    public function rules() 
    {
        return [
            [ array_keys($this->attributes), 'trim'],
            // username and password are both required  loginform-verifycode
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha', 'captchaAction' => 'home/captcha', 'when' => [$this, 'isNeedCaptcha']],
        ];
    }

    /**
     * 检验登录是否需要验证码 (当客户端错误超过2次即需要)
     * @param Object $this 当前模型对象
     * @param string $attribute 验证码verifyCode
     * @return boolean
     */
    public function isNeedCaptcha($model, $attribute) 
    {
        $session = \Yii::$app->getSession();
        return $session->has('loginTime') && $session->get('loginTime') > 2;
    }
    
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->$attribute)) {
                $this->addError($attribute, '用户名或密码不正确');
            }
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser() 
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
