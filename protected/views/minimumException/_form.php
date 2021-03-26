

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'minimum-exception-form',
	'enableAjaxValidation'=>false,
)); ?>
<div class="form">

	<?php echo $form->errorSummary($model); ?>
	<div class="row">
		<?php echo $form->labelEx($model,'titre'); ?>
	</div>
	
	<div id="gestion_exception">
		<?php echo $this->actionGetException();?>
	</div>

	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'minimum.form.dateDebut')); ?>
		<?php echo $form->labelEx($model,'dateDebut'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
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
		<?php echo $this->tooltip(Yii::t('views', 'minimum.form.dateFin')); ?>
		<?php echo $form->labelEx($model,'dateFin'); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker',array(
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
		<?php echo $this->tooltip(Yii::t('views', 'minimum.form.minimum')); ?>
		<?php echo $form->labelEx($model,'minimum'); ?>
		<?php echo $form->textField($model,'minimum'); ?>
		<?php echo $form->error($model,'minimum'); ?>
	</div>
	
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'minimum.form.niveauAlerte')); ?>
		<?php echo $form->labelEx($model,'niveauAlerte'); ?>
		<?php echo $form->textField($model,'niveauAlerte'); ?>
		<?php echo $form->error($model,'niveauAlerte'); ?>
	</div>
	
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'minimum.form.caserne')); ?>
		<?php echo $form->labelEx($model,'tbl_caserne_id'); ?>
		<?php echo $form->dropDownList($model,'tbl_caserne_id',$lstCaserne); ?>
		<?php echo $form->error($model,'tbl_caserne_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->hiddenField($model,'tbl_usager_id'); ?>
	</div>
</div><!-- form -->
<div class="styleButtons">
	<div class="buttons">
			<?php echo CHtml::submitButton('Sauvegarder'); ?>
	</div>
	<div class="finButtons"></div>
	<div style="clear:both;"></div>
</div>
<?php $this->endWidget(); ?>