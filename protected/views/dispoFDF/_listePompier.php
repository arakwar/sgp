<?php
	echo $titre.'<br/><br/>';
	$this->widget('zii.widgets.grid.CGridView',array(
		'dataProvider'=>$dataUsager,
		'columns'=>array('matricule','prenom','nom'),
		'template'=>'{items}',
		'hideHeader'=>'true'
		//'itemView'=>'/usager/_viewMini',
		//'summaryText'=>'',
		//'itemsCssClass'=>'usager'
	));
	echo $titre2.'<br/><br/>';
	$this->widget('zii.widgets.grid.CGridView',array(
			'dataProvider'=>$dataUsagerNDispo,
			'columns'=>array('matricule','prenom','nom'),
			'template'=>'{items}',
			'hideHeader'=>'true'
			//'itemView'=>'/usager/_viewMini',
			//'summaryText'=>'',
			//'itemsCssClass'=>'usager'
	));
?>