<?php 
use yii\helpers\Url;
use yii\helpers\Html;

?>
<!DOCTYPE HTML>
<html>
<head>
    <title>后台管理系统</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?= Html::csrfMetaTags() ?>
    <link href="/bui/css/dpl-min.css" rel="stylesheet" type="text/css" />
    <link href="/bui/css/bui-min.css" rel="stylesheet" type="text/css" />
    <link href="/bui/css/main-min.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div class="header">

    <div class="dl-title">
        <!--<img src="/bui/img/top.png">-->
    </div>
    
    <div class="dl-log">欢迎您，<span class="dl-log-user"><?=Yii::$app->getUser()->identity->username?></span>
    <!--  <a href="<?=Url::to(['home/logout'])?>" title="退出系统" class="dl-log-quit" >[退出]</a> -->
    <?=Html::a('[退出]', '#', ['class'=>'dl-log-quit', 'onclick' => 'logout()'])?>
    <a href="#" title="修改密码" class="dl-log-quit" id="btnShow" >[修改密码]</a>
    </div>
    
</div>
<div class="content">

    <!-- 导航栏内容 -->
    <div class="dl-main-nav">
        <div class="dl-inform"><div class="dl-inform-title"><s class="dl-inform-icon dl-up"></s></div></div>
        <ul id="J_Nav"  class="nav-list ks-clear">
        <!--  
            <li class="nav-item dl-selected"><div class="nav-item-inner nav-home">系统管理</div></li>
            <li class="nav-item dl-selected"><div class="nav-item-inner nav-order">业务管理</div></li> -->
            <?php foreach ($nav as $v): ?>		
            <li class="nav-item dl-selected"><div class="nav-item-inner "><?=$v?></div></li>
            <?php endforeach; ?>	
        </ul>
    </div>
    
    <!-- 菜单栏内容 -->
    <ul id="J_NavContent" class="dl-tab-conten">

    </ul>
    
</div>

<!-- 用户修改密码 -->
<div id="content" class="hidden sr-only" style="display: none" >
  <?=Html::beginForm('', 'post', ['class'=>'form-horizontal', 'id'=>'form'])?>
    <div class="row">
      <div class="control-group span8">
        <label class="control-label span1">旧密码：</label>
        <div class="controls">
          <?=Html::activeTextInput(Yii::$app->getUser()->identity, 'oldPassword', ['class'=>'span5'])?>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="control-group span8">
        <label class="control-label span1">新密码：</label>
        <div class="controls">
          <?=Html::activeTextInput(Yii::$app->getUser()->identity, 'password', ['class'=>'span5'])?>
        </div>
      </div>
    </div>
  <?=Html::endForm()?>
</div>

<script type="text/javascript" src="//cdn.bootcss.com/jquery/1.8.1/jquery.min.js"></script>
<script type="text/javascript" src="/bui/js/bui-min.js"></script>
<script type="text/javascript" src="/bui/js/common/main-min.js"></script>
<script type="text/javascript" src="/bui/js/config-min.js"></script>
<script>
var logout = function() 
{
	var csrfToken = '<?=Yii::$app->getRequest()->getCsrfToken()?>';
	$.post("<?=Url::to(['home/logout'])?>", {"_csrf-backend":csrfToken}, function(url){
		location.href = url;
	}, 'json');
}

BUI.use('common/main',function(){
    var config = '<?=$menu?>';
    config = $.parseJSON(config);
    new PageUtil.MainPage({
        modulesConfig : config
    });
});

BUI.use(['bui/overlay','bui/form'],function(Overlay,Form){
  var form = new Form.HForm({
     srcNode : '#form'
  }).render();

  var dialog = new Overlay.Dialog({
        title:'修改密码',
        width:450,
        height:200,
        //配置DOM容器的编号
        contentId:'content',
        success:function () {
            var obj = this;
           	var form = $('#form');
            $.post('<?=Url::to(['home/reset-password'])?>', form.serialize(), function(data){
            	if(data.status){
            		msg();
            		obj.close();
            	}else{
            		BUI.Message.Alert(data.msg,'warning');
            	}
            }, 'JSON');
            
            return;
        }
   });
   
   $('#btnShow').on('click',function () {
        dialog.show();
   });

   function msg () {
       BUI.Message.Show({
         msg : '密码修改成功',
         icon : 'success',
         buttons : [],
         autoHide : true,
         autoHideDelay : 2000
       });
   }
});
 
</script>

</body>
</html>