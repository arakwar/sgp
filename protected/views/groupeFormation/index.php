<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/evenement.css');
$this->breadcrumbs=array(
	'Groupes formation',
);

$this->menu=array(
	array('label'=>'Créer un groupe de formation', 'url'=>array('create')),
);
?>

<?php
$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
