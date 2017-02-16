<?php
namespace backend\service ;

use Yii;
use backend\models\LoginForm;
use backend\module\rbac\service\MenuService;
class HomeService  extends BaseService 
{
    /**
     * Logs in a user using the provided username and password.
     * @param LoginForm $model
     * @return boolean whether the user is logged in successfully
     */
    public static function login(LoginForm $model)
    {
        if ($model->validate()) {
            return Yii::$app->getUser()->login($model->getUser(), $model->rememberMe ? 30 : 0);
        } else {
            $session = Yii::$app->getSession();
            if($session->has('loginTime')){
                $session->set('loginTime', $session->get('loginTime') + 1);
            }else {
                $session->setTimeout(1800);
                $session->set('loginTime', 1);
            }
            return false;
        }
    }

    /**
     * 登录失败调用方法
     * @return boolean
     */
    public static function loginFail()
    {
        $catpcha = false;
        $session = Yii::$app->getSession();
        if ($session->has('loginTime') && $session->get('loginTime')>2) {
            $catpcha = true;
        }
        return $catpcha;
    }

    /**
     * 获取后台首页所需数据
     * @return array
     */
    public static function index()
    {
        $menuList = MenuService::getMenuNav();
        return [
           'menu' => json_encode($menuList),
           'nav' => array_column($menuList, 'name', 'name')
        ];
    }
    


}