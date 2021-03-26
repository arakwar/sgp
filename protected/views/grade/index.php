<?php
$this->breadcrumbs=array(
	'Grades',
);

$this->menu=array(
	array('label'=>'Créer un grade', 'url'=>array('create')),
);
?>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'template'=>'{items}<div style="clear:both"></div>{pager}',
)); ?>
