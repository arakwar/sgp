<div class="view span-17">
	<!-- Si c'est une formation -->
	<?php if ($data->tbl_formation_id != '0'): ?>
	<h4><?php echo CHtml::label('Formation',''); ?></h4>
	<?php endif;?>
	<b><?php echo CHtml::encode($data->getAttributeLabel('nom')); ?>:</b>
	<?php echo CHtml::encode($data->nom); ?>
	<br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('lieu')); ?>:</b>
	<?php echo CHtml::encode($data->lieu); ?>
	<br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('dateDebut')); ?>:</b>
	<?php echo CHtml::encode($data->dateDebut); ?>
	<br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('dateFin')); ?>:</b>
	<?php echo CHtml::encode($data->dateFin); ?>
	
	<!-- Si c'est une formation -->
	<?php if ($data->tbl_formation_id != '0'): ?>
	<br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('instituteur')); ?>:</b>
	<?php echo CHtml::encode($data->Instituteur->getPrenomNom()); ?>	
	<br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('moniteur')); ?>:</b>
	<?php echo CHtml::encode($data->Moniteur->getPrenomNom()); ?>
	<?php endif; ?>	
	<br /><br />

	<?php if(Yii::app()->user->checkAccess('Evenement:create')):?>
	<b><?php echo CHtml::link('Modifier', array('update', 'id'=>$data->id)); ?></b>
	<b><?php echo CHtml::link('Supprimer',array('delete','id'=>$data->id),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer cet item?')"));?></b>
		<?php if(Yii::app()->params['moduleFormation'] && $data->tbl_formation_id != '0'):?>
	<b><?php echo CHtml::link('Évaluer', array('formation/evaluation', 'id'=>$data->id)); ?></b>
	
	<?php	
		endif;
	endif;
	?>
</div>