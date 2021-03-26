<?php
if(!is_array($tblPompierGroupe)) throw new CHttpException(500,'Erreur #1.');

$i=0;//limite l'affichage à trois jours...
$parametres = Parametres::model()->findByPk(1);
foreach($tblPompierGroupe as $caserne){
	foreach($caserne as $timestamp=>$quarts){
		if($i<=2){
			$dateJour = new DateTime('@'.$timestamp);
			$dateJour->setTimezone(new DateTimeZone($parametres->timezone));
			echo '<tr class="ligne">';
			echo '<td rowspan="'.$Nbrquarts.'" class="jour" date="'.$timestamp.'">';
			echo '<div class="dateFDF" style="height:'.($Nbrquarts*30).'px;line-height:'.($Nbrquarts*30).'px">'.
				$jourSemaine[$dateJour->format("w")].' '.$dateJour->format("d").'</div></td>';
			if(!is_array($quarts)) throw new CHttpException(500,'Erreur #2.');
			$premierQuart = 0;
			foreach($quarts as $nom=>$equipes){
				if($premierQuart>0){
					echo '<tr class="ligne">';
				}
				$premierQuart++;
				echo '<td class="pompierSP">'.$equipes['nom'].'</td>';
				unset($equipes['nom']);
				foreach($equipes as $idEquipe => $equipe){
					unset($equipe['nomEquipe']);
					$nbrPompier = $equipe['nombre']; //0
					if(isset($parametres->garde_sur_total_groupe) && $parametres->garde_sur_total_groupe && $equipe['garde_sur_total_groupe']){
						echo '<td class="pompierSP listePompierSP" date="'.$timestamp.'" quart="'.$nom.'" groupe="'.$idEquipe.'">'.
							$equipe['nombre_garde'].' / '.$equipe['nombre']."</td>";
					}else{
					  	echo '<td class="pompierSP listePompierSP" date="'.$timestamp.'" quart="'.$nom.'" groupe="'.$idEquipe.'">'.
							$equipe['nombre']."</td>";
					}
				}	
				echo "</tr>";
			}
	
			unset($dateJour);$i++;
		}else{
			break;
		}
	}
}
?>