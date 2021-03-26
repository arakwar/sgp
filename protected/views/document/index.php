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
	
	$("#lstType").on("change", function(event){
		caserne = $("select#lstCaserne").val();
		type = $("select#lstType").val();
		window.location = "index.php?r=document/index&caserne="+caserne+"&type="+type;
	});
');

$this->menu=array(
	array('label'=>'Créer un document', 'url'=>array('create'),'visible'=>Yii::app()->user->checkAccess('Document:create')),
);

if(isset($accueil)){
	$accueil=true;
}else{
	$accueil=false;
?>

<div class="equipeMini">
	<div class="premier"></div><div class="view">
		<?php echo CHtml::label('Caserne : ', 'lstCaserne'); ?>
		<?php echo CHtml::dropDownList('lstCaserne', $caserne, $dataCaserne); ?>	
	</div><div class="centreRRH"></div><div class="view">
		<?php echo CHtml::label('Type : ', 'lstType'); ?>
		<?php echo CHtml::dropDownList('lstType', $type, $dataType); ?>
	</div>
</div>

<?php
}

	$view = (($accueil)?'../document/_view':'_view');
	$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>$view,
	'viewData'=>array('accueil'=>$accueil),
	'template'=>'{items}<div style="clear:both"></div>{pager}',	
)); ?>
