<?php
$this->breadcrumbs=array(
	'Gardes',
);

$this->menu=array(
	array('label'=>'Créer une garde', 'url'=>array('create')),
);
?>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'template'=>'{items}<div style="clear:both"></div>{pager}',
)); ?>


