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
if(Yii::app()->user->checkAccess('Horaire:create')):
?>
<p>Nouveau pompier : <input type="text" id="txtModifPompier" poste="<?php echo $caseHoraire->tbl_poste_horaire_id;?>" date="<?php echo $caseHoraire->date;?>" /></p>
<?php endif;?>
Historique des changements :
<table>
<tr><th>Pompier en place</th><th>Date de modif</th><th>Responsable</th></tr>
<?php
foreach($listeModif as $modif){
	echo '<tr><td>'.$modif->Usager->prenomnom.'</td><td>'.$modif->dateModif.'</td><td>'.$modif->ModifUsager->prenomnom.'</td></tr>';
}
if(isset($caseHoraire->tbl_usager_id)){
	echo '<tr><td>'.$caseHoraire->Usager->prenomnom.'</td><td colspan="3">Premier pompier</td></tr>';
}else{
	echo '<tr><td colspan="4">Aucun pompier en liste</td></tr>';
}
?>
</table>
<hr/>
Pompiers disponible :
<table id="tblDispoModif">
<tr><th>Matricule</th><th>Nom</th><th>Téléphone</th><th></th></tr>
<?php 
foreach($listeDispo as $dispo){
	if(($dispo->dHeureDebut !== '00:00:00' && $dispo->dHeureFin !== '00:00:00') && ($dispo->dHeureDebut != $qHeureDebut && $dispo->dHeureFin != $qHeureFin)){
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
	if(($dispo->dHeureDebut !== '00:00:00' && $dispo->dHeureFin !== '00:00:00') && ($dispo->dHeureDebut != $qHeureDebut && $dispo->dHeureFin != $qHeureFin)){
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
