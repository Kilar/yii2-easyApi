<?php
namespace app\api\v1\validators;

use app\api\BaseValidator;
use common\models\Member as MUser;
use app\lib\helpers\ApiException;
use common\lib\helpers\Error;
/**
 * 用户操作验证器
 * @author Yong
 *
 */
class UserValidator extends BaseValidator
{
    /**
     * 注册行为验证操作
     * @param MUser $user
     * @param array $data
     * @return boolean
     */
    public static function register(MUser $model, $data)
    {
        $model->addRules([
            [['username', 'password'], 'required'],
        ]);
        return self::validate($model ,$data);
    }
    
    /**
     * 注册行为验证操作
     * @param MUser|null $user
     * @param array $data
     * @return boolean
     */
    public static function login($user, $data)
    {
        if (!$user) {
            throw new ApiException(Error::INFO_NOTFOUND, '用户不存在');
        } elseif (!$user->validatePassword(trim($data['password'] ?? ''))) {
            throw new ApiException(Error::LOGIN_FAIL, '账号或者密码不存在');
        }
        return true;
    }
    
}