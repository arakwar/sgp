<?php
$this->breadcrumbs=array(
	'Notices'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Liste des notices', 'url'=>array('index')),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model,'casernesUsager'=>$casernesUsager)); ?>