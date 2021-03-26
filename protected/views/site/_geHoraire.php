<?php
/**
 * Les variables label permettent de conserner l'étiquette de l'élément présentement en
 * traitement car rendu à l'affichage ou la mise en buffer le curseur SQL a déjà avancé sur le prochain élément.
 * Les variables buffer permettent de simplifier la mise en place des rowspan pour les quarts.
 * À la fin le substr_replace permet d'injecter le code du td du Quart au début de son buffer.
 * 		Raison : il était plus simple de directement ouvrir et fermer chaque ligne de poste que de laisser que la première ouverte...
 */

$row = $curseurHoraire->read();
$premiereLigne = true;
$i=1;

echo '<table class="geHoraireGE SSCal_table_h">';
do{
	$quart = $row['idQuart'];
	$labelQuart = $row['nomQuart'];
	$bufferQuart = "";	$nbQuart = 0;
	do{
		$poste = $row['idPoste'];
		$labelPoste = $row['diminutifPoste'];
		$bufferPoste = "";	$bufferJourHeader = "";
		do{
			$jour=$row['Jour'];
			$bufferPosteHoraire = "";
			do{
				$bufferPosteHoraire .= '<div class="case" style="background-color:#'.
						$row['couleur_garde'].'">'.($row['matricule_modification']===NULL?$row['Matricule_horaire']:$row['matricule_modification']).'</div>';
				$row = $curseurHoraire->read();
			}while($jour==$row['Jour']);
			if($premiereLigne)	$bufferJourHeader .= '<td><div class="case">'.$jourSemaine[date('w',strtotime($jour))].' '.
					date('d',strtotime($jour)).'</div></td>';
			$bufferPoste .= '<td><div class="case">'.$bufferPosteHoraire.'</div></td>';
		}while($poste == $row['idPoste']);
		if($premiereLigne){
			echo '<tr class="poste"><td><div class="case"></div></td><td><div class="case"></div></td>'.$bufferJourHeader.'</tr>';
			$premiereLigne = false;
		}
		$bufferQuart .= '<tr class="poste"><td><div class="case">'.$labelPoste.'</div></td>'.$bufferPoste.'</tr>';
		$nbQuart++;
	}while($quart == $row['idQuart']);
	echo substr_replace($bufferQuart,'<td rowspan="'.$nbQuart.'"><div style="height:'.($nbQuart*30+$nbQuart-1).'px;">'.$labelQuart.'</div></td>',18,0);
	if($i < $nbrQuarts && $parametres->grandEcran_style==1){
		echo '<tr><td colspan="9" style="border:1px solid #000;">&nbsp;</td></tr>';
	}
	$i++;	
}while($row!==false);
echo '</table>';
if($parametres->grandEcran_nbr_periode_horaire == 2){
	echo '<table class="geHoraireGE SSCal_table_h deuxieme">';
	
	$row = $curseurHoraire2->read();
	$premiereLigne = true;
	$i=1;
	do{
		$quart = $row['idQuart'];
		$labelQuart = $row['nomQuart'];
		$bufferQuart = "";	$nbQuart = 0;
		do{
			$poste = $row['idPoste'];
			$labelPoste = $row['diminutifPoste'];
			$bufferPoste = "";	$bufferJourHeader = "";
			do{
				$jour=$row['Jour'];
				$bufferPosteHoraire = "";
				do{
					$bufferPosteHoraire .= '<div class="case" style="background-color:#'.
							$row['couleur_garde'].'">'.($row['matricule_modification']===NULL?$row['Matricule_horaire']:$row['matricule_modification']).'</div>';
					$row = $curseurHoraire2->read();
				}while($jour==$row['Jour']);
				if($premiereLigne)	$bufferJourHeader .= '<td><div class="case">'.$jourSemaine[date('w',strtotime($jour))].' '.
						date('d',strtotime($jour)).'</div></td>';
				$bufferPoste .= '<td><div class="case">'.$bufferPosteHoraire.'</div></td>';
			}while($poste == $row['idPoste']);
			if($premiereLigne){
				echo '<tr class="poste"><td><div class="case"></div></td><td><div class="case"></div></td>'.$bufferJourHeader.'</tr>';
				$premiereLigne = false;
			}
			$bufferQuart .= '<tr class="poste"><td><div class="case">'.$labelPoste.'</div></td>'.$bufferPoste.'</tr>';
			$nbQuart++;
		}while($quart == $row['idQuart']);
		echo substr_replace($bufferQuart,'<td rowspan="'.$nbQuart.'"><div style="height:'.($nbQuart*30+$nbQuart-1).'px;">'.$labelQuart.'</div></td>',18,0);
		if($i < $nbrQuarts && $parametres->grandEcran_style==1){
			echo '<tr><td colspan="9" style="border:1px solid #000;">&nbsp;</td></tr>';
		}
		$i++;
	}while($row!==false);
	echo '</table>';		
}
?>