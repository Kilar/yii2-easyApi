<?php
namespace backend\controllers;

use backend\models\LoginLog;
use backend\models\OperationLog;
use backend\service\LogService;
/**
 *
 * @author Yong
 *
 */
class LogController extends BaseController
{
    /**
     * 登录日志列表
     * @return string
     */
    public function actionLoginLog()
    {
        $searchModel = new LoginLog();
        $searchModel->formName = 'LoginLogS';
        $filter = self::safeValidate($searchModel, [
           'start_time', 'end_time',
        ]);
        $data = LogService::loginLogList($searchModel, $filter);
        $data['searchModel'] = $searchModel;
        return $this->render('login/logList', $data);
    }

    /**
     * 操作日志列表
     * @return string
     */
    public function actionOperationLog()
    {
        $searchModel = new OperationLog();
        $searchModel->formName = 'OperationLogS';
        $filter = self::safeValidate($searchModel, [
           'start_time', 'end_time',
        ]);
        $data = LogService::operationLogList($searchModel, $filter);
        $data['searchModel'] = $searchModel;
        return $this->render('operation/logList', $data);
    }
}

