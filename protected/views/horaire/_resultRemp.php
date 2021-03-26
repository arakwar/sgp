<?php
	if($dataRemp !==NULL){
		//echo '<pre>';print_r($dataRemp);
		$row = $dataRemp->read();
		echo '<div class="items">';
		if($row!==FALSE){
			do{
				$id_horaire = $row['ID_Horaire'];
				echo '<div class="view span-17">';
				$horaire = true;
				do{
					if($horaire){
						$horaire = false;
						echo '<div class="span-'.(($row['Absence']!==NULL)?'8':'17 last').'">';
						echo '<h3>Info case horaire</h3>';					
						echo CHtml::label('Date : ','');
						echo $row['DateHoraire'];
						echo CHtml::label('Quart : ','');
						echo $row['Quart'];
						echo CHtml::label('Usager : ','');
						echo $row['UsagerHoraire'];
						echo '</div>';
						if($row['Absence']!==NULL){
							echo '<div class="span-9 last" style="height:136px;">';
							echo '<h3>Avis d\'absence</h3>';					
							echo CHtml::label('Heures demandées : ','');
							echo $row['HeureDemandee'];						
							echo CHtml::label('Raison : ','');
							echo $row['Raison'];
							echo '</div>';	
						}
					}
					if($row['Type']==2){
						echo '<div class="span-17 last" style="margin-top:20px;">';
						echo CHtml::label('Supression par : ','');
						echo $row['UsagerModif'];
						echo CHtml::label('Date : ','');
						echo $row['DateModif'];
						echo '</div>';					
					}else{
						echo '<div class="span-8" style="margin-top:20px;">';
						echo CHtml::label('Remplacé par : ','');
						echo $row['UsagerRemp'];
						echo CHtml::label('Période : ','');
						echo $row['heureDebutRemp'].' à '.$row['heureFinRemp'];	
						echo '</div>';
						echo '<div class="span-9 last" style="margin-top:20px;">';
						echo CHtml::label('Modification par : ','');
						echo $row['UsagerModif'];
						echo CHtml::label('Date : ','');
						echo $row['DateModif'];
						echo '</div>';
					}
					$row = $dataRemp->read();
				}while($id_horaire==$row['ID_Horaire']);
				echo '</div>';	
			}while($row!==false);
		}else{
			echo 'Aucun résultats';
		}
		
		echo '</div>';
	}
?>