<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'equipe-form',
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
		<?php echo $this->tooltip(Yii::t('views', 'equipe.form.couleur')); ?>
		<?php echo $form->labelEx($model,'couleur'); ?>
		<?php $this->widget('system.ext.colorpicker.EColorPicker',array(
			'id' => 'Equipe_couleur',
			'name' => 'Equipe[couleur]',
			'mode' => 'textfield',
			'value' => $model->couleur
			
		)); ?>
		<?php echo $form->error($model,'couleur'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'siHoraire'); ?>
		<?php echo $form->checkBox($model,'siHoraire'); ?>
		<?php echo $form->error($model,'siHoraire'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'siFDF'); ?>
		<?php echo $form->checkBox($model,'siFDF'); ?>
		<?php echo $form->error($model,'siFDF'); ?>
	</div>
	
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'equipe.form.caserne')); ?>
		<?php echo $form->labelEx($model,'tbl_caserne_id'); ?>
		<?php echo $form->dropDownList($model,'tbl_caserne_id',$lstCaserne); ?>
		<?php echo $form->error($model,'tbl_caserne_id'); ?>
	</div>

</div><!-- form -->
	<div class="styleButtons"><div class="buttons"><?php echo CHtml::submitButton((($model->isNewRecord)?'Créer':'Sauvegarder')); ?></div><div class="finButtons"></div>
		<div style="clear:both;"></div>
	</div>
	
<?php $this->endWidget(); ?>