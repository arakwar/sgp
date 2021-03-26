<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'type-document-form',
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
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textField($model,'description',array('size'=>45,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'description'); ?>
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