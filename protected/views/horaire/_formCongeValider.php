<?php 
$form=$this->beginWidget('CActiveForm', array(
	'id'=>'absence-form',
	'enableAjaxValidation'=>false,
	'action'=>array('congeValider'),
));
?>

<div class="form" style="border:none;padding:0px;">
	
	<div class="row last">
		<?php echo $form->labelEx($model,'raison'); ?>
		<?php echo $form->textArea($model,'raison',array('rows'=>2, 'cols'=>100,'disabled'=>((Yii::app()->user->id==$model->tbl_usager_id || $model->statut!=1)?'disabled':''))); ?>
		<?php echo $form->error($model,'raison'); ?>
	</div>
	
	<div class="clear"></div>	

</div><!-- form -->
	<div class="styleButtons">
		<div class="buttons">
		<?php 
		if($model->isNewRecord){
			echo CHtml::submitButton('Soumettre',array('onClick'=>"return confirm('Vous ne pourrez modifier après avoir sauvegarder. Souhaitez-vous continuer?')"));		
		}else{
			if($model->statut==1 && $model->tbl_usager_id != Yii::app()->user->id){
				echo CHtml::button('Accepter', array('onclick' => 'js:document.location.href="index.php?r=horaire/congeValider&id='.$model->id.'&statut=2&raison="+document.getElementById("Absence_raison").value+"&dir=conge"'));
				echo CHtml::button('Refuser', array('onclick' => 'js:document.location.href="index.php?r=horaire/congeValider&id='.$model->id.'&statut=3&raison="+document.getElementById("Absence_raison").value+"&dir=conge"'));
			}	
		}		
		?>
		</div>
		<div class="finButtons"></div>
		<div style="clear:both;"></div>
	</div>
	
<?php $this->endWidget(); ?>