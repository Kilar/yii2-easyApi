<?php
namespace backend\lib\components;

use backend\module\rbac\models\AuthItem;
/**
 * 
 * @author Yong
 *
 */
class User extends \yii\web\User
{
    /**
     * @var array | string 超级管理员
     */
    public $admin;
    /**
     * @var boolean 是否检查权限
     */
    public $check = true;

    /**
     * @overide
     * @see \yii\web\User::can()
     */
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        if ((!$this->isGuest && $this->isAdmin()) || !$this->check) {
            return true;
        }
        return parent::can($permissionName, $params, $allowCaching);
    }

    /**
     * 检验当前用户是否为超级管理员
     * @return boolean
     */
    protected function isAdmin()
    {
        $roleList  = AuthItem::getRoleNameByUid($this->getId());
        if (is_string($this->admin) && in_array($this->admin, $roleList, true)) {
            return true;
        } elseif(is_array($this->admin) && array_intersect($roleList, $this->admin)) {
            return true;
        }
        return false;
    }
}