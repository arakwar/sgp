<div class="form">

<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/evenement.css');

$this->breadcrumbs=array(
		'Évènement'=>array('index'),
		'Validation',
);

$form=$this->beginWidget('CActiveForm', array(
	'id'=>'evenement-validation-form',
	'enableAjaxValidation'=>false,
)); ?>

	<h2>Validation : <?php echo $model->nom;?></h2>
	
	<h3>Usager(s) non-disponibles :</h3>
	<?php echo CHtml::checkBoxList('Dispo', '', $lstUsagerNDispo, array('labelOptions'=>array('style'=>'display:inline;'))); ?>
	<br/><br/>
	
	<?php if($model->tbl_formation_id!=0): ?>
	<h3>Usager(s) n'ayant pas complété les pré-requis :</h3>
	<?php echo CHtml::checkBoxList('Prerequis', '', $lstUsagerPreRequis, array('labelOptions'=>array('style'=>'display:inline;'))); ?>
	<br/><br/>
	<?php endif; ?>
	
	<?php echo CHtml::hiddenField('validation','Validation');?>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Ajouter ces usagers'); ?>
	</div>
	
<?php $this->endWidget(); ?>
	
</div><!-- form -->