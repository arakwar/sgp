<?php
return array(
	'class'=>'CLogRouter',
	'routes'=>array(
		'file'=>array(
			'class'=>'CFileLogRoute',
			'levels'=>'info, error, warning,',
		),
		array(
            'class'=>'CEmailLogRoute',
            'levels'=>'error',
            'emails'=>'info@swordware.com',
        ),
	)
);
