<?php
$this->breadcrumbs=array(
	'Postes'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des postes', 'url'=>array('index')),
	array('label'=>'Créer un poste', 'url'=>array('create')),
	array('label'=>'Voir ce poste', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>