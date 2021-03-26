<?php
$this->breadcrumbs=array(
	'Avis d\'absence',
);

$this->menu=array(
	array('label'=>'Créer un type', 'url'=>array('typeCreate')),
);
?>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_viewType',
	'template'=>'{items}<div style="clear:both"></div>{pager}',
)); ?>