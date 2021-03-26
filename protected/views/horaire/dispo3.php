<?php
$this->breadcrumbs=array(
	'Horaire',
);
$arrayMois = array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");
//Cette déclaration a été mis au début du code alors qu,elle ne sera utilisé que plus loin pounr 1 raison : on a besoin de savoir le jour de semaine de début de la période affiché.
$dateParcours = new DateTime($periode->dateDebut,new DateTimeZone($parametres->timezone));
$diff = $parametres->nbJourPeriode - $parametres->moduloDepotDispo;
$dateDepot = date_add(new DateTime($periode->dateDebut,new DateTimeZone($parametres->timezone)),new DateInterval("P".$diff."D"));
$today = new DateTime('now',new DateTimeZone($parametres->timezone));

 //the javascript that doing the job
 $script = "function changePage(){
             window.location = '".$this->createUrl('horaire/dispo',array('dateDebut'=>$dateParcours->format("Ymd")))."&idUsager='+document.getElementById('lstUsager').value+'&caserne='+document.getElementById('lstCaserne').value;
}";
Yii::app()->clientScript->registerScript('js1', $script, CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/dispoHoraire.css');

Yii::app()->clientScript->registerScript('tabDispo',<<<EOT

$("div.vertical-text.parDefaut").bind('click',function(){
	$("div.vertical-text.dispo").removeClass('active');
	$("#tab1").hide();
	$("div.vertical-text.parDefaut").addClass('active');
	$("#tab2").show();
});
		
$("div.vertical-text.dispo").bind('click',function(){
	$("div.vertical-text.parDefaut").removeClass('active');
	$("#tab2").hide();
	$("div.vertical-text.dispo").addClass('active');
	$("#tab1").show();
});

EOT
,CClientScript::POS_READY);

?>

<div id="btnControl" class="verticalEntete">
	<div class="vertical-text dispo active"> </div>
	<div class="vertical-text parDefaut"> </div>
</div>

<div id="tab1">
	<?php 
		$this->renderPartial('_dispo_tabDispo',array(
							'caserne'=>$caserne,
							'tblUsager'=>$tblUsager,
							'tblCaserne'=>$tblCaserne,
							'usager'=>$usager,
							'dateParcours'=>$dateParcours,
							'siMax'=>$siMax,
							'parametres'=>$parametres,
							'arrayMois'=>$arrayMois,
							'siPrecedente'=>$siPrecedente,
							'periode'=>$periode,
							'tblQuart'=>$tblQuart,
							'jourSemaine'=>$jourSemaine,
							'tblEquipeGarde'=>$tblEquipeGarde));
	
	
	?>
</div>
<div id="tab2">
<?php 
		$this->renderPartial('_dispo_tabDefaut',array(
			'caserne'=>$caserne,
			'tblUsager'=>$tblUsager,
			'tblCaserne'=>$tblCaserne,
			'usager'=>$usager,
			'dateParcours'=>$dateParcours,
			'siMax'=>$siMax,
			'parametres'=>$parametres,
			'arrayMois'=>$arrayMois,
			'siPrecedente'=>$siPrecedente,
			'periode'=>$periode,
			'tblQuart'=>$tblQuart,
			'jourSemaine'=>$jourSemaine,
			'tblEquipeGarde'=>$tblEquipeGarde));
	?>
	
</div>