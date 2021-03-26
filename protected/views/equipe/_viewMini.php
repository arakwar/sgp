<div class="view" style="margin:10px;float:left;">
	<b><?php echo CHtml::encode($data->nom); ?>:</b>
	<?php
		if($data->couleur!=''):?> 
		<div style="display:inline;width:60px;border:solid thin black; height:30px;background-color:#<?php echo $data->couleur;?>; background-image:url('images/degrade.png');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
		<?php endif;?>
</div>