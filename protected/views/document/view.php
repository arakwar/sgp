<?php
$this->breadcrumbs=array(
	'Documents'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Lister les documents', 'url'=>array('index')),
	array('label'=>'Créer un document', 'url'=>array('create'),'visible'=>Yii::app()->user->checkAccess('Document:create')),
	array('label'=>'Modifier le document', 'url'=>array('update', 'id'=>$model->id),'visible'=>Yii::app()->user->checkAccess('Document:create')),
	array('label'=>'Supprimer le document', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?'),'visible'=>Yii::app()->user->checkAccess('Document:create')),
);


	$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		'nom',
		'date',
		'description',
	),
)); ?>
