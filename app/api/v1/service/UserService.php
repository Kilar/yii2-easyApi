<?php
namespace app\api\v1\service;

use app\api\BaseService;
use app\lib\helpers\ApiException;
use common\lib\helpers\Error;
use common\lib\helpers\Common;
use common\lib\helpers\Status;
use common\lib\helpers\Des3;
use common\redis\Member as RUser;
use common\models\Member as MUser;
use app\api\v1\validators\UserValidator;
use yii\web\IdentityInterface;
/**
 * 
 * @author Yong
 *
 */
class UserService extends BaseService
{
    /**
     * 注册操作处理接口
     */
    public function register()
    {
        $model = new MUser();
        $this->setTokenKey($model);
        $model->status = Status::ACTIVE;
        $model->setPassword(trim($this->data['password'] ?? ''));
        if (!UserValidator::register($model, $this->data) || !$model->save(false)) {
            throw new ApiException(Error::SERVER_ERROR, Common::getModelFirstError($model));
        } else {
            if (!RUser::createUser($model)) {
                throw new ApiException(Error::SERVER_ERROR, '服务器异常');
            }
        }
        return $this->getTokenKey($model);
    }
    
    /**
     * 登录操作处理接口
     */
    public function login()
    {   
        $user = MUser::findByUsername(trim($this->data['username'] ?? ''));
        $this->setTokenKey($user);
        if (!UserValidator::login($user, $this->data) || !$user->save(false)) {
            throw new ApiException(Error::SERVER_ERROR, Common::getModelFirstError($user));
        } else {
            if (!RUser::updateUser($user)) {
                throw new ApiException(Error::SERVER_ERROR, '服务器异常');
            }
        }
        return $this->getTokenKey($user);
    }
    
    /**
     * 设置用户access_token和auth_key
     * @param MUser $model
     */
    public function setTokenKey(MUser $model)
    {
        $model->generateAuthKey();
        $model->generateAccessToken();
    }
    
    /**
     * 获取用户access_token和auth_key
     * @param IdentityInterface $user
     * @return NULL[]|string[]
     */
    public function getTokenKey(IdentityInterface $user)
    {
        $des3 = new Des3(); //3Des对称加密类
        return [
            'key' => $des3->encrypt($user->auth_key),
            'token' => $user->access_token,
        ];
    }
   
    /**
     * 获取用户信息验证
     * @return string[]
     */
    public function getInfo()
    {
        $user = \Yii::$app->getUser()->getIdentity();
        return $user->toArray([
            'id', 'username', 'real_name', 'mobile', 'email',
        ]);
    }
   
}