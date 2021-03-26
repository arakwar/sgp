<?php
$this->breadcrumbs=array(
	'Usagers'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Liste des usagers', 'url'=>array('index'),'visible'=>Yii::app()->user->checkAccess('Usager:index')),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'listRole'=>$listRole)); ?>