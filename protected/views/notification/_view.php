<?php
/* @var $this NotificationController */
/* @var $data Notification */
?>

<div class="view <?php echo (!isset($data->dateVisionnement)? 'nouveau': '') ?>">
	<b><?php echo CHtml::encode($data->getAttributeLabel('dateCreation')); ?>:</b>
	<?php echo CHtml::encode($data->dateCreation); ?>
	<br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('message')); ?>:</b>
	<?php echo $data->getMessage(); ?>
	<br />
	<?php echo CHtml::link('Supprimer',array('delete','id'=>$data->id),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer la notification')"));?>
	<br />
</div>