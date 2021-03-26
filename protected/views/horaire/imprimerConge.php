<h2>Avis d'absence</h2>
<table>
	<tr>
		<td>	
			<strong>Demandeur : </strong>
			<?php 
				echo $model->tblUsager->getMatPrenomNom();
			?>
		</td>
		<td>
			<strong>Demandé le : </strong>
			<?php 
				echo $model->dateEmis;
			?>
		</td>
	</tr>
	<tr>
		<td>	
			<strong>Date du congé : </strong>
			<?php 
				echo $model->dateConge;
			?>
		</td>
		<td>
			<strong>Type : </strong>
			<?php 
				echo $model->tblType->nom;
			?>
		</td>
	</tr>
	<tr>
		<td colspan="2">	
			<strong>Heures : </strong>
			<?php 
				echo $model->heureDebut.' à '.$model->heureFin;
			?>
		</td>
	</tr>
	<?php if($model->note != '' && $model->note !== NULL):?>
	<tr>
		<td colspan="2">	
			<strong>Note : </strong>
			<?php 
				echo $model->note;
			?>
		</td>
	</tr>
	<?php endif;?>
</table>
<hr></hr>
<table>
	<tr>
		<td colspan="2">	
			<strong>Statut : </strong>
			<?php 
				$statut = array('1'=>'Nouveau', '2'=>'Accepté','3'=>'Refusé','4'=>'Fermé');
				echo $statut[$model->statut];
			?>
		</td>
	</tr>
	<?php if($model->chef_id !== NULL):?>
	<tr>
		<td>	
			<strong>Validé par : </strong>
			<?php 
				echo $model->tblChefs->getMatPrenomNom();
			?>
		</td>
		<td>
			<strong>Validé le : </strong>
			<?php 
				echo $model->dateRecu.' '.$model->heureRecu;
			?>
		</td>
	</tr>
	<?php 
		endif;
		if($model->raison != '' && $model->raison !== NULL):
	?>
	<tr>
		<td colspan="2">	
			<strong>Raison : </strong>
			<?php 
				echo $model->raison;
			?>
		</td>
	</tr>
	<tr>
		<td colspan="2">	
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="2">	
			&nbsp;
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:right;">
			<strong>Signature responsable : _______________________________________</strong>
		</td>
	</tr>
	<?php endif;?>
</table>