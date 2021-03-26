<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'caserne-form',
	'enableAjaxValidation'=>false,
)); ?>

<div class="form">

	<?php echo $form->errorSummary($model); ?>
	
	<div class="row">
		<?php echo $form->labelEx($model,'nom'); ?>
		<?php echo $form->textField($model,'nom',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'nom'); ?>
	</div>
	<div class="row">
		<div class="span-3">
			<?php echo $form->labelEx($model,'numero'); ?>
			<?php echo $form->textField($model,'numero',array('size'=>10,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'numero'); ?>
		</div>
		
		<div class="span-15">		
			<?php echo $form->labelEx($model,'adresse'); ?>
			<?php echo $form->textField($model,'adresse',array('size'=>45,'maxlength'=>255)); ?>
			<?php echo $form->error($model,'adresse'); ?>
		</div>
	</div>
	<div class="row">
		<div class="span-3">
			<?php echo $form->labelEx($model,'codePostal'); ?>
			<?php echo $form->textField($model,'codePostal',array('size'=>10,'maxlength'=>6)); ?>
			<?php echo $form->error($model,'codePostal'); ?>
		</div>
		
		<div class="span-15">
			<?php echo $form->labelEx($model,'ville'); ?>
			<?php echo $form->textField($model,'ville',array('size'=>45,'maxlength'=>100)); ?>
			<?php echo $form->error($model,'ville'); ?>
		</div>
	</div>
	<div class="row">
		<div class="span-6">
			<?php echo $form->labelEx($model,'siGrandEcran'); ?>
			<?php echo $form->checkBox($model,'siGrandEcran'); ?>
			<?php echo $form->error($model,'siGrandEcran'); ?>
		</div>

		<div class="span-6">
			<?php echo $form->labelEx($model,'si_fdf'); ?>
			<?php echo $form->checkBox($model,'si_fdf'); ?>
			<?php echo $form->error($model,'si_fdf'); ?>
		</div>

		<div class="span-6">
			<?php echo $form->labelEx($model,'si_horaire'); ?>
			<?php echo $form->checkBox($model,'si_horaire'); ?>
			<?php echo $form->error($model,'si_horaire'); ?>
		</div>
	</div>
	

<div style="clear:both"></div>

</div><!-- form -->
	<div class="styleButtons">
		<div class="buttons">
				<?php echo CHtml::submitButton($model->isNewRecord ? 'Créer' : 'Sauvegarder'); ?>
		</div>
		<div class="finButtons"></div>
		<div style="clear:both;"></div>
	</div>
	
<?php $this->endWidget(); ?>