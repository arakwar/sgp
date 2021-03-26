<?php
$row = $curseurDispo->read();
$arrayMois = array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");
//Cette déclaration a été mis au début du code alors qu'elle ne sera utilisé que plus loin pounr 1 raison : on a besoin de savoir le 
//jour de semaine de début de la période affiché.
$dateParcours = new DateTime( $row['Jour'],new DateTimeZone($parametres->timezone));

$diff = $parametres->nbJourPeriode - $parametres->moduloDepotDispo;
$dateDepot = date_sub(new DateTime( $row['Jour'],new DateTimeZone($parametres->timezone)),new DateInterval("P".$diff."D"));

//refaire ste barrure la
$today = new DateTime('now',new DateTimeZone($parametres->timezone));
$siPasBarrer = true;

if($today>=$dateDepot) {
	if($parametres->dispo_horaire_debarre == 0 /*&& $valide==1*/){ //retiré pour rdl
		$siPasBarrer = false; /**************REMETTRE A FALSE POUR QUE LES DISPOS SE BARRENT*************/
	}
	if(Yii::app()->user->checkAccess('gesCaserne') || Yii::app()->user->checkAccess('gesService')){
		$siPasBarrer = true;
	}
}

 //the javascript that doing the job
 $script = "function changePage(){
 			var url = '".$this->createUrl('horaire/dispo',array('date'=>$dateParcours->format("Ymd")))."';
 			if(document.getElementById('lstUsager'))
 				url += '&idUsager='+document.getElementById('lstUsager').value;
 			 if(document.getElementById('lstCaserne'))
 				url += '&caserne='+document.getElementById('lstCaserne').value;
             window.location = url;
}";
Yii::app()->clientScript->registerScript('js1', $script, CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('js/jquery_json.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/dispoHoraire.css');
if($parametres->dispoParHeure==1){
	Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/dispoHoraire2.css');
}
?>

<div id="btnControl" class="verticalEntete hidden-mobile">
	<div class="vertical-text dispo active"> </div>
	<div class="vertical-text parDefaut"> </div>
</div>

<div id="tab1" class="<?php if($siPasBarrer) echo "modifiable";?>">
	<div class=""><div class="SSCal_conteneur2">
	<div class="SSCal_header" style="clear:both;">
		<div class="enTeteDiv">
			<div class="enTeteSec dernier"><?php
			if(!$siMax)
			echo CHtml::link('►',array("dispo","idUsager"=>$usager->id,"date"=>$dateSuivante->format("Ymd"),"caserne"=>$caserne));?></div>
			<div class="enTeteSec centreGRH milieu"></div>
			<div class="enTeteSec milieu texte">
				<?php echo $dateParcours->format("d")." ".$arrayMois[$dateParcours->format("n")]." ".$dateParcours->format("Y");?></div>
			<div class="enTeteSec centreRGH milieu"></div>
			<div class="enTeteSec milieu"><?php 
			echo CHtml::link('◄',array("dispo","idUsager"=>$usager->id,"date"=>$datePrecedente->format("Ymd"),"caserne"=>$caserne));?></div>
			<?php if($siPasBarrer):?>
			<div class="enTeteSec centreRRH milieu hidden-mobile"></div>
			<div class="enTeteSec milieu hidden-mobile">
				<?php echo CHtml::link('Copier les dispo par défaut',array("horaire/defautDispo",'caserne'=>$caserne,'date'=>$row['Jour'],'usagerid'=>$usager->id));?>
			</div>
			<?php endif; if(count($tblUsager)>0):?>
				<div class="enTeteSec centreRRH milieu hidden-mobile"></div>
				<div class="enTeteSec milieu hidden-mobile"><?php echo CHtml::dropDownList('lstUsager',$usager->id,$tblUsager,array('onChange'=>"changePage();"));?></div>
			<?php endif;?>	
			<div class="enTeteSec centreRRH milieu hidden-mobile"></div>
			<div class="enTeteSec milieu hidden-mobile"><?php echo CHtml::dropDownList('lstCaserne',$caserne,$tblCaserne,array('onChange'=>"changePage();",
																										'options'=>$tblCasernesDisabled));?></div>
			<div class="enTeteSec premier"></div>
		</div>
	</div>
	<div class="SSCal_content">
		<table class="SSCal_table dispoPeriode" cellpadding="0" cellspacing="0" style="position:relative;">
			<!--  <tr class="parHeure">
				<td></td>
				<td colspan="7" style="position:relative;">
					<div style="position:absolute; width:100%;margin-top:30px;">
						<?php 
							for ($i = 0; $i < 24; $i++) {
								echo '<div class="grille" style=""></div>';
							}
						?>
					</div>
				</td>
			</tr> -->
		<?php
			//Boucle pour l'affichage de la grille des dispo de la période
			$moduloActuel=$parametres->moduloDebut;
			$siModifiable = true;
			$ajd = new DateTime(null,new DateTimeZone($parametres->timezone)); //aujourdhui
			$dP = new DateTime($row['Jour'], new DateTimeZone($parametres->timezone)); //debut periode
			$dP->sub(new DateInterval("P".$parametres->moduloDepotDispo."D")); //on recule du début dla periode pour savoir la date ou faut ca barre
			if($ajd>=$dP) $siModifiable = false; //si la datte du jour est >= a celle qui faut ca barre, ca barre.
			
			$indexJourDepart = $indexJour = date('w',strtotime($row['Jour']));
			if($parametres->horaire_mensuel)
				$indexJourDepart = $indexJour = 0;
			$indexSemaine = 0;
			$premiereSemaine = true;
			$debut_mois = true;
			$dispoSepareAvant = array('0'=>false); //Pour les dispos séparés avant le jour actuel (le quart finit dans la journée suivante)
			$dispoSepareApres = array('0'=>false); //Pour les dispos séparés après le jour actuel 
			do{
				$jourEnCours = $row['Jour'];
				$dateJour = new DateTime($jourEnCours);
			
				if($indexJour==$indexJourDepart){
					$bufferQuart = '<tr><td class="jour"><div>';
					if($premiereSemaine)
						$bufferQuart .= '<img class="toutclock" src="'.Yii::app()->baseUrl.'/images/clock.png"/>';
					$bufferQuart .='</div>';
					$bufferJour = "";
					if($premiereSemaine){
						for ($decalage=0; $decalage < $dateJour->format('w'); $decalage++) { 
							$bufferJour .= '<td></td>';
							$indexJour++;
						}
					}
					$premiereSemaine = false;
					$bufferJour .= "<td><div>".$jourSemaine[$dateJour->format('w')]." - ".substr($jourEnCours,8,2)."</div>";
				}else{
					echo "<td><div>".$jourSemaine[$dateJour->format('w')]." - ".substr($jourEnCours,8,2)."</div>";
				}
			
				do{
					$quart = $row['Quart'];
					if($indexJour==$indexJourDepart || $debut_mois){
						$heuresQuart = Quart::diffHeures($row['qHeureDebut'],$row['qHeureFin']);
						$bufferQuart .= '<div semaine="'.$indexSemaine.'" quart_id="'.$row['quart_id'].'" oheight="'.($heuresQuart*20).'">'.$quart.
															'<img class="clock" src="'.Yii::app()->baseUrl.'/images/clock.png"/></div>';
						$bufferJour .= '<div usager_id="'.$usager->id.'" semaine="'.$indexSemaine.'" date="'.$row['Jour'].'" quart_id="'.$row['quart_id'].'" modulo="'.$moduloActuel.
									   '" style="background-color:#'.$row['couleur'].'"><div class="heure"><span class="heureDebut">'.$row['qHeureDebut'].'</span><span class="heureFin">'.$row['qHeureFin'].'</span></div><div class="caseNoire"> ';
					}else{
						echo '<div date="'.$row['Jour'].'" usager_id="'.$usager->id.'" semaine="'.$indexSemaine.'" quart_id="'.$row['quart_id'].'" modulo="'.$moduloActuel.'" style="background-color:#'.
																$row['couleur'].'"><div class="heure"><span class="heureDebut">'.$row['qHeureDebut'].'</span><span class="heureFin">'.$row['qHeureFin'].'</span></div><div class="caseNoire"> ';
					}
					$ajustement = 0;
					do{
						if($dispoSepareApres['0']!=false){
							$heureDebut = $row['qHeureDebut'];
							$heureFin = $dispoSepareApres['1'];
							$heuresQuart = Quart::diffHeures($row['qHeureDebut'],$row['qHeureFin']);
							$pourcentDebut = Quart::diffHeures($row['qHeureDebut'],$heureDebut);
							$top = ($pourcentDebut/$heuresQuart)*100;
							$heures = Quart::diffHeures($heureDebut,$heureFin);
							$hauteur = ($heures/$heuresQuart)*100;
							//$top = $top - $ajustement;
							if($indexJour==$indexJourDepart || $debut_mois){
								$bufferJour .= '<div dispo_id="'.$dispoSepareApres['0'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
							}else{
								echo '<div dispo_id="'.$dispoSepareApres['0'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
							}
							$dispoSepareApres['0']=false;
						}
						if($row['dispo']=="0"){
							if($dispoSepareAvant['0']==false){
								if($row['dhHeureDebut']===NULL){
									$heureDebut = $row['qHeureDebut'];
								}else{
									$heureDebut = $row['dhHeureDebut'];
								}
								if($row['dhHeureFin']===NULL){
									$heureFin = $row['qHeureFin'];
								}else{
									$FdispoSepareApres = false;
									if($row['qHeureFin']>$row['qHeureDebut'] && $row['dhHeureFin']>$row['qHeureFin']){
										$FdispoSepareApres = true;
									}else{
										if($row['qHeureFin']<$row['qHeureDebut']){
											$hfQ = substr($row['qHeureFin'],0,strpos($row['qHeureFin'],':'));
											if($row['dhHeureFin'] > $row['dhHeureDebut']){
												$hfQ += 24;
											}
											$heureFinQuart = $hfQ.substr($row['qHeureFin'],strpos($row['qHeureFin'],':'),6);
											if($row['dhHeureFin'] > $heureFinQuart){
												$FdispoSepareApres = true;
											}
										}
									}
									if($FdispoSepareApres){
										$heureFin = $row['qHeureFin'];
										$dispoSepareApres['0'] = $row['dispo_id'];
										$dispoSepareApres['1'] = $row['dhHeureFin'];
									}else{
										$heureFin = $row['dhHeureFin'];
									}
								}
								$heuresQuart = Quart::diffHeures($row['qHeureDebut'],$row['qHeureFin']);
								$pourcentDebut = Quart::diffHeures($row['qHeureDebut'],$heureDebut);
								$top = ($pourcentDebut/$heuresQuart)*100;
								$heures = Quart::diffHeures($heureDebut,$heureFin);
								$hauteur = ($heures/$heuresQuart)*100;
								//$top = $top - $ajustement;
								if($indexJour==$indexJourDepart || $debut_mois){
									$bufferJour .= '<div dispo_id="'.$row['dispo_id'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
								}else{
									echo '<div dispo_id="'.$row['dispo_id'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
								}
								$ajustement += $hauteur+$top;
							}else{
								$heureDebut = $dispoSepareAvant['1'];
								$heureFin = $row['dhHeureFin'];
								$heuresQuart = Quart::diffHeures($row['qHeureDebut'],$row['qHeureFin']);
								$pourcentDebut = Quart::diffHeures($row['qHeureDebut'],$heureDebut);
								$top = ($pourcentDebut/$heuresQuart)*100;
								$heures = Quart::diffHeures($heureDebut,$heureFin);
								$hauteur = ($heures/$heuresQuart)*100;
								//$top = $top - $ajustement;
								if($indexJour==$indexJourDepart || $debut_mois){
									$bufferJour .= '<div dispo_id="'.$row['dispo_id'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
								}else{
									echo '<div dispo_id="'.$row['dispo_id'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
								}
								$dispoSepareAvant['0']=false;								
							}
						}
						$heureQuartDebut = $row['qHeureDebut'];//Permet d'avoir les heures de l'ancien quarts
						$heureQuartFin = $row['qHeureFin'];
						$dernierJour = $row['Jour'];
						$row = $curseurDispo->read();
						if($row['Jour']!=$dernierJour){
							if($row['dhHeureDebut']!==NULL && $row['dhHeureDebut']<$row['qHeureDebut']){
								$heureDebut = $row['dhHeureDebut'];
								$heureFin = $heureQuartFin;
								$heuresQuart = Quart::diffHeures($heureQuartDebut,$heureQuartFin);
								$pourcentDebut = Quart::diffHeures($heureQuartDebut,$heureDebut);
								$top = ($pourcentDebut/$heuresQuart)*100;
								$heures = Quart::diffHeures($heureDebut,$heureFin);
								$hauteur = ($heures/$heuresQuart)*100;
								//$top = $top - $ajustement;
								if($indexJour==$indexJourDepart || $debut_mois){
									$bufferJour .= '<div dispo_id="'.$row['dispo_id'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
								}else{
									echo '<div dispo_id="'.$row['dispo_id'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
								}
								$dispoSepareAvant['0']=$row['dispo_id'];
								$dispoSepareAvant['1']=$heureQuartFin;								
							}
						}
					}while($quart == $row['Quart']);
					if($indexJour==$indexJourDepart || $debut_mois){
						$bufferJour .= '</div></div>';
					}else{
						echo '</div></div>';
					}
				}while($row['Jour']==$jourEnCours);
				if($indexJour==$indexJourDepart || $debut_mois){
					echo $bufferQuart.'</td>'.$bufferJour.'</td>';
				}else{
					echo '</td>';
				}
				$indexJour++;
				$debut_mois = false;
				if($indexJour>6){
					$indexJour=0;
				}
				if($indexJour==$indexJourDepart){
					$indexSemaine++;
					echo "</tr>";
				}
				$moduloActuel++;
				if($moduloActuel>$parametres->nbJourPeriode-1){
					$moduloActuel=0;
				}
				//unset($dateJour);
			}while($row!==false);
		?>
		</table>
	</div>
	<div class="SSCal_footer"></div>
	</div></div>
</div>
<div id="tab2" class="modifiable">
<div class="">
<div class="SSCal_conteneur2">
	<div class="SSCal_header">
		<div class="enTeteDiv">
			<div class="enTeteSec dernier">Disponibilité par défaut</div>
			<div class="enTeteSec premier"></div>
		</div>
	</div>
	<div class="SSCal_content">
		<table class="SSCal_table dispoDefaut" cellpadding="0" cellspacing="0">
		<?php
		$indexJour = 0;
		$indexSemaine = 0;
		$row = $curseurDispoDefaut->read();
		$moduloActuel=$parametres->moduloDebut;
		$premiereSemaine = true;			
		$dispoSepareAvant = array('0'=>false); //Pour les dispos séparés avant le jour actuel (le quart finit dans la journée suivante)
		$dispoSepareApres = array('0'=>false); //Pour les dispos séparés après le jour actuel
		do{
			$jourEnCours = $row['Jour'];
			//$dateJour = new DateTime($jourEnCours);
		
			if($indexJour==0){
				$bufferQuart = '<tr><td class="jour"><div>'.($premiereSemaine?'<img class="toutclock" src="'.Yii::app()->baseUrl.'/images/clock.png"/>':'').
								'</div>';
				$premiereSemaine = false;
				$bufferJour = "<td><div>".$jourSemaine[$indexJour]."</div>";
			}else{
				echo "<td><div>".$jourSemaine[$indexJour]."</div>";
			}
		
			do{
				$quart = $row['Quart'];
				if($indexJour==0){
					$heuresQuart = Quart::diffHeures($row['qHeureDebut'],$row['qHeureFin']);
					$bufferQuart .= '<div semaine="'.$indexSemaine.'" quart_id="'.$row['quart_id'].'" oheight="'.($heuresQuart*20).'">'.$quart.
														'<img class="clock" src="'.Yii::app()->baseUrl.'/images/clock.png"/></div>';
					$bufferJour .= '<div usager_id="'.$usager->id.'" semaine="'.$indexSemaine.'" date="'.$row['Jour'].'" quart_id="'.$row['quart_id'].'" modulo="'.$moduloActuel.
								   '" style="background-color:#'.$row['couleur'].'"><div class="caseNoire"> ';
				}else{
					echo '<div date="'.$row['Jour'].'" usager_id="'.$usager->id.'" semaine="'.$indexSemaine.'" quart_id="'.$row['quart_id'].'" modulo="'.$moduloActuel.'" style="background-color:#'.
															$row['couleur'].'"><div class="caseNoire"> ';
				}
				$ajustement = 0;
				do{
					if($dispoSepareApres['0']!=false){
						$heureDebut = $row['qHeureDebut'];
						$heureFin = $dispoSepareApres['1'];
						$heuresQuart = Quart::diffHeures($row['qHeureDebut'],$row['qHeureFin']);
						$pourcentDebut = Quart::diffHeures($row['qHeureDebut'],$heureDebut);
						$top = ($pourcentDebut/$heuresQuart)*100;
						$heures = Quart::diffHeures($heureDebut,$heureFin);
						$hauteur = ($heures/$heuresQuart)*100;
						//$top = $top - $ajustement;
						if($indexJour==0){
							$bufferJour .= '<div dispo_id="'.$dispoSepareApres['0'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
						}else{
							echo '<div dispo_id="'.$dispoSepareApres['0'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
						}
						$dispoSepareApres['0']=false;
					}
					if($row['dispo']=="0"){
						if($dispoSepareAvant['0']==false){
							if($row['dhHeureDebut']===NULL){
								$heureDebut = $row['qHeureDebut'];
							}else{
								$heureDebut = $row['dhHeureDebut'];
							}
							if($row['dhHeureFin']===NULL){
								$heureFin = $row['qHeureFin'];
							}else{
								if($row['dhHeureFin']>$row['qHeureFin']){
									$heureFin = $row['qHeureFin'];
									$dispoSepareApres['0'] = $row['dispo_id'];
									$dispoSepareApres['1'] = $row['dhHeureFin'];
								}else{
									$heureFin = $row['dhHeureFin'];
								}
							}
							$heuresQuart = Quart::diffHeures($row['qHeureDebut'],$row['qHeureFin']);
							$pourcentDebut = Quart::diffHeures($row['qHeureDebut'],$heureDebut);
							$top = ($pourcentDebut/$heuresQuart)*100;
							$heures = Quart::diffHeures($heureDebut,$heureFin);
							$hauteur = ($heures/$heuresQuart)*100;
							//$top = $top - $ajustement;
							if($indexJour==0){
								$bufferJour .= '<div dispo_id="'.$row['dispo_id'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
							}else{
								echo '<div dispo_id="'.$row['dispo_id'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
							}
							$ajustement += $hauteur+$top;
						}else{
							$heureDebut = $dispoSepareAvant['1'];
							$heureFin = $row['dhHeureFin'];
							$heuresQuart = Quart::diffHeures($row['qHeureDebut'],$row['qHeureFin']);
							$pourcentDebut = Quart::diffHeures($row['qHeureDebut'],$heureDebut);
							$top = ($pourcentDebut/$heuresQuart)*100;
							$heures = Quart::diffHeures($heureDebut,$heureFin);
							$hauteur = ($heures/$heuresQuart)*100;
							//$top = $top - $ajustement;
							if($indexJour==0){
								$bufferJour .= '<div dispo_id="'.$row['dispo_id'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
							}else{
								echo '<div dispo_id="'.$row['dispo_id'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
							}
							$dispoSepareAvant['0']=false;								
						}
					}
					$heureQuartDebut = $row['qHeureDebut'];//Permet d'avoir les heures de l'ancien quarts
					$heureQuartFin = $row['qHeureFin'];
					$dernierJour = $row['Jour'];
					$row = $curseurDispoDefaut->read();
					if($row['Jour']!=$dernierJour){
						if($row['dhHeureDebut']!==NULL && $row['dhHeureDebut']<$row['qHeureDebut']){
							$heureDebut = $row['dhHeureDebut'];
							$heureFin = $heureQuartFin;
							$heuresQuart = Quart::diffHeures($heureQuartDebut,$heureQuartFin);
							$pourcentDebut = Quart::diffHeures($heureQuartDebut,$heureDebut);
							$top = ($pourcentDebut/$heuresQuart)*100;
							$heures = Quart::diffHeures($heureDebut,$heureFin);
							$hauteur = ($heures/$heuresQuart)*100;
							//$top = $top - $ajustement;
							if($indexJour==0){
								$bufferJour .= '<div dispo_id="'.$row['dispo_id'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
							}else{
								echo '<div dispo_id="'.$row['dispo_id'].'" class="caseDispo" style="height:'.$hauteur.'%; top:'.$top.'%;"><div class="petitx"></div></div>';
							}
							$dispoSepareAvant['0']=$row['dispo_id'];
							$dispoSepareAvant['1']=$heureQuartFin;								
						}
					}
				}while($quart == $row['Quart']);
				if($indexJour==0){
					$bufferJour .= '</div></div>';
				}else{
					echo '</div></div>';
				}
			}while($row['Jour']==$jourEnCours);
			if($indexJour==0){
				echo $bufferQuart.'</td>'.$bufferJour.'</td>';
			}else{
				echo '</td>';
			}
			$indexJour++;
			if($indexJour>6){
				$indexJour=0;
				echo "</tr>";
				$indexSemaine++;
			}
			$moduloActuel++;
			if($moduloActuel>$parametres->nbJourPeriode-1){
				$moduloActuel=0;
			}
			//unset($dateJour);
		}while($row!==false);
		?>
		</table>
	</div>
	<div class="SSCal_footer"></div>
</div>
</div>

</div>
<?php 
$cs = Yii::app()->clientScript;
$cs->registerCoreScript('jquery.ui');
$cs->registerCssFile($cs->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css'); 
Yii::app()->clientScript->registerScript('actionHorloge',
'var siPasBarrer = '.($siPasBarrer?'true':'false').';'.
<<<EOT

$('img.clock').live('click',function(){
		semaine = $(this).parent().attr("semaine");
		quart = $(this).parent().attr("quart_id");
		oheight = $(this).parent().attr("oheight");
		ceci = $(this);
		ligne = ceci.parentsUntil('table','tr');
		dispoLigne = ligne.find("div[quart_id="+quart+"] div.caseDispo");
		if(ceci.hasClass("opened")){
			ceci.removeClass("opened");
			ligne.find("div[quart_id="+quart+"] div.caseNoire").removeClass("opened");
			ligne.find("div[quart_id="+quart+"] div.heure").removeClass("opened");
			if(siPasBarrer) dispoLigne.resizable("disable");
			dispoLigne.each(function(){
				var parent = $(this).parent();
			    $(this).css({
			       	top: $(this).position().top/parent.height()*100+"%",
			      	height: $(this).height()/parent.height()*100+"%"
			    });
			});
			ligne.find("div[quart_id="+quart+"]").animate({height:30},500,function(){
				if(ligne.parent().find("img.clock.opened").length<1){
					ligne.parent().find('img.toutclock').removeClass("opened");
				}
			});
		}else{
			ceci.addClass("opened");
			ligne.find("div[quart_id="+quart+"] div.caseNoire").addClass("opened");
			ligne.find("div[quart_id="+quart+"] div.heure").addClass("opened");
			ligne.find("div[quart_id="+quart+"]").animate({height:oheight},500,'swing',function(){
				if(ligne.parent().find("img.clock:not(.opened)").length<1){
					ligne.parent().find('img.toutclock').addClass("opened");
				}
				$(this).find("div.caseDispo").each(function(e){
				    var divCase = $(this);
					var calculTop = divCase.position().top;
					calculTop = Math.round(calculTop/10)*10;
					var calculHeight = divCase.height();
					calculHeight = Math.floor(calculHeight/10)*10;
					divCase.css({
				       	top: calculTop+"px",
				      	height: calculHeight+"px"
				    });
				});
				if(siPasBarrer) dispoLigne.resizable("enable");
			});
			
		}
});
		
$('img.toutclock').live('click',function(){
		clock = $(this);
		if(clock.hasClass("opened")){
			clock.parentsUntil('div.SSCal_content','table').find("img.clock.opened").click();
		}else{
			clock.parentsUntil('div.SSCal_content','table').find("img.clock:not(.opened)").click();
		}
});
EOT
,CClientScript::POS_READY);

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


Yii::app()->clientScript->registerScript('gestionHeures',<<<EOT
		
$(".modifiable div.caseNoire:not(.opened)").live("click touchstart",function(){ //quand les semaines sont fermée
	var ceci = $(this);
	var tblDispo = ceci.find("div.caseDispo");
	if(tblDispo.length<1){ // si ya pas de dispo dans la case
		tblData = {	'date':ceci.parent().attr("date"),
						'idQuart':ceci.parent().attr("quart_id"),
						'idUsager':ceci.parent().attr("usager_id"),};
EOT
.(CHtml::ajax(array(
				'type'=>'GET',
				'url' =>array('horaire/coche'),
				'cache'=>false,
				'data'=>"js:tblData",
				'success'=>'function(result){
					if(result!="0"){ // le ajax retourne toujours un 0 à la fin, il ajoute des nombres avants si ca passe
						divAjout = $(\'<div class="caseDispo" dispo_id="\'+result/10+\'" style="height:100%; top:0%;"><div class="petitx"></div></div>\');
						$(divAjout).appendTo(ceci);
						$(divAjout).resizable(tblResizable);
						$(divAjout).resizable("disable");
					}else{
						alert("Une erreur est survenue lors de l\'ajout de la disponibilité.");
					}
				}',
				'error'=>'function(){
					alert("Une erreur est survenue lors de la requête.");
				}',
			))).
<<<EOT
	}else{
		var tblId = [];
		tblDispo.each(function(x){
			tblId.push($(this).attr("dispo_id"));
		});
		tblData = {'strDispoId':$.json.encode(tblId)};
EOT
.(CHtml::ajax(array(
				'type'=>'GET',
				'url' =>array('horaire/decoche'),
				'cache'=>false,
				'data'=>"js:tblData",
				'success'=>'function(result){
					if(result!="0"){ // le ajax retourne toujours un 0 à la fin, il ajoute des nombres avants si ca passe
						var results = result.split(";");
						var length = results.length;
						for (var i = 0; i < length; i++) {
							if(results[i]!=0){
								$("div[dispo_id="+results[i]+"]").each(function(j,el){
								  $(el).remove();
								});
							}				
						}
					}else{
						alert("Une erreur est survenue lors de la suppression de la disponibilité.");
					}
				}',
				'error'=>'function(){
					alert("Une erreur est survenue lors de la requête.");
				}',
			))).		
<<<EOT
		
	}
});
		
$(".modifiable div.caseNoire.opened div.petitx").live("click",function(){

		var parent = $(this).parent();
		var jour   = parent.parent(); 
		jour.append('<div class="ajaxLoading"></div>');
		tblId = [parent.attr("dispo_id")];
		tblData = {'strDispoId':$.json.encode(tblId)};
		
EOT
.(CHtml::ajax(array(
				'type'=>'GET',
				'url' =>array('horaire/decoche'),
				'cache'=>false,
				'data'=>"js:tblData",
				'success'=>'function(result){
					if(result!="0"){ // le ajax retourne toujours un 0 à la fin, il ajoute des nombres avants si ca passe
						var results = result.split(";");
						var length = results.length;
						for (var i = 0; i < length; i++) {
							if(results[i]!=0){
								$("div[dispo_id="+results[i]+"]").each(function(j,el){
								  $(el).remove();
								});
							}				
						}
					}else{
						alert("Une erreur est survenue lors de la suppression de la disponibilité.");
					}
				}',
				'error'=>'function(){
					alert("Une erreur est survenue lors de la requête.");
				}',
				'complete'=>'function(){
					jour.find("div.ajaxLoading").remove();
				}'
			))).		
<<<EOT
		
});
		
function fResizeStop(ui,originalSize,originalPosition){
	ui.parent().append('<div class="ajaxLoading"></div>');
	precedent = getPrecedent(ui);
	suivant = getSuivant(ui);
	oldDiv = null;
	oldDiv2 = null;
	newHeight = ui.height();
	newTop = ui.position().top;		

	if(suivant.length>0){
		if(ui.position().top+ui.height() >= suivant.position().top){
			newHeight = newHeight + suivant.height();
			oldDiv = suivant;
		}
	}
	if(precedent.length>0){
		if(ui.position().top <= precedent.position().top+precedent.height()){
			newHeight = newHeight + precedent.height();
			newTop = precedent.position().top;
			oldDiv2 = precedent;
		}
	}
	
	tblData = {	'date':ui.parent().parent().attr("date"),
				'idQuart':ui.parent().parent().attr("quart_id"),
				'idDispo':ui.attr("dispo_id"),
				'idUsager':ui.parent().parent().attr("usager_id"),
				'heureDebut':newTop,
				'heureFin':newHeight,};
	strID = "";
	if(oldDiv!=null){
		strID = oldDiv.attr("dispo_id")+", ";
	}
	if(oldDiv2!=null){
		strID += oldDiv2.attr("dispo_id")+", ";
	}
	if(strID!=""){
		strID = strID.slice(0,-2);
		tblData['idAncienDispo'] = strID;
	}
EOT
.(CHtml::ajax(array(
				'type'=>'GET',
				'url' =>array('horaire/coche'),
				'cache'=>false,
				'data'=>"js:tblData",
				'success'=>'function(result){
					if(result!="0"){ // le ajax retourne toujours un 0 à la fin, il ajoute des nombres avants si ca passe
						if(oldDiv != null){
							ui.height(newHeight);
							ui.css({top: newTop});
							oldDiv.resizable("destroy");
							oldDiv.remove();
						}
						if(oldDiv2 != null){
							ui.height(newHeight);
							ui.css({top: newTop});
							oldDiv2.resizable("destroy");
							oldDiv2.remove();
						}
						if(typeof originalSize == "undefined"){
							ui.attr("dispo_id",result/10);
						}
					}else{
						if(typeof originalSize == "undefined"){
							ui.resizable("destroy");
							ui.remove();
						}else{
							ui.height(originalSize);
							ui.css({top: originalPosition});
						}
					}
				}',
				'error'=>'function(){
					if(typeof originalSize == "undefined"){
						ui.resizable("destroy");
						ui.remove();
					}else{
						ui.height(originalSize);
						ui.css({top: originalPosition});
					}
				}',
				'complete'=>'function(){
					ui.parent().find("div.ajaxLoading").remove();
					$(ui).siblings("div.caseDispo").resizable("enable");
					$(ui).resizable({maxHeight: null });
				}'
			))).
<<<EOT
}
		
function getPrecedent(ui){
	return 	$(ui).siblings().filter(function(i){
		   		return $(this).position().top+$(this).height() <= ui.position().top+1;
	   		}).last();
}

function getSuivant(ui){
	return 	$(ui).siblings().filter(function(d){
				return $(this).position().top+1 >= ui.position().top+ui.height();
			}).first();
}
		
var tblResizable = {
		grid:[40,10], 
		handles:'n, s', 
		containment:'parent',
		autoHide:true,
		start: function(e, ui){
			$(ui.element).siblings("div.caseDispo").resizable("disable");
			activeHandle = $(e.originalEvent.target);
			if(activeHandle.hasClass("ui-resizable-n")){
				precedent = getPrecedent(ui.element);
				if(precedent && precedent.length>0){
					$(this).resizable({maxHeight: ui.size.height+ui.position.top-precedent.position().top-precedent.height()});
				}
			}
			else if(activeHandle.hasClass("ui-resizable-s")){
				suivant = getSuivant(ui.element);
				if(suivant && suivant.length>0){
					$(this).resizable({maxHeight: suivant.position().top-ui.position.top});
				}
			}
		}, 
		stop: function(e, ui) {
			fResizeStop(ui.element,ui.originalSize.height,ui.originalPosition.top);
    	},
	};
		
var currentMousePos = { x: -1, y: -1 };
    $(document).mousemove(function(event) {
        currentMousePos.x = event.pageX;
        currentMousePos.y = event.pageY;
    });		
		
var timeout;
var divAction = null;		
		
$(".modifiable  div.caseNoire.opened").live("mousedown",function(e){
	ceci = $(this);
	if($(e.target).hasClass("caseNoire")){
		pos = $(e.target).offset();
		topDiv = e.pageY-pos.top;
		topDiv = Math.floor(topDiv/10)*10;
		divAction = divAjout = $('<div class="caseDispo" style="height:10px; top:'+topDiv+'px;"><div class="petitx"></div></div>');
		$(divAjout).appendTo(ceci);
		$(divAjout).siblings("div.caseDispo").resizable("disable");
		var mouseStart = e.pageY;
		suivant = getSuivant(divAjout);
		if(suivant.length>0){
			var maxHeight = suivant.position().top-$(divAjout).position().top;
		}else{
			var maxHeight = ceci.height()-$(divAjout).position().top;
		}
		
		timeout = setInterval(function(){
			siEnBas = Math.round((currentMousePos.y - mouseStart)/10)*10;
			if(siEnBas>0){
				if(siEnBas>maxHeight) siEnBas = maxHeight;
				$(divAjout).height(siEnBas);
			}else{
				$(divAjout).height(10);
			}
		},25);
	}
	return false;
});
		
$(document).mouseup(function(){
	if(divAction!=null){
		clearInterval(timeout);
		$(divAction).resizable(tblResizable);
		fResizeStop(divAction);
		divAction = null;
	}
    
    return false;
});
		
$(".modifiable div.caseNoire div.caseDispo").resizable(tblResizable);

$(".modifiable div.caseNoire div.caseDispo").resizable("disable");

EOT
,CClientScript::POS_READY);

//à faire : copier dispo par défaut dans horaire, cocher la dispo au complet,
// copier de caserne vers caserne
// ajouter des indication d'heures qui suivent les dispo
?>