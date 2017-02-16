<?php
namespace app\lib\behaviors;

use yii\base\Behavior;
use yii\base\Action;
use yii\web\Request;
use common\redis\Member;
use app\models\ParamsFilter;
use app\lib\helpers\ApiException;
use common\lib\helpers\Error;
use \common\lib\helpers\Common;
use common\models\AppVersionRecord;
use app\controllers\BaseController;
use app\api\BaseService;
use app\api\BaseValidator;
use app\lib\helpers\ApiSign;
/**
 * 请求控制行为
 * @author Yong
 *
 */
class RequestControl extends Behavior
{
    /**
     * @var \app\models\ParamsFilter 基础参数过滤器
     */
    public $filterModel;
    /**
     * @var integer 限制请求时间在多少秒内时间, 默认0为不限制
     */
    public $underTime = 0;
    
    public function init()
    {
        parent::init();
        $this->filterModel = new ParamsFilter();
    }
    
    /**
     * {@inheritDoc}
     * @see \yii\base\Behavior::events()
     */
    public function events()
    {
        return [
            BaseController::EVENT_BEFORE_ACTION => 'filter'
        ];
    }
    
    /**
     * 参数过滤验证
     * @param Action $event
     */
    public function filter($event)
    {
        //参数过滤
        $data = $this->getData();
        $this->filterModel->load($this->getData(), '');
        if (!$this->filterModel->validate()) {
            throw new ApiException(Error::PARAMS_ERROR, Common::getModelFirstError($this->filterModel));
        } else if ($this->underTime) {
            $this->checkTime($data['time']);
        } 
        
        //用户设置
        if ($this->filterModel->isNeedToken()) {
            $user = $this->setApiUserIdentity($data['token']);
            $key = $user->auth_key;
        }
        
        //验签校验
        $key = $key ?? $this->getVerKey($data);
        if (!$this->checkSign($data, $key)) {
            throw new ApiException(Error::SIGN_FAIL, '验签校验失败');
        }
        
        //实例化服务对象操作
        $apiVer = $this->getApiVer($data);
        $controller = $event->action->controller;
        $id = ucfirst($controller->id);
        $this->instanceService($controller, $apiVer, $id);
        $controller->service->data = $data;
    }
    
    /**
     * 校验验签
     * @param array $data
     * @param string $key
     * @return boolean
     */
    public function checkSign($data, $key)
    {
        $sign = $data['sign'];
        unset($data['sign']);
        
        return trim($sign) === ApiSign::generateSign($data, $key);
    }
    
    /**
     * 获取对应版本客户端密钥
     * @param array $data
     * @throws ApiException
     */
    public function getVerKey($data)
    {
        $verData = AppVersionRecord::getByPfVer($data['ver'], $data['pf']);
        if (!$verData) {
            throw new ApiException(Error::TIME_ERROR, '客户端版本不存在');
        }
        return $verData['local_key'];
    }
    
    /**
     * 实例化当前控制器服务对象
     * @param BaseController $controller 当前控制器
     * @param string $apiVer 当前接口版本
     * @param string $id 当前控制器ID
     */
    public function instanceService($controller, $apiVer, $id) 
    {
        //检查类是否存在
        $sClass = str_replace('//', '\\', "app//api//$apiVer//service//{$id}Service");
        if (!class_exists($sClass)) {
            throw new ApiException(Error::FILE_NOTFOUND, '服务类不存在');
        }
        
        //实例化操作
        $controller->service = \Yii::$container->get($sClass);
        if (!$controller->service instanceof BaseService) {
            throw new ApiException(Error::CLASS_ERROR, '服务类必需继承' . BaseService::class . '或者' . BaseValidator::class);
        }
    }
    
    /**
     * 获取api接口版本
     * @param array $data 请求传递的参数
     * @return string
     */
    public function getApiVer($data) 
    {
        $verData = AppVersionRecord::getByPfVer($data['ver'], $data['pf']);
        if (!$verData) {
            throw new ApiException(Error::TIME_ERROR, '客户端版本不存在');
        }
        return $verData['api_version'];
    }
    
    /**
     * 检验请求时间是否在规定时间内
     * @param integer $time
     */
    public function checkTime($time)
    {
        $maxTime = $time + $this->underTime;
        if (time() >= $maxTime) {
            throw new ApiException(Error::TIME_ERROR, '请求时间超时');
        }
    }
    
    /**
     * 设置当前api接口用户
     * @param string 请求传递的token
     * @return \common\redis\Member
     */
    public function setApiUserIdentity($token)
    {
        $user = Member::findIdentityByAccessToken($token);
        if (!$user) {
            throw new ApiException(Error::TOKEN_FAILURE, '登陆异常,请重新登录');
        }
        \Yii::$app->getUser()->setIdentity($user);
        return $user;
    }
    
    /**
     * 获取请求数据
     * @return array
     */
    public function getData()
    {
        $request = $this->owner->getRequest();
        $method = $request->getMethod();
        switch ($method) {
            case 'GET':
                $data = $request->get();
                break;
            case 'POST':
                $data = $request->post();
                break;
        }
        return $data;
    }
    
    
}