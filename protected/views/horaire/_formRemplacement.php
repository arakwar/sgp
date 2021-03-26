<?php 
Yii::app()->clientScript->registerScript('dispoPartielle','
$(".dispoPartielle").live("hover",function(e){
		var ceci = $(this);
		$(".dispoPart").empty();
		$(".dispoPart").append($(ceci).attr("dispo"));
		$(".dispoPart").css("visibility","visible");
		$(".dispoPart").offset({left: e.pageX, top: e.pageY});
	}),
	$(".dispoPartielle").live("mouseout",function(){
		$(".dispoPart").css("visibility","hidden");
	})
	;
');
?>
<p>Pompier actuel : <?php echo $usager;?></p>
<?php 
if($absence != 0){
	echo '<p>Heures demandées : '.$heureDebut.' - '.$heureFin.'</p>';
	echo '<p>Raison : '.$raison.'</p>';
}
?>
<p>Remplacé par (matricule) : <input type="text" id="txtModifPompier" poste="<?php echo $caseHoraire->tbl_poste_horaire_id;?>" date="<?php echo $caseHoraire->date;?>" value="<?php echo $usagerR; ?>"/></p>
<p>Période : <?php 
			$this->widget('system.ext.jui_timepicker.JTimePicker',array(
				'name'=>'txtHeureDebut',
				'htmlOptions'=>array(
					'value'=>$heureDebut,
					),				
			));?> 
			à 
			 <?php 
			$this->widget('system.ext.jui_timepicker.JTimePicker',array(
				'name'=>'txtHeureFin',
				'htmlOptions'=>array(
					'value'=>$heureFin,
					),				
			));?>
</p>
<input type="hidden" id="idR" value="<?php echo $id;?>"/><input type="hidden" id="type" value="<?php echo $type;?>"/>
Pompiers disponible :
<table id="tblDispoModif">
<tr><th>Matricule</th><th>Nom</th><th>Téléphone</th><th></th></tr>
<?php 
foreach($listeDispo as $dispo){
	if(($dispo->dHeureDebut != '00:00:00' && $dispo->dHeureFin != '00:00:00') && ($dispo->dHeureDebut != $qHeureDebut && $dispo->dHeureFin != $qHeureFin)){
		$dispoP = true;
		$dispoPartielle = '';
		foreach($dispoPart as $DP){
			if($DP->tbl_usager_id == $dispo->id){
				$dispoPartielle .= $DP->heureDebut.' - '.$DP->heureFin.'<br/>';
			}
		}
		$dispoPartielle = substr($dispoPartielle,0,strlen($dispoPartielle)-5);
	}else{
		$dispoP = false;
	}
	echo '<tr matricule="'.$dispo->matricule.'" class="ligne"><td>'.$dispo->matricule.'</td><td>'.$dispo->prenomnom.(($dispoP)?CHtml::image(Yii::app()->baseUrl.'/images/clock.png', 'Dispo',array('class'=>'dispoPartielle', 'dispo'=>$dispoPartielle)):'').'</td><td>'.$dispo->telephone1.'</td><td>'.$dispo->telephone2.'</td></tr>';
}
foreach($listeDispoNonPoste as $dispo){
	if(($dispo->dHeureDebut != '00:00:00' && $dispo->dHeureFin != '00:00:00') && ($dispo->dHeureDebut != $qHeureDebut && $dispo->dHeureFin != $qHeureFin)){
		$dispoP = true;
		$dispoPartielle = '';
		foreach($dispoPart as $DP){
			if($DP->tbl_usager_id == $dispo->id){
				$dispoPartielle .= $DP->heureDebut.' - '.$DP->heureFin.'<br/>';
			}
		}
		$dispoPartielle = substr($dispoPartielle,0,strlen($dispoPartielle)-5);
	}else{
		$dispoP = false;
	}
	echo '<tr matricule="'.$dispo->matricule.'" class="ligne" style="color:#999;"><td>'.$dispo->matricule.'</td><td>'.$dispo->prenomnom.(($dispoP)?CHtml::image(Yii::app()->baseUrl.'/images/clock.png', 'Dispo',array('class'=>'dispoPartielle', 'dispo'=>$dispoPartielle)):'').'</td><td>'.$dispo->telephone1.'</td><td>'.$dispo->telephone2.'</td></tr>';
}
?>
</table>
<?php 
if($absence!=0){
?>
Appels :
<table id="tblAppelAbsence">
<tr><th>Pompier</th><th>Réponse</th></tr>
<?php
foreach($listeAppels as $appel){
	foreach($listeUsager as $usager){
		if($usager->id == $appel->tbl_usager_id){
			break;
		}
	}
	echo '<tr>';
	echo '<td>'.$usager->matricule.'</td>';
	echo '<td>'.(($appel->reponse==0)?'Oui':'Non').'</td>';
	echo '</tr>';
}
echo '<tr>';
echo '<td>'.CHtml::textField('appel_matricule', '').'</td>';
echo '<td>'.CHtml::radioButtonList('appel_reponse', '0', array('0'=>'Oui', '1'=>'Non')).'</td>';
echo '</tr>';
?>
</table>
<?php 
	echo CHtml::ajaxButton('Sauvegarder l\'appel',array('horaire/congeAppel'), array(
													
													'type'=>'post',
													'data'=>"js:{id:".$absence.",matricule:$('#appel_matricule').val(),reponse:$('input[name=appel_reponse]:checked').val()}",
													'cache'=>true,
													'update'=>"#tblAppelAbsence"	
													), array('id'=>'ajoutAppel'));
}
?>