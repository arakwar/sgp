<?php
$this->breadcrumbs=array(
	'Groupes',
);

Yii::app()->clientScript->registerScript('updateLstQuarts','
	$("#lstCaserne").on("change", function(event){
		id = $("select#lstCaserne").val();
		window.location = "index.php?r=groupe/index&caserne="+id;
	});
');

$this->menu=array(
	array('label'=>'Créer une équipe spécialisée', 'url'=>array('create')),
);
?>

<div class="equipeMini">
	<div class="premier"></div><div class="view">
		<?php echo CHtml::label('Caserne : ', 'lstCaserne'); ?>
		<?php echo CHtml::dropDownList('lstCaserne', $caserne, $dataCaserne); ?>	
	</div>
</div>

<?php
$this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
