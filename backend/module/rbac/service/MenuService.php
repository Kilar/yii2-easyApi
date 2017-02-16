<?php
namespace backend\module\rbac\service;

use Yii;
use yii\db\Transaction;
use yii\helpers\Url;
use yii\helpers\Json;
use common\lib\helpers\ArrayHelper;
use backend\models\OperationLog;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use backend\module\rbac\models\AuthMenu;
use backend\module\rbac\models\AuthItem;
use backend\service\LogService;
/**
 *
 * @author Yong
 *
 */
class MenuService extends BaseService
{
   /**
    * 菜单列表
    * @param AuthMenu $model
    * @param bool $filter
    * @return \yii\data\ActiveDataProvider[]
    */
   public static function index(AuthMenu $model, bool $filter)
   {
       $return = [];
       $query = $model::find();
       $dataProvider = new ActiveDataProvider([
           'query' => $query,
           'sort' => [
               'defaultOrder' => [
                   'level' => SORT_ASC,
                   'pid' => SORT_ASC,
                   'sort' => SORT_ASC,
                   'created_at' => SORT_DESC,
               ]
           ],
       ]);

       if ($filter) {
           $query->filterWhere([
               'id' => $model->id,
               'pid' => $model->pid,
               'created_at' => $model->created_at,
               'level' => $model->level,
           ]);
           $query->andFilterWhere(['like', 'name', $model->name])
               ->andFilterWhere(['like', 'uri', $model->uri]);
       }

       $allMenu = AuthMenu::getAllMenu(); //全部菜单
       $pidList = ArrayHelper::arArrayColumn($dataProvider->getModels(), 'pid', true);
       $return['dataProvider'] = $dataProvider;
       $return['preLevelMenuList'] = AuthMenu::getByIds($pidList);//当前页的菜单的上级菜单列表
       $return['levelCnameList'] = self::getLevelCName(1111); //菜单级别名称列表
       //选择框选项菜单列表
       $return['menuList'] = [
           '一级菜单' => array_column($allMenu[0], 'name', 'id'),
           '二级级菜单' => array_column($allMenu[1], 'name', 'id'),
           '三级级菜单' => array_column($allMenu[2], 'name', 'id'),
       ];
       return $return;
   }

   /**
    * 获取详情显示所需要数据
    * @param AuthMenu $model
    * @return string[]|mixed[]|\backend\module\rbac\logic\string[][]
    */
   public static function view(AuthMenu $model)
   {
       return [
           'pMenuName' => self::getPMenuName($model),
           'levelCnameList' => self::getLevelCName(1111),
       ];
   }

   /**
    * 获取更新所需要数据
    * @param AuthMenu $model
    * @return string[]|mixed[]|\backend\module\rbac\logic\string[][]
    */
   public static function getUpdateNData(AuthMenu $model)
   {
       return [
           'pMenuName' => self::getPMenuName($model),
           'levelCnameList' => self::getLevelCName(1111),
           'preLevelMenu' => self::getPlevelMenuList($model),
       ];
   }

   /**
    * 获取父菜单名字 （view对应数据方法）
    * @param AuthMenu $model
    * @return string|mixed
    */
   public static function getPMenuName(AuthMenu $model)
   {
       $pMenu = AuthMenu::getByid($model->pid);
       return $pMenu['name'] ?? '';
   }

   /**
    * 获取当前菜单上级菜单列表
    * @param AuthMenu $model
    */
   public static function getPlevelMenuList(AuthMenu $model)
   {
       return array_column(AuthMenu::getLevelMenu($model->level-1), 'name', 'id');
   }

   /**
    * 保存菜单数据
    * @param AuthMenu $model
    * @param string $runValidation
    * @param mix $attributeNames
    * @throws Exception
    * @return boolean
    */
   public static function saveMenu(AuthMenu $model, $runValidation = true, $attributeNames = null)
   {
       $model->updated_at = time();
       $connection = Yii::$app->getDb();
       $auth = Yii::$app->getAuthManager();
       $transaction = $connection->beginTransaction(Transaction::READ_COMMITTED);
       try {
           //当前操作类型
           $type = $model->isNewRecord ? OperationLog::CREATE : OperationLog::UPDATE ;
           $oldUri = $model->isNewRecord ? '' : $model->oldAttributes['uri'];
           $oldLevel = $model->isNewRecord ? $model->level : $model->oldAttributes['level'];
           if ($model->id >0 && $model->id == $model->pid) {
               $model->addError('pid', '不可以将自己修改为父级菜单');
               throw new ServerErrorHttpException;
           }//禁止修改含有子级菜单的菜单的菜单级别
           elseif (!$model->isNewRecord  && $oldLevel != $model->level && !empty(AuthMenu::getByPid($model->id))) {
               $model->addError('level', '含有子级菜单的不可以修改菜单级别');
               throw new ServerErrorHttpException;
           }//三四级菜单添加修改
           elseif (($model->isNewRecord || $oldLevel >= 3) && $model->level >= 3) {
               if($model->uri === 'null'){
                   $model->addError('uri', '路由不能为null');
                   throw new ServerErrorHttpException;
               }
               if ($model->isNewRecord) {
                   $newMenu = $auth->createPermission($model->uri);
                   $newMenu->description = $model->name;
                   $auth->add($newMenu);
               } else {
                   if($oldUri !== $model->uri && $model->uri !== 'null'){
                       $connection->createCommand()->update('{{%auth_item}}', [
                          'name' => $model->uri, 'updated_at' => time(), 'description' => $model->name
                       ], ['name' => $oldUri])->execute();
                   }
               }
           }//一二级菜单转换三四级菜单
           elseif (!$model->isNewRecord && $oldLevel < 3 &&  $model->level >= 3 ) {
               $newMenu = $auth->createPermission($model->uri);
               $newMenu->description = $model->name;
               $auth->add($newMenu);
           }//三四级转换一二级
           elseif (!$model->isNewRecord && $oldLevel >= 3 && $model->level<3) {
               $model->pid = $model->level==1? 0 : $model->pid;
               $model->uri = 'null';
               if(!$model->save($runValidation, $attributeNames)){
                   throw new ServerErrorHttpException;
               }
               $permission = $auth->createPermission($oldUri);
               if (!$auth->remove($permission)) {
                   throw new ServerErrorHttpException;
               }
           }//一二级菜单添加修改
           else {
               $model->uri = 'null';
               $model->pid = $model->level == 1 ? 0 : $model->pid;
           }//保存菜单
           if (!$model->save($runValidation, $attributeNames)) {
               throw new ServerErrorHttpException;
           } 
           //保存系统操作日志
           LogService::saveOperationLog($type, __METHOD__, Json::encode($model));
           $transaction->commit();
           //重置缓存
           AuthMenu::updateMenuCache($model, $type === OperationLog::CREATE, $oldLevel);
           return true;
       } catch (\Exception $e) {
           $transaction->rollBack();
           if (stripos($e->getMessage(), 'duplicate') !== false) {
               $model->addError('uri', '该路由已经存在');
           }
           return false;
       }
   }

   /**
    * 检查菜单级别是否大于1
    * @param  AuthMenu $model
    * @return boolean
    */
   public static function isLevelMoreThanOne(AuthMenu $model)
   {
       return $model->level > 1;
   }

   /**
    * 检查菜单级别是否大于2
    * @param AuthMenu $model
    * @return boolean
    */
   public static function isLevelMoreThanTwo(AuthMenu $model)
   {
       $model->uri = strtolower($model->uri);
       return $model->level > 2;
   }

   /**
    * 删除一个菜单
    * @param AuthMenu $model
    * @throws ForbiddenHttpException
    * @return number|\yii\db\false
    */
   public static function delete(AuthMenu $model)
   {
       if($model->isNewRecord){
           throw new ForbiddenHttpException('禁止空模型调删除接口');
       } elseif (AuthMenu::getByPid($model->id)) {
           throw new ForbiddenHttpException('禁止删除含有子级菜单的菜单');
       } elseif (AuthItem::findOne(['name' => $model->uri])->delete()) {
           AuthMenu::updateMenuCache($model, false, 1, true);
           LogService::saveOperationLog(OperationLog::DELETE, __METHOD__, Json::encode($model));
           return true;
       }
   }

   /**
    * 批量删除菜单
    * @param array $idList
    * @return string
    */
   public static function bulkDelete(array $idList)
   {
       $idList = array_unique(array_map('intval', $idList));
       $hasCHildIdList = AuthMenu::getByPids($idList, ['pid']);
       $noCHildIdList = array_diff($idList, $hasCHildIdList);
       if (empty($noCHildIdList)) {
           $msg = '选择数据均不能删除';
       } else {
           $nameList = $uriList = [];
           $menus = AuthMenu::findAll(['id' => $noCHildIdList]);
           foreach ($menus as $menu) {
               if ($menu->level >= 3) {
                   $nameList[] = $menu->uri;
               }
               $uriList[] = $menu->uri;
           }
           $num = AuthItem::deleteAll(['name' => $nameList, 'type' => AuthItem::MENU]);
           $num += AuthMenu::deleteAll(['id' => $noCHildIdList]);
           if ($num) {
               AuthMenu::bulkDelMenuCache($menus);
               $msg = '成功删除ID为'.implode(',', $noCHildIdList).',uri值为' . implode(',', $uriList) . '的'.$num.'条数据';
               LogService::saveOperationLog(OperationLog::DELETE, __METHOD__, $msg);
           } else {
               \Yii::$app->getResponse()->setStatusCode(500);
               $msg = '系统异常';
           }
       }
       return $msg;
   }

   /**
    * 获取某一级菜单名称或者菜单名称列表
    * @param int $level
    * @return string[]|string
    */
   public static function getLevelCName(int $level)
   {
       $levelList = [
           1 => '导航栏',
           2 => '二级菜单',
           3 => '三级菜单',
           4 => '按钮操作',
       ];
       return $levelList[$level] ?? $levelList;
   }

   /**
    * 获取菜单列表
    * @return array
    */
   public static function getMenuNav()
   {
       $return = [];
       $user = Yii::$app->getUser();
       list ($level1, $level2, $level3) = AuthMenu::getAllMenu();
       foreach ($level3 as $v) {
           if (!$user->can($v['uri']) || $v['uri'] === 'debug/default/view') {
               continue;
           }
           $items = [];
           $items['id'] = $v['id'];
           $items['text'] = $v['name'];
           $items['href'] = Url::to([ '/' . $v['uri']]);
           $level2[$v['pid']]['items'][] = $items ;
       }
       foreach ($level2 as $v) {
           if (empty($v['items'])) {
               continue;
           }
           $menu = [];
           $menu['collapsed'] = true;
           $menu['text'] = $v['name'];
           $menu['items'] = $v['items'];
           $level1[$v['pid']]['menu'][] = $menu;
       }
       foreach ($level1 as $v) {
           if (empty($v['menu'])) {
               continue;
           }
           $return[] = $v;
       }
       return $return;
   }

}