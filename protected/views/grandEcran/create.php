<?php
/* @var $this GrandEcranController */
/* @var $model GrandEcran */

$this->breadcrumbs=array(
	'Grand Ecrans'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List GrandEcran', 'url'=>array('index')),
	array('label'=>'Manage GrandEcran', 'url'=>array('admin')),
);
?>

<h1>Create GrandEcran</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>