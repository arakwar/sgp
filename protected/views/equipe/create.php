<?php
$this->breadcrumbs=array(
	'Equipes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Liste des équipes', 'url'=>array('index')),
	array('label'=>'Ordre des équipes FDF', 'url'=>array('ordre')),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'lstCaserne'=>$lstCaserne)); ?>