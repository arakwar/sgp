<?php
$this->breadcrumbs=array(
	'Quarts'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Liste des quarts', 'url'=>array('index')),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>