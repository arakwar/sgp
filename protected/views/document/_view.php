<div class="document span-9" style="margin:10px;min-height:165px;">
	<?php 
		if(!isset($data->url))
		{
	?>
		<div class="view" style="min-height: 112px">
			<?php 
				$ext = substr(strrchr($data->nom_fichier ,'.'),1);
				switch($ext){
					case 'doc':
					case 'docx':
						$image = 'word';
						break;
					case 'xls':
					case 'xlsx':
						$image = 'excel';
						break;
					case 'zip':
						$image = 'zip';
						break;
					case 'txt':
						$image = 'texte';
						break;
					case 'pdf':
						$image = 'pdf';
						break;
					default:
						$image = 'image';
						break;
				}
				echo CHtml::image('./images/ico/'.$image.'.png', $image); 
			?>
		
			<b><?php echo CHtml::encode($data->getAttributeLabel('nom')); ?>:</b>
			<?php 
				echo CHtml::encode($data->nom);
			?>
			<br />
		
			<b><?php echo CHtml::encode($data->getAttributeLabel('date')); ?>:</b>
			<?php echo CHtml::encode($data->date); ?>
			<br />
		
			<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
			<?php echo CHtml::encode($data->description); ?>
			<br />
			
			<?php if(!$accueil){?>
			<br />
			<b><?php echo CHtml::link('Plus de détails', array('view', 'id'=>$data->id)); ?></b>
			<?php if(Yii::app()->user->checkAccess('Document:create')){?>
			<b><?php echo CHtml::link('Modifier', array('update', 'id'=>$data->id)); ?></b>
			<b><?php echo CHtml::link('Supprimer',array('delete','id'=>$data->id),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer cet item?')"));?></b>
			<?php }//Fin du if Droit d'accès?>
			<?php }//Fin du if Page d'accueil?>
			
		</div>
		<div class="styleButtons">
			<div class="buttons">
					<?php echo CHtml::button('Télécharger', array(
						'submit'=>array('document/telecharger','id' => $data->id),
					)); ?>
			</div>
			<div class="finButtons"></div>
			<div style="clear:both;"></div>
		</div>
	<?php }//Fin du if document
	else
	{
	?>
		<div class="view" style="min-height: 112px">
			<b><?php echo CHtml::encode($data->getAttributeLabel('nom')); ?>:</b>
			<?php 
				echo CHtml::encode($data->nom);
			?>
			<br />
		
			<b><?php echo CHtml::encode($data->getAttributeLabel('date')); ?>:</b>
			<?php echo CHtml::encode($data->date); ?>
			<br />
		
			<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
			<?php echo CHtml::encode($data->description); ?>
			<br />
			
			<?php if(!$accueil){?>
			<br />
			<b><?php echo CHtml::link('Plus de détails', array('view', 'id'=>$data->id)); ?></b>
			<?php if(Yii::app()->user->checkAccess('Document:create')){?>
			<b><?php echo CHtml::link('Modifier', array('update', 'id'=>$data->id)); ?></b>
			<b><?php echo CHtml::link('Supprimer',array('delete','id'=>$data->id),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer cet item?')"));?></b>
			<?php }//Fin du if Droit d'accès?>
			<?php }//Fin du if Page d'accueil?>
		</div>
		<div class="styleButtons">
			<div class="buttons">
					<?php echo CHtml::button('Visionner', array(
						'submit'=>array('document/visionner','id' => $data->id),
					)); ?>
			</div>
			<div class="finButtons"></div>
			<div style="clear:both;"></div>
		</div>
	<?php }//Fin du if youtube?>
</div>