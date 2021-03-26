	<?php 

		$this->widget('zii.widgets.CListView',array(
				'dataProvider'=>$dataDispo,
				'itemView'=>'_rapportDecoche',
				'id'=>'ajaxListView',
				'template'=>'{items}<div style="clear:both"></div>{pager}',
		));

	?>