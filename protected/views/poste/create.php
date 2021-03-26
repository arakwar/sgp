<?php
$this->breadcrumbs=array(
	'Postes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Liste des postes', 'url'=>array('index')),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>