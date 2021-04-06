<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/posteHoraire.css');
$this->breadcrumbs=array(
	'Poste Horaires',
);

Yii::app()->clientScript->registerScript('updateLstPO','
	$("#lstCaserne").on("change", function(event){
		id = $("select#lstCaserne").val();
		window.location = "index.php?r=posteHoraire/index&caserne="+id;
	});
');

$this->menu=array(
	array('label'=>'Créer un poste/horaire', 'url'=>array('create')),
);
?>

<div class="equipeMini">
	<div class="premier"></div><div class="view">
		<?php echo CHtml::dropDownList('lstCaserne', $caserne, $dataCaserne); ?>	
	</div>
</div>

<table class="tablePH">
<?php 
	$quart = 0;$poste = 0;$ch = true;
	foreach($posteHoraires as $ph){		
		echo '<tr class="ligne">';
		if($ph->tbl_quart_id != $quart){
			$ch = true;
			$quart = $ph->tbl_quart_id;
			$r = 0;
			foreach($posteHoraires as $ph2){
				if($ph2->tbl_quart_id == $quart){
					$r++;
				}
			}
			echo '<td rowspan="'.$r.'">';
			foreach($quarts as $q){
				if($q->id == $quart){
					echo '<b>'.$q->nom.'</b>';
					break;
				}
			}
			echo '</td>';
		}else{$ch = false;}
		if($ph->tbl_poste_id != $poste || $ch){
			$poste = $ph->tbl_poste_id;
			$r = 0;
			foreach($posteHoraires as $ph2){
				if($ph2->tbl_poste_id == $poste AND $ph2->tbl_quart_id == $quart){
					$r++;
				}
			}
			echo '<td rowspan="'.$r.'"';
			if(Yii::app()->params['poste_horaire_couleur'] === 1)
				echo ' style="background-color:#'.$ph->couleur.';" ';
			echo '>';
			foreach($postes as $p){
				if($p->id == $poste){
					echo '<b>'.$p->nom.'</b>';
					break;
				}
			}	
			echo '</td>';		
		}
		echo '<td><b>Heure debut :</b> '.$ph->heureDebut.' <br/> <b>Heure fin :</b> '.$ph->heureFin.'<br/>';
		echo '<br/><b>'.CHtml::link('Modifier', array('update', 'id'=>$ph->id)).'</b> <b>'
				.CHtml::link('Supprimer',array('delete','id'=>$ph->id),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer cet item?')")).'</b></td>';
		echo '</tr>';
	}
?>
</table>
