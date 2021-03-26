<?php
$arrayMois = array("","janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre");
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/horaire.css');
//$dateDebut = new DateTime($periode->dateDebut."T00:00:00",new DateTimeZone("America/Montreal"));
Yii::app()->clientScript->registerScriptFile('js/jquery_json.js');
Yii::app()->clientScript->registerScript('changeCaserne','
			function changePage(caserne){
		             window.location = "'.$this->createUrl('horaire/indexTP',array('date'=>$dateDebut->format('Y-m-d'))).'&caserne="+caserne;
		}',CClientScript::POS_HEAD
);
Yii::app()->clientScript->registerScript('updateHoraire','
		
		/*compteHeures(pompier,semaine) Compte les heures totales du pompier(matricule) pour la semaine X. Retourne le total*/
		
		function compteHeures(pompier,semaine)
		{
			var heuresTotal = 0;
			$("div[semaine="+semaine+"][pompier="+pompier+"]").each(function(j,contenu){
				heuresTotal = heuresTotal*1 + $(contenu).attr("heures")*1;
			});
			return heuresTotal;
		}
		
		function compteHeuresPeriode(pompier)
		{
			var heuresTotal = 0;
			$("div[pompier="+pompier+"]").each(function(j,contenu){
				heuresTotal = heuresTotal*1 + $(contenu).attr("heures")*1;
			});
			return heuresTotal;
		}
		
		//permet de mettre les heures dans la liste des pompiers a gauche
		function refreshTableauHeures()
		{
			$("span.afficheHeure").each(function(){
				var ceci = $(this);
				var total = compteHeures($(ceci).attr("usager"),$(ceci).attr("date"));
				$(ceci).empty();
				$(ceci).append(total);
				$(ceci).attr("nbHeures",total);
			});
			
			$("span.totalHeure").each(function(){
				var ceci = $(this);
				var total = 0;
				$("span.afficheHeure[usager="+$(ceci).attr("usager")+"]").each(function(){
					caseHoraire = $(this);
					total = total*1 + $(caseHoraire).attr("nbHeures")*1;
				});
				$(ceci).empty();
				$(ceci).append(total);
			});
		}
		
		//valide les heures pour lhoraire en entier
		function validerHeures(){
			$("div.formPoste").find("div[posteH][pompier!=\"\"]").addClass("parcours");
			$("div.formPoste").find("div[posteH][pompier]").removeClass("warning");
			$("div.formPoste").find("div[posteH][pompier]").parent().find(".label").removeClass("warning");
			
			while($("div.parcours").length>0){
				var ceci = $("div.parcours").filter(":first");
				$("div.parcours[semaine="+$(ceci).attr("semaine")+"][pompier="+$(ceci).attr("pompier")+"]").removeClass("parcours");
				var heuresTotal = compteHeures($(ceci).attr("pompier"),$(ceci).attr("semaine"));
				if(heuresTotal>'.$parametres->heureMaximum.'){
					$("div[semaine="+$(ceci).attr("semaine")+"][pompier="+$(ceci).attr("pompier")+"]").addClass("warning");
					$("div[semaine="+$(ceci).attr("semaine")+"][pompier="+$(ceci).attr("pompier")+"]").parent().find(".label").addClass("warning");
				}/*else{
					$("div[semaine="+$(ceci).attr("semaine")+"][pompier="+$(ceci).attr("pompier")+"]").removeClass("warning");
				}*/
			}
		}
		
		//permet de valider les heures des pompiers pour 1 semaine seulement
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
				}*/
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
		
		/* validerHoraire() Passe lhoraire en revue, met en rouge les cases avec erreurs (2 fois le même gars dans les mêmes heures)*/
		function validerHoraire(){
			validerHeures();
			$("div.formPoste").find("div[posteH][pompier!=\"\"]").addClass("parcours");
			$("div.formPoste").find("div[posteH][pompier]").removeClass("error");
			$("div.formPoste").find("div[posteH][pompier]").parent().find(".label").removeClass("error");
			timerHoraire = setInterval(traitementHoraire,100);			
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
				'data'=>"js:{usager:$(input).val(),date:$(input).parent().parent().attr('date'),poste:$(input).parent().attr('posteH'),caserne:".$caserne."}",
				'success'=>'function(result){
					if(result!=""){
						tblResult = $.json.decode(result);
						$(input).val(tblResult["matricule"]);
						$(input).parent().attr("pompier",tblResult["matricule"]);
					}else{
						$(input).val("");
						$(input).parent().attr("pompier","");
					}
					if($(input).attr("indexCaseHoraire")=="0"){
						$(input).parent().parent().children("div.label").html($(input).val());
					}
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
		validerHoraire();
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
		dateDebut = y+m+j;
		'.
		CHtml::ajax(array(
			'type'=>'GET',
			'url'=>array('indexTP'),
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
					$("#valider").addClass("valider");
					$("#imprimer").removeClass("valider");
					validerHoraire();
				}else{
					$("a.FDFnavigate[choix=\'last\']").css("visibility","visible");
					$("#valider").removeClass("valider");
					$("#imprimer").addClass("valider");
				}
				if($("#siSuivant").val()=="0"){
					$("a.FDFnavigate[choix=\'last\']").css("visibility","hidden");
				}else{
					$("a.FDFnavigate[choix=\'last\']").css("visibility","visible");
				}
				refreshTableauHeures();
			}'
		))
		.'
	});

	$("#valider.valider").live("click",function(){
		var ceci = $(this);
		$(ceci).removeClass("valider");
		$("#dialogValider").dialog("open");
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
	
	$(".formPoste:not(.opened)").live("click",function(){
		$(".opened").animate({height:30,width:75,paddingTop:0},500,function(){$(this).removeClass("opened");});
		$(".opened .label").animate({marginTop:0},250);
		$(ceci).children(".caseDispo").empty();
		var ceci = $(this);
		$(ceci).addClass("opened");
		$(ceci).animate({height:340,width:425},500);
		$(ceci).children(".label").animate({marginTop:-30},250);
		'./*.
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
						total = compteHeures(ligneHeures.parent().attr("valeur"),$(ceci).attr("semaine"));
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
					
				}'
			))).*/'
	});
	$(".formPoste .close").live("click",function(){
		var ceci = $(this).parent();
		$(ceci).animate({height:30,width:75,paddingTop:0},500,function(){$(ceci).removeClass("opened");});
		$(ceci).children(".label").animate({marginTop:0},250);
		$(ceci).children(".caseDispo").empty();
		
	});
');
?>
<div class="barreTour">
	<div style="background-image:url(images/coinRougeDB.png);border-bottom:none;"></div>
	<div id="valider" class="texte gris valider" style="background-image:url(images/degradeRouge40.png);"> Vérifier l'horaire</div>
	<div style="background-image:url(images/coinRougeGB.png);border-top:none;margin-top:1px;"></div>
</div>
<div class="span-10" <?php echo (($parametres->colonneGauche==1)?'':'style="display:none;"'); ?>>
<div id="listeEquipe">
<?php 
	$this->widget('zii.widgets.CListView',array(
		'dataProvider'=>$listeEquipe,
		'itemView'=>'/horaire/_viewEquipe',
		//'summaryText'=>'',
		'itemsCssClass'=>'equipeMini',
		'template'=>'{items}',
		'separator'=>'<div class="centreRRH"></div>',
		'viewData'=>array('fichier'=>'TP'),
	));
?><div class="equipeMini">
		<div class="premier"></div><div class="view"><?php echo CHtml::link('Afficher toute les équipes',array('index')); ?></div></div>
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
<table class="SSCal_table_h">
<?php 

$this->renderPartial('_horaireTP',array(
	'tblEquipeGarde'  =>$tblEquipeGarde,
	'jourSemaine'	  =>$jourSemaine,
	'curseurHoraire'  =>$curseurHoraire,
	'parametres'	  =>$parametres,
	'caserne'		  => $caserne,
	'garde'			  => $garde,
));
?>
</table>
</div>
<?php 

$tblBoutonM = array();
$tblBoutonR = array();

if(Yii::app()->user->checkAccess('createHoraire')){
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
								raison:$('#raison').val(),raisonTxt:$('#raisonTxt').val()}",
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
								raison:$('#raison').val(),raisonTxt:$('#raisonTxt').val()}",
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
				'data'=>array('date'=>'js:$(this).data("diviseur").parent().parent().attr("date")','posteHoraire'=>'js:$(this).data("diviseur").parent().attr("posteH")'),
				//'async'=>'false',
				'success'=>'js:function(result){
						$("#mydialog").empty();
						$("#mydialog").append(result);
				}'
			)).'}',
		'beforeClose'=>'js:function(){$("#mydialog").empty();}'
    ),
));
?>
<?php 
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<?php 
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
				'data'=>array('date'=>'js:$(this).data("diviseur").parent().parent().attr("date")','posteHoraire'=>'js:$(this).data("diviseur").parent().attr("posteH")','type'=>'js:$(this).data("diviseur").parent().attr("typeH")'),
				//'async'=>'false',
				'success'=>'js:function(result){
						$("#mydialogR").empty();
						$("#mydialogR").append(result);
				}'
			)).'}',
		'beforeClose'=>'js:function(){$("#mydialogR").empty();}'
    ),
));
?>
<?php 
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
					array('text'=>'Fermer cette fenêtre','click'=>'js:function(){$(this).dialog("close");}')
				) ,
				'open'=>'js:function(){
						bouton = $("#dialogValider").next(".ui-dialog-buttonpane").find("button:contains(\'horaire\')")
						bouton.button("disable");
						$(".error").removeClass("error");
						$(".error").removeClass("warning");
						$("#dialogValider").append("Validation de l\'horaire en cours.");
						'.CHtml::ajax(array(
						'type'=>'GET',
						'url'=>array('valider'),
						'data'=>array('date'=>$dateDebut->format('Y-m-d'),
									  'caserne'=>'js:$("#lstCaserne").val()'),
							//'async'=>'false',
							'success'=>'js:function(result){
										if(result==2){
											$("#dialogValider").empty();
											$("#dialogValider").append("La validation d\'un horaire de travail vide est impossible.");
										} else {
										tblResult = $.json.decode(result);
										$("#dialogValider").empty();
										$("#dialogValider").append("Avertissements : "+tblResult["W"].length+"<br/>Erreurs : "+tblResult["E"].length);
										for(erreur in tblResult["E"]){
					var tblElementErreur = $("div.formPoste[date=\'"+tblResult["E"][erreur]["jour"]+"\']")
					.find("div[posteh=\'"+tblResult["E"][erreur]["posteHoraire"]+"\'][pompier=\'"+tblResult["E"][erreur]["pompier"]+"\']");
												console.log(tblElementErreur);
												tblElementErreur.addClass("error");tblElementErreur.parent().find(".label").addClass("error");
										}
										for(erreur in tblResult["W"]){
					var tblElementWarning = $("div.formPoste[semaine=\'"+tblResult["W"][erreur]["semaine"]+"\']")
					.find("div[pompier=\'"+tblResult["W"][erreur]["pompier"]+"\']");
												console.log(tblElementWarning);
												tblElementWarning.addClass("warning");tblElementWarning.parent().find(".label").addClass("warning");
										}
										if(tblResult["E"].length<1) bouton.button("enable");
									}
								}',
							'error'=>'js:function(){
									$("#dialogValider").empty();
									$("#dialogValider").append("Une erreur est survenue lors de la validation de l\'horaire.");
								}',
							'complete'=>'js:function(){
									$("#valider").addClass("valider");
							}
						',
						)).'}',
						'beforeClose'=>'js:function(){$("#dialogValider").empty();}'
		),
));
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>