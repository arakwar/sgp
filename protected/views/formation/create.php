<?php
$this->breadcrumbs=array(
	'Formations'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Liste des formations', 'url'=>array('index')),
);

echo $this->renderPartial('_form', array('model'=>$model, 'lstFormations'=>$lstFormations, 'lstFormationsPre'=>$lstFormationsPre, 'lstFormationsPreC'=>$lstFormationsPreC)); ?>