<div class="form">

<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/evenement.css');

$disponibilites = '';
foreach($lstUsagersDispo as $Udispo){
	$disponibilites .= '"'.$Udispo.'",';
}
$disponibilites = substr($disponibilites, 0, strlen($disponibilites)-1);

$pre = '';
foreach($lstPreRequis as $preRequis){
	$pre .= '"'.$preRequis.'", ';
}
$pre = substr($pre, 0, strlen($pre)-2);

Yii::app()->clientScript->registerScript('groupeUsager',
'function loadUsager(cb){
		if(cb.checked==true){
			var idGroupe = cb.value;'.
			CHtml::ajax(array(
			'type'=>'POST',
			'url' =>array('usagerGroupe'),
			'cache'=>false,
			'data'=>array('idGroupe'=>'js:idGroupe'),
			'success'=>'function(result){
						$("#tblUsagers input").each(function(){
							var usagers = jQuery.parseJSON(result);
							if(usagers.indexOf($(this).val()) >= 0){
								$(this).prop(\'checked\', true);
							}
						})
					}',
			))
			.'
		}
	}'
	,CClientScript::POS_END);

Yii::app()->clientScript->registerScript('usagerDispo','

$(document).ready(function(){
	var dispos = new Array('.$disponibilites.');
	$("#tblUsagers label").each(function(){
		var input = $("#"+$(this).attr("for"));
		var valeur = input.val();
		if(jQuery.inArray(valeur, dispos) != -1) {
			$(this).css("color", "#000");
		}
	});
	$("#tblInvites label").each(function(){
		var input = $("#"+$(this).attr("for"));
		var valeur = input.val();
		if(jQuery.inArray(valeur, dispos) != -1) {
			$(this).css("color", "#000");
		}
	});
});
$(document).ready(function(){
	var prerequis = new Array('.$pre.');
	$("#tblUsagers label").each(function(){
		var input = $("#"+$(this).attr("for"));
		var valeur = input.val();
		if(jQuery.inArray(valeur, prerequis)  != -1) {
			$(this).css("text-decoration", "line-through");
		}
	});
	$("#tblInvites label").each(function(){
		var input = $("#"+$(this).attr("for"));
		var valeur = input.val();
		if(jQuery.inArray(valeur, prerequis)  != -1) {
			$(this).css("text-decoration", "line-through");
		}
	});
});
'
,CClientScript::POS_READY);

$this->breadcrumbs=array(
		'Formation'=>array('index'),
		'Plan',
);
?>

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'evenement-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
	
	<div class="row">
		<?php echo $form->labelEx($model,'nom'); ?>
		<?php echo $form->textField($model,'nom',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'nom'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'lieu'); ?>
		<?php echo $form->textField($model,'lieu',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'lieu'); ?>
	</div>
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'evenement.form.dateDebut')); ?>
		<?php echo $form->labelEx($model,'dateDebut'); ?>
		<?php
			$form->widget('zii.widgets.jui.EJuiDateTimePicker',array(
				'model'=>$model, //Model object
				'attribute'=>'dateDebut', //attribute name
				'options'=>array(
						'timeFormat' => 'hh:mm:ss',
						'dateFormat'=>'yy-mm-dd',
						'changeMonth' => true,
						'changeYear' => true,
				),
			));
		?>
		<?php echo $form->error($model,'dateDebut'); ?>		
	</div>
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'evenement.form.dateFin')); ?>
		<?php echo $form->labelEx($model,'dateFin'); ?>
		<?php
		$form->widget('zii.widgets.jui.EJuiDateTimePicker',array(
			'model'=>$model, //Model object
			'attribute'=>'dateFin', //attribute name
			'options'=>array(
					'timeFormat' => 'hh:mm:ss',
					'dateFormat'=>'yy-mm-dd',
					'changeMonth' => true,
					'changeYear' => true,
			),
		));
		?>
		<?php echo $form->error($model,'dateFin'); ?>			
	</div>
	
	<?php if(!$model->isNewRecord):?>
	<div class="row">
		<?php echo $this->tooltip(Yii::t('views', 'evenement.form.nonDisponible')); echo $form->labelEx($model,'tblUsagers', array('style'=>'display:inline-block;width:200px;')); ?>
		<?php echo $form->labelEx($model,'tblInvites', array('style'=>'display:inline-block;width:200px;')); ?>
		<?php if($model->tbl_formation_id != '0'): echo CHtml::label('Groupe', 'groupe', array('style'=>'display:inline-block;width:200px;')); endif; ?>
	</div>
	<div class="row">
		<?php echo CHtml::checkBoxList('tblUsagers', CHtml::listData($model->tblUsagers,'id','id'), $lstUsagers, array('labelOptions'=>array('style'=>'display:inline;')));?>
		<?php echo CHtml::checkBoxList('tblInvites', CHtml::listData($model->tblUsagers,'id','id'), $lstInvites, array('labelOptions'=>array('style'=>'display:inline;')));?>
		<?php echo $form->error($model,'tblUsagers'); ?>
		<?php if($model->tbl_formation_id != '0'): echo CHtml::checkBoxList('groupe', '', $lstGroupeF, array('labelOptions'=>array('style'=>'display:inline;'), 'onclick'=>'loadUsager(this)')); endif;?>
	</div>
	<?php endif;?>

		<?php echo $form->hiddenField($model, 'tbl_formation_id'); ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Créer et ajouter des usagers' : 'Sauvegarder'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->