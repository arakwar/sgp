<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/evenement.css');
$this->breadcrumbs=array(
	'Groupes formation'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des groupes de formation', 'url'=>array('index')),
	array('label'=>'Créer un groupe de formation', 'url'=>array('create')),
	array('label'=>'Retour au groupe de formation', 'url'=>array('view', 'id'=>$model->id)),
);

echo $this->renderPartial('_form', array('model'=>$model, 'lstUsagers'=>$lstUsagers, 'lstGFUsagers'=>$lstGFUsagers)); ?>