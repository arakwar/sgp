<div class="document span-9" style="margin:10px;min-height:165px;">
		<div class="view">
		
			<b><?php echo CHtml::encode($data->getAttributeLabel('tbl_document_id')); ?>:</b>
			<?php 
				echo CHtml::encode($data->tblDocuments->nom);
			?>
			<br />
		
			<b><?php echo CHtml::encode($data->getAttributeLabel('tbl_usager_id')); ?>:</b>
			<?php echo CHtml::encode($data->tblUsagers->getMatPrenomNom()); ?>
			<br />
		
			<b><?php echo (($data->tblDocuments->url!==NULL)?'Visionné le':'Téléchargé le'); ?>:</b>
			<?php echo CHtml::encode($data->date); ?>
			<br />			
		</div>
</div>