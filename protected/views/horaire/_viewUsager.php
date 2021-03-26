<?php
	$this->widget('zii.widgets.CListView',array(
		'dataProvider'=>$dataUsager,
		'itemView'=>'/horaire/_listeUsager',
		'template'=>'{items}{pager}',
		'itemsCssClass'=>'usager',
		'updateSelector'=>'#lstUsager a',
		'id'=>'lstUsager',
		'afterAjaxUpdate'=>'function(id, data){refreshTableauHeures();}',
		'viewData'=>array('parametres'=>$parametres)
	));
?>