<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'poste-form',
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
		<?php echo $form->labelEx($model,'diminutif'); ?>
		<?php echo $form->textField($model,'diminutif',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'diminutif'); ?>
	</div>
	
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'poste.form.formationObligatoire')); ?>
		<?php echo $form->labelEx($model,'formationObli'); ?>
		<?php echo $form->checkBox($model,'formationObli'); ?>
		<?php echo $form->error($model,'formationObli'); ?>
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