<?php
$this->breadcrumbs=array(
	'Quarts'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des quarts', 'url'=>array('index')),
	array('label'=>'Créer un quart', 'url'=>array('create')),
	array('label'=>'Voir ce quart', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>