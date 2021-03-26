<?php
$this->breadcrumbs=array(
	'Poste Horaires'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Liste des poste/horaire', 'url'=>array('index')),
	array('label'=>'Créer un poste/horaire', 'url'=>array('create')),
	array('label'=>'Modifier ce poste/horaire', 'url'=>array('update', 'id'=>$model->id)),
	//array('label'=>'Suprimer ce poste/horaire', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?')),
);
?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		'heureDebut',
		'heureFin',
		array('name'=>'tbl_quart_id','value'=>CHtml::encode($model->Quart->nom)),
		array('name'=>'tbl_poste_id','value'=>CHtml::encode($model->poste->nom)),
	),
)); ?>
