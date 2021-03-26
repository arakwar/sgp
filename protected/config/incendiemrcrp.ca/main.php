<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
//return array(
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'SGP',
	'sourceLanguage'=>'en',
	'language'=>'fr',
	'homeUrl'=>array('site/index'),

	// preloading 'log' component
	'preload'=>array('log','layoutHandler'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'system.ext.uploadify.MUploadify',
		'system.ext.CAdvancedArBehavior',
		'system.ext.Mobile_Detect',
		'ext.yii-mail.YiiMailMessage',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>(YII_DEBUG)?
		array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		):''
	),

	// application components
	'components'=>array(
		'authManager'=>array( 'class'=>'CDbAuthManager', // Provides support authorization item sorting.
		'connectionID'=>'db'
		),
		'layoutHandler'=>array(
			'class'=>'application.components.LayoutHandler',
		),
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>false,
		),
		'menu'=>array(
			'class'=>'ext.menu.SMenu'
		),
		'db'=> require(dirname(__FILE__).DIRECTORY_SEPARATOR.'main_db.php'),
		'mail' => require(dirname(__FILE__).DIRECTORY_SEPARATOR.'main_mail.php'),
		// uncomment the following to enable URLs in path-format
		/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		*/
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>require(dirname(__FILE__).DIRECTORY_SEPARATOR.'main_log.php'),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>require(dirname(__FILE__).DIRECTORY_SEPARATOR.'main_params.php'),
);