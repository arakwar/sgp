<?php
$this->breadcrumbs=array(
	'Documents'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Liste des documents', 'url'=>array('index')),
);
?>

<?php echo $this->renderPartial('_form', array(
	'model'=>$model,
	'lstType'=>$lstType,
	'casernesUsager'=>$casernesUsager
)); ?>