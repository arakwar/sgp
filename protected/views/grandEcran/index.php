<?php
/* @var $this GrandEcranController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Grand Ecrans',
);

$this->menu=array(
/*	array('label'=>'Create GrandEcran', 'url'=>array('create')),
	array('label'=>'Manage GrandEcran', 'url'=>array('admin')),*/
);
?>

<h1>Grand Ecrans</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
