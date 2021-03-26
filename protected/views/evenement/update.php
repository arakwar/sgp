<?php
$this->breadcrumbs=array(
	'Évènements'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des évènements', 'url'=>array('index')),
	array('label'=>'Créer un évènement', 'url'=>array('create')),
	array('label'=>'Retour à l\'évènement', 'url'=>array('view', 'id'=>$model->id)),
);

echo $this->renderPartial('_form', array('model'=>$model, 'lstUsagers'=>$lstUsagers, 'lstInvites'=>$lstInvites, 'lstUsagersDispo'=>$lstUsagersDispo, 'lstGroupeF'=>$lstGroupeF, 'lstGroupeE'=>$lstGroupeE, 'lstPreRequis'=>$lstPreRequis)); ?>