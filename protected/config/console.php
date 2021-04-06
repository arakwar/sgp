<?php

$database = dirname(__FILE__).DIRECTORY_SEPARATOR.DOMAINE.DIRECTORY_SEPARATOR.'main_db.php';

if(YII_DEBUG){
	$database = dirname(__FILE__).DIRECTORY_SEPARATOR.DOMAINE.DIRECTORY_SEPARATOR.'main_db_dev.php';
}

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',
	// application components
	'components'=>array(
		'db'=> require($database),
		'params'=>require(dirname(__FILE__).DIRECTORY_SEPARATOR.DOMAINE.DIRECTORY_SEPARATOR.'main_params.php'),
		'authManager'=>array( 
			'class'=>'CDbAuthManager', // Provides support authorization item sorting.
			'connectionID'=>'db',
			'defaultRoles'=>array('Usager','Guest')
		),
	),
);