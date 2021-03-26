<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'poste-horaire-form',
	'enableAjaxValidation'=>false,
)); ?>

<div class="form">

	<p class="note">Les champs avec une <span class="required">*</span> sont requis.</p>
	<p class="note">Les champs d'heures servent à mettre un poste seulement pour une partie du quart ciblé au besoin. Laissez vide si le poste occupe tout le quart..</p>
	<?php echo $form->errorSummary($model); ?>
	<div class="row">
		<?php echo $form->labelEx($model,'heureDebut'); ?>
		<?php 
			$this->widget('system.ext.jui_timepicker.JTimePicker',array(
				'model'=>$model,
				'attribute'=>'heureDebut',
				'options'=>array(),
				
			));
		?>
		<?php echo $form->error($model,'heureDebut'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'heureFin'); ?>
		<?php 
			$this->widget('system.ext.jui_timepicker.JTimePicker',array(
				'model'=>$model,
				'attribute'=>'heureFin',
				'options'=>array(),
				
			));
		?>
		<?php echo $form->error($model,'heureFin'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'tbl_quart_id'); ?>
		<?php echo $form->dropDownList($model,'tbl_quart_id',$model->getHoraireQuartOptions()); ?>
		<?php echo $form->error($model,'tbl_quart_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'tbl_poste_id'); ?>
		<?php echo $form->dropDownList($model,'tbl_poste_id',$model->getHorairePosteOptions()); ?>
		<?php echo $form->error($model,'tbl_poste_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'tblCasernes');?>
		<?php echo CHtml::checkBoxList('tblCasernes',CHtml::listData($model->tblCasernes,'id','id'),CHtml::listData(Caserne::model()->findAll(),'id','nom'),array_merge(array(),array('labelOptions'=>array('style'=>'display:inline;'))));?>
		<?php echo $form->error($model,'tblCasernes');?>
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