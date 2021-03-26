<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/evenement.css');

$this->breadcrumbs=array(
		'Formation'=>array('index'),
		'Plan',
);
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'formation-plan-form',
	'enableAjaxValidation'=>false,
)); ?>
	
	<div class="row">
		<?php echo CHtml::label('Formation', 'formation'); ?>
		<?php echo CHtml::dropDownList('formation', '', $lstFormations); ?>
	</div>
	<div class="row">
		<?php echo CHtml::label('Lieu', 'lieu', array('style'=>'display:inline-block;')); ?>
		<?php echo CHtml::textField('lieu', ''); ?>	
	</div>
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'formation.plan.dateDebut'));?> 
		<?php echo CHtml::label('Date et heure de début', 'dateDebut', array('style'=>'display:inline-block;margin-right:5px;'));
			$form->widget('zii.widgets.jui.EJuiDateTimePicker',array(
				'name' => 'dateDebut',
				'options'=>array(
						'timeFormat' => 'hh:mm:ss',
						'dateFormat'=>'yy-mm-dd',
						'changeMonth' => true,
						'changeYear' => true,
				),
			));
		?>	
	</div>
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'formation.plan.dateFin')); ?>
		<?php echo CHtml::label('Date et heure de fin', 'dateFin', array('style'=>'display:inline-block;margin-right:5px;'));
			$form->widget('zii.widgets.jui.EJuiDateTimePicker',array(
				'name' => 'dateFin',
				'options'=>array(
						'timeFormat' => 'hh:mm:ss',
						'dateFormat'=>'yy-mm-dd',
						'changeMonth' => true,
						'changeYear' => true,
				),
			));
		?>	
	</div>
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'formation.plan.instituteur')); ?>
		<?php echo CHtml::label('Instituteur', 'instituteur', array('style'=>'display:inline-block;')); ?>
		<?php echo CHtml::dropDownList('instituteur', '', $lstUsagers); ?>
	</div>	
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'formation.plan.moniteur')); ?>
		<?php echo CHtml::label('Moniteur', 'moniteur', array('style'=>'display:inline-block;')); ?>
		<?php echo CHtml::dropDownList('moniteur', '', $lstUsagers); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Créer et ajouter des usagers'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->