<?php
$this->breadcrumbs=array(
	'Quarts',
);

$this->menu=array(
	array('label'=>'Créer un quart', 'url'=>array('create')),
);
?>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
