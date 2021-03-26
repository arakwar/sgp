<?php
$this->breadcrumbs=array(
	'Postes'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Liste des avis', 'url'=>array('conge')),
);

if($siAdmin){
	$this->avisNV = array();
	foreach($avisNV as $avis){
		$this->avisNV[] = array('label'=>$avis->id, 'url'=>array('congeUpdate','id'=>$avis->id));
	}
}
?>

<?php echo $this->renderPartial('_formConge', array('model'=>$model, 'siAdmin'=>$siAdmin, 'listType'=>$listType)); ?>