<?php
$this->breadcrumbs=array(
	'Postes'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Liste des types', 'url'=>array('typeConge')),
	array('label'=>'Créer un type', 'url'=>array('typeCreate')),
	array('label'=>'Modifier ce type', 'url'=>array('typeModif', 'id'=>$model->id)),
	array('label'=>'Supprimer ce type', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?')),
);
?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		'nom',
		'abrev',
	),
)); 
?>
