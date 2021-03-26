<?php 
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/usager.css');

$form=$this->beginWidget('CActiveForm', array(
	'id'=>'invite-form',
	'enableAjaxValidation'=>false,
)); 
$options['disabled'] = 'disabled';
if(Yii::app()->user->checkAccess('Admin')) $options['disabled'] = false;?>
<div class="form" style="height:472px;">

	<p class="note">Les champs avec une <span class="required">*</span> sont requis.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row span-7">
		<?php echo $form->labelEx($model,'nom'); ?>
		<?php echo $form->textField($model,'nom',array_merge(array('size'=>45,'maxlength'=>45),$options)); ?>
		<?php echo $form->error($model,'nom'); ?>
	</div>

	<div class="row span-7">
		<?php echo $form->labelEx($model,'prenom'); ?>
		<?php echo $form->textField($model,'prenom',array_merge(array('size'=>45,'maxlength'=>45),$options)); ?>
		<?php echo $form->error($model,'prenom'); ?>
	</div>

	<div class="row span-14">
		<?php echo $form->labelEx($model,'pseudo'); ?>
		<?php echo $form->textField($model,'pseudo',array_merge(array('size'=>45,'maxlength'=>45),$options));  ?>
		<?php echo $form->error($model,'pseudo'); ?>
	</div>

	<div class="row span-7">
		<?php echo $form->labelEx($model,'nmotdepasse'); ?>
		<?php echo $form->passwordField($model,'nmotdepasse',array_merge(array('size'=>45,'maxlength'=>45)));  ?>
		<?php echo $form->error($model,'nmotdepasse'); ?>
		<?php echo $form->error($model,'motdepasse'); ?>
	</div>
	<div class="row span-7">
		<?php echo $form->label($model,'nmotdepasse_repeat'); ?>
		<?php echo $form->passwordField($model,'nmotdepasse_repeat',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'nmotdepasse_repeat'); ?>
	</div>
	<div class="span-11">
		<div class="row">
			<?php echo $form->labelEx($model,'courriel'); ?>
			<?php echo $form->textField($model,'courriel',array('size'=>60,'maxlength'=>255)); ?>
			<?php echo $form->error($model,'courriel'); ?>
		</div>
	
		<div class="row">
			<?php echo $form->labelEx($model,'adresseCivique'); ?>
			<?php echo $form->textField($model,'adresseCivique',array('size'=>60,'maxlength'=>255)); ?>
			<?php echo $form->error($model,'adresseCivique'); ?>
		</div>
	
		<div class="row">
			<?php echo $form->labelEx($model,'ville'); ?>
			<?php echo $form->textField($model,'ville',array('size'=>60,'maxlength'=>100)); ?>
			<?php echo $form->error($model,'ville'); ?>
		</div>
		<div class="row span-5">
			<?php echo $form->labelEx($model,'telephone1'); ?>
			<?php echo $form->textField($model,'telephone1',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'telephone1'); ?>
		</div>
	
		<div class="row span-5">
			<?php echo $form->labelEx($model,'telephone2'); ?>
			<?php echo $form->textField($model,'telephone2',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'telephone2'); ?>
		</div>
		
		<div class="row span-10">
			<?php echo $form->labelEx($model,'telephone3'); ?>
			<?php echo $form->textField($model,'telephone3',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'telephone3'); ?>
		</div>
	</div>
</div><!-- form -->
<div style="clear:both;" class="styleButtons">
	<div class="buttons">
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Créer' : 'Sauvegarder'); ?>
	</div>
	<div class="finButtons"></div>
	<div style="clear:both;"></div>
</div>
<?php $this->endWidget(); ?>