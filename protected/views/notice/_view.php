<div class="view span-8" style="margin:10px;">

	<b><?php echo CHtml::encode($data->message); ?></b>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('dateDebut')); ?>:</b>
	<?php echo CHtml::encode($data->dateDebut); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('dateFin')); ?>:</b>
	<?php echo CHtml::encode($data->dateFin); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('tbl_usager_id')); ?>:</b>
	<?php echo CHtml::encode($data->tblUsager->prenomnom); ?>
	<br /><br />
			
	<b><?php echo CHtml::link('Modifier', array('update', 'id'=>$data->id)); ?></b>	
	<b><?php echo CHtml::link('Supprimer',array('delete','id'=>$data->id),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer cet item?')"));?></b>

</div>