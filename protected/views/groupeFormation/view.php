<?php
$this->breadcrumbs=array(
	'Groupes formation'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Liste des groupes de formation', 'url'=>array('index')),
	array('label'=>'Créer un groupe de formation', 'url'=>array('create')),
	array('label'=>'Modifier ce groupe de formation', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Supprimer ce groupe de formation', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?')),
);

$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		'nom',
	),
)); ?>
