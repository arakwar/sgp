<?php
$this->breadcrumbs=array(
	'Minimum Exceptions'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List MinimumException', 'url'=>array('index')),
	array('label'=>'Create MinimumException', 'url'=>array('create')),
	array('label'=>'View MinimumException', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'lstCaserne'=>$lstCaserne)); ?>