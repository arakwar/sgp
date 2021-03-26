<?php
$this->breadcrumbs=array(
	'Invités'=>array('invite'),
	'viewInvite',
);

$this->menu=array(
	array('label'=>'Liste des invités', 'url'=>array('invite'),'visible'=>Yii::app()->user->checkAccess('Usager:create')),
	array('label'=>'Créer un invité', 'url'=>array('createInvite'),'visible'=>Yii::app()->user->checkAccess('Usager:create')),
	array('label'=>'Modifier cet invité', 'url'=>array('updateInvite', 'id'=>$model->id)),
	array('label'=>'Supprimer cet invité', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?'),'visible'=>Yii::app()->user->checkAccess('Usager:create')),
);
?>

<h1><?php echo $model->prenom. " ".$model->nom; ?></h1>

<?php 
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		'pseudo',
		'courriel',
		'adresseCivique',
		'ville',
		'telephone1',
		'telephone2',
		'telephone3',
	),
));
?>
