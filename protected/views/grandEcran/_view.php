<?php
/* @var $this GrandEcranController */
/* @var $data GrandEcran */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('Titre')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->titre), array('view', 'id'=>$data->id)); ?>
	<br />


</div>