<?php
$this->breadcrumbs=array(
	'Notices'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Retour à la liste', 'url'=>array('index')),
	array('label'=>'Créer une notice', 'url'=>array('create')),
	array('label'=>'Modifier cette notice', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Supprimer cette notice', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?')),
);
?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		'message',
		'dateDebut',
		'dateFin',
		array('name'=>'tbl_usager_id','value'=>CHtml::encode($model->tblUsager->prenomnom)),
	),
)); ?>
