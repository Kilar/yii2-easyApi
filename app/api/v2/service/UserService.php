<?php
namespace app\api\v2\service;

use app\api\BaseService;
use app\lib\helpers\ApiException;
use common\lib\helpers\Error;
use common\lib\helpers\Common;
use common\redis\Member as RUser;
use common\models\Member as MUser;
use app\api\v2\validators\UserValidator;
/**
 * 
 * @author Yong
 *
 */
class UserService extends BaseService
{
    /**
     * 信息修改出来接口
     * @throws ApiException
     * @return boolean
     */
    public function update()
    {
        $user = MUser::findOne(\Yii::$app->getUser()->getId());
        if (UserValidator::update($user, $this->data) && !$user->save(false)) {
            throw new ApiException(Error::SERVER_ERROR, Common::getModelFirstError($user));
        } else {
            if (!RUser::updateUser($user)) {
                throw new ApiException(Error::SERVER_ERROR, '服务器异常');
            }
        }
        return true;
    }
}