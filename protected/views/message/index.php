<?php
$this->breadcrumbs=array(
	'Messages',
);

Yii::app()->clientScript->registerScript('ajtDest','
	$(".ajouteDestinataire").live("click",function(){
		ceci = $(this);
		//Si n\'est pas déja la
		if($("#listeDestinataire option[value=\'"+$(ceci).attr("courriel")+"\']").length < 1){
			$("#listeDestinataire").append(\'<option value="\'+$(ceci).attr("courriel")+\'">\'+$(ceci).attr("nom")+\'</option>\');
		}
	});

	$("#lstCourriel .view").live("click",function(){
		if($(this).children(".message").is(":visible")){
			$(this).children(".message").fadeOut(300);
		}else{
			$(this).children(".message").fadeIn(300);
		}
	});

	$("#ajtAll").live("click",function(){
		$(".ajouteDestinataire").each(function(){
			if($("#listeDestinataire option[value=\'"+$(this).attr("courriel")+"\']").length < 1){
				$("#listeDestinataire").append(\'<option value="\'+$(this).attr("courriel")+\'">\'+$(this).attr("nom")+\'</option>\');
			}
		});
	});

	$("#listeDestinataire option").live("dblclick",function(){
		$(this).remove();
	});

	$("#videListe").live("click",function(){
		$("#listeDestinataire option").remove();
	});
		
	$("#lstEquipe").on("change", function(event){
		id = $("#lstEquipe").val();
		caserne = $("#lstCaserne").val();
		$.fn.yiiListView.update("lstUsager",{data:{"caserne":caserne,"equipe":id}});
	});
');
?>

<div class="span-12">
<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'id'=>'lstCourriel'
)); ?>
</div>
<div class="span-12 last">
<div id="listeEquipe">
	<div class="equipeMini">
		<div class="premier"></div><div class="view">
			<?php echo CHtml::label('Caserne : ', 'lstCaserne'); ?>
			<?php echo CHtml::dropDownList('lstCaserne', '', $dataCaserne,array('empty'=>array(0=>'- Tous -'),
											'onchange'=>CHtml::ajax(array(
													'url'=>array('usager/getEquipe'),
													'type'=>'POST',
													'cache'=>true,
													'data'=>array('caserne'=>'js:this.options[this.selectedIndex].value'),
													'update'=>"#lstEquipe",
													'success'=>'function(result){
																	jQuery("#lstEquipe").html(result);
																	jQuery("#lstEquipe").val(0);
																	jQuery("#lstEquipe").change();
																}',
													)
												))); ?>
		</div><div class="centreRRH"></div><div class="view">
			<?php echo CHtml::label('Équipe : ', 'lstEquipe'); ?>
			<?php echo CHtml::dropDownList('lstEquipe', '', $dataEquipe,array('empty'=>array(0=>'- Tous -'))); ?>
		</div>
	</div>
	<div class="equipeMini">
		<div class="premier"></div><div class="view"><?php echo CHtml::link('Afficher toute les équipes',array('message/index')); ?></div><div class="centreRRH"></div><div class="view"><span id="ajtAll">Ajouter toute cette liste</span></div></div>
	</div>
<div>
<?php 
	$this->widget('zii.widgets.CListView',array(
		'dataProvider'=>$dataUsager,
		'itemView'=>'/message/_viewUsager',
		//'summaryText'=>'',
		'itemsCssClass'=>'usager',
		'id'=>'lstUsager',
		'template'=>'{items}{pager}'
	));
?>
</div>
</div>
<hr/>
