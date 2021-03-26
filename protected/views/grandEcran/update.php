<?php
/* @var $this GrandEcranController */
/* @var $model GrandEcran */

$this->breadcrumbs=array(
	'Grand Ecrans'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List GrandEcran', 'url'=>array('index')),
	array('label'=>'Create GrandEcran', 'url'=>array('create')),
	array('label'=>'View GrandEcran', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage GrandEcran', 'url'=>array('admin')),
);
?>

<h1>Update GrandEcran <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>