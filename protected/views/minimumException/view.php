<?php
$this->breadcrumbs=array(
	'Minimum Exceptions'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List MinimumException', 'url'=>array('index')),
	array('label'=>'Create MinimumException', 'url'=>array('create')),
	array('label'=>'Update MinimumException', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete MinimumException', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?')),
);
?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'dateDebut',
		'dateFin',
		'minimum',
		'tbl_usager_id',
		array('name'=>'tbl_caserne_id','value'=>CHtml::encode($model->tblCaserne->nom))
	),
)); ?>
