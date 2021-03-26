<?php
$this->breadcrumbs=array(
	'Usagers'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Liste des invités', 'url'=>array('invites'),'visible'=>Yii::app()->user->checkAccess('Usager:index')),
);
?>

<?php echo $this->renderPartial('_formInvite', array('model'=>$model)); ?>