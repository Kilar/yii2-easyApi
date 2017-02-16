<?php
namespace console\controllers;

use yii\console\Controller;
use yii;

class TestController extends Controller 
{
    public function actionIndex() 
    {
        //注册登录
//         $arr = [
//             'pf' => '1',
//             'ver' => '1.0.0.0',
//             'time' => '1487001826',
//             'key' => 'KbOlJHdNq6UF07mP',
//             'username' => 'qazwsx',
//             'password' => 'qazwsx',
//         ];
        //获取信息
//         $arr = [
//             'pf' => '1',
//             'ver' => '1.0.0.0',
//             'time' => '1487001826',
//             'token' => 'jEedzmM6_KzGSHYZPaanXtftYqILFVuc',
//             'key' => 'L51AwVROwskWpcRtNw4g3ehiV8hXuhIDJcG5mXLq2hS8C32ceq7guvbRwL-Ued9r',
//         ];
        //信息修改
//         $arr = [
//             'pf' => '1',
//             'ver' => '1.0.0.1',
//             'time' => '1487001826',
//             'real_name' => '发的风格的风格',
//             'mobile' => '34534534',
//             'email' => '343sdf@sfd.com',
//             'token' => '8fEOXx23wQKDlPvOZp9TlPVz80p3_B-P',
//             'key' => 'qcRpQOkzZiqFTJeqDXO55yeczUgejE0dgu-cUXkQHTxBjKSm2RJaRMSDoHMbejty',
//         ];
        ksort($arr);
        print_r(md5(urldecode(http_build_query($arr))));
        die("\n");
    }
    
    function my_sort($a,$b)
    {
        echo 'dddd';
        if ($a==$b) return 0;
        return ($a<$b)?-1:1;
    }
    
    public function actionTest()
    {
        $person =  array(
            array('id'=>1,'name'=>'fj','weight'=>100,'height'=>180),
            array('id'=>2,'name'=>'tom','weight'=>53,'height'=>150),
            array('id'=>3,'name'=>'jerry','weight'=>120,'height'=>156),
            array('id'=>4,'name'=>'bill','weight'=>110,'height'=>190),
            array('id'=>5,'name'=>'linken','weight'=>80,'height'=>200),
            array('id'=>6,'name'=>'madana','weight'=>95,'height'=>110),
            array('id'=>7,'name'=>'jordan','weight'=>70,'height'=>170),
            array('id'=>8,'name'=>'fj1','weight'=>1001,'height'=>180),
            array('id'=>9,'name'=>'tom1','weight'=>531,'height'=>150),
            array('id'=>10,'name'=>'jerry1','weight'=>1201,'height'=>156),
            array('id'=>11,'name'=>'bill1','weight'=>1101,'height'=>190),
            array('id'=>12,'name'=>'linken1','weight'=>801,'height'=>200),
            array('id'=>13,'name'=>'madana1','weight'=>951,'height'=>110),
            array('id'=>14,'name'=>'jordan1','weight'=>701,'height'=>170),
        );
        usort($person, function($a, $b){
            if ($a['weight']==$b['weight']) return 0;
            return ($a['weight']>$b['weight']?-1:1);
        });
        print_r(strnatcmp('9', '1'));die;
    }
    
    
   
    
    
}