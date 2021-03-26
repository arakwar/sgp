<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'message-form',
	'enableAjaxValidation'=>false,
));

Yii::app()->clientScript->registerScript('submitMessage','
	$("#message-form").bind("submit",function(){$("#listeDestinataire option").attr("selected","selected");});
');

?>
<div class="form">

	<p class="note">Les champs avec une <span class="required">*</span> sont requis.</p>	

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->hiddenField($model,'dateEnvoi'); ?>
	</div>
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'message.form.listeDestinataire')); ?>
		<label>Liste de destinataires</label>
		<select id="listeDestinataire" multiple="multiple" name="listeDestinataire[]"></select>
		<div id="videListe" style="float:right;">
			<?php echo $this->tooltip(Yii::t('views', 'message.form.viderListe')); ?>
			Vider la liste
		</div>
		<div style="clear:both;"></div>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'objet'); ?>
		<?php echo $form->textField($model,'objet',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'objet'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'message'); ?>
		<?php echo $form->textArea($model,'message',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'message'); ?>
	</div>

	<div class="row">
		<?php echo $form->hiddenField($model,'auteur'); ?>
	</div>

</div>
<div class="styleButtons">
	<div class="buttons">
			<?php echo CHtml::submitButton('Envoyer'); ?>
	</div>
	<div class="finButtons"></div>
	<div style="clear:both;"></div>
</div>
<?php $this->endWidget(); ?><!-- form -->