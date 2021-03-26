<?php

$this->breadcrumbs=array(
	'Évènements'=>array('index'),
	'Create',
);

echo $this->renderPartial('_form', array(
	'model'=>$model,
	'lstUsagers'=>$lstUsagers, 
	'lstUsagersDispo'=>$lstUsagersDispo, 
	'lstGroupeF'=>$lstGroupeF, 
	'lstPreRequis'=>$lstPreRequis
)); ?>