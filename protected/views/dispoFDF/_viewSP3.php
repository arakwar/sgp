<?php
if(!is_array($tblPompierGroupe)) throw new CHttpException(500,'Erreur #1.');
if($Ajax=='first'){
	echo '
	<ul class="ligne">
	<li '.((isset($grandEcran))?'style="width:150px;"':'').' class="dateFDF">Jour</li>
	<li '.((isset($grandEcran))?'style="width:150px;"':'').' class="pompierSP">Quart</li>';
	$nbrEquipe = 0;
	foreach($tblEquipeSP as $value){
		$nbrEquipe ++;
		echo '<li '.((isset($grandEcran))?'style="width:150px;"':'').' class="pompierSP">'.((isset($grandEcran))?$value['nomL']:$value['nom']).'</li>';
	}
	echo '</ul>';
}
$i=0;
$parametres = Parametres::model()->findByPk(1);
foreach($tblPompierGroupe as $timestamp=>$quarts){
	if($i<=2){
		$dateJour = new DateTime('@'.$timestamp);
		$dateJour->setTimezone(new DateTimeZone($parametres->timezone));
		echo '<ul class="ligne">';
		echo '<li class="jour" date="'.$timestamp.'" style="width:'.(($nbrEquipe+1)*(59+22)+88).'px">';
		echo '<div class="dateFDF" style="height:'.($Nbrquarts*30).'px;line-height:'.($Nbrquarts*30).'px">'.$jourSemaine[$dateJour->format("w")].' '.$dateJour->format("d").'</div>';
		if(!is_array($quarts)) throw new CHttpException(500,'Erreur #2.');
		foreach($quarts as $nom=>$equipes){
			echo "<ul>";
			echo '<li class="pompierSP">'.$equipes['nom'];
			unset($equipes['nom']);
			foreach($equipes as $idEquipe => $equipe){
				unset($equipe['nomEquipe']);
				$nbrPompier = $equipe['nombre']; //0
				/*foreach($equipe as $pompier){
					//if($value['id']==$valueSP['tbl_groupe_id']){
						$nbrPompier++;
					//}
				}*/
				echo '<li class="pompierSP listePompierSP" date="'.$timestamp.'" quart="'.$nom.'" groupe="'.$idEquipe.'">'.$nbrPompier."</li>";
			}	
			echo "</li>";
			echo "</ul>";
		}
		echo "</li>";
		echo "</ul>";
		unset($dateJour);$i++;
	}else{
		break;
	}
}
?>