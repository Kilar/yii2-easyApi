<?php
namespace app\controllers;

use yii\rest\Controller;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\api\BaseService;
/**
 * 
 * @author Yong
 *
 */
class BaseController extends Controller
{
    /**
     * @var BaseService 逻辑处理服务类
     */
    public $service;
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::className(),
                'actions' => $this->verbs(),
            ],
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \yii\rest\Controller::afterAction()
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        return ['code' => 0 ,'message' => '请求成功', 'data' => $result];
    }
    
}