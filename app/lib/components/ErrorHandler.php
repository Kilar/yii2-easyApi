<?php
namespace app\lib\components;

use Yii;
use yii\web\Response;
use yii\base\UserException;
use yii\web\HttpException;
use app\lib\helpers\ApiException;
use common\lib\helpers\Error;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
/**
 * 重写组件
 * @author Yong
 */
class ErrorHandler extends \yii\web\ErrorHandler
{
    /**
     * @var array 错误code对应信息
     */
    static $message = [
        Error::API_NOTFOUND => '接口不存在',
        Error::INFO_NOTFOUND => '信息不存在',
        Error::REQUEST_ERROR => '请求方式错误',
        Error::FILE_NOTFOUND => '文件不存在',
        Error::SERVER_ERROR => '服务器异常',
        Error::PARAMS_ERROR => '传输数据错误',
        Error::VERSION_ERROR => 'app版本错误',
        Error::TIME_ERROR => '时间格式错误',
        Error::SIGN_FAIL => '验签校验失败',
        Error::LOGIN_FAIL => '登录失败',
        Error::TOKEN_FAILURE => '登录异常，请重新登录',
        Error::CLASS_ERROR => '类操作错误',
    ];
    
    /**
     * 自定义即可异常渲染方法，统一渲染为json格式数据
     * {@inheritDoc}
     * @see \yii\web\ErrorHandler::renderException()
     */
    protected function renderException($exception)
    {
        //父类方法部分代码
        if (Yii::$app->has('response')) {
            $response = Yii::$app->getResponse();
            // reset parameters of response to avoid interference with partially created response data
            // in case the error occurred while sending the response.
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;
        } else {
            $response = new Response();
        }
        
        if (!YII_DEBUG && !$exception instanceof UserException && !$exception instanceof HttpException 
            && !$exception instanceof ApiException) {
            $exception = new ApiException(Error::SERVER_ERROR, self::$message[Error::SERVER_ERROR]);
        }
        
        //异常数据理
        if ($exception instanceof ApiException) {
            $code = $exception->statusCode;
            $statusCode = 500;
            $message = $exception->getMessage() ?: self::$message[$code] ?? '系统异常';
        } else if ($exception instanceof HttpException) {
            $code = $this->getHttpExCode($exception);
            $statusCode = $exception->statusCode;
            $message = self::$message[$code] ?? '系统异常';
        } else {
            $code = Error::SERVER_ERROR;
            $statusCode = 500;
            $message =  self::$message[$code];
        }
        
        //发送异常
        $response->format = Response::FORMAT_JSON;
        $response->setStatusCode($statusCode);
        $response->data = ['code' => $code, 'message' => $message , 'data' => []];
        $response->send();
    }
    
    /**
     * 获取http异常对应错误code
     * @param HttpException $httpException
     * @return number
     */    
    protected function getHttpExCode($httpException)
    {
        if ($httpException instanceof MethodNotAllowedHttpException) {
            return Error::REQUEST_ERROR;
        } elseif ($httpException instanceof NotFoundHttpException) {
            return Error::API_NOTFOUND;
        } else {
            return Error::SERVER_ERROR;
        }
    }
}
