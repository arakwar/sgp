<?php

$this->breadcrumbs=array(
	'Minimum',
);

Yii::app()->clientScript->registerScript('updateLstMinimum','
	$("#lstCaserne").on("change", function(event){
		id = $("select#lstCaserne").val();
		window.location = "index.php?r=minimum/index&caserne="+id;
	});
');

$form=$this->beginWidget('CActiveForm', array(
	'id'=>'minimum-form',
	'enableAjaxValidation'=>false,
))
?>
<div class="span-7">
	<div class="equipeMini">
		<div class="premier"></div><div class="view">
			<?php echo CHtml::dropDownList('lstCaserne', $caserne, $lstCaserne,array('style'=>'margin:0;')); ?>	
		</div>
	</div>
	<div class="form">
		<div class="row span-2">
			<?php echo CHtml::label('Minimum',''); ?>
		</div>
		<div class="row span-2">
			<?php echo CHtml::label('Nbr. Équipe',''); ?>
		</div>
		<div class="row clear">
			<?php echo $form->labelEx($model,'dimanche'); ?>
			<?php echo $form->textField($model,'dimancheMin', array('size'=>10)); ?>
			<?php echo $form->error($model,'dimancheMin'); ?>
			
			<?php echo $form->textField($model,'dimancheNiv', array('size'=>10,'maxlength'=>4)); ?>
			<?php echo $form->error($model,'dimancheNiv'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model,'lundi'); ?>
			<?php echo $form->textField($model,'lundiMin', array('size'=>10)); ?>
			<?php echo $form->error($model,'lundiMin'); ?>
			
			<?php echo $form->textField($model,'lundiNiv', array('size'=>10,'maxlength'=>4)); ?>
			<?php echo $form->error($model,'lundiNiv'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model,'mardi'); ?>
			<?php echo $form->textField($model,'mardiMin', array('size'=>10)); ?>
			<?php echo $form->error($model,'mardiMin'); ?>
			
			<?php echo $form->textField($model,'mardiNiv', array('size'=>10,'maxlength'=>4)); ?>
			<?php echo $form->error($model,'mardiNiv'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model,'mercredi'); ?>
			<?php echo $form->textField($model,'mercrediMin', array('size'=>10)); ?>
			<?php echo $form->error($model,'mercrediMin'); ?>
			
			<?php echo $form->textField($model,'mercrediNiv', array('size'=>10,'maxlength'=>4)); ?>
			<?php echo $form->error($model,'mercrediNiv'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model,'jeudi'); ?>
			<?php echo $form->textField($model,'jeudiMin', array('size'=>10)); ?>
			<?php echo $form->error($model,'jeudiMin'); ?>
			
			<?php echo $form->textField($model,'jeudiNiv', array('size'=>10,'maxlength'=>4)); ?>
			<?php echo $form->error($model,'jeudiNiv'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model,'vendredi'); ?>
			<?php echo $form->textField($model,'vendrediMin', array('size'=>10)); ?>
			<?php echo $form->error($model,'vendrediMin'); ?>
						
			<?php echo $form->textField($model,'vendrediNiv', array('size'=>10,'maxlength'=>4)); ?>
			<?php echo $form->error($model,'vendrediNiv'); ?>
		</div>
		<div class="row">
			<?php echo $form->labelEx($model,'samedi'); ?>
			<?php echo $form->textField($model,'samediMin', array('size'=>10)); ?>
			<?php echo $form->error($model,'samediMin'); ?>
			
			<?php echo $form->textField($model,'samediNiv', array('size'=>10,'maxlength'=>4)); ?>
			<?php echo $form->error($model,'samediNiv'); ?>
		</div>
		<div id="req_res"></div>
	</div>
	<div class="styleButtons">
		<div class="buttons">
				<?php echo CHtml::ajaxSubmitButton('Sauvegarder',
									array('minimum/save'),
									array('update'=>'#req_res')
								); ?>
		</div>
		<div class="finButtons"></div>
		<div style="clear:both;"></div>
	</div>
</div>
<?php $this->endWidget();?>
<div class="span-16">
	<?php 
		$exception = new MinimumException;
		$this->renderPartial('../minimumException/_form',array('model'=>$exception, 'lstCaserne'=>$lstCaserne));
	?>
</div>


