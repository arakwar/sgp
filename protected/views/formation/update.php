<?php
$this->breadcrumbs=array(
	'Formations'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des formations', 'url'=>array('index')),
	array('label'=>'Créer une formation', 'url'=>array('create')),
	array('label'=>'Retour à la formation', 'url'=>array('view', 'id'=>$model->id)),
);

echo $this->renderPartial('_form', array('model'=>$model, 'lstFormations'=>$lstFormations, 'lstFormationsPre'=>$lstFormationsPre, 'lstFormationsPreC'=>$lstFormationsPreC)); ?>