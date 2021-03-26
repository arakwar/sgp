<?php
$this->breadcrumbs=array(
	'Formations'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Liste des formations', 'url'=>array('index')),
	array('label'=>'Créer une formation', 'url'=>array('create')),
	array('label'=>'Modifier cet formation', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Supprimer cette formation', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?')),
);

$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		'nom',
	),
)); 
echo '<div class="span-19">';
$ordre = 'even';
echo '<table class="detail-view">';
echo '<tr class="'.$ordre.'">';
echo '<th>Pré-requis : </th><td></td>';
echo '</tr>';
foreach($lstPrerequis as $prerequis){
	if($ordre=='odd'){
		$ordre = 'even';
	}else{
		$ordre = 'odd';
	}
	echo '<tr class="'.$ordre.'">';
	echo '<td></td><td>'.$prerequis.'</td>';
	echo '</tr>';
}
echo '</table>';
echo '</div>';	

?>
