<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/evenement.css');

$this->breadcrumbs=array(
		'Formation'=>array('index'),
		'Evaluation',
);
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'formation-evaluation-form',
	'enableAjaxValidation'=>false,
));?>
	<h2><?php echo $formation->nom; ?></h2>
	
	<div class="row">
		<h3><?php echo CHtml::label('Date : '.substr($formation->dateDebut,0,10), ''); ?></h3>
	</div>
	
	<div class="row">
		<?php echo CHtml::label('Formation complétée :', 'tblResultat'); ?>		
		<?php echo CHtml::checkBoxList('tblResultat', $lstResultats, $lstUsagers, array('labelOptions'=>array('style'=>'display:inline;')));?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Sauvegarder'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->