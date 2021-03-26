<div class="view" style="height:30px;">
	<div class="itemTop">
		<div class="numero">
		<?php echo CHtml::encode($data->matricule); ?>
		</div>
		<div class="diviseur100"></div>
		<div class="nom"><?php echo CHtml::link(CHtml::encode($data->prenom)." ".CHtml::encode($data->nom),array('view','id'=>$data->id)); ?></div>
		<div class="tel"></div>
	</div>

	
</div>