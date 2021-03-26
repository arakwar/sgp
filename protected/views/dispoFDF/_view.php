<?php

if(!is_array($tblPompier)) throw new CHttpException(500,'Erreur #1.');
	$parametres = Parametres::model()->findByPk(1);
//if(isset($max)) echo '0';
//print_r($tblPompier);

	$icaserne = 0;
	foreach($tblPompier as $caserne){
		if($parametres->affichage_fdf_equipe==1){
			$timestamps = array_keys($caserne);
			$idquarts = array_keys($caserne[$timestamps[0]]);
			$idequipes = array_keys($caserne[$timestamps[0]][$idquarts[0]]);
			$nbrEquipes = count($idequipes);
			$nomEquipes = array();
			for($i = 1;$i<$nbrEquipes;$i++){
				$nomEquipes[] = $caserne[$timestamps[0]][$idquarts[0]][$idequipes[$i]]['nomEquipe'];
			}
		}
		//echo '<pre>';print_r($caserne[$timestamps[0]][$idquarts[0]]);echo '</pre>';
		if(!isset($ajax)){
			echo '<table class="fdf cas'.$icaserne.'"><tbody>';
			if($parametres->affichage_fdf == 1){echo '<tr class="enTete"><th colspan="4">'.$caserneNom[$icaserne].'</th></tr>';}
			echo '<tr class="entete"><th class="dateFDF">Date</th><th class="pompier enTete">Quart</th><th class="pompier enTete">'.(($parametres->affichage_fdf_equipe==0)?'Garde':$nomEquipes[0]).'</th>';
			for($i=2;$i<=$nbrEquipe[$icaserne];$i++){
				echo '<th class="pompier enTete">'.(($parametres->affichage_fdf_equipe==0)?'Equipe '.$i:$nomEquipes[($i-1)]).'</th>';
			}
			echo '<th class="pompier enTete">Total</th></tr>';
		}
		foreach($caserne as $timestamp=>$quarts){
			$dateJour = new DateTime('@'.$timestamp);
			$dateJour->setTimezone(new DateTimeZone('America/Montreal'));
			//ul 2
			echo '<tr class="ligne cas'.$icaserne.'">';
			//li 1
			echo '<td rowspan="'.$Nbrquarts.'" class="jour" date="'.$timestamp.'">';
			echo '<div class="dateFDF" style="height:'.(($Nbrquarts*30)-2).'px;line-height:'.(($Nbrquarts*30)-2).'px">'.
				$jourSemaine[$dateJour->format("w")].' '.$dateJour->format("d").'</div></td>';
			$testMinimum = $tblMinimum[$caserneId[$icaserne]][$dateJour->format("w")]['minimum'];
			$dateRequete = $dateJour->format('Y-m-d');
			$criteria = new CDbCriteria;
			$criteria->condition = 'dateDebut <= "'.$dateRequete.'" AND dateFin >= "'.$dateRequete.'"';
			$criteria->order = 'minimum ASC';
			$criteria->limit = 1;
			$exception = MinimumException::model()->find($criteria);
			if(count($exception) == 1){
				$testMinimum = $exception->minimum;
			}
			$premierQuart = 0;	
			foreach($quarts as $nom=>$equipes){
				if($premierQuart>=1){
					echo '<tr class="ligne">';
				}
				$premierQuart++;
				$totalPompier = 0;
				$totalPompierMinimum = 0;
				echo '<td class="pompier" style="background-color:#'.$tblGarde[$icaserne][(floor($timestamp/86400)%$garde->nbr_jour_periode).$nom].
						'; background-image:url(images/degrade.png);">'.$equipes['nom'].'</td>';
				unset($equipes['nom']);
				foreach($equipes as $numeroEquipe=>$listePompier){
					unset($listePompier['nomEquipe']);
					$nbrPompier = $listePompier['nombre'];//count($listePompier);
					$totalPompier += $nbrPompier;
					if($parametres->fdf_minimum_type==0){
						$totalPompierMinimum += $nbrPompier;
					}elseif($parametres->fdf_minimum_type==1){
						$totalPompierMinimum = $nbrPompier;
					}
					echo '<td class="pompier listePompier '.($totalPompierMinimum<$testMinimum?'fdf-error':'').
						'" date="'.$timestamp.'" quart="'.$nom.'" equipe="'.$numeroEquipe.
						'" style="background-color:#'.($totalPompierMinimum<$testMinimum?'F00':'0F0').
						'; background-image:url(images/degrade.png)">'.$nbrPompier."</td>";
				}
				echo '<td class="pompier" style="background-color:#0F0; background-image:url(images/degrade.png)">'.$totalPompier.'</td>';
				echo '</tr>';
			}
			unset($dateJour);
			//fin li 1 ul 2
		}
		if(!isset($ajax)){echo '</tbody></table>';}
		$icaserne++;
	}
?>