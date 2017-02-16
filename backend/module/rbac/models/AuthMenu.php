<?php

namespace backend\module\rbac\models;

use Yii;
use \common\models\BaseActiveRecord;
/**
 * This is the model class for table "auth_menu".
 *
 * @property integer $id
 * @property integer $pid
 * @property string $name
 * @property integer $level
 * @property string $uri
 * @property integer $sort
 * @property integer $created_at
 * @property integer $updated_at
 */
class AuthMenu extends BaseActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%auth_menu}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeRules() 
    {
        $attributes = $this->attributes();
        return [
            [$attributes, 'trim'],
            [['name', 'uri'], 'string', 'max' => 64],
            ['sort', 'integer', 'min' => 0, 'max' => 99],
            [['pid', 'created_at', 'level', 'sort'], 'integer', 'min' => 0],
            ['uri', 'match', 'pattern' => '/^[^,\.\\"\'\|]+$/', 'message' => '{attribute}不可以含有 特殊符号'],
            ['name', 'match', 'pattern' => '/^([\x{4e00}-\x{9fa5}]|\w)+$/u', 'message' => '{attribute}不允许填写特殊符号'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => '父级菜单',
            'name' => '菜单名称',
            'created_at' => '创建时间',
            'level' => '菜单级别',
            'uri' => '菜单路由',
            'sort' => '排序',
            'params' => '路由参数',
        ];
    }

    /**
     * 根据pid获取子级菜单
     * @param integer $pid 父id
     * @param bool $takeOne
     * @param array $select 选择字段
     * @return array
     */
    public static function getByPid(int $pid, array $columns=['id', 'name', 'uri'])
    {
        $return = [];
        if ($pid<0) {
            return $return;
        }
        $query = self::find()->select($columns)->where(['pid' => $pid]);
        return count($columns) == 1 ? $query->groupBy($columns)->column() : $query->indexBy('id')->asArray()->all();
    }

    /**
     * 根据pid列表获取子级菜单
     * @param array $pidList
     * @param array $select
     * @return array
     */
    public static function getByPids(array $pidList, array $columns=['id', 'name', 'uri'])
    {
        $return = [];
        if (empty($pidList)) {
            return $return;
        }
        $query = self::find()->select($columns)->where(['pid' => $pidList]);
        return count($columns) == 1 ? $query->groupBy($columns)->column() : $query->indexBy('id')->asArray()->all();
    }

    /**
     * 根据id列表获取子级菜单
     * @param array $idList
     * @param array $select
     * @param $cached 是否读取缓存数据
     * @return array
     */
    public static function getByIds(array $idList, array $columns = ['id', 'level', 'uri', 'name'])
    {
        $return = [];
        if (empty($idList)) {
            return $return;
        }
        $query = self::find()->select($columns)->where(['id' => $idList]);
        return count($columns) == 1 ? $query->groupBy($columns)->column() : $query->indexBy('id')->asArray()->all();
    }

    /**
     * 获取全部菜单
     * @param   boolean $cached 缓存设置
     * @return  array
     */
    public static function getAllMenu(bool $cached = true)
    {
        $return = [];
        $return[0] = self::getLevelMenu(1, ['id', 'name'], $cached);
        $return[1] = self::getLevelMenu(2, ['id', 'name', 'pid'], $cached);
        $return[2] = self::getLevelMenu(3, ['id', 'name', 'pid','uri'], $cached);
        $return[3] = self::getLevelMenu(4, ['id', 'name', 'pid','uri'], $cached);
        return $return;
    }

    /**
     * 获取某一级菜单
     * @param integer $level
     * @param array $select
     * @param boolean $cached
     * @return array
     */
    public static function getLevelMenu(int $level, array $columns = ['id', 'name'], $cached=true)
    {
        $return = [];
        $cache = Yii::$app->getCache();
        $cachekey = Yii::$app->params['cache_prefix']['rbac']['get_all_menu'] . '_' . $level;
        if ($level<1) {
            return $return ;
        } elseif ($cached && ($return = $cache->get($cachekey) )) {
            $oldColumns = array_keys(current($return));
            if (!array_diff($columns, $oldColumns)) { //判断columns的列是否全部包含于缓存的列
                return $return;
            }
            $columns = array_merge($columns, array_diff($oldColumns, $columns));
        }

        $return = self::find()->select($columns)->indexBy('id')->where(['level'=>$level])
             ->orderBy(['pid' => SORT_ASC, 'sort' => SORT_ASC, 'created_at' => SORT_DESC])->asArray()->all();
        if ($cached && $return) {
            $cache->set($cachekey, $return);
        }
        return $return;
    }

    /**
     * 根据id获取一条记录
     * @param   integer $id id
     * @param   array $select 选择字段
     * @param   boolean $cached 是否读取缓存
     * @return  array
     */
    public static function getById(int $id, array $columns=['id', 'name', 'pid', 'uri'], bool $cached = true)
    {
        $return = [];
        $data = self::getAllMenu();
        if ($id<1) {
            return $return;
        } elseif ($cached && $data) {
            $menuList   = $data[0] + $data[1] + $data[2] + $data[3];
            $return = $menuList[$id];
            $oldColumns = array_keys($return);
            if (!array_diff($columns, $oldColumns)) {
                return $return;
            }
            $columns = array_merge($columns, array_diff($oldColumns, $columns));
            unset($menuList, $oldColumns);
        }

        $return = self::find()->select($columns)->where(['id' => $id])->asArray()->one();
        if ($return && $cached) {
            $level = 0 ;
            foreach ($data as $k => $v) {
                if (isset($v[$id])) {
                    $level = $k + 1;
                    $data = $v;
                    break;
                }
            }
            $cachekey = Yii::$app->params['cache_prefix']['rbac']['get_all_menu'] . '_' . $level;
            $data[$return['id']] = $return;
            Yii::$app->getCache()->set($cachekey, $data);
        }
        return $return;
    }

    /**
     * 改变菜单缓存
     * @param AuthMenu $menu
     * @param bool $isNew
     * @param int $oldLevel
     * @param bool $clear
     */
    public static function updateMenuCache(AuthMenu $menu, bool $isNew, int $oldLevel, bool $clear = false)
    {
        if ($menu->isNewRecord) {
            return;
        }

        $id = $menu->id;
        $level = $menu->level;
        $cache = Yii::$app->getCache();
        $cachekey = Yii::$app->params['cache_prefix']['rbac']['get_all_menu'] . '_' . $level;
        $return = $cache->get($cachekey);
        if ($clear) {
            unset($return[$id]);
        } else {
            $row = current($return);
            $return = self::getLevelMenu($level, array_keys($row), false);;
            //如果为修改菜单级别，则清除旧级别缓存数据
            if (!$isNew && $oldLevel != $level) {
                $key = Yii::$app->params['cache_prefix']['rbac']['get_all_menu'] . '_' . $oldLevel;
                $data = $cache->get($key);
                unset($data[$id]);
                $cache->set($key, $data);
            }
        }

        //清除系统rbac缓存数据
        $auth = Yii::$app->getAuthManager();
        $auth->invalidateCache();

        $cache->set($cachekey, $return);
    }

    /**
     * 批量清除菜单缓存
     * @param array $idlist
     */
    public static function bulkDelMenuCache(array $menus)
    {
        foreach ($menus as $menu) {
            if (is_object($menu) && $menu instanceof AuthMenu) {
                self::updateMenuCache($menu, false, 1, true);
            }
        }
    }
}
