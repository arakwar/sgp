<?php
$this->breadcrumbs=array(
	'Type Documents'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Liste des types', 'url'=>array('index')),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>