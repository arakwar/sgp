<?php
return array(
	'class'=>'CLogRouter',
	'routes'=>array(
		'file'=>array(
			'class'=>'CFileLogRoute',
			'levels'=>'info, error, warning,',
		),
		/*'Usager'=>array(
			'class'=>'CFileLogRoute',
			'levels'=>'trace, info, error, warning,',
			'categories'=>'Usager.*',
			'logFile'=>'Usager.txt'
		),
		'Equipe'=>array(
			'class'=>'CFileLogRoute',
			'levels'=>'trace, info, error, warning,',
			'categories'=>'Equipe.*',
			'logFile'=>'Equipe.txt'
		),*/
		'DispoFDF'=>array(
			'class'=>'CFileLogRoute',
			'levels'=>'trace, info, error, warning,',
			'categories'=>'DispoFDF.*',
			'logFile'=>'DispoFDF.txt'
		),
		/*'web'=>array(
			'class'=>'CWebLogRoute',
			'levels'=>'trace, info, error, warning',
			'showInFireBug'=>true
		),*/
	    array(
            'class'=>'CEmailLogRoute',
            'levels'=>'error',
            'emails'=>'info@swordware.com',
        ),
	)
);
