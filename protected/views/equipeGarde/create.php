<?php
$this->breadcrumbs=array(
	'Gardes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Liste des gardes', 'url'=>array('garde')),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>