<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'groupe-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="row">
		<?php echo $form->labelEx($model,'nom'); ?>
		<?php echo $form->textField($model,'nom',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'nom'); ?>
	</div>
	
	<div class="row">
		<?php echo CHtml::label('Usagers', 'usagers'); ?>
		<?php echo CHtml::checkBoxList('usagers', (($model->isNewRecord)?'':$lstGFUsagers), $lstUsagers, array('labelOptions'=>array('style'=>'display:inline;')));?>
	</div>	

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Créer' : 'Sauvegarder'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->