<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'formation-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="row">
		<?php echo $form->labelEx($model,'nom'); ?>
		<?php echo $form->textField($model,'nom',array('size'=>100,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'nom'); ?>
	</div>
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'formation.form.preRequis')); ?>
		<?php echo $form->labelEx($model,'tblPreRequis'); ?>
		<?php echo CHtml::checkBoxList('tblPreRequis',$lstFormationsPreC,$lstFormationsPre,array('labelOptions'=>array('style'=>'display:inline;')));?>		
		<?php echo $form->error($model,'tblPreRequis'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Créer' : 'Sauvegarder'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->