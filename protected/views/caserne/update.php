<?php
$this->menu=array(
	array('label'=>'Liste des casernes', 'url'=>array('index')),
	array('label'=>'Ajouter une caserne', 'url'=>array('create')),
	array('label'=>'Retour à la caserne', 'url'=>array('view', 'id'=>$model->id)),
);

echo $this->renderPartial('_form', array('model'=>$model)); ?>