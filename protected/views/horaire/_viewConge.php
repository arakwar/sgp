<div class="view">
	<div class="span-9">
		<b><?php echo CHtml::encode($data->getAttributeLabel('tbl_usager_id')); ?>:</b>
		<?php echo CHtml::encode($data->tblUsager->getMatPrenomNom()); ?>
		<br />
		
		<b><?php echo CHtml::encode($data->getAttributeLabel('tbl_type_id')); ?>:</b>
		<?php echo CHtml::encode($data->tblType->abrev.' - '.$data->tblType->nom); ?>
		<br />
	
		<b><?php echo CHtml::encode($data->getAttributeLabel('dateConge')); ?>:</b>
		<?php echo CHtml::encode($data->dateConge); ?>
		<br />
		
		<b><?php echo CHtml::encode($data->getAttributeLabel('tbl_quart_id')); ?>:</b>
		<?php echo CHtml::encode($data->tblQuarts->nom); ?>
		<br />
		
		<b><?php echo CHtml::encode($data->getAttributeLabel('heureDebut')); ?>:</b>
		<?php echo CHtml::encode($data->heureDebut); ?>
		<b><?php echo CHtml::encode('- '.$data->getAttributeLabel('heureFin')); ?>:</b>
		<?php echo CHtml::encode($data->heureFin); ?>
		<br />
		
		<?php if($type == 'perso'):?>
			<b><?php  echo CHtml::encode($data->getAttributeLabel('statut'));?>:</b>
			<?php 
				$statut = array(1=>'Non-validé', 2=>'Accepté', 3=>'Refusé');
				
				echo $statut[$data->statut];
			?>
			<br />
		<?php endif;?>
		
		<b><?php echo CHtml::encode($data->getAttributeLabel('dateEmis')); ?>:</b>
		<?php echo CHtml::encode($data->dateEmis); ?>
		<br />
		<?php if($data->chef_id!==NULL):?>
			<b><?php echo CHtml::encode($data->getAttributeLabel('chef_id')); ?>:</b>
			<?php echo CHtml::encode($data->tblChefs->getMatPrenomNom()); ?>
			<br />
		<?php endif;?>
				
		<b><?php echo CHtml::link('Plus de détails', array('congeUpdate', 'id'=>$data->id)); ?></b>
		<?php if($type=='perso'):?>
			<b><?php echo CHtml::link('Dupliquer', array('congeCreate', 'id'=>$data->id)); ?></b>
		<?php endif;?>
		<?php if($type=='fermer') :?>
			<b><?php echo CHtml::label('Valider', '', array('class'=>"btnValider", 'id'=>$data->id, 'Style'=>'color:#06c;display:inline;cursor:pointer;text-decoration:underline;')); ?></b>
		<?php endif;?>
		<?php if(($type=='Accepter' || $type=='Refuser') && $data->archive == 0) :?>
			<b><?php echo CHtml::link('Archiver',array('congeArchiver','id'=>$data->id),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir archiver cet item?')"));?></b>
		<?php endif;?>
		<b><?php echo CHtml::link('Imprimer',array('imprimerConge','id'=>$data->id));?></b>
	</div>
	<div class="span-9 clear" style="float:none"></div>
</div>