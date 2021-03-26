<?php
$this->menu=array(
	array('label'=>'Liste des casernes', 'url'=>array('index')),
);

echo $this->renderPartial('_form', array('model'=>$model)); ?>