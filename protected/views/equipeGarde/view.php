<?php
$this->breadcrumbs=array(
	'Gardes'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Liste des gardes', 'url'=>array('garde')),
	array('label'=>'Créer une garde', 'url'=>array('create')),
	array('label'=>'Modifier cette garde', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Supprimer cette garde', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?')),
);
?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		'id',
		'nom',
		'nbr_jour_affiche',
		'nbr_jour_periode',
		'nbr_jour_depot',
		'nbr_jour_ge',
		'date_debut',
	),
)); 
?>
