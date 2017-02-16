<?php
namespace backend\lib\extensions\rbac;

use yii\base\Component;
/**
 * 
 * @author Yong
 *
 */
class RbacRule extends Component
{
    /**
     * @var boolean whether this is an 'allow' rule or 'deny' rule.
     */
    public $allow;
    /**
     * @var array list of user IP addresses that this rule applies to. An IP address
     * can contain the wildcard `*` at the end so that it matches IP addresses with the same prefix.
     * For example, '192.168.*' matches all IP addresses in the segment '192.168.'.
     * If not set or empty, it means this rule applies to all IP addresses.
     * @see Request::userIP
     */
    public $ips;
    /**
     * @var callable a callback that will be called to determine if the rule should be applied.
     * The signature of the callback should be as follows:
     *
     * ~~~
     * function ($rule, $action)
     * ~~~
     *
     * where `$rule` is this rule, and `$action` is the current [[Action|action]] object.
     * The callback should return a boolean value indicating whether this rule should be applied.
     */
    public $matchCallback;
    /**
     * @var string
     * permission of user curent applies to .
     */
    public $permission;
    /**
     * Checks whether the Web user is allowed to perform the specified action.
     * @param Action $action the action to be performed
     * @param User $user the user object
     * @param \yii\web\Request $request
     * @return boolean|null true if the user is allowed, false if the user is denied, null if the rule does not apply to the user
     */
    public function allows($action, $user, $request)
    {
        if ($this->matchIP($request->getUserIP())
                && $this->matchPermission($user)
                && $this->matchCustom($action)
        ) {
            return $this->allow ? true : false;
        } else {
            return null;
        }
    }

    /**
     * @param string $ip the IP address
     * @return boolean whether the rule applies to the IP address
     */
    protected function matchIP($ip)
    {
        if (empty($this->ips)) {
            return true;
        }

        foreach ($this->ips as $rule) {
            if ($rule === '*' || $rule === $ip || (($pos = strpos($rule, '*')) !== false && !strncmp($ip, $rule, $pos))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Action $action the action to be performed
     * @return boolean whether the rule should be applied
     */
    protected function matchCustom($action)
    {
        return empty($this->matchCallback) || call_user_func($this->matchCallback, $this, $action);
    }

    /**
     * @param User $user the user object
     * @return boolean whether the rule applies to the permission
     */
    protected function matchPermission($user)
    {
        if (!$user->getIsGuest() && $user->can($this->permission, \Yii::$app->getRequest()->get())) {
            return true;
        }

        return false;
    }
}
