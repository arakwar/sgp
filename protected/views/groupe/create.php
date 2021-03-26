<?php
$this->breadcrumbs=array(
	'Groupes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Liste des équipes spécialisées', 'url'=>array('index')),
);

echo $this->renderPartial('_form', array('model'=>$model, 'lstCaserne'=>$lstCaserne)); ?>