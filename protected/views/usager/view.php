<?php
$this->breadcrumbs=array(
	'Usagers'=>array('index'),
	'view',
);

$this->menu=array(
	array('label'=>'Liste des usagers', 'url'=>array('index'),'visible'=>Yii::app()->user->checkAccess('Usager:index')),
	array('label'=>'Créer un usager', 'url'=>array('create'),'visible'=>Yii::app()->user->checkAccess('Usager:create')),
	array('label'=>'Modifier cet usager', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Supprimer cet usager', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Êtes-vous sûr de vouloir supprimer cet item?'),'visible'=>Yii::app()->user->checkAccess('Usager:create')),
);
?>

<h1><?php echo $model->prenom. " ".$model->nom; ?></h1>

<?php 
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'cssFile' => Yii::app()->baseUrl .'/css/main.css',
	'attributes'=>array(
		'matricule',
		'pseudo',
		'courriel',
		'adresseCivique',
		'ville',
		'telephone1',
		'telephone2',
		'telephone3',
		array('name'=>'tbl_grade_id','value'=>CHtml::encode($model->grade->nom)),
		array(
				'label'=>'Postes',
				'type'=>'raw',
				'value'=>$model->getPostes(),
		),
	),
));
echo '<br/>';
$ordre = 'odd';
foreach($Casernes as $caserne){
	echo '<div class="span-9">';
	if($ordre=='odd'){
		$ordre = 'even';
	}else{
		$ordre = 'odd';
	}
	echo "<h2>".$caserne['nom']."</h2>";
	echo '<table class="detail-view">';
	echo '<tr class="'.$ordre.'">';
	if(isset($caserne['Equipe'])){
		echo '<th>Équipes : </th><td></td>';
		echo '</tr>';
		foreach($caserne['Equipe'] as $equipe){
			if($ordre=='odd'){
				$ordre = 'even';
			}else{
				$ordre = 'odd';
			}
			echo '<tr class="'.$ordre.'">';
			echo '<td></td><td>'.$equipe.'</td>';
			echo '</tr>';
		}
	}
	if(isset($caserne['Groupe'])){
		if($ordre=='odd'){
			$ordre = 'even';
		}else{
			$ordre = 'odd';
		}
		echo '<tr class="'.$ordre.'">';
		echo '<th>Équipes spécialisées : </th><td></td>';
		echo '</tr>';
		foreach($caserne['Groupe'] as $groupe){
			if($ordre=='odd'){
				$ordre = 'even';
			}else{
				$ordre = 'odd';
			}
			echo '<tr class="'.$ordre.'">';
			echo '<td></td><td>'.$groupe.'</td>';
			echo '</tr>';
		}
	}
	echo '</table>';
	echo '</div>';
}
?>
