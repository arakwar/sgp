<?php
$this->breadcrumbs=array(
	'Postes'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Liste des postes', 'url'=>array('index')),
	array('label'=>'Créer un poste', 'url'=>array('create')),
	array('label'=>'Modifier ce poste', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Supprimer ce poste', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?')),
);
?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		'id',
		'nom',
		'diminutif',
	),
)); 
if(isset($message)){
	echo $message;
}
?>
