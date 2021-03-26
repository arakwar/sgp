<div class="view">
	<div class="photo" 
	<?php 
	if($data->getImageFinal()){
		echo 'style="background-image:url(\'imagesProfil/'.DOMAINE.'/final/'.$data->getImageFinal().'\')" ';
	}else{
		Yii::log("Aucune image de profil pour l'usager #".$data->id,'info','Usager');
	}
	?>
	></div>
	<div class="itemTop">
		<div class="numero">
		<?php echo CHtml::encode($data->matricule); ?>
		</div>
		<div class="diviseur100"></div>
		<div class="nom"><?php echo CHtml::encode($data->prenom)." ".CHtml::encode($data->nom); ?></div>
		<div class="tel"><?php echo CHtml::encode($data->getAttributeLabel('telephone1')); ?>:
		<?php echo CHtml::encode($data->telephone1); ?> / <?php echo CHtml::encode($data->telephone2); ?> / <?php echo CHtml::encode($data->telephone3); ?>
		<br />
		<?php if(Yii::app()->user->checkAccess('Admin')):?>
		<b><?php echo CHtml::link('Plus de détails', array('view', 'id'=>$data->id)); ?></b>
		<b><?php echo CHtml::link('Modifier', array('update', 'id'=>$data->id)); ?></b>
		<b><?php echo CHtml::link('Supprimer',array('delete','id'=>$data->id),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer cet item?')"));?></b>
		<?php endif;?>
		</div>
	</div>	
</div>