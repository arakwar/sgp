<div class="viewMess" style="height:30px;">
	<div class="itemTop">
		<div class="numero">
		<?php echo CHtml::encode($data->matricule); ?>
		</div>
		<div class="diviseur100"></div>
		<div class="nom"><?php 
		echo '<span class="ajouteDestinataire" nom="'.$data->prenomnom.'" courriel="'.$data->id.'">+</span> '.CHtml::encode($data->prenom).' '.CHtml::encode($data->nom); 
		
		?></div>
	</div>	
</div>