<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/notification.css');
$this->breadcrumbs=array(
	'Notifications',
);
?>

<h1>Notifications</h1>
<div class="document span-20" style="margin:10px;">
	<?php $this->widget('zii.widgets.CListView', array(
		'dataProvider'=>$dataProvider,
		'itemView'=>'_view',
	)); ?>
</div>