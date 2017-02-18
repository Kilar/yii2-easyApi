# yii2-easyApi

这提供了一个简单的通用api接口设计demo代码，还有一个实现了基本yii RBAC权限控制通用后台的demo代码，可以参考或者直接拉下本人代码进行开发


安装条件：

1 必须使用PHP7+、安装redis服务、mongo服务

2 安装好环境后，请安装composer命令，然后在项目根目录执行一下composer install命令（第一次安装可能比较久，

请耐心等待一下，然后composer命令安装可以参考一下http://www.yiichina.com/doc/guide/2.0/start-installation）

3 参考根目录下.env.example文件配置数据库、缓存等信息（这里参考laravel，主要为了解决配置文件覆盖问题）,

复制多一份.env，配置好你的数据库、缓存等配置即可

4 导入一下本目录下的yii.sql文件数据到你的MySQL数据库

5 配置好网站根目录（配置backend和app，本人使用的是nginx,可以参考一下本目录yii.conf配置）

6 后台超级管理员账号密码为1234567 、测试账号密码为test123

* 安装好后可能使用pjax导致php报错，配置一下php.ini以下参数即可

  always_populate_raw_post_data = -1
  
  以下为yii2需求配置
  
  expose_php = Off
  
  allow_url_include = Off
  
本人已经完成app目录（简单接口demo）和backend目录rbac管理后台功能。

app目录下有讲解demo接口演示讲解文挡。然后rbac管理后台，你配置好环境，登录进去

测试一下功能，然后看一下代码即可




