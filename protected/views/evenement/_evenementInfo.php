<?php	
	$evenementsDateDebut = new DateTime($evenement->dateDebut,new DateTimeZone('America/Montreal'));
	$evenementsDateFin = new DateTime($evenement->dateFin,new DateTimeZone('America/Montreal'));
	echo '<div class="close">X</div>'.
		($evenement->tbl_formation_id == 1? '<p title="'.Yii::t('views', 'evenement.titre.formation').'">'.Yii::t('views', 'evenement.titre.formation').'</p>' : '').
		'<p>'.$evenementsDateDebut->format('Y-m-d').'</p>'.
		'<p>'.$evenementsDateDebut->format('H:i').'</p>'.
		'<p>';
			echo Yii::t('views', 'evenement.titre.a');
		echo '</p>';
		echo ($evenementsDateDebut->format('Y-m-d') != $evenementsDateFin->format('Y-m-d')? '<p>'.$evenementsDateFin->format('Y-m-d').'</p>' : '');
		echo '<p>'.$evenementsDateFin->format('H:i').'</p>'.
		'<p title="'.$evenement->nom.'">'.$evenement->nom.'</p>';
	if(Yii::app()->user->checkAccess('Evenement:create'))
	{
		echo '<p>'.
			CHtml::link('<img class="modif" src="images/edit.png">', array('update', 'id'=>$evenement->id)).
			CHtml::link('<img class="supp" src="images/delete.png">',array('delete','id'=>$evenement['id']),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer ".($evenement->tbl_formation_id == 1? 'cette formation' : 'cet evenement')." ?')")).
		'</p>';
	}
