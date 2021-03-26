<?php
$this->breadcrumbs=array(
	'Casernes'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Liste des casernes', 'url'=>array('index')),
	array('label'=>'Ajouter une caserne', 'url'=>array('create')),
	array('label'=>'Modifier cette caserne', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Supprimer cette caserne', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?')),
);

$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'nom',
		'numero',
		'adresse',
		'ville',
		'codePostal',
	),
)); 
if(isset($_GET['m'])){
	if($_GET['m']==1){
		echo 'Attention : Un ou plusieurs éléments du système (quart, équipe, équipe sépcialisée ou grade) sont encore lié à cette caserne. Veuillez la délié de tout élément du système avant de supprimer cette caserne.';
	}
}
?>
