<div class="view" style="float:left;width:345px;margin-right:10px;margin-left:10px">
	<b><?php echo CHtml::encode($data->getAttributeLabel('date')); ?>:</b>
	<?php 
		echo CHtml::encode($data->date);
	?>
	<br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('tbl_quart_id')); ?>:</b>
	<?php 
		echo CHtml::encode($data->tblQuart->nom);
	?>
	<br />
	<b>De: </b><?php echo CHtml::encode($data->heureDebut); ?>
	<b> - À: </b><?php echo CHtml::encode($data->heureFin); ?>
	<br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('tbl_usager_id')); ?>:</b>
	<?php 
		if($data->tblUsager===NULL){echo "Donnée non-disponible";}else{echo CHtml::encode($data->tblUsager->getMatPrenomNom());}
	?>
	<br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('dateDecoche')); ?>:</b>
	<?php 
		if($data->dateDecoche===NULL){echo 'Donnée non-disponible';}else{echo CHtml::encode($data->dateDecoche);}
	?>
	<br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('tbl_usager_action')); ?>:</b>
	<?php 
		if($data->tblUsagerAction===NULL){echo "Donnée non-disponible";}else{echo CHtml::encode($data->tblUsagerAction->getMatPrenomNom());}
	?>
	<br />
</div>