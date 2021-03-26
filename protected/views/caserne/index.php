<?php
$this->breadcrumbs=array(
	'Casernes',
);

$this->menu=array(
	array('label'=>'Ajouter une caserne', 'url'=>array('create')),
);
?>

<?php
$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
