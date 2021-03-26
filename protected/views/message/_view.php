<div class="view" style="cursor:pointer;">
	<div style="display:inline-block; min-width:150px"><b><?php echo CHtml::encode($data->getAttributeLabel('objet')); ?>:</b>
	<?php echo CHtml::encode($data->objet); ?>
	</div>
	<div style="display:inline-block; min-width:100px">
	<b><?php echo CHtml::encode($data->getAttributeLabel('dateEnvoi')); ?>:</b>
	<?php echo CHtml::encode($data->dateEnvoi); ?>
	</div>
	<div style="display:inline-block; min-width:100px">
	<b><?php echo CHtml::encode($data->getAttributeLabel('auteur')); ?>:</b>
	<?php echo CHtml::encode($data->auteur0->prenom." ".$data->auteur0->nom); ?>
	</div>
	<br/>
	<span class="message" style="display:none;">
	<b><?php echo CHtml::encode($data->getAttributeLabel('message')); ?>:</b>
	<?php echo CHtml::encode($data->message); ?>
	<br />
	<b>Destinataires :</b><br/>
	<?php 
		foreach($data->tblUsagers as $usager){
			echo ' '.$usager->prenomnom.'<br/>';
		}
	?>
	</span>
</div>