<?php
$this->breadcrumbs=array(
	'Notices'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des notices', 'url'=>array('index')),
	array('label'=>'Créer une notice', 'url'=>array('create')),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model,'casernesUsager'=>$casernesUsager)); ?>