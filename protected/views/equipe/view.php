<?php
$this->breadcrumbs=array(
	'Equipes'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Liste des équipes', 'url'=>array('index')),
	array('label'=>'Créer un équipe', 'url'=>array('create')),
	array('label'=>'Modifier cette équipe', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Supprimer cette équipe', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?')),
	array('label'=>'Ordre des équipes FDF', 'url'=>array('ordre')),
);
?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		'id',
		'nom',
		'couleur',
		array('name'=>'siHoraire','value'=>($model->siHoraire==1?'Oui':'Non')),
		array('name'=>'siFDF','value'=>($model->siFDF==1?'Oui':'Non')),
		array('name'=>'siAlerte','value'=>($model->siAlerte==1?'Oui':'Non')),
		array('name'=>'tbl_caserne_id','value'=>CHtml::encode($model->tblCaserne->nom))
	),
)); 
if(isset($_GET['m'])){
	if($_GET['m']==1){
		echo 'Attention : Il ne reste plus qu\'une équipe inscrite dans l\'horaire. Veuillez en ajouter une nouvelle avant d\'enlever celle-ci. La dernière modification n\'a pas été totalement enregistrée.';
	}elseif($_GET['m']==2){
		echo 'Attention : Cette équipe est encore dans une des gardes, veuillez aller modifier la garde avant de supprimer cette équipe.';
	}else{
		echo 'Attention : Un ou plusieurs usagers sont encore liés à l\'équipe que vous souhaitez supprimée. Changer d\'abord les usagers concernés d\'équipe puis supprimé la de nouveau.';
	}
}
?>
