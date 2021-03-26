<?php if($index==0) echo '<div class="premier"></div>';?><div class="view triEquipe">
	<?php echo CHtml::ajaxLink(
						CHtml::encode($data->nom),
						array('index'.$fichier,'idEquipe'=>$data->id,'ajax'=>'lstUsager'),
						array('success'=>'
							function(result){
								$("#listeUsager").html(result);
								refreshTableauHeures();
							}')
				); 
	?></div>