<?php
$this->breadcrumbs=array(
	'Gardes'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des gardes', 'url'=>array('garde')),
	array('label'=>'Créer une garde', 'url'=>array('create')),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>