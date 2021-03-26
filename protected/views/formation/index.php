<?php
$this->breadcrumbs=array(
	'Formations',
);

$this->menu=array(
	array('label'=>'Créer une formation', 'url'=>array('create')),
	array('label'=>'Planifier une formation', 'url'=>array('plan')),
);
?>

<?php
$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
