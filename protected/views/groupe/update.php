<?php
$this->breadcrumbs=array(
	'Groupes'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des équipes spécialisées', 'url'=>array('index')),
	array('label'=>'Créer une équipes spécialisée', 'url'=>array('create')),
	array('label'=>'Retour à l\'équipe spécialisée', 'url'=>array('view', 'id'=>$model->id)),
);

echo $this->renderPartial('_form', array('model'=>$model, 'lstCaserne'=>$lstCaserne)); ?>