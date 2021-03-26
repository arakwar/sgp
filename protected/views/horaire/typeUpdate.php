<?php
$this->breadcrumbs=array(
	'Postes'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des types', 'url'=>array('typeConge')),
	array('label'=>'Créer un type', 'url'=>array('typeCreate')),
);
?>

<?php echo $this->renderPartial('_formType', array('model'=>$model)); ?>