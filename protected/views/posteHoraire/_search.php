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
		<?php echo $form->label($model,'heureDebut'); ?>
		<?php echo $form->textField($model,'heureDebut'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'heureFin'); ?>
		<?php echo $form->textField($model,'heureFin'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'tbl_quart_id'); ?>
		<?php echo $form->textField($model,'tbl_quart_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'tbl_poste_id'); ?>
		<?php echo $form->textField($model,'tbl_poste_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->