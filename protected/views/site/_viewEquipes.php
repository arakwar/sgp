<div class="view" style="display:inline-block; padding:10px;">

	<b><?php echo CHtml::encode($data->getAttributeLabel('nom')); ?>:</b>
	<?php echo CHtml::encode($data->nom); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('couleur')); ?>:</b>
	<?php
		if($data->couleur!=''):?> 
		<div style="display:inline;width:60px;border:solid thin black; height:30px;background-color:#<?php echo $data->couleur;?>; background-image:url('images/degrade.png');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
		<?php endif;?>
	<br /><br />
	<?php if(!isset($dispo)) {?>
	<b><?php echo CHtml::link('Plus de détails', array('view', 'id'=>$data->id)); ?></b>
	<b><?php echo CHtml::link('Modifier', array('update', 'id'=>$data->id)); ?></b>
	<b><?php echo CHtml::link('Supprimer',array('delete','id'=>$data->id),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer cet item?')"));?></b>
	<?php } //Fin du if?>

</div>