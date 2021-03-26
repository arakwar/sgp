<?php
$this->breadcrumbs=array(
	'Grades'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Liste des grades', 'url'=>array('index')),
	array('label'=>'Créer un grade', 'url'=>array('create')),
	array('label'=>'Modifier ce grade', 'url'=>array('update', 'id'=>$model->id)),
	//array('label'=>'Gérer les droits', 'url'=>array('droits', 'id'=>$model->id)),
	//array('label'=>'Supprimer ce grade', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?')),
);
?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		'nom',
		//'roleName',
	),
)); 
if(isset($_GET['m'])){
	echo 'Attention : un ou plusieurs usagers sont encore lié à ce grade. Aller changer ces usagers de grade pour pouvoir le supprimer.';
}?>
