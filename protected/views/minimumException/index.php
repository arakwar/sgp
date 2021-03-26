<?php
$this->breadcrumbs=array(
	'Minimum Exceptions',
);

$this->menu=array(
	array('label'=>'Create MinimumException', 'url'=>array('create')),
);
?>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
