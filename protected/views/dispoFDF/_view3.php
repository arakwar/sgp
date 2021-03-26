<?php
if(!is_array($tblPompier)) throw new CHttpException(500,'Erreur #1.');
if(isset($max)) echo '0';
	//ul 1
	echo '<ul class="ligne span-14">';
	echo '<li class="dateFDF">Date</li><li class="pompier enTete">Quart</li><li class="pompier enTete">Garde</li>';
	for($i=2;$i<=$nbrEquipe;$i++){
		echo '<li class="pompier enTete">Equipe '.$i.'</li>';
	}
	echo '<li class="pompier enTete">Total</li>';
	//fin ul 1
	echo '</ul>';
	$parametres = Parametres::model()->findByPk(1);
	foreach($tblPompier as $timestamp=>$quarts){
		$dateJour = new DateTime('@'.$timestamp);
		$dateJour->setTimezone(new DateTimeZone($parametres->timezone));
		//ul 2
		echo '<ul class="ligne span-14">';
		//li 1
		echo '<li class="jour" style="width:550px" date="'.$timestamp.'">';
		echo '<div class="dateFDF" style="height:'.($Nbrquarts*30).'px;line-height:'.($Nbrquarts*30).'px">'.$jourSemaine[$dateJour->format("w")].' '.$dateJour->format("d").'</div>';
		$testMinimum = $tblMinimum[$dateJour->format("w")];
		$dateRequete = $dateJour->format('Y-m-d');
		$criteria = new CDbCriteria;
		$criteria->condition = 'dateDebut <= "'.$dateRequete.'" AND dateFin >= "'.$dateRequete.'"';
		$criteria->order = 'minimum ASC';
		$criteria->limit = 1;
		$exception = MinimumException::model()->find($criteria);
		if(count($exception) == 1){
			$testMinimum = $exception->minimum;
		}	
		foreach($quarts as $nom=>$equipes){
			$totalPompier = 0;
			//ul 3
			echo '<ul>';
			echo '<li class="pompier" style="background-color:#'.$tblGarde[(floor($timestamp/86400)%$parametres->nbJourPeriode).$nom].
					'; background-image:url(images/degrade.png);">'.$equipes['nom'].'</li>';
			unset($equipes['nom']);
			foreach($equipes as $numeroEquipe=>$listePompier){
				//li 4
				echo '<li class="equipe">';
				unset($listePompier['nomEquipe']);
				//ul 4
				echo "<ul>";
				$nbrPompier = $listePompier['nombre'];//count($listePompier);
				$totalPompier += $nbrPompier;
				echo '<li class="pompier listePompier '.($totalPompier<$testMinimum?'fdf-error':'').'" date="'.$timestamp.'" quart="'.$nom.'" equipe="'.$numeroEquipe.'" style="background-color:#'.($totalPompier<$testMinimum?'F00':'0F0').'; background-image:url(images/degrade.png)">'.$nbrPompier."</li>";
				//fin ul 4
				echo "</ul>";
				//fin li 4
				echo "</li>";
			}
			//li 5
			echo '<li class="equipe">';
			//ul 5
			echo "<ul>";
			echo '<li class="pompier" style="background-color:#0F0; background-image:url(images/degrade.png)">'.$totalPompier.'</li>';
			//fin ul 5
			echo "</ul>";
			//fin li 5
			echo "</li>";
			//fin ul 3
			echo '</ul>';

		}
		unset($dateJour);
		//fin li 1 ul 2
		echo '</li></ul>';
	}
?>