<?php
$this->breadcrumbs=array(
	'Usagers'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des invités', 'url'=>array('invite'),'visible'=>Yii::app()->user->checkAccess('Usager:index')),
	array('label'=>'Créer un invités', 'url'=>array('createInvite'),'visible'=>Yii::app()->user->checkAccess('Usager:create')),
	array('label'=>'Retour à l\'invité', 'url'=>array('view', 'id'=>$model->id)),
);
?>

<?php echo $this->renderPartial('_formInvite', array('model'=>$model)); ?>