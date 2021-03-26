<div class="view">
	<div class="itemTop">
		<div class="numero">
		<?php echo CHtml::encode($data->matricule); ?>
		</div>
		<div class="diviseur100"></div>
		<div class="nom"><?php echo CHtml::encode($data->prenomnom); ?> : <?php
			foreach($data->tblPostes as $poste){
				echo $poste->diminutif." ";
			}
		?></div>
		<div class="tel">
		<?php 
			$nbSemaine = floor($parametres->nbJourPeriode/7);
			$titreSemaine = "";
			$heureSemaine = "";
			for($i=1; $i<=$nbSemaine; $i++){
				$titreSemaine .= "<td>".$i."</td>";
				$heureSemaine .= '<td><span class="afficheHeure" date="'.$i.'" usager="'.$data->matricule.'">0</span></td>';
			}
			$titreSemaine .= "<td>Total</td>";
			$heureSemaine .= '<td><span class="totalHeure" usager="'.$data->matricule.'" nbheures="0" >0</span></td>';
		?>
		<table class="heureUsager">
			<tr class="titre">
				<?php echo $titreSemaine;?>
			</tr>
			<tr class="heures">
				<?php echo $heureSemaine;?>
			</tr>
		</table>
		</div>
	</div>

	
</div>