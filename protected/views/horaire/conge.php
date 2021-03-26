<?php
$this->breadcrumbs=array(
	'Avis d\'absence',
);

$this->menu=array(
	array('label'=>'Gestion avancée', 'url'=>array('congeGestion')),
	array('label'=>'Remplir un avis', 'url'=>array('congeCreate')),
	array('label'=>'Archives', 'url'=>array('congeArchive')),
);

Yii::app()->clientScript->registerScript('dialogueValidation','		
	$(".btnValider").live("click",function(){
		$("#mydialogR").data("lien",$(this)).dialog("open");
	});
');

Yii::app()->clientScript->registerScript('filtre','
function changePage(filtre){
	window.location = "'.$this->createUrl('horaire/conge').'&filtre="+filtre;
}	
', CClientScript::POS_END);
?>

<div class="enTeteDiv">
	<div class="enTeteSec milieu">
		<?php echo CHtml::dropDownList('lstFiltre',$filtre,$tblFiltre,array('onChange'=>"js:changePage(this.options[this.selectedIndex].value);"));?>
	</div>
	<div class="enTeteSec milieu"></div>
	<div class="enTeteSec premier"></div>
</div>

<div class="row">
	<div class="span-9 clear">
	<?php
	if($siAdmin){
		echo CHtml::label('Mes avis', '').'<br/><br/>';
	}

	$this->widget('zii.widgets.CListView', array(
		'dataProvider'=>$dataProviderPerso,
		'itemView'=>'_viewConge',
		'template'=>'{items}<div style="clear:both"></div>{pager}',
		'viewData'=>array(
					'siAdmin'=>$siAdmin,
					'type'=>'perso',	
					),
	)); 
	?>
	</div>
	<?php
	if($siAdmin):
	?>
	<div class="span-9 last">
	<?php	
		echo CHtml::label('Avis non-validés', '').'<br/><br/>';
		
		$this->widget('zii.widgets.CListView', array(
		'dataProvider'=>$dataProviderFermer,
		'itemView'=>'_viewConge',
		'template'=>'{items}<div style="clear:both"></div>{pager}',
		'viewData'=>array(
					'siAdmin'=>$siAdmin,
					'type'=>'fermer',	
					),
		)); 
	?>
	</div>
</div>
<div class="row">
	<div class="span-9 clear">
	<?php
		
		echo CHtml::label('Avis acceptés', '').'<br/><br/>';

		$this->widget('zii.widgets.CListView', array(
		'dataProvider'=>$dataProviderAccepter,
		'itemView'=>'_viewConge',
		'template'=>'{items}<div style="clear:both"></div>{pager}',
		'viewData'=>array(
					'siAdmin'=>$siAdmin,
					'type'=>'Accepter',
					),
		)); 
	?>
	</div>
	<div class="span-9 last">
	<?php
		
		echo CHtml::label('Avis refusés', '').'<br/><br/>';

		$this->widget('zii.widgets.CListView', array(
		'dataProvider'=>$dataProviderRefuser,
		'itemView'=>'_viewConge',
		'template'=>'{items}<div style="clear:both"></div>{pager}',
		'viewData'=>array(
					'siAdmin'=>$siAdmin,
					'type'=>'Refuser',
					),
	));
	?>
	</div>

<?php

	$tblBoutonR = array();
	$tblBoutonR[] = array('text'=>'Annuler','click'=>'js:function(){$(this).dialog("close");}');
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
			'id'=>'mydialogR',
			// additional javascript options for the dialog plugin
			'options'=>array(
					'title'=>'Validation congé',
					'autoOpen'=>false,
					'modal'=>true,
					'resizable'=>false,
					'width'=>'600',
					'buttons'=> $tblBoutonR ,
					'open'=>'js:function(){'.CHtml::ajax(array(
							'type'=>'GET',
							'url'=>array('congeValider'),
							'data'=>array('id'=>'js:$(this).data("lien").attr("id")'),
							//'async'=>'false',
							'success'=>'js:function(result){
						$("#mydialogR").empty();
						$("#mydialogR").append(result);
				}'
					)).'}',
					'beforeClose'=>'js:function(){$("#mydialogR").empty();}'
			),
	));
	
	$this->endWidget('zii.widgets.jui.CJuiDialog');
	
endif;//Fin if?>
</div>