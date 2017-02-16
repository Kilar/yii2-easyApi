<?php
namespace app\api\v2\validators;

use app\api\BaseValidator;
use common\models\Member as MUser;
/**
 * 用户操作验证器
 * @author Yong
 *
 */
class UserValidator extends BaseValidator
{
    /**
     * 修改用户信息验证
     * @param MUser $user
     * @param array $data
     * @return boolean
     */
    public static function update(MUser $user, array $data)
    {
        $user->addRules([
            [['real_name', 'mobile', 'email'], 'required'],
        ]);
        return self::validate($user, $data);
    }
}