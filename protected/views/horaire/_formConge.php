<?php 
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'absence-form',
	'enableAjaxValidation'=>false,
	'action'=>array('congeCreate'),
));
?>

<div class="form">

	<p class="note">Les champs avec une <span class="required">*</span> sont requis.</p>
	<?php 
		if($model->archive==1){
			echo '<h2>Archivé</h2>';
		}
	?>

<?php if($model->statut==0 && Yii::app()->user->id==$model->tbl_usager_id){$enable = "";}else{$enable="disabled";}?>
	<?php echo $form->errorSummary($model); ?>
	<?php if(!$model->isNewRecord):?>
	
	<div class="row">
		<div class="span-6">
			<?php echo $form->labelEx($model,'tbl_usager_id'); ?>
			<?php echo $model->tblUsager->getMatPrenomNom();?>
		</div>
		<div class="span-12 last">
			<?php echo $form->labelEx($model,'dateEmis'); ?>
			<?php echo $model->dateEmis;?>
		</div>		
		<div class="clear"></div>
	</div>
	
	<?php endif;?>
	<div class="row">
		<div class="span-6">
			<?php echo $form->labelEx($model,'dateConge'); ?>
			<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
					'model'=>$model,
					'attribute'=>'dateConge',
					'options'=>array(
						'showAnim'=>'fold',
						'dateFormat'=>'yy-mm-dd'
					),
					'htmlOptions'=>array(
						'disabled'=>$enable,
					),
				));
			?>
			<?php echo $form->error($model,'dateConge'); ?>
		</div>
		
		<div class="span-6">
			<?php echo $form->labelEx($model,'heureDebut'); ?>
			<?php 
				$this->widget('system.ext.jui_timepicker.JTimePicker',array(
					'model'=>$model,
					'attribute'=>'heureDebut',
					'options'=>array(),
					'htmlOptions'=>array(
						'disabled'=>$enable,
					),
					
				));
			?>
			<?php echo $form->error($model,'heureDebut'); ?>
		</div>
		
		<div class="span-6 last">
			<?php echo $form->labelEx($model,'heureFin'); ?>
			<?php 
				$this->widget('system.ext.jui_timepicker.JTimePicker',array(
					'model'=>$model,
					'attribute'=>'heureFin',
					'options'=>array(),
					'htmlOptions'=>array(
						'disabled'=>$enable,
					),
					
				));
			?>
			<?php echo $form->error($model,'heureFin'); ?>
		</div>
	</div>
	<div class="row">
		<div class="span-6">
			<?php echo $form->labelEx($model,'tbl_type_id'); ?>
			<?php echo $form->dropDownList($model,'tbl_type_id',$listType,array('disabled'=>$enable)); ?>
			<?php echo $form->error($model,'tbl_type_id'); ?>
		</div>
		
		<div class="span-12 last">
			<?php echo $form->labelEx($model,'tbl_quart_id'); ?>
			<?php echo $form->dropDownList($model,'tbl_quart_id',$model->getHoraireQuartOptions(Yii::app()->user->id),array('disabled'=>$enable)); ?>
			<?php echo $form->error($model,'tbl_quart_id'); ?>
		</div>
	</div>
	<div class="clear"></div>
	
	<div class="row last">
		<?php echo $form->labelEx($model,'note'); ?>
		<?php echo $form->textArea($model,'note',array('rows'=>2, 'cols'=>100,'disabled'=>$enable)); ?>
		<?php echo $form->error($model,'note'); ?>
	</div>
	
	<?php if(!$model->isNewRecord && $model->statut!=1):
			$statut = array('2'=>'Accepté','3'=>'Refusé','4'=>'Fermé');?>
	<div class="row">
		<div class="span-6">
			<?php echo $form->labelEx($model,'statut'); ?>
			<?php echo $statut[$model->statut];?>
		</div>
		<div class="span-6">
			<?php echo $form->labelEx($model,'chef_id'); ?>
			<?php echo $model->tblChefs->getMatPrenomNom();?>
		</div>
		<div class="span-6 last">
			<?php echo $form->labelEx($model,'dateRecu'); ?>
			<?php echo $model->dateRecu.' '.$model->heureRecu?>
		</div>
		<div class="clear"></div>
	</div>
	
	<?php endif;?>
	
	<?php if(!$model->isNewRecord && !(Yii::app()->user->id==$model->tbl_usager_id && $model->statut==1)):?>
	
	
	<div class="row last">
		<?php echo $form->labelEx($model,'raison'); ?>
		<?php echo $form->textArea($model,'raison',array('rows'=>2, 'cols'=>100,'disabled'=>((Yii::app()->user->id==$model->tbl_usager_id || $model->statut!=1)?'disabled':''))); ?>
		<?php echo $form->error($model,'raison'); ?>
	</div>
	
	<?php endif;?>
	
	<div class="clear"></div>
	
	
	
	
	

</div><!-- form -->
	<div class="styleButtons">
		<div class="buttons">
		<?php 
		if($model->isNewRecord){
			echo CHtml::submitButton('Soumettre',array('onClick'=>"return confirm('Vous ne pourrez modifier après avoir sauvegarder. Souhaitez-vous continuer?')"));		
		}else{
			if($idP != 0){
				echo CHtml::button('<', array('onclick' => 'js:document.location.href="index.php?r=horaire/congeUpdate&id='.$idP.'"'));
			}
			if($model->statut==1 && $model->tbl_usager_id != Yii::app()->user->id){
				echo CHtml::button('Accepter', array('onclick' => 'js:document.location.href="index.php?r=horaire/congeValider&id='.$model->id.'&statut=2&raison="+document.getElementById("Absence_raison").value'));
				echo CHtml::button('Refuser', array('onclick' => 'js:document.location.href="index.php?r=horaire/congeValider&id='.$model->id.'&statut=3&raison="+document.getElementById("Absence_raison").value'));
			}
			echo CHtml::button('Retour', array('onclick' => 'js:document.location.href="index.php?r=horaire/conge"'));
			if($idS != 0){
				echo CHtml::button('>', array('onclick' => 'js:document.location.href="index.php?r=horaire/congeUpdate&id='.$idS.'"'));
			}	
		}		
		?>
		</div>
		<div class="finButtons"></div>
		<div style="clear:both;"></div>
	</div>
	
<?php $this->endWidget(); ?>