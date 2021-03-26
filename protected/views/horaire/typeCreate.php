<?php
$this->breadcrumbs=array(
	'Postes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Liste des types', 'url'=>array('typeConge')),
);
?>

<?php echo $this->renderPartial('_formType', array('model'=>$model)); ?>