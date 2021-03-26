<div class="view span-5" style="margin:10px">
	<b><?php echo CHtml::encode($data->getAttributeLabel('nomL')); ?>:</b>
	<?php echo CHtml::encode($data->nomL); ?>
	<br /><br />

	<b><?php echo CHtml::link('Modifier', array('update', 'id'=>$data->id)); ?></b>
	<b><?php echo CHtml::link('Supprimer',array('delete','id'=>$data->id),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer cet item?')"));?></b>
</div>