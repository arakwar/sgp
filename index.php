<?php
// change the following paths if necessary
$yii=dirname(__FILE__).'/protected/framework1.1.13/yii.php';
//Détection de domaine
defined('DOMAINE') or define('DOMAINE',str_replace('www.','',$_SERVER['HTTP_HOST']));

// remove the following lines when in production mode
//defined('YII_DEBUG') or define('YII_DEBUG',true);
if(YII_DEBUG) set_time_limit(300000);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
date_default_timezone_set('America/Montreal');
$config=dirname(__FILE__).'/protected/config/main.php';

require_once($yii);

Yii::createWebApplication($config)->run();
