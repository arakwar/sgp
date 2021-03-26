<?php
$this->breadcrumbs=array(
	'Postes'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Liste des avis', 'url'=>array('conge')),
	array('label'=>'Remplir un avis', 'url'=>array('congeCreate')),
);
?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		array('name'=>'tbl_type_id','value'=>CHtml::encode((($model->tblType->abrev !== NULL)?$model->tblType->abrev.' - ':'').$model->tblType->nom)),
		array('name'=>'tbl_quart_id','value'=>CHtml::encode($model->tblQuarts->nom)),		
		'dateConge',
		'heureDebut',
		'heureFin',
	),
)); 
?>
