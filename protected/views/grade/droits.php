<?php 
$this->menu=array(
	array('label'=>'Liste des grades', 'url'=>array('index')),
);
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'taches-form',
	'enableAjaxValidation'=>false,
)); ?>
<h1><?php echo $model->nom;?></h1>
<div class="form">
	<?php 
		echo CHtml::checkBoxList('listeTaches',$tblTachesActuelle,$tblTaches,array('labelOptions'=>array('style'=>'display:inline;font-size:1.1em;')));
		//echo json_encode($tblTaches);
	?>
		<?php echo $form->hiddenField($model,'roleName');?>


</div><!-- form -->
	<div class="styleButtons">
		<div class="buttons">
				<?php echo CHtml::submitButton((($model->isNewRecord)?'Créer':'Sauvegarder')); ?>
		</div>
		<div class="finButtons"></div>
		<div style="clear:both;"></div>
	</div>
<?php $this->endWidget(); ?>