<div class="view span-7" style="margin:10px">

	<b><?php echo CHtml::encode($data->getAttributeLabel('nom')); ?>:</b>
	<?php echo CHtml::encode($data->nom); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('diminutif')); ?>:</b>
	<?php echo CHtml::encode($data->diminutif); ?>
	<br /><br />	
			
	<b><?php echo CHtml::link('Modifier', array('update', 'id'=>$data->id)); ?></b>
	<b><?php echo CHtml::link('Supprimer',array('delete','id'=>$data->id),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer cet item?')"));?></b>
	
</div>