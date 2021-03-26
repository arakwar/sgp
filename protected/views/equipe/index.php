<?php
$this->breadcrumbs=array(
	'Equipes',
);

Yii::app()->clientScript->registerScript('updateLstEquipe','
	$("#lstCaserne").on("change", function(event){
		id = $("select#lstCaserne").val();
		window.location = "index.php?r=equipe/index&caserne="+id;
	});
');

$this->menu=array(
	array('label'=>'Créer une équipe', 'url'=>array('create')),
	array('label'=>'Ordre des équipes FDF', 'url'=>array('ordre')),
);
?>

<div class="equipeMini">
	<div class="premier"></div><div class="view">
		<?php echo CHtml::label('Caserne : ', 'lstCaserne'); ?>
		<?php echo CHtml::dropDownList('lstCaserne', $caserne, $dataCaserne,array('empty'=>array(0=>'- Tous -'))); ?>	
	</div>
</div>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'itemsCssClass'=>'items',
	'template'=>'{items}<div style="clear:both"></div>{pager}',
	'pager'=>array('pageSize'=>12),
)); ?>
