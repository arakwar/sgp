<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'groupe-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="row">
		<?php echo $form->labelEx($model,'nomL'); ?>
		<?php echo $form->textField($model,'nomL',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'nomL'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'nom'); ?>
		<?php echo $form->textField($model,'nom',array('size'=>45,'maxlength'=>8)); ?>
		<?php echo $form->error($model,'nom'); ?>
	</div>
	
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'groupe.form.caserne')); ?>
		<?php echo $form->labelEx($model,'tbl_caserne_id'); ?>
		<?php echo $form->dropDownList($model,'tbl_caserne_id',$lstCaserne); ?>
		<?php echo $form->error($model,'tbl_caserne_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'garde_sur_total_groupe'); ?>
		<?php echo $form->checkBox($model, 'garde_sur_total_groupe'); ?>
		<?php echo $form->error($model,'garde_sur_total_groupe'); ?>
	</div>
	

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Créer' : 'Sauvegarder'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->