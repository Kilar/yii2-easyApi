<?php 
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\base\Widget;

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<title>Cloud Admin | Login</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<!-- STYLESHEETS --><!--[if lt IE 9]><script src="js/flot/excanvas.min.js"></script><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script><![endif]-->
	<link rel="stylesheet" type="text/css" href="/login/css/cloud-admin.css" >
	
	<link href="/login/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<!-- DATE RANGE PICKER -->
	<link rel="stylesheet" type="text/css" href="/login/js/bootstrap-daterangepicker/daterangepicker-bs3.css" />
	<!-- UNIFORM -->
	<link rel="stylesheet" type="text/css" href="/login/js/uniform/css/uniform.default.min.css" />
	<!-- ANIMATE -->
	<link rel="stylesheet" type="text/css" href="/login/css/animatecss/animate.min.css" />
	<!-- FONTS -->
	<link href='http://fonts.useso.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
</head>
<body class="login">	
	<!-- PAGE -->
	<section id="page">
			<!-- HEADER -->
			<header>
				<!-- NAV-BAR -->
				<div class="container">
					<div class="row">
						<div class="col-md-4 col-md-offset-4">
							<div id="logo">
								<a href="index.html"><img src="/login/img/logo/logo-alt.png" height="40" alt="logo name" /></a>
							</div>
						</div>
					</div>
				</div>
				<!--/NAV-BAR -->
			</header>
			<!--/HEADER -->
			<!-- LOGIN -->
			<section id="login" class="visible">
				<div class="container">
					<div class="row">
						<div class="col-md-4 col-md-offset-4">
							<div class="login-box-plain">
								<h2 class="bigintro">Sign In</h2>
								<div class="divide-40"></div>
								<?php $form = ActiveForm::begin(['id' => 'login-form', 'action' => ['login'] ]); ?>

                                    <?= $form->field($model, 'username') ?>
                        
                                    <?= $form->field($model, 'password')->passwordInput() ?>
                                    
                                    <?php 
                                         if($catpcha){
                                              //echo Captcha::widget(['model'=>$model,'attribute'=>'verifyCode', 'captchaAction' => 'home/captcha']);
                                              echo  $form->field($model, 'verifyCode', ['options'=>['class' => 'form-group']])->widget(Captcha::className(), [
                                                'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                                                'captchaAction' => 'home/captcha'
                                              ]) ;
                                         }
                                    ?>
                        
                                    <div class="form-actions">
                                        <?= $form->field($model, 'rememberMe')->checkbox(['class'=>['uniform'], 'style'=>'width:13px']) ?>
                                        <?= Html::submitButton('Login', ['class' => 'btn btn-danger', 'name' => 'login-button']) ?>
                                    </div>
                                <?php ActiveForm::end(); ?>
								<!-- SOCIAL LOGIN -->
								
							</div>
						</div>
					</div>
				</div>
			</section>
	</section>

</body>
</html>