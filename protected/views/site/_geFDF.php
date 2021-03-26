<div class="SSCal_conteneur">
	<div class="SSCal_content">
		<?php
				$this->renderPartial('/dispoFDF/_view',array('tblPompier'=>$tblPompier,'jourSemaine'=>$jourSemaine,
				'parametres'=>$parametres,'tblGarde'=>$tblGarde, 'Nbrquarts'=>$Nbrquarts, 'tblMinimum'=>$tblMinimum, 'garde'=>$garde, 
				'nbrEquipe'=>$nbrEquipe, 'caserneNom'=>$caserneNom, 'caserneId'=>$caserneId));

		?>
	</div>
</div>
<!--FDF Groupe-->
<div class="SSCal_conteneur">
	<div class="SSCal_content">
	<?php
		$nbrEquipe = 0;
		$tableau = '';
		foreach($tblEquipeSP as $value){
			$nbrEquipe ++;
			$tableau .= '<th class="pompierSP">'.$value['nom'].'</th>';
		}
		if($nbrEquipe>0){
			echo '<div class="grilleFDF" id="grilleDataSP">';
			echo '<table id="tableauFDFGroupe"><tbody>';
			echo '
				<tr class="entete">
				<th class="dateFDF">Date</th>
				<th class="pompierSP">Quart</th>';
			echo $tableau.'</tr>';
			$this->renderPartial('/dispoFDF/_viewSP',array('Nbrquarts'=>$Nbrquarts,'tblPompierGroupe'=>$tblPompierGroupe, 'jourSemaine'=>$jourSemaine,'parametres'=>$parametres, 'tblEquipeSP'=>$tblEquipeSP, 'Ajax'=>'first'));
			echo '</tbody></table>';
			echo "</div>";
		}
	?>
	</div>
</div>