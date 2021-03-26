<?php
$this->breadcrumbs=array(
	'Documents',
);
Yii::app()->clientScript->registerScript('updateLstDocument','
	$("#lstCaserne").on("change", function(event){
		caserne = $("select#lstCaserne").val();
		type = $("select#lstType").val();
		window.location = "index.php?r=document/index&caserne="+caserne+"&type="+type;
	});
');

$this->menu=array(
	array('label'=>'Retour à la liste', 'url'=>array('index')),
	array('label'=>'Créer un document', 'url'=>array('create'),'visible'=>Yii::app()->user->checkAccess('Document:create')),
);

?>
<!--
<div class="equipeMini">
	<div class="premier"></div><div class="view">
		<?php //echo CHtml::label('Caserne : ', 'lstCaserne'); ?>
		<?php //echo CHtml::dropDownList('lstCaserne', $caserne, $dataCaserne); ?>	
	</div><div class="centreRRH"></div>
</div>
-->
<?php

	$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_viewSuivi',
	'template'=>'{items}<div style="clear:both"></div>{pager}',	
)); ?>
