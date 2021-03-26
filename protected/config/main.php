<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
Yii::setPathOfAlias('chartjs', dirname(__FILE__).'/../extensions/chartjs');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
//return array(
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'SGP',
	//'sourceLanguage'=>'fr_CA',
	'language'=>'fr',
	'homeUrl'=>array('site/index'),

	// preloading 'log' component
	'preload'=>array('log','layoutHandler','chartjs'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'system.ext.uploadify.MUploadify',
		'system.ext.CAdvancedArBehavior',
		//'system.ext.Mobile_Detect', //Maintenant dans components
		'system.ext.yii-mail.YiiMailMessage',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>(YII_DEBUG)?
		array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		):'',
		'rbam'=>array(
				'userClass'=>'Usager',
				'userNameAttribute'=>'id',
				'rbacManagerRole'=>'SuperAdmin',
				'authAssignmentsManagerRole'=>'SuperAdmin',
				'authItemsManagerRole'=>'SuperAdmin',
		),
	),

	// application components
	'components'=>array(
		'authManager'=>array( 
			'class'=>'CDbAuthManager', // Provides support authorization item sorting.
			'connectionID'=>'db',
			'defaultRoles'=>array('Usager','Guest')
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
		'db'=> require(dirname(__FILE__).DIRECTORY_SEPARATOR.DOMAINE.DIRECTORY_SEPARATOR.'main_db.php'),
		'db_log' => array(
			'connectionString' => 'mysql:host=localhost;dbname=admin_sgp_log',
			'emulatePrepare' => true,
			'username' => 'sgp_externe',
			'password' => 'fJe2M2Ny6yrHBWYF',
			'charset' => 'utf8',
			'enableProfiling'=>true,
			'enableParamLogging'=>true,
			'class'      => 'CDbConnection' 
		),
		'mail' => require(dirname(__FILE__).DIRECTORY_SEPARATOR.DOMAINE.DIRECTORY_SEPARATOR.'main_mail.php'),
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
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
/*				array(
		            'class'=>'CEmailLogRoute',
		            'levels'=>'error',
		            'emails'=>'info@tech.swordware.com',
		            'subject' => 'Log applicatif : '.DOMAINE,
		        ),
		        array(
		            'class'=>'CEmailLogRoute',
		            'levels'=>'trace, info, error, warning',
					'categories'=>'Tech.*',
		            'emails'=>'info@tech.swordware.com',
		            'subject' => 'Log applicatif : '.DOMAINE,
		        ),*/
		        array(
                    'class'=>'ext.LogDb',
                    'autoCreateLogTable'=>true,
                    'connectionID'=>'db_log',
                    'enabled'=>true,
                    'levels'=>'warning,error,',//You can replace trace,info,warning,error
                ),
			)
		),
		'mobileDetect' => array(
        	'class' => 'system.ext.MobileDetect.MobileDetect'
    	),
    	'ePdf' => array(
    			'class'         => 'ext.yii-pdf.EYiiPdf',
    			'params'        => array(
    					'mpdf'     => array(
    							'librarySourcePath' => 'application.vendors.mpdf.*',
    							'constants'         => array(
    									'_MPDF_TEMP_PATH' => Yii::getPathOfAlias('application.runtime'),
    							),
    							'class'=>'mpdf', // the literal class filename to be loaded from the vendors folder
    							'defaultParams'     => array( // More info: http://mpdf1.com/manual/index.php?tid=184
    									//'mode'              => '', //  This parameter specifies the mode of the new document.
    									'format'            => 'Letter', // format A4, A5, ...
    									'default_font_size' => 0, // Sets the default document font size in points (pt)
    									'default_font'      => 'Arial', // Sets the default font-family for the new document.
    									'mgl'               => 15, // margin_left. Sets the page margins for the new document.
    									'mgr'               => 15, // margin_right
    									'mgt'               => 16, // margin_top
    									'mgb'               => 16, // margin_bottom
    									'mgh'               => 9, // margin_header
    									'mgf'               => 9, // margin_footer
    									'orientation'       => 'P', // landscape or portrait orientation
    							)
    					),
    			),
    	),
    	'chartjs' => array('class' => 'chartjs.components.ChartJs'),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>require(dirname(__FILE__).DIRECTORY_SEPARATOR.DOMAINE.DIRECTORY_SEPARATOR.'main_params.php'),
);