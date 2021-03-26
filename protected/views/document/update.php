<?php
$this->breadcrumbs=array(
	'Documents'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des documents', 'url'=>array('index')),
	array('label'=>'Créer un document', 'url'=>array('create')),
	array('label'=>'Voir le document', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model,'lstType'=>$lstType,'casernesUsager'=>$casernesUsager)); ?>