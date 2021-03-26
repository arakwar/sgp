<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />
	<meta name="viewport" content="width=device-width; initial-scale=1.0; user-scalable=no;" />
	<?php /*
	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->
	*/?>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/mainMobile.css" />

	<link rel="apple-touch-icon" sizes="57x57" href="/favicons/apple-touch-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="/favicons/apple-touch-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/favicons/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="/favicons/apple-touch-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/favicons/apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="/favicons/apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="/favicons/apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="/favicons/apple-touch-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon-180x180.png">
	<link rel="icon" type="image/png" href="/favicons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/favicons/android-chrome-192x192.png" sizes="192x192">
	<link rel="icon" type="image/png" href="/favicons/favicon-96x96.png" sizes="96x96">
	<link rel="icon" type="image/png" href="/favicons/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/favicons/manifest.json">
	<link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="shortcut icon" href="/favicons/favicon.ico">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="msapplication-TileImage" content="/favicons/mstile-144x144.png">
	<meta name="msapplication-config" content="/favicons/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">

	<title><?php echo 'SGP - '.CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<?php 

	Yii::app()->clientScript->registerScript('ajaxIcone','
		jQuery(document).ajaxStart(function(){$("#ajaxCharge").addClass("wait");});
		jQuery(document).ajaxStop(function(){$("#ajaxCharge").removeClass("wait");});
	');
	if(Yii::app()->getController()->id <> 'site')
	{
		$this->redirect(Yii::app()->homeUrl);
	}
?>
<div id="topMenu">
		<?php //echo $image = CHtml::image('images/logo.png','Mois courant',array('id'=>'logoTop'));
				//echo CHtml::link($image,array('/site/index'));?>
		<div id="menu">
			<?php
		
			$this->widget('system.ext.menu.SMenu',array(
				"menu"=>array(
					array("url"=>array("route"=>"site/site_complet"),
						  "label"=>"Site complet",
						  "visible"=>!(Yii::app()->user->isGuest),
						),
					),
				"stylesheet"=>"menu_sgh.css",
				"menuID"=>"menuSite",
				"delay"=>"2"
				)
			);
			?>
		</div>
		<div id="info">
			<?php 
				if(Yii::app()->user->isGuest) echo "<span>".CHtml::link("Connexion",array('/site/login'),array('style'=>'color:#fff'))."</span>";
				else echo "<span>".CHtml::link("Déconnexion (".Yii::app()->user->nom.")",array('/site/logout'),array('style'=>'color:#fff'))."</span>";
			?>
		</div>
	</div>

	<?php echo $content; ?>

	<div id="footer">
		<a href="http://www.swordware.com" target="_blank"><div id="footLogoSwordware"></div></a>
		<?php /* 
		<div id="footTxt">
			Copyright &copy; <a href="http://www.swordware.com" target="_blank">Studio Swordware</a> 2010-<?php echo date('Y'); ?> 
			<?php /*echo Yii::powered();
		</div>
		<div id="footLogoVille"><div id="footLienVille"></div></div>
		*/?>
	</div><!-- footer -->

<?php if(defined('PIWIK_CODE')) echo PIWIK_CODE;?>
</body>
</html>