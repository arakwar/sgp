<?php
$this->breadcrumbs=array(
	'Évènement'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Liste des évènements', 'url'=>array('index')),
	array('label'=>'Créer un évènement', 'url'=>array('create')),
	array('label'=>'Modifier cet évènement', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Supprimer cet évènement', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?')),
);

if($model->tbl_formation_id == 0){
	$this->widget('zii.widgets.CDetailView', array(
		'data'=>$model,
		'cssFile' => Yii::app()->baseUrl .'/css/main.css',
		'attributes'=>array(
			'nom',
			'lieu',
			'dateDebut',
			'dateFin',
		),
	)); 
}elseif($model->tbl_formation_id != '0'){
	$this->widget('zii.widgets.CDetailView', array(
			'data'=>$model,
			'cssFile' => Yii::app()->baseUrl .'/css/main.css',
			'attributes'=>array(
					'nom',
					'lieu',
					'dateDebut',
					'dateFin',
					array('name'=>'instituteur','value'=>CHtml::encode($model->Instituteur->getPrenomnom())),
					array('name'=>'moniteur','value'=>CHtml::encode($model->Moniteur->getPrenomnom())),
			),
	));	
}
echo '<div class="span-19">';
$ordre = 'odd';
echo '<table class="detail-view">';
echo '<tr class="'.$ordre.'">';
echo '<th>Usagers : </th><td></td>';
echo '</tr>';
foreach($lstUsagers as $usager){
	if($ordre=='odd'){
		$ordre = 'even';
	}else{
		$ordre = 'odd';
	}
	echo '<tr class="'.$ordre.'">';
	echo '<td></td><td>'.$usager.((in_array ($usager , $lstResultats))?' (Completé)':'').'</td>';
	echo '</tr>';
}
echo '</table>';
echo '</div>';

?>
