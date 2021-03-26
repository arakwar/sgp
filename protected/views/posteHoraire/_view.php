<div class="view span-8" style="margin:20px">
	<b><?php echo CHtml::encode($data->getAttributeLabel('heureDebut')); ?>:</b>
	<?php echo CHtml::encode($data->heureDebut); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('heureFin')); ?>:</b>
	<?php echo CHtml::encode($data->heureFin); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('tbl_quart_id')); ?>:</b>
	<?php echo CHtml::encode($data->Quart->nom); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('tbl_poste_id')); ?>:</b>
	<?php echo CHtml::encode($data->poste->nom); ?>
	<br /><br />	
			
	<b><?php echo CHtml::link('Modifier', array('update', 'id'=>$data->id)); ?></b>
	<b><?php echo CHtml::link('Supprimer',array('delete','id'=>$data->id),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer cet item?')"));?></b>


</div>