<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'quart-form',
	'enableAjaxValidation'=>false,
)); ?>

<div class="form">

	<p class="note">Les champs avec une <span class="required">*</span> sont requis.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'nom'); ?>
		<?php echo $form->textField($model,'nom',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'nom'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'heureDebut'); ?>
		<?php 
			$this->widget('system.ext.jui_timepicker.JTimePicker',array(
				'model'=>$model,
				'attribute'=>'heureDebut',
				'options'=>array(),
				
			));
		?>
		<?php echo $form->error($model,'heureDebut'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'heureFin'); ?>
		<?php 
			$this->widget('system.ext.jui_timepicker.JTimePicker',array(
				'model'=>$model,
				'attribute'=>'heureFin',
				'options'=>array(),
				
			));
		?>
		<?php echo $form->error($model,'heureFin'); ?>
	</div>

</div><!-- form -->
	<div class="styleButtons">
		<div class="buttons">
				<?php echo CHtml::submitButton((($model->isNewRecord)?'Créer':'Sauvegarder')); ?>
		</div>
		<div class="finButtons"></div>
		<div style="clear:both;"></div>
	</div>
	
<?php $this->endWidget(); ?>