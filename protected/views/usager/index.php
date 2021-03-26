<?php
Yii::app()->clientScript->registerScript('updateLstUsager','
		
$("#lstEquipe").on("change", function(event){
	id = $("select#lstEquipe").val();
	caserne = $("select#lstCaserne").val();
	$.fn.yiiListView.update("lstUsager",{data:{"caserne":caserne,"equipe":id}});
});
');

$this->breadcrumbs=array(
	'Usagers',
);

$this->menu=array(
	array('label'=>'Créer un usager', 'url'=>array('create'),'visible'=>Yii::app()->user->checkAccess('Usager:create')),
);
?>
<div id="listeEquipe">
	<div class="equipeMini">
		<div class="premier"></div><div class="view">
			<?php echo CHtml::label('Caserne : ', 'lstCaserne'); ?>
			<?php echo CHtml::dropDownList('lstCaserne', $caserne, $dataCaserne,array('empty'=>array(0=>'- Tous -'),
											'onchange'=>CHtml::ajax(array(
													'url'=>array('usager/getEquipe'),
													'type'=>'POST',
													'cache'=>true,
													'data'=>array('caserne'=>'js:this.options[this.selectedIndex].value'),
													'success'=>'function(result){
																	jQuery("#lstEquipe").html(result);
																	jQuery("#lstEquipe").val(0);
																	jQuery("#lstEquipe").change();
																}',
													)
												))); ?>
		</div><div class="centreRRH"></div><div class="view">
			<?php echo CHtml::label('Équipe : ', 'lstEquipe'); ?>
			<?php echo CHtml::dropDownList('lstEquipe', $equipe, $dataEquipe,array('empty'=>array(0=>'- Tous -'),
					)); ?>
		</div>
	</div>
</div>
<?php 
$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'itemsCssClass'=>'usager',
	//'updateSelector'=>'#listeEquipe a',
	'id'=>'lstUsager',
	'template'=>'{items}{pager}'
)); ?>
