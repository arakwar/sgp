<?php
/*	//permet de valider les heures des pompiers pour 1 semaine seulement
		function validerHeuresSemaine(semaine){
			$("div.formPoste[semaine="+semaine+"]").find("div[posteH][pompier!=\"\"]").addClass("parcours");
			$("div.formPoste[semaine="+semaine+"]").find("div[posteH][pompier]").removeClass("warning");
			$("div.formPoste[semaine="+semaine+"]").find("div[posteH][pompier]").parent().find(".label").removeClass("warning");
			
			while($("div.parcours").length>0){
				var ceci = $("div.parcours").filter(":first");
				$("div.parcours[semaine="+$(ceci).attr("semaine")+"][pompier="+$(ceci).attr("pompier")+"]").removeClass("parcours");
				var heuresTotal = compteHeures($(ceci).attr("pompier"),$(ceci).attr("semaine"));
				if(heuresTotal>'.$parametres->heureMaximum.'){
					$("div[semaine="+$(ceci).attr("semaine")+"][pompier="+$(ceci).attr("pompier")+"]").addClass("warning");
					$("div[semaine="+$(ceci).attr("semaine")+"][pompier="+$(ceci).attr("pompier")+"]").parent().find(".label").addClass("warning");
				}/*else{
					$("div[semaine="+$(ceci).attr("semaine")+"][pompier="+$(ceci).attr("pompier")+"]").removeClass("warning");
				}*//*
				$("span.afficheHeure[usager="+$(ceci).attr("pompier")+"][date="+$(ceci).attr("semaine")+"]").empty();
				$("span.afficheHeure[usager="+$(ceci).attr("pompier")+"][date="+$(ceci).attr("semaine")+"]").append(heuresTotal);
				$("span.afficheHeure[usager="+$(ceci).attr("pompier")+"][date="+$(ceci).attr("semaine")+"]").attr("nbHeures",heuresTotal);
				var total = 0;
				$("span.afficheHeure[usager="+$(ceci).attr("pompier")+"]").each(function(){
					caseHoraire = $(this);
					total = total*1 + $(caseHoraire).attr("nbHeures")*1;
				});
				$("span.totalHeure[usager="+$(ceci).attr("pompier")+"]").empty();
				$("span.totalHeure[usager="+$(ceci).attr("pompier")+"]").append(total);
			}
			$("div.heures").each(function(){
						var ligneHeures = $(this);
						var total = 0;
						total = compteHeures(ligneHeures.parent().attr("valeur"),ligneHeures.parent().attr("semaine"));
						$(ligneHeures).empty();
						$(ligneHeures).append(total);
			});
			$("div.heuresPeriode").each(function(){
						var ligneHeures = $(this);
						var total = 0;
						total = compteHeuresPeriode(ligneHeures.parent().attr("valeur"));
						$(ligneHeures).empty();
						$(ligneHeures).append(total);
			});
		}
		
		//utilisé lors du traitement des erreurs dhoraire
		var timerHoraire = "";
		function traitementHoraire(){
			var ceci = $("div.parcours").filter(":first");
			$(ceci).removeClass("parcours");
			var tsDebut = $(ceci).attr("tsDebut");
			var tsFin = $(ceci).attr("tsFin");
			if(	
				$("div[posteH][pompier="+$(ceci).attr("pompier")+"]")
				.filter(function(){
					return (tsDebut <= $(this).attr("tsDebut"))&&(tsFin > $(this).attr("tsDebut"));
				})
				.length > 1)
			{
				$(ceci).addClass("error");
				$(ceci).parent().find(".label").addClass("error");
			}
			if(
				$("div[posteH][pompier="+$(ceci).attr("pompier")+"]")
				.filter(function(){
					return (tsDebut < $(this).attr("tsFin"))&&(tsFin >= $(this).attr("tsFin"));
				})
				.length > 1)
			{
				$(ceci).addClass("error");
				$(ceci).parent().find(".label").addClass("error");
			}
			if($("div.parcours").length<1){
				clearInterval(timerHoraire);
			}
		}
		
		/* validerHoraire() Passe lhoraire en revue, met en rouge les cases avec erreurs (2 fois le même gars dans les mêmes heures)*//*
		function validerHoraire(){
			validerHeures();
			$("div.formPoste").find("div[posteH][pompier!=\"\"]").addClass("parcours");
			$("div.formPoste").find("div[posteH][pompier]").removeClass("error");
			$("div.formPoste").find("div[posteH][pompier]").parent().find(".label").removeClass("error");
			timerHoraire = setInterval(traitementHoraire,100);			
		}*/

$userAccess = Yii::app()->user->checkAccess('GesHoraire');
$listeAccess = (($parametres->droitVoirDispoHoraire==0)?Yii::app()->user->checkAccess('GesHoraire'):true);
$arrayMois = array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/horaire.css');
//$dateDebut = new DateTime($periode->dateDebut."T00:00:00",new DateTimeZone("America/Montreal"));
Yii::app()->clientScript->registerScriptFile('js/jquery_json.js');
Yii::app()->clientScript->registerScript('changeCaserne','
			function changePage(caserne){
					var elements = document.getElementsByClassName("formPoste");
					var date = elements[0].getAttribute("date");
		             window.location = "'.$this->createUrl('horaire/index',array()).'&date="+date+"&caserne="+caserne;
		}',CClientScript::POS_HEAD
);
Yii::app()->clientScript->registerScript('updateHoraire','
		
		/*compteHeures(pompier,semaine) Compte les heures totales du pompier(matricule) pour la semaine X. Retourne le total*/
		
		/*VERSION PAR SEMAINE*/
		function compteHeuresSemaine(pompier,semaine)
		{
			var heuresTotal = 0;
			$("div[semaine="+semaine+"][pompier="+pompier+"]").each(function(j,contenu){
				heuresTotal = heuresTotal*1 + $(contenu).attr("heures")*1;
			});
			return heuresTotal;
		}
		
		/*VERSION PAR QUART*/
		function compteHeuresQuart(pompier,quart)
		{
			var heuresTotal = 0;
			$("div[pompier="+pompier+"]").each(function(j,contenu){
				if($(contenu).attr("quarth")==quart){
					heuresTotal = heuresTotal*1 + $(contenu).attr("heures")*1;
				}
			});
			if($("[name="+quart+"-"+pompier+"]").length){
				heuresTotal = heuresTotal + ($("[name="+quart+"-"+pompier+"]").val()*1);
			}
			return heuresTotal;
		}
		
		function compteHeuresPeriode(pompier, quart)
		{
			var heuresTotal = 0;
			$("div[pompier="+pompier+"]").each(function(j,contenu){
				if(quart!=0){
					if($(contenu).attr("quarth")==quart){
						heuresTotal = heuresTotal*1 + $(contenu).attr("heures")*1;
					}
				}else{
					heuresTotal = heuresTotal*1 + $(contenu).attr("heures")*1;
				}
			});
			return heuresTotal;
		}
		
		//permet de mettre les heures dans la liste des pompiers a gauche
		function refreshTableauHeures()
		{
			$("span.totalHeure").attr("nbheures",0).html(0);
			var totalAvancement = $("span.afficheHeure").length;
			var avancement = 0;

			var tblAfficheHeure = $("span.afficheHeure");
			var length = tblAfficheHeure.length;
			var index = 0;

			var process = function(){
				for (; index < length; index++) { 
					var ceci = tblAfficheHeure.get(index);
					var total = compteHeuresSemaine($(ceci).attr("usager"),$(ceci).attr("date"));
					$(ceci).empty();
					$(ceci).append(total);
					$(ceci).attr("nbHeures",total);
					var usager = $(ceci).attr("usager");
					var heuresTotal = $("span.totalHeure[usager="+usager+"]").attr("nbheures");
					heuresTotal = heuresTotal*1 + total*1;
					$("span.totalHeure[usager="+usager+"]").attr("nbheures",heuresTotal).html(heuresTotal);
					avancement++;
					$("div.progressBarre").html(avancement+"/"+totalAvancement);
					setTimeout(process, 50);
					index++;
					break;		
				}
			}
			process();
			/*$("span.afficheHeure").each(function(){
			});*/
			
			/*$("span.totalHeure").each(function(){
				var ceci = $(this);
				var total = 0;
				$("span.afficheHeure[usager="+$(ceci).attr("usager")+"]").each(function(){
					caseHoraire = $(this);
					total = total*1 + $(caseHoraire).attr("nbHeures")*1;
				});
				$(ceci).empty();
				$(ceci).append(total);
			});*/
		}
		
		//valide les heures pour lhoraire en entier
		function validerHeures(){
			$("div.formPoste").find("div[posteH][pompier!=\"\"]").addClass("parcours");
			$("div.formPoste").find("div[posteH][pompier]").removeClass("warning");
			$("div.formPoste").find("div[posteH][pompier]").parent().find(".label").removeClass("warning");
			
			while($("div.parcours").length>0){
				var ceci = $("div.parcours").filter(":first");
				$("div.parcours[semaine="+$(ceci).attr("semaine")+"][pompier="+$(ceci).attr("pompier")+"]").removeClass("parcours");
				var heuresTotal = compteHeuresSemaine($(ceci).attr("pompier"),$(ceci).attr("semaine"));
				if(heuresTotal>'.$parametres->heureMaximum.'){
					$("div[semaine="+$(ceci).attr("semaine")+"][pompier="+$(ceci).attr("pompier")+"]").addClass("warning");
					$("div[semaine="+$(ceci).attr("semaine")+"][pompier="+$(ceci).attr("pompier")+"]").parent().find(".label").addClass("warning");
				}/*else{
					$("div[semaine="+$(ceci).attr("semaine")+"][pompier="+$(ceci).attr("pompier")+"]").removeClass("warning");
				}*/
			}
		}
		
		//Permet la validation lors de la modification de une seule case
		function validerHorairePoste(tsDebut, tsFin){
		
			$("div[posteH][pompier]")
				.filter(function(){
					return (tsDebut <= $(this).attr("tsDebut"))&&(tsFin > $(this).attr("tsDebut"));
			}).removeClass("error");
			$("div[posteH][pompier]")
				.filter(function(){
					return (tsDebut < $(this).attr("tsFin"))&&(tsFin >= $(this).attr("tsFin"));
			}).removeClass("error");
			
			$("div[posteH][pompier]")
				.filter(function(){
					return (tsDebut <= $(this).attr("tsDebut"))&&(tsFin > $(this).attr("tsDebut"));
			}).parent().find(".label").removeClass("error");
			$("div[posteH][pompier]")
				.filter(function(){
					return (tsDebut < $(this).attr("tsFin"))&&(tsFin >= $(this).attr("tsFin"));
			}).parent().find(".label").removeClass("error");
			
			$("div[posteH][pompier!=\"\"]")
				.filter(function(){
					return (tsDebut <= $(this).attr("tsDebut"))&&(tsFin > $(this).attr("tsDebut"));
			}).addClass("parcours");
			$("div[posteH][pompier!=\"\"]")
				.filter(function(){
					return (tsDebut < $(this).attr("tsFin"))&&(tsFin >= $(this).attr("tsFin"));
			}).addClass("parcours");
			
			timerHoraire = setInterval(traitementHoraire,10);
		}
		
		$(".textTransparent").live("change",function(){
			$(this).val($(this).val().trim());
			var input = $(this);
			'.
			(CHtml::ajax(array(
				'type'=>'POST',
				'url' =>array('horaire/change'),
				'cache'=>false,
				'data'=>"js:{usager:$(input).val(),date:$(input).parent().parent().attr('date'),
						poste:$(input).parent().attr('posteH'),caserne:".$caserne."}",
				'success'=>'function(result){
					if(result!=""){
						tblResult = $.json.decode(result);
						$(input).val(tblResult["matricule"]);
						$(input).parent().attr("pompier",tblResult["matricule"]);
						//validerHeuresSemaine($(input).parent().attr("semaine"));
						//validerHorairePoste($(input).parent().attr("tsDebut"),$(input).parent().attr("tsFin"));
					}else{
						$(input).val("");
						$(input).parent().attr("pompier","");
						//validerHeuresSemaine($(input).parent().attr("semaine"));
						//validerHorairePoste($(input).parent().attr("tsDebut"),$(input).parent().attr("tsFin"));
					}
					if($(input).attr("indexCaseHoraire")=="0"){
						$(input).parent().parent().children("div.label").html($(input).val());
					}
					'.(($parametres->colonneGauche==1)?'refreshTableauHeures();':'').'
				}'
			)))
		.'
			
		});
	
		$(".SSCal_table_h").delegate( "input", "focus blur", function( event ) {
		    var elem = $(this);
		    elem.parent().click();
		    setTimeout(function() {
		       elem.toggleClass( "focused", elem.is( ":focus" ) );
		    }, 125);
		});
		$(".ddedit_item").live("click",function(){
			$(".textTransparent.focused").val($(this).attr("valeur")).change().blur();
		});

	refreshTableauHeures();
	if($("#statutPeriode").val()==0){
		/*validerHoraire();*/
	}

	var tabmois=new Array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet", "Août","Septembre","Octobre","Novembre","Décembre");
	$("a.FDFnavigate").live("click",function(){
		var ceci = $(this);
		$(ceci).removeClass("FDFnavigate");
		choix = $(ceci).attr("choix");
		d = new Date($("div.formPoste").filter(":"+choix).attr("date"));
		if(choix=="last"){
			d.setDate(d.getDate()+2);
		}else{
			d.setDate(d.getDate()-'.($parametres->nbJourPeriode-2).');
		}
		y = d.getFullYear();
		m = d.getMonth()*1+1;
		if(m<10){
			m = 0+m.toString();
		}else{
			m = m.toString();
		}
		j = d.getDate();
		if(j<10){
			j = 0+j.toString();
		}
		dateDebut = y+"-"+m+"-"+j;
		'.
		CHtml::ajax(array(
			'type'=>'GET',
			'url'=>array('index'),
			'data'=>array('date'=>'js:dateDebut','caserne'=>$caserne),
			'success'=>'js:function(result){
				$(".SSCal_table_h").empty();
				$(".SSCal_table_h").append(result);
				var texteJour = "";
				tblDateDebut = $("div.formPoste").filter(":first").attr("date").split("-");
				tblDateFin   = $("div.formPoste").filter(":last").attr("date").split("-");
				texteJour = "Du "+tblDateDebut[2]*1+" "+tabmois[tblDateDebut[1]*1]+" au "+tblDateFin[2]*1+" "+tabmois[tblDateFin[1]*1];
				$("#dateJour").empty();
				$("#dateJour").append(texteJour);
				$(ceci).addClass("FDFnavigate");
				if($("#siPrec").val()=="0"){
					$("a.FDFnavigate[choix=\'first\']").css("visibility","hidden");
				}else{
					$("a.FDFnavigate[choix=\'first\']").css("visibility","visible");
				}
				if($("#statutPeriode").val()==0){
					$("a.FDFnavigate[choix=\'last\']").css("visibility","hidden");
					//validerHoraire();
				}else{
					$("a.FDFnavigate[choix=\'last\']").css("visibility","visible");

				}
				if($("#statutHoraire").val()==0){
					$("#importer").addClass("valider");
					$("#valider").addClass("valider");
					$("#imprimer").removeClass("valider");
				}else{
					$("#importer").removeClass("valider");
					$("#valider").removeClass("valider");
					$("#imprimer").addClass("valider");					
				}
				if($("#siSuivant").val()=="0" && $("#statutPeriode").val()=="0"){
					$("a.FDFnavigate[choix=\'last\']").css("visibility","hidden");
				}else{
					$("a.FDFnavigate[choix=\'last\']").css("visibility","visible");
				}
				'.(($parametres->colonneGauche==1)?'refreshTableauHeures();':'').'
			}'
		))
		.'
	});

	$("#valider.valider").live("click",function(){
		var ceci = $(this);
		$(ceci).removeClass("valider");
		$("#importer").removeClass("valider");
		$("a.FDFnavigate[choix=\'first\']").css("visibility","hidden");
		$("a.FDFnavigate[choix=\'last\']").css("visibility","hidden");
		$("#dialogValider").dialog("open");
	});
	
	$("#imprimer.valider").live("click",function(){
		var dateDebut = $(".case .formPoste").first().attr("date");
		window.open("'.$this->createUrl("pdfHoraire").'&date="+dateDebut+"&caserne="+'.$caserne.');
	});
	
	$("#importer.valider").live("click",function(){
		var ceci = $(this);
		$(ceci).removeClass("valider");
		$("a.FDFnavigate[choix=\'first\']").css("visibility","hidden");
		$("a.FDFnavigate[choix=\'last\']").css("visibility","hidden");
		if($("div.error").length==0){
		var idPeriode = $("#idPeriode").val();
		var dateDebut = $("div.formPoste").filter(":first").attr("date");
		'.
		CHtml::ajax(array(
			'type'=>'GET',
			'url'=>array('importerTP'),
			'data'=>array('date'=>'js:dateDebut','caserne'=>$caserne),
			'success'=>'js:function(result){
				if(result==1){
					$(ceci).addClass("valider");
					'.
					CHtml::ajax(array(
						'type'=>'GET',
						'url'=>array('index'),
						'data'=>array('date'=>'js:dateDebut','caserne'=>$caserne),
						'success'=>'js:function(result){
							$(".SSCal_table_h").empty();
							$(".SSCal_table_h").append(result);
							var texteJour = "";
							tblDateDebut = $("div.formPoste").filter(":first").attr("date").split("-");
							tblDateFin   = $("div.formPoste").filter(":last").attr("date").split("-");
							texteJour = "Du "+tblDateDebut[2]*1+" "+tabmois[tblDateDebut[1]*1]+" au "+tblDateFin[2]*1+" "+tabmois[tblDateFin[1]*1];
							$("#dateJour").empty();
							$("#dateJour").append(texteJour);
							$(ceci).addClass("valider");
							if($("#siPrec").val()=="0"){
								$("a.FDFnavigate[choix=\'first\']").css("visibility","hidden");
							}else{
								$("a.FDFnavigate[choix=\'first\']").css("visibility","visible");
							}
							$("a.FDFnavigate[choix=\'last\']").css("visibility","visible");
							$("#valider").removeClass("valider");
							$("#imprimer").addClass("valider");
						}'
					))
				.'
				}else{
					alert("L\'importation a échouée.");
				}
			}'
		))
		.'
		}else{
			$(ceci).addClass("valider");
		}		
	});
	
	$(".modif").live("click",function(){
		$("#mydialog").data("diviseur",$(this)).dialog("open");
	});
	
	$(".remp").live("click",function(){
		$("#mydialogR").data("diviseur",$(this)).dialog("open");
	});
	
	$("#tblDispoModif tr.ligne").live("click",function(){
		$("#txtModifPompier").val($(this).attr("matricule"));
	});
	'.(($listeAccess)?'
	$(".formPoste:not(.opened)").live("click",function(){
		$(".opened").animate({height:30,width:75,paddingTop:0},500,function(){$(this).removeClass("opened");});
		$(".opened .label").animate({marginTop:0},250);
		$(ceci).children(".caseDispo").empty();
		var ceci = $(this);
		$(ceci).addClass("opened");
		$(ceci).animate({height:340,width:425},500);
		$(ceci).children(".label").animate({marginTop:-30},250);
		'.
			(CHtml::ajax(array(
				'type'=>'POST',
				'url' =>array('horaire/listeDispo'),
				'cache'=>false,
				'data'=>"js:{date:$(ceci).attr('date'),poste:$(ceci).attr('poste'),quart:$(ceci).attr('quart'),semaine:$(ceci).attr('semaine'),caserne:".$caserne."}",
				'success'=>'function(result){
					$(ceci).children(".caseDispo").empty();
					$(ceci).children(".caseDispo").append(result);
					$(ceci).find("div.heures").each(function(){
						var ligneHeures = $(this);
						var total = 0;
						total = '.(($parametres->horaireCalculHeure==1)?'compteHeuresQuart(ligneHeures.parent().attr("valeur"),ligneHeures.attr("quart"))':'compteHeuresSemaine(ligneHeures.parent().attr("valeur"),ligneHeures.parent().attr("semaine"))').';
						$(ligneHeures).empty();
						$(ligneHeures).append(total);
					});
					$("div.heuresPeriode").each(function(){
						var ligneHeures = $(this);
						var total = 0;
						total = compteHeuresPeriode(ligneHeures.parent().attr("valeur"), '.(($parametres->horaireCalculHeure==1)?'ligneHeures.attr("quart")':'0').');
						$(ligneHeures).empty();
						$(ligneHeures).append(total);
					});
					
				}'
			))).'
	});
	$(".formPoste .close").live("click",function(){
		var ceci = $(this).parent();
		$(ceci).animate({height:30,width:75,paddingTop:0},500,function(){$(ceci).removeClass("opened");});
		$(ceci).children(".label").animate({marginTop:0},250);
		$(ceci).children(".caseDispo").empty();
		
	});
	':'').'
	$(".dispoPartielle").live("hover",function(e){
		var ceci = $(this);
		$(".dispoPart").empty();
		$(".dispoPart").append($(ceci).attr("dispo"));
		$(".dispoPart").css("visibility","visible");
		$(".dispoPart").offset({left: e.pageX, top: e.pageY});
	}),
	$(".dispoPartielle").live("mouseout",function(){
		$(".dispoPart").css("visibility","hidden");
	})
	;
');
?>
	<div class="barreTour">
		<div style="background-image:url(images/coinRougeDB.png);border-bottom:none;"></div>
		<div id="importer" class="texte gris <?php if($valide==NULL || $valide==0) echo 'valider';?>" style="background-image:url(images/degradeRouge40.png);"> Importer</div>
		<div class="centreRRB40"></div>
		<div id="valider" class="texte gris <?php if($valide==NULL || $valide==0) echo 'valider';?>" style="background-image:url(images/degradeRouge40.png);"> Valider</div>
		<div class="centreRRB40"></div>
		<div id="imprimer" class="texte gris <?php if($valide==1) echo 'valider';?>" style="background-image:url(images/degradeRouge40.png);">Enregistrer en Excel</div>
		<div style="background-image:url(images/coinRougeGB.png);border-top:none;margin-top:1px;"></div>
	</div>
<div class="span-10 hidden-mobile" <?php echo (($parametres->colonneGauche==1)?'':'style="display:none;"'); ?>>
<div id="listeEquipe">
	<!-- <div class="progressBarre">&nbsp;</div>  -->
<?php
	$this->widget('zii.widgets.CListView',array(
		'dataProvider'=>$listeEquipe,
		'itemView'=>'/horaire/_viewEquipe',
		//'summaryText'=>'',
		'itemsCssClass'=>'equipeMini',
		'template'=>'{items}',
		'separator'=>'<div class="centreRRH"></div>',
		'viewData'=>array('fichier'=>''),
		'ajaxUpdate'=>'lstUsager',
	));
?><div class="equipeMini">
		<div class="premier"></div><div class="view"><?php echo CHtml::ajaxLink('Afficher toute les équipes',array('index','ajax'=>'lstUsager'),						
							array('success'=>'
								function(result){
									$("#listeUsager").html(result);
									refreshTableauHeures();
								}'));?></div></div>
</div>
<div id="listeUsager">
<?php
$this->renderPartial('_viewUsager',array(
	'dataUsager'	=>	$dataUsager,
	'parametres'	=>	$parametres,
));
?>
</div>

	

</div>
<div class="SSCal_conteneur span-14 last">
	<div class="SSCal_header">
		<div class="enTeteDiv">
			<div class="enTeteSec milieu"><?php echo CHtml::dropDownList('lstCaserne',$caserne,$tblCaserne,array('onChange'=>"js:changePage(this.options[this.selectedIndex].value);"));?></div>
			<div class="enTeteSec premier"></div>
		</div>
	</div>
	<div class="SSCal_table_h">
<?php
$this->renderPartial('_horaire',array(
	'curseurTempsAutre'	=>$curseurTempsAutre,
	'curseurTemps'	=>$curseurTemps,
	'dateAuj'		=> $dateAuj,
	'jourSemaine'	  =>$jourSemaine,
	'curseurHoraire'  =>$curseurHoraire,
	'valide'		  => $valide,
	'parametres'	  =>$parametres,
	'siPeriodePrecedente'=>$siPeriodePrecedente,
	'periodeSuivante' => $periodeSuivante,
	'caserne'		  => $caserne,
	'quarts'		=>$quarts,
	'userAccess'	=>$userAccess,
	'dateDebut'	=>$dateDebut,
	'dateFin' => $dateFin,
	'arrayMois'	=> $arrayMois
	
));
?>
	</table>
	</div>
</div>
<div class="dispoPart">Test</div>
<?php 

$tblBoutonM = array();
$tblBoutonR = array();

if(Yii::app()->user->checkAccess('GesHoraire')){
	$tblBoutonM[0] = array('text'=>'OK','click'=>'js:function(){
				var ceci = $(this);
				if($("#txtModifPompier").val()!=""){
					$("#txtModifPompier").val($("#txtModifPompier").val().trim());
					var input = $("#txtModifPompier");
					'.
					(CHtml::ajax(array(
						'type'=>'GET',
						'url' =>array('horaire/createModif'),
						'cache'=>false,
						'data'=>"js:{usager:$('#txtModifPompier').val(),poste:$('#txtModifPompier').attr('poste'),date:$('#txtModifPompier').attr('date'),
								raison:$('#raison').val(),raisonTxt:$('#raisonTxt').val(),
								caserne:".$caserne."}",
						'success'=>'function(result){
							if(result==1){
							'.
								CHtml::ajax(array(
									'type'=>'GET',
									'url'=>array('index'),
									'data'=>array('date'=>'js:$("#txtModifPompier").attr("date")','caserne'=>$caserne),
									'success'=>'js:function(result){
										$(".SSCal_table_h").empty();
										$(".SSCal_table_h").append(result);
										var texteJour = "";
										tblDateDebut = $("div.formPoste").filter(":first").attr("date").split("-");
										tblDateFin   = $("div.formPoste").filter(":last").attr("date").split("-");
										texteJour = "Du "+tblDateDebut[2]*1+" "+tabmois[tblDateDebut[1]*1]+" au "+tblDateFin[2]*1+" "+tabmois[tblDateFin[1]*1];
										$("#dateJour").empty();
										$("#dateJour").append(texteJour);
										refreshTableauHeures();
									}'
								))
							.'
								$(ceci).dialog("close");
							}else{
								alert("Pompier invalide");
							}
						}'
					)))
				.'
				}
			}');


	$tblBoutonR[0] = array('text'=>'OK','click'=>'js:function(){
				var ceci = $(this);
				if($("#txtModifPompier").val()!=""){
					$("#txtModifPompier").val($("#txtModifPompier").val().trim());
					var input = $("#txtModifPompier");
					'.
					(CHtml::ajax(array(
						'type'=>'GET',
						'url' =>array('horaire/createRemp'),
						'cache'=>false,
						'data'=>"js:{usager:$('#txtModifPompier').val(),poste:$('#txtModifPompier').attr('poste'),date:$('#txtModifPompier').attr('date'),
								heureDebut:$('#txtHeureDebut').val(),heureFin:$('#txtHeureFin').val(),id:$('#idR').val(),type:$('#type').val(),
								caserne:".$caserne."}",
						'success'=>'function(result){
							if(result==1){
							'.
								CHtml::ajax(array(
									'type'=>'GET',
									'url'=>array('index'),
									'data'=>array('date'=>'js:$("#txtModifPompier").attr("date")','caserne'=>$caserne),
									'success'=>'js:function(result){
										$(".SSCal_table_h").empty();
										$(".SSCal_table_h").append(result);
										var texteJour = "";
										tblDateDebut = $("div.formPoste").filter(":first").attr("date").split("-");
										tblDateFin   = $("div.formPoste").filter(":last").attr("date").split("-");
										texteJour = "Du "+tblDateDebut[2]*1+" "+tabmois[tblDateDebut[1]*1]+" au "+tblDateFin[2]*1+" "+tabmois[tblDateFin[1]*1];
										$("#dateJour").empty();
										$("#dateJour").append(texteJour);
										refreshTableauHeures();
									}'
								))
							.'
								$(ceci).dialog("close");
								
							}else{
								alert(result.substr(1));
							}
						}'
					)))
				.'
				}
			}');

}
$tblBoutonM[] = array('text'=>'Fermer','click'=>'js:function(){$(this).dialog("close");}');
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'Modification',
        'autoOpen'=>false,
		'modal'=>true,
		'resizable'=>false,
		'width'=>'600',
		'buttons'=> $tblBoutonM ,
		'open'=>'js:function(){'.CHtml::ajax(array(
				'type'=>'GET',
				'url'=>array('modif'),
				'data'=>array('date'=>'js:$(this).data("diviseur").parent().parent().attr("date")',
							  'posteHoraire'=>'js:$(this).data("diviseur").parent().attr("posteH")',
								'caserne'=>$caserne),
				//'async'=>'false',
				'success'=>'js:function(result){
						$("#mydialog").empty();
						$("#mydialog").append(result);
				}'
			)).'}',
		'beforeClose'=>'js:function(){$("#mydialog").empty();}'
    ),
));

$this->endWidget('zii.widgets.jui.CJuiDialog');

$tblBoutonR[] = array('text'=>'Fermer','click'=>'js:function(){$(this).dialog("close");}');
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialogR',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'Remplacement',
        'autoOpen'=>false,
		'modal'=>true,
		'resizable'=>false,
		'width'=>'600',
		'buttons'=> $tblBoutonR ,
		'open'=>'js:function(){'.CHtml::ajax(array(
				'type'=>'GET',
				'url'=>array('remp'),
				'data'=>array('id'=>'js:$(this).data("diviseur").attr("id")', 'date'=>'js:$(this).data("diviseur").parent().parent().attr("date")',
							  'posteHoraire'=>'js:$(this).data("diviseur").parent().attr("posteH")',
							  'type'=>'js:$(this).data("diviseur").parent().attr("typeH")',
								'caserne'=>$caserne),
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

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
		'id'=>'dialogValider',
		// additional javascript options for the dialog plugin
		'options'=>array(
				'title'=>'Validation',
				'autoOpen'=>false,
				'modal'=>true,
				'resizable'=>false,
				'width'=>'600',
				'buttons'=> array(
					array('text'=>"Fermer l'horaire",'click'=>'js:function(){
bouton = $("#dialogValider").next(".ui-dialog-buttonpane").find("button:contains(\'horaire\')")
						bouton.button("disable");
'.CHtml::ajax(array(
					'type'=>'GET',
					'url'=>array('fermer'),
					'data'=>array('date'=>'js:$("div.formPoste").first().attr("date")',
					'caserne'=>'js:$("#lstCaserne").val()'),
					//'async'=>'false',
					'success'=>'js:function(result){
							 window.location = "'.$this->createUrl('horaire/index').'&date="+$("div.formPoste").first().attr("date")+"&caserne="+$("#lstCaserne").val();
						}',
					'error'=>'js:function(){
						alert("Il y a eu une erreur lors de la fermeture de l\'horaire.");
					}',
					'complete'=>'js:function(){
						bouton.button("enable");
					}',
)).'

}'),
					array('text'=>'Fermer cette fenêtre','click'=>'js:function(){$(this).dialog("close");}')
				) ,
				'open'=>'js:function(){
						bouton = $("#dialogValider").next(".ui-dialog-buttonpane").find("button:contains(\'horaire\')")
						bouton.button("disable");
						$(".error").removeClass("error");
						$(".warning").removeClass("warning");
						$("#dialogValider").append("Validation de l\'horaire en cours.");
						'.CHtml::ajax(array(
						'type'=>'GET',
						'url'=>array('valider'),
						'data'=>array('date'=>'js:$("div.formPoste").first().attr("date")',
									  'caserne'=>'js:$("#lstCaserne").val()'),
							//'async'=>'false',
							'success'=>'js:function(result){
										tblResult = $.json.decode(result);
										if(tblResult[0][0]==2){
											$("#dialogValider").empty();
											$("#dialogValider").append("La validation d\'un horaire de travail vide est impossible.");
										} else {
										$("#dialogValider").empty();
										for(var message in tblResult[0]){
											if(message != 0){
												$("#dialogValider").append(tblResult[0][message]+"<br/>");
											}
										}
										if(tblResult[0].length<2){
											$("#dialogValider").append("Il n\'y a pas d\'erreur dans cet horaire.");
										}
										
										for(erreur in tblResult[1]){
											if("semaine" in tblResult[1][erreur]){
												var tblElementWarning = $("div.formPoste[semaine=\'"+tblResult[1][erreur]["semaine"]+"\']")
													.find("div[pompier=\'"+tblResult[1][erreur]["pompier"]+"\']");
												tblElementWarning.addClass(tblResult[1][erreur]["type"]);tblElementWarning.parent().find(".label").addClass(tblResult[1][erreur]["type"]);
											}
											if("jour" in tblResult[1][erreur]) {
												var tblElementErreur = $("div.formPoste[date=\'"+tblResult[1][erreur]["jour"]+"\']")
													.find("div[posteh=\'"+tblResult[1][erreur]["posteHoraire"]+"\'][pompier=\'"+tblResult[1][erreur]["pompier"]+"\']");
												tblElementErreur.addClass(tblResult[1][erreur]["type"]);tblElementErreur.parent().find(".label").addClass(tblResult[1][erreur]["type"]);
											}
	
										}
										if(tblResult[0][0] == 1) bouton.button("enable");
									}
								}',
							'error'=>'js:function(){
									$("#dialogValider").empty();
									$("#dialogValider").append("Une erreur est survenue lors de la validation de l\'horaire.");
								}',
							'complete'=>'js:function(){
									if($("#siPrec").val()=="0"){
										$("a.FDFnavigate[choix=\'first\']").css("visibility","hidden");
									}else{
										$("a.FDFnavigate[choix=\'first\']").css("visibility","visible");
									}
									$("a.FDFnavigate[choix=\'last\']").css("visibility","visible");
									$("#valider").addClass("valider");
									$("#importer").addClass("valider");
									$("#imprimer").addClass("valider");
							}
						',
						)).'}',
						'beforeClose'=>'js:function(){$("#dialogValider").empty();}'
		),
));
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>