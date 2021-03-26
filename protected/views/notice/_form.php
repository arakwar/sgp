<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'notice-form',
	'enableAjaxValidation'=>false,
)); ?>

<div class="form">
	
	<p class="note">Les champs avec une <span class="required">*</span> sont requis.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php 
			echo $this->tooltip(Yii::t('views','notice.form.dateDebut'));
			echo $form->labelEx($model,'dateDebut');	
			$form->widget('zii.widgets.jui.CJuiDatePicker',array(
				'model'=>$model,
				'attribute'=>'dateDebut',
				'options'=>array(
					'showAnim'=>'fold',
					'dateFormat'=>'yy-mm-dd'
				),
			));
		?>
		<?php echo $form->error($model,'dateDebut'); ?>
	</div>

	<div class="row">
		<?php 
			echo $this->tooltip(Yii::t('views','notice.form.dateFin'));
			echo $form->labelEx($model,'dateFin'); 
			$form->widget('zii.widgets.jui.CJuiDatePicker',array(
				'model'=>$model,
				'attribute'=>'dateFin',
				'options'=>array(
					'showAnim'=>'fold',
					'dateFormat'=>'yy-mm-dd'
				),
			));
		?>
		<?php echo $form->error($model,'dateFin'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'message'); ?>
		<?php echo $form->textField($model,'message',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'message'); ?>
	</div>
	
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'notice.form.caserne')); ?>
		<?php echo $form->labelEx($model,'tblCasernes');?>
		<?php echo CHtml::checkBoxList('tblCasernes',CHtml::listData($model->tblCasernes,'id','id'),CHtml::listData(Caserne::model()->findAll('siActif = 1 AND id IN ('.$casernesUsager.')',array()),'id','nom'),array_merge(array(),array('labelOptions'=>array('style'=>'display:inline;'))));?>
		<?php echo $form->error($model,'tblCasernes');?>
	</div>

	<div class="row">
		<?php echo $form->hiddenField($model,'tbl_usager_id'); ?>
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