<?php 
	$roles = Yii::app()->authManager->getRoles();
	
	$listRole = array();
	foreach($roles as $role){
		if($role->name != 'GesHoraire'){
			$listRole[$role->name] = $role->name;
		}
	}
	
	echo CHtml::dropDownList('lstDroits,'',$listeRole); ?>
?>