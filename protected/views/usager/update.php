<?php
$this->breadcrumbs=array(
	'Usagers'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des usagers', 'url'=>array('index'),'visible'=>Yii::app()->user->checkAccess('Usager:index')),
	array('label'=>'Créer un usager', 'url'=>array('create'),'visible'=>Yii::app()->user->checkAccess('Usager:create')),
	array('label'=>'Retour à l\'usager', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model, 'listRole'=>$listRole)); ?>