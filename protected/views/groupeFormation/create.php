<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/evenement.css');
$this->breadcrumbs=array(
	'Groupes formation'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Liste des groupes de formation', 'url'=>array('index')),
);

echo $this->renderPartial('_form', array('model'=>$model, 'lstUsagers'=>$lstUsagers)); ?>