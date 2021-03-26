<div class="view span-7" style="margin:10px">

	<b><?php echo CHtml::encode($data->getAttributeLabel('nom')); ?>:</b>
	<?php echo CHtml::encode($data->nom); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('abrev')); ?>:</b>
	<?php echo CHtml::encode($data->abrev); ?>
	<br /><br />	
			
	<b><?php echo CHtml::link('Modifier', array('typeModif', 'id'=>$data->id)); ?></b>
	<b><?php echo CHtml::link('Supprimer',array('typeSupprimer','id'=>$data->id),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer cet item?')"));?></b>
	
</div>