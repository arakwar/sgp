<?php
$this->breadcrumbs=array(
	'Type Evenements'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Liste des types', 'url'=>array('index')),
	array('label'=>'Créer un type', 'url'=>array('create')),
	array('label'=>'Modifier ce type', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Supprimer ce type', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?')),
);
?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		'nom',
	),
)); ?>
