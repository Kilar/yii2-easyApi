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
    
 
    public function actionTest()
    {
        $this->daemon(function($args){  
          //do something 
          echo $args;
        }, "\n",2);
    }
    
    function daemon($func_name,$args,$number){
        while(true){
            $pid=pcntl_fork();
            echo "$pid\n";
            if($pid==-1){
                echo "fork process fail";
                exit();
            }elseif($pid){//创建的子进程
                static $num=0;
                $num++;
                if($num>=$number){
                    //当进程数量达到一定数量时候，就对子进程进行回收。
                    pcntl_wait($status);
    
                    $num--;
                }
            }else{ //为0 则代表是子进程创建的，则直接进入工作状态
    
//                 if($func_name){
//                     while (true) {
//                         $ppid=posix_getpid();
//                         var_dump($ppid);
//                         call_user_func($func_name,$args);
//                         sleep(2);
//                         break;
//                     }
//                 }else{
//                     echo "function is not exists";
//                 }
            }
        }
        
        echo microtime(1)."\n";
    }
    public function actionCurl()
    {
        $ch = curl_init('http://yii.ee/site/index');
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        curl_close($ch);
        exit(0);
    }
    
    
}