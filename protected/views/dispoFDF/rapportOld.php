<?php 
Yii::app()->clientScript->registerScriptFile('js/jquery_json.js');
Yii::app()->clientScript->registerScript('checkTous','
jQuery("#cmbEquipe_all").live("click",function() {
	jQuery("input[name=\'cmbEquipe\[\]\']").attr("checked", this.checked);
	jQuery("input[name=\'cmbEquipeSpe\[\]\']").attr("checked",false);
	jQuery("#cmbEquipeSpe_all").attr("checked",false);
});
jQuery("input[name=\'cmbEquipe[]\']").live("click",function() {
	jQuery("#cmbEquipe_all").attr("checked", !jQuery("input[name=\'cmbEquipe\[\]\']:not(:checked)").length);
	jQuery("input[name=\'cmbEquipeSpe\[\]\']").attr("checked",false);
	jQuery("#cmbEquipeSpe_all").attr("checked",false);
});
jQuery("#cmbEquipeSpe_all").live("click",function() {
	jQuery("input[name=\'cmbEquipeSpe\[\]\']").attr("checked", this.checked);
	jQuery("input[name=\'cmbEquipe\[\]\']").attr("checked",false);
	jQuery("#cmbEquipe_all").attr("checked",false);
});
jQuery("input[name=\'cmbEquipeSpe[]\']").live("click",function() {
	jQuery("#cmbEquipeSpe_all").attr("checked", !jQuery("input[name=\'cmbEquipeSpe\[\]\']:not(:checked)").length);
	jQuery("input[name=\'cmbEquipe\[\]\']").attr("checked",false);
	jQuery("#cmbEquipe_all").attr("checked",false);
});
');
echo CHtml::beginForm('','post',array('id'=>'formRapport')); ?>
<div class="form">
	<div class="row span-4">
	<?php echo CHtml::label('Date de début :','dateDebut'); 
		  $this->widget('zii.widgets.jui.CJuiDatePicker',array(
		  	'name'=>'dateDebut',
		  	'language'=>'fr',
		  	'options'=>array(
		  		'dateFormat'=>'yy-mm-dd',
		  		'onSelect'=>'js:function(date){'.CHtml::ajax(array(
		  			'url'=>array('dispoFDF/updateForm'),
		  			'async'=>false,
		  			'data'=>array('dateDebut'=>'js:date','dateFin'=>'js:$("#dateFin").val()'),
		  			'dataType'=>'text',
		  			'type'=>'POST',
		  			'success'=>'function(result){
		  				tblResult = $.json.decode(result);
		  				$(".Equipe").find(":gt(0)").remove();
		  				$(".Groupe").find(":gt(0)").remove();
		  				$(".Equipe").append(tblResult["equipe"]);
		  				$(".Groupe").append(tblResult["groupe"]);
		  			}',
		  		)).'}'
		  	),
		  ));
	?>
	<?php echo CHtml::label('Date de fin :','dateFin');
		  $this->widget('zii.widgets.jui.CJuiDatePicker',array(
		  	'name'=>'dateFin',
		  	'language'=>'fr',
		  	'options'=>array(
		  		'dateFormat'=>'yy-mm-dd',
		  		'onSelect'=>'js:function(date){'.CHtml::ajax(array(
		  			'url'=>array('dispoFDF/updateForm'),
		  			'async'=>false,
		  			'data'=>array('dateFin'=>'js:date','dateDebut'=>'js:$("#dateDebut").val()'),
		  			'dataType'=>'text',
		  			'type'=>'POST',
		  			'success'=>'function(result){
		  				tblResult = $.json.decode(result);
		  				$(".Equipe").find(":gt(0)").remove();
		  				$(".Groupe").find(":gt(0)").remove();
		  				$(".Equipe").append(tblResult["equipe"]);
		  				$(".Groupe").append(tblResult["groupe"]);
		  			}',
		  		)).'}'
		  	),
		  ));
	?>
	</div>
	<div class="row span-4">
		<?php echo CHtml::label('Quart :','cmbQuart');
			  echo CHtml::dropDownList('cmbQuart','tous',$tblQuart);
		?>
		<?php echo CHtml::label('Disponibilité :','cmbDispo');
			  echo CHtml::dropDownList('cmbDispo','tous',array('tous'=>"Tous",'oui'=>"Oui",'non'=>"Non"));
		?>
	</div>
	<div class="row span-5 Equipe">
		<?php echo CHtml::label('Équipes :','cmbEquipes');
			  /*echo CHtml::checkBoxList('cmbEquipe',array('100','200','300'),array('100'=>"100",'200'=>"200",'300'=>"300"),
			  	                       	array('checkAll'=>"Tous",'labelOptions'=>array('style'=>'display:inline;')));*/
		?>
	</div>
	<div class="row span-5 Groupe">
		<?php echo CHtml::label('Équipes spécialisés :','cmbEquipesSpe');
			  /*echo CHtml::checkBoxList('cmbEquipeSpe',array('foret','nautique','hauteur'),array('foret'=>"Forêt",'nautique'=>"Nautique",'hauteur'=>"Hauteur"),
			  	                       	array('checkAll'=>"Tous",'labelOptions'=>array('style'=>'display:inline;')));*/
		?>
	</div>
	<div style="clear:both;"></div>
</div>
<div class="styleButtons">
		<div class="buttons">
				<?php echo CHtml::ajaxSubmitButton('Rechercher','',array('update'=>'#result')); ?>
		</div>
		<div class="finButtons"></div>
		<div style="clear:both;"></div>
	</div>
<?php echo CHtml::endForm(); ?>
<div id="result" class="form">
<?php 

?>
</div>