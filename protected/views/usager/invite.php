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
	array('label'=>'Créer un invité', 'url'=>array('createInvite')),
);
 
$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_viewInvite',
	'itemsCssClass'=>'usager',
	//'updateSelector'=>'#listeEquipe a',
	'id'=>'lstUsager',
	'template'=>'{items}{pager}'
)); ?>
