<?php
$this->breadcrumbs=array(
	'Type Documents',
);

$this->menu=array(
	array('label'=>'Créer un type', 'url'=>array('create')),
);
?>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
