<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'dateDebut'); ?>
		<?php echo $form->textField($model,'dateDebut'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'dateFin'); ?>
		<?php echo $form->textField($model,'dateFin'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'minimum'); ?>
		<?php echo $form->textField($model,'minimum'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'tbl_usager_id'); ?>
		<?php echo $form->textField($model,'tbl_usager_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->