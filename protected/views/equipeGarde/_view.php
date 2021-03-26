<div class="view span-7" style="margin:10px">

	<b><?php echo CHtml::encode($data->getAttributeLabel('nom')); ?>:</b>
	<?php echo CHtml::encode($data->nom); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('nbr_jour_affiche')); ?>:</b>
	<?php echo CHtml::encode($data->nbr_jour_affiche); ?>
	<br />	
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('nbr_jour_periode')); ?>:</b>
	<?php echo CHtml::encode($data->nbr_jour_periode); ?>
	<br />
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('nbr_jour_depot')); ?>:</b>
	<?php echo CHtml::encode($data->nbr_jour_depot); ?>
	<br />
	
	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('nbr_jour_ge')); ?>:</b>
	<?php echo CHtml::encode($data->nbr_jour_ge); ?>
	<br />
	*/?>
	
	<b><?php echo CHtml::encode($data->getAttributeLabel('date_debut')); ?>:</b>
	<?php echo CHtml::encode($data->date_debut); ?>
	<br /<br />	
	
	<b><?php echo CHtml::link('Gérer la garde', array('index', 'id'=>$data->id)); ?></b>		
	<b><?php echo CHtml::link('Modifier', array('update', 'id'=>$data->id)); ?></b>
	<b><?php echo CHtml::link('Supprimer',array('delete','id'=>$data->id),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer cet item?')"));?></b>
	
</div>