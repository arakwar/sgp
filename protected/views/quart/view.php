<?php
$this->breadcrumbs=array(
	'Quarts'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Liste des quarts', 'url'=>array('index')),
	array('label'=>'Créer un quart', 'url'=>array('create')),
	array('label'=>'Modifier ce quart', 'url'=>array('update', 'id'=>$model->id)),
	//array('label'=>'Supprimer ce quart', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?')),
);
?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		'nom',
		'heureDebut',
		'heureFin',
	),
)); ?>
