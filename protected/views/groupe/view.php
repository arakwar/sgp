<?php
$this->breadcrumbs=array(
	'Groupes'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Liste des équipes spécialisées', 'url'=>array('index')),
	array('label'=>'Créer une équipe spécialisée', 'url'=>array('create')),
	array('label'=>'Modifier cette équipe spécialisée', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Supprimer cette équipe spécialisée', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?')),
);

$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		'nomL',
		'nom',
		array('name'=>'tbl_caserne_id','value'=>CHtml::encode($model->tblCaserne->nom))
	),
)); ?>
