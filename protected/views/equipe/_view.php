<?php 
	if(!isset($dispo)){
?>
<div class="view span-5" style="margin:10px;">
<?php 
	}else{
?>
<div class="view">
<?php }?>

	<b><?php echo CHtml::encode($data->getAttributeLabel('nom')); ?>:</b>
	<?php echo CHtml::encode($data->nom); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('couleur')); ?>:</b>
	<?php
		if($data->couleur!=''){
			echo '<div style="display:inline-block;border:solid thin black;background-color:#'.$data->couleur.';background-image:url(\'images/degrade.png\');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>';
		}?>
	<br />
	<?php if(!isset($dispo)) {?>
		<b><?php echo CHtml::encode($data->getAttributeLabel('caserne'));?>:</b>
		<?php echo CHtml::encode($data->tblCaserne->nom);?>
	<br /><br />
	<b><?php echo CHtml::link('Plus de détails', array('view', 'id'=>$data->id)); ?></b><br />
	<b><?php echo CHtml::link('Modifier', array('update', 'id'=>$data->id)); ?></b><br />
	<b><?php echo CHtml::link('Supprimer',array('delete','id'=>$data->id),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer cet item?')"));?></b>
	<?php } //Fin du if?>

</div>