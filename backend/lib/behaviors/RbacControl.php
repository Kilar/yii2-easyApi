<?php
namespace backend\lib\behaviors;

use Yii;
use yii\di\Instance;
use yii\base\ActionFilter;
use yii\base\InvalidConfigException;
use yii\web\ForbiddenHttpException;
use backend\lib\components\User;
/**
 * 
 * @author Yong
 *
 */
class RbacControl extends ActionFilter
{
    /**
     * @var array $rule configuration array for creating the rule object
     */
    public $rule = [];
    /**
     * @var User|array|string the user object representing the authentication status or the ID of the user application component.
     * Starting from version 2.0.2, this can also be a configuration array for creating the object.
     */
    public $user = 'user';

    /**
     * Initializes the [[rules]] array by instantiating rule objects from configurations.
     */
    public function init()
    {
        parent::init();
        $this->user = Instance::ensure($this->user, User::className());
        if(empty($this->rule)){
            throw new InvalidConfigException('没有配置规则类');
        }
        $this->rule = Yii::createObject($this->rule);
    }

    /**
     * Returns a value indicating whether the filer is active for the given action.
     * @param Action $action the action being filtered
     * @return boolean whether the filer is active for the given action.
     */
    protected function isActive($action)
    {
        $id = $this->getActionId($action);
        if (in_array($id, $this->except, true)) {
            return false;
        } else {
            foreach ($this->except as $v){
                if($id === $v || (($pos = strpos($v, '*')) !== false && !strncmp($id, $v, $pos))){
                    return false;
                }
            }
        }
        return !in_array($id, $this->except, true) && (empty($this->only) || in_array($id, $this->only, true));
    }

    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * You may override this method to do last-minute preparation for the action.
     * @param Action $action the action to be executed.
     * @return boolean whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        $user = $this->user;
        $request = Yii::$app->getRequest();
        $this->rule->permission = $this->getActionId($action);
        if(!$this->rule->allows($action, $user, $request)){
            $this->denyAccess($user);
            return false;
        }
        return true;
    }

    /**
     * Denies the access of the user.
     * The default implementation will redirect the user to the login page if he is a guest;
     * if the user is already logged, a 403 HTTP exception will be thrown.
     * @param User $user the current user
     * @throws ForbiddenHttpException if the user is already logged in.
     */
    protected function denyAccess($user)
    {
        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }

}