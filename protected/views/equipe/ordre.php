<?php
 $form=$this->beginWidget('CActiveForm', array(
	'id'=>'evenement-form',
	'enableAjaxValidation'=>false,
)); 

Yii::app()->clientScript->registerScript('updateLstEquipe','
	$("#lstCaserne").on("change", function(event){
		id = $("select#lstCaserne").val();
		window.location = "index.php?r=equipe/ordre&caserne="+id;
	});
');

$this->menu=array(
	array('label'=>'Liste des équipes', 'url'=>array('index')),
	array('label'=>'Créer un équipe', 'url'=>array('create')),
);
?>

<div class="equipeMini">
	<div class="premier"></div><div class="view">
		<?php echo CHtml::label('Caserne : ', 'lstCaserne'); ?>
		<?php echo CHtml::dropDownList('lstCaserne', $caserne, $dataCaserne); ?>	
	</div>
</div>

<div class="form">
<?php
	echo $this->actionOrdreEquipe($caserne);
?>
<div id="req_res"></div>
</div>
	<div style="clear:both;" class="styleButtons">
		<div class="buttons">
				<?php echo CHtml::ajaxSubmitButton('Sauvegarder',
								array('equipe/saveO'),
								array('update'=>'#req_res')	
							); ?>
		</div>
		<div class="finButtons"></div>
		<div style="clear:both;"></div>
	</div>
<?php $this->endWidget(); ?>