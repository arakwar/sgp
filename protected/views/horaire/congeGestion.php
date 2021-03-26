<?php
$this->breadcrumbs=array(
	'Avis d\'absence - Gestion avancée',
);

$this->menu=array(
	array('label'=>'Retour à la liste', 'url'=>array('conge')),
	array('label'=>'Remplir un avis', 'url'=>array('congeCreate')),
	array('label'=>'Archives', 'url'=>array('congeArchive')),
);

Yii::app()->clientScript->registerScript('dialogueValidation','		
	$(".btnValider").live("click",function(){
		$("#mydialogR").data("lien",$(this)).dialog("open");
	});
');

Yii::app()->clientScript->registerScript('filtre','
function changePage(filtre){
	window.location = "'.$this->createUrl('horaire/congeGestion').'&filtre="+filtre;
}	
', CClientScript::POS_END);
?>

<div class="enTeteDiv">
	<div class="enTeteSec milieu">
		<?php echo CHtml::dropDownList('lstFiltre',$filtre,$tblFiltre,array('onChange'=>"js:changePage(this.options[this.selectedIndex].value);"));?>
	</div>
	<div class="enTeteSec milieu"></div>
	<div class="enTeteSec premier"></div>
</div>

<div class="row">
<?php
$date = '';$colonne = 0;
foreach($absences as $absence){
	if(strpos($filtre,'dateConge')!==false){
		if($date != $absence->dateConge){
			if($date != ''){
				echo '</div>';
			}
			$date = $absence->dateConge;
			if($colonne == 2){
				$colonne = 0;
				echo '</div>';
				echo '<div class="row">';
			}else{
				$colonne++;
			}
			echo '<div class="span-9 '.(($colonne == 1)?'clear':'last').'">';
			echo '<h3>'.$date.'</h3>';
		}
	}else{
		if($date != $absence->dateEmis){
			if($date != ''){
				echo '</div>';
			}
			$date = $absence->dateEmis;
			if($colonne == 2){
				$colonne = 0;
				echo '</div>';
				echo '<div class="row">';
			}else{
				$colonne++;
			}
			echo '<div class="span-9 '.(($colonne == 1)?'clear':'last').'">';
			echo '<h3>'.$date.'</h3>';
		}	
	}
	echo '<div class="view">';
	echo '<div class="span-9">';
	echo '<b>Demandeur:</b> '.CHtml::encode($absence->tblUsager->getMatPrenomNom()).'<br>';
	echo '<b>Catégorie de congé:</b> '.CHtml::encode($absence->tblType->abrev.' - '.$absence->tblType->nom).'<br>';
	echo '<b>Date:</b> '.CHtml::encode($absence->dateConge).'<br>';
	echo '<b>Quart:</b> '.CHtml::encode($absence->tblQuarts->nom).'<br>';
	echo '<b>De:</b> '.CHtml::encode($absence->heureDebut).' - <b>À:</b> '.CHtml::encode($absence->heureFin).'<br>';
	echo '<b>Date émis:</b> '.CHtml::encode($absence->dateEmis).'<br>';
	echo '<b>'.CHtml::link('Plus de détails', array('congeUpdate', 'id'=>$absence->id)).'</b> ';
	echo '<b>'.CHtml::label('Valider', '', array('class'=>"btnValider", 'id'=>$absence->id, 'Style'=>'color:#06c;display:inline;cursor:pointer;text-decoration:underline;')).'</b> ';
	echo '<b>'.CHtml::link('Imprimer',array('imprimerConge','id'=>$absence->id)).'</b> ';
	echo '</div><div class="span-9 clear" style="float:none"></div>';
	echo '</div>';	
}
?>
	</div>
</div>

<?php

	$tblBoutonR = array();
	$tblBoutonR[] = array('text'=>'Annuler','click'=>'js:function(){$(this).dialog("close");}');
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
			'id'=>'mydialogR',
			// additional javascript options for the dialog plugin
			'options'=>array(
					'title'=>'Validation congé',
					'autoOpen'=>false,
					'modal'=>true,
					'resizable'=>false,
					'width'=>'600',
					'buttons'=> $tblBoutonR ,
					'open'=>'js:function(){'.CHtml::ajax(array(
							'type'=>'GET',
							'url'=>array('congeValider'),
							'data'=>array('id'=>'js:$(this).data("lien").attr("id")'),
							//'async'=>'false',
							'success'=>'js:function(result){
						$("#mydialogR").empty();
						$("#mydialogR").append(result);
				}'
					)).'}',
					'beforeClose'=>'js:function(){$("#mydialogR").empty();}'
			),
	));
	
	$this->endWidget('zii.widgets.jui.CJuiDialog');
	
?>