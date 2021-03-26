<?php
$this->breadcrumbs=array(
	'Poste Horaires'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des postes/horaires', 'url'=>array('index')),
	array('label'=>'Créer un poste/horaire', 'url'=>array('create')),
	array('label'=>'Voir ce poste/horaire', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'lstCaserne'=>$lstCaserne)); ?>