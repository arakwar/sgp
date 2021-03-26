<?php
$this->breadcrumbs=array(
	'Poste Horaires'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Liste des postes/horaires', 'url'=>array('index')),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'lstCaserne'=>$lstCaserne)); ?>