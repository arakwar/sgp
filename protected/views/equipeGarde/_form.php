<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'poste-form',
	'enableAjaxValidation'=>false,
)); ?>

<div class="form">

	<p class="note">Les champs avec une <span class="required">*</span> sont requis.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'nom'); ?>
		<?php echo $form->textField($model,'nom',array('size'=>25,'maxlength'=>25)); ?>
		<?php echo $form->error($model,'nom'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'nbr_jour_affiche'); ?>
		<?php echo $form->textField($model,'nbr_jour_affiche'); ?>
		<?php echo $form->error($model,'nbr_jour_affiche'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'nbr_jour_periode'); ?>
		<?php echo $form->textField($model,'nbr_jour_periode'); ?>
		<?php echo $form->error($model,'nbr_jour_periode'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'nbr_jour_depot'); ?>
		<?php echo $form->textField($model,'nbr_jour_depot'); ?>
		<?php echo $form->error($model,'nbr_jour_depot'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'date_debut'); ?>
				<?php 
			$this->widget('zii.widgets.jui.CJuiDatePicker',array(
				'name'=>'date_debut',
				'options'=>array(
					'showAnim'=>'fold',
					'dateFormat'=>'yy-mm-dd'
				),
				'value'=>$model->date_debut
			));
		?>
		<?php echo $form->error($model,'date_debut'); ?>
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