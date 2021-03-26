<?php
$this->breadcrumbs=array(
	'Grades'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des grades', 'url'=>array('index')),
	array('label'=>'Créer un grade', 'url'=>array('create')),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>