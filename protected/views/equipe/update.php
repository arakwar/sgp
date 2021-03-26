<?php
$this->breadcrumbs=array(
	'Equipes'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des équipes', 'url'=>array('index')),
	array('label'=>'Créer un équipe', 'url'=>array('create')),
	array('label'=>'Voir cet équipe', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Ordre des équipes FDF', 'url'=>array('ordre')),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'lstCaserne'=>$lstCaserne)); ?>