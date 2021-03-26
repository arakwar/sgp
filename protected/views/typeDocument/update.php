<?php
$this->breadcrumbs=array(
	'Type Documents'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des types', 'url'=>array('index')),
	array('label'=>'Créer un type', 'url'=>array('create')),
	array('label'=>'Voir ce type', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>