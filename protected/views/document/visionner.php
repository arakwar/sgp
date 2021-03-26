<?php
$this->breadcrumbs=array(
	'Visionner'=>array('index'),
);

$this->menu=array(
	array('label'=>'Liste des documents', 'url'=>array('index')),
);
?>
<h2 style="text-align:center"><?php echo $document->nom ?></h2>
<div class="document span-19" style="margin:10px;min-height:165px;">
	<div class="video" style="margin:auto; width:560px;">
		<iframe width="560" height="315" src="<?php echo $document->url?>" frameborder="0" allowfullscreen></iframe>
	</div>
</div>
