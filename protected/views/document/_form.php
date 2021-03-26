<?php $form=$this->beginWidget('CActiveForm', array(
	'htmlOptions'=>array('enctype'=>'multipart/form-data',),
	'id'=>'document-form',
	'enableAjaxValidation'=>false,
));
?>

<div class="form">

	<p class="note">Les champs avec une <span class="required">*</span> sont requis.</p>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="row">
		<?php echo $form->labelEx($model,'nom'); ?>
		<?php echo $form->textField($model,'nom',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'nom'); ?>
	</div>

	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'document.form.date')); ?>
		<?php echo $form->labelEx($model,'date'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
				'model'=>$model,
				'attribute'=>'date',
				'language'=>'fr',
				'options'=>array(
					'showAnim'=>'fold',
					'dateFormat'=>'yy-mm-dd'
				),
			));
		?>
		<?php echo $form->error($model,'date'); ?>
	</div>

	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'document.form.description')); ?>
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description', array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>
	
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'document.form.file')); ?>
		<?php echo $form->fileField($model,'fichier'); ?>
	</div>
	
	<p>OU</p>

	<div class="row">
		<div>
		<?php echo $this->tooltip(Yii::t('views', 'document.form.url')); ?>
		<?php echo $form->labelEx($model,'url'); ?>
		</div>
		<?php echo $form->textField($model,'url', array('size'=>60,'maxlength'=>300)); ?>
		<?php echo $form->error($model,'url'); ?>
	</div>

	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'document.form.caserne')); ?>
		<?php echo $form->labelEx($model,'tblCasernes');?>
		<?php echo CHtml::checkBoxList('tblCasernes',CHtml::listData($model->tblCasernes,'id','id'),CHtml::listData(Caserne::model()->findAll('siActif = 1 AND id IN ('.$casernesUsager.')',array()),'id','nom'),array_merge(array(),array('labelOptions'=>array('style'=>'display:inline;'))));?>
		<?php echo $form->error($model,'tblCasernes');?>
	</div>
	
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'document.form.type')); ?>
		<?php echo $form->labelEx($model,'tbl_type_id'); ?>
		<?php echo $form->dropDownList($model,'tbl_type_id',$lstType); ?>
		<?php echo $form->error($model,'tbl_type_id'); ?>
	</div>
	
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'document.form.suivi')); ?>
		<?php echo $form->labelEx($model,'suivi'); ?>
		<?php echo $form->checkBox($model, 'suivi'); ?>
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