<?php
$this->breadcrumbs=array(
	'Minimum Exceptions'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List MinimumException', 'url'=>array('index')),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'lstCaserne'=>$lstCaserne)); ?>