<?php
$this->breadcrumbs=array(
	'Postes'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Liste des avis', 'url'=>array('conge')),
	array('label'=>'Remplir un avis', 'url'=>array('congeCreate')),
);

if($siAdmin){
	$this->avisNV = array();
	foreach($avisNV as $avis){
		$this->avisNV[] = array('label'=>$avis->id, 'url'=>array('congeUpdate','id'=>$avis->id));
	}
}
?>

<?php echo $this->renderPartial('_formConge', array('idS'=>$idS, 'idP'=>$idP, 'model'=>$model, 'listType'=>$listType, 'listUsager'=>$listUsager,'listRadioButton'=>$listRadioButton)); ?>