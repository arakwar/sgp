<?php
$this->breadcrumbs=array(
	'Postes',
);

$this->menu=array(
	array('label'=>'Créer un poste', 'url'=>array('create')),
);
?>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'template'=>'{items}<div style="clear:both"></div>{pager}',
)); ?>


