<?php 
$siPrec = true;
$indiceSemaine = 1;
$indiceJour = 0;
if(!empty($parametres->horaire_mensuel)){
	$indiceJour = $dateDebut->format('w');
}
$row = $curseurHoraire->read();
$rowTemps = (($curseurTemps!== NULL)?$curseurTemps->read():false);
$rowAutre = (($curseurTempsAutre!==NULL)?$curseurTempsAutre->read():false);
$poste = "";
$caseHoraire = "";

echo '<div class="SSCal_header">
	<div class="enTeteDiv">
			<div class="enTeteSec dernier">
				<a href="#" class="FDFnavigate" choix="last"';
				if($userAccess){
					echo '';
				}elseif(!$periodeSuivante){ echo 'style="visibility:hidden;"';}
				
echo '			>►</a>
			</div>
			<div class="enTeteSec centreGRH milieu"></div>
			<div id="dateJour" class="enTeteSec milieu texte" style="font-size:0.9em;">';
echo 'Du '.$dateDebut->format("j")." ".$arrayMois[$dateDebut->format("n")]." au ".$dateFin->format("j")." ".$arrayMois[$dateFin->format("n")];
echo '</div>
			<div class="enTeteSec centreRGH milieu"></div>
			<div class="enTeteSec milieu">
				<a href="#" class="FDFnavigate" choix="first"';
if(!$siPeriodePrecedente && !$userAccess) echo 'style="visibility:hidden;"';
echo '>◄</a>
			</div>
			<div class="enTeteSec centreRRH milieu"></div>
			<div class="enTeteSec milieu">';
$image = CHtml::image('images/symbol34M.png','Mois courant',array('height'=>'30'));
echo CHtml::link($image,array('index', 'caserne'=>$caserne));

echo '</div>
			<div class="enTeteSec premier"></div>
		</div>
</div>
<table>';

/**
 * @var $quart String : Permet d'enregistrer le HTML de chaque quart pour ensuite le monter dans le tableau HTML dans le bon ordre
 * Ce array est réinitialisé à chaque nouvelle semaine
 */
$quart = array();
$debut_du_mois = true;
$output_debut_du_mois = true;
do{
	$jour = $row['Jour'];
	$dateJour = new DateTime($row['Jour']);
	$siPremierDuJour = true;
	do{
		$idQuart = $row['idQuart'];
		if($indiceJour==0 || $debut_du_mois){
			 $quart[$idQuart]['ligne'] = '';
			 $quart[$idQuart]['nom'] = '';
			 if($siPremierDuJour){
			 	$quart[$idQuart]['nom'] .= '<div>';
			 	if($siPrec) {
					$userAccess = Yii::app()->user->checkAccess('GesHoraire');
			 		if(!$siPeriodePrecedente && !$userAccess){
						$quart[$idQuart]['nom'] .= CHtml::hiddenField('siPrec','0');
					} 
					if($userAccess){
						$quart[$idQuart]['nom'] .= CHtml::hiddenField('siSuivant','1');
					}elseif($periodeSuivante){
						$quart[$idQuart]['nom'] .= CHtml::hiddenField('siSuivant','1');
					}else{
						$quart[$idQuart]['nom'] .= CHtml::hiddenField('siSuivant','0');
					}
						
					
					/*if(($userAccess && $valide==0 && $dateJour->format('Y-m-d') >= $dateAuj->format('Y-m-d')) || 
					   (!$userAccess && $periodeSuivante) || 
					   (!$periodeSuivante)) $quart[$idQuart]['nom'] .= CHtml::hiddenField('siSuivant','0');*/
						
					$quart[$idQuart]['nom'] .= CHtml::hiddenField('statutPeriode',(($periodeSuivante)?'1':'0'));
					$quart[$idQuart]['nom'] .= CHtml::hiddenField('statutHoraire',(($valide)?'1':'0'));
					$siPrec = false;
				}
				elseif($siPrec){ $quart[$idQuart]['nom'] .= CHtml::hiddenField('siPrec','1');$siPrec = false;}
				//$quart[$idQuart]['nom'] .= CHtml::hiddenField('idPeriode',$periode->id);
			 	$quart[$idQuart]['nom'] .= '</div>';
			 }
			 $quart[$idQuart]['nomQuart'] = $row['nomQuart'];
		}
		$quart[$idQuart]['ligne'] .= '<td>';
		$quart[$idQuart]['poste'] = '';
		$nbPoste = 0;
		do{
			$idPoste = $row['idPoste'];
			if($siPremierDuJour){
				$quart[$idQuart]['ligne'] .= '<div>'.$jourSemaine[$dateJour->format('w')].' '.$dateJour->format('d').'</div>';
				$quart[$idQuart]['poste'] .= '<div></div>';
				$siPremierDuJour = false;
			}
			$quart[$idQuart]['poste'] .= '<div class="cursor" title="'.$row['Poste'].'">'.$row['diminutifPoste'].'</div>';
			$quart[$idQuart]['ligne'] .= '<div'.
											' class="case"'.
											' style="background-color:#'.$row['couleur_garde'].
											'">
											<div'.
											  ' class="formPoste"'.
											  ' date="'.$dateJour->format('Y-m-d').
											  '" poste="'.$row['idPoste'].
											  '" quart="'.$row['idQuart'].
											  '" semaine="'.$indiceSemaine.
											  '" caserne="'.$caserne.
											  '" style="height: 30px; width: 75px; padding-top: 0px;">';
			$caseHoraire = "";
			$pompierLabel = (($row['absence']!==NULL)?'A ':'').($row['matricule_modification']===NULL?$row['Matricule_horaire']:$row['matricule_modification']);
			$pompierQuart = false;
			$modifQuart = false;
			$indexCaseHoraire = 0;
			do{
				// ------ CASE HORAIRE -------
				if($valide == 0){
					if($row['typeH']==2){
						$modifQuart = true;
					}
					$caseHoraire .= '<div'.
									  ' style="width:395px"'.
					                  ' semaine="'.$indiceSemaine.
					                  '" posteH="'.$row['phId'].
					                  '" quartH="'.$row['idQuart'].
					                  '" heures="'.($row['hHeureReel']>0?$row['hHeureReel']:($row['phHeureReel']>0?$row['phHeureReel']:$row['qHeureReel'])).
					                  '" pompier="'.$row['Matricule_horaire'].
									  '" tsDebut="'.($row['hHeureReel']>0?$row['tsHDebut']:($row['phHeureReel']>0?$row['tsPHDebut']:$row['tsQDebut'])).
									  '" tsFin="'.($row['hHeureReel']>0?$row['tsHFin']:($row['phHeureReel']>0?$row['tsPHFin']:$row['tsQFin'])).
									  '" typeH="'.($row['typeH']===NULL?'0':$row['typeH']).
					                  '">'.CHtml::textField('horaire',(($row['absence']!==NULL)?'A ':'').$row['Matricule_horaire'],array('class'=>'textTransparent',
																								'indexCaseHoraire'=>$indexCaseHoraire,
																								'style'=> 'float:left',
																								));	
				} else {
					if($row['matricule_modification']!=NULL || $row['typeH']==2){
						$modifQuart = true;
					}
					if($row['ID_pompier_horaire']==Yii::app()->user->id){
						$pompierQuart = true;
					}
					$caseHoraire .= '<div'.
									  ' style="width:395px"'.
									  ' semaine="'.$indiceSemaine.
									  '" posteH="'.$row['phId'].
					                  '" quartH="'.$row['idQuart'].
					                  '" heures="'.($row['hHeureReel']>0?$row['hHeureReel']:($row['phHeureReel']>0?$row['phHeureReel']:$row['qHeureReel'])).
					                  '" pompier="'.($row['matricule_modification']===NULL?($row['Matricule_horaire']?$row['Matricule_horaire']:'&nbsp;'):$row['matricule_modification']).
									  '" tsDebut="'.($row['hHeureReel']>0?$row['tsHDebut']:($row['phHeureReel']>0?$row['tsPHDebut']:$row['tsQDebut'])).
									  '" tsFin="'.($row['hHeureReel']>0?$row['tsHFin']:($row['phHeureReel']>0?$row['tsPHFin']:$row['tsQFin'])).
									  '" tsFin="'.($row['phHeureReel']>0?$row['tsQFin']:$row['tsPHFin']).
									  '" typeH="'.($row['matricule_modification']===NULL?($row['typeH']===NULL?'0':$row['typeH']):'1').
									  '">
									  <div style="float:left;">'.
							($row['matricule_modification']===NULL?(($row['absence']!==NULL)?'A ':'').($row['Matricule_horaire']?$row['Matricule_horaire']:'&nbsp;'):'<span style="font-size:17px;">'.$row['matricule_modification'].'</span>').
									 '</div>
									  '.(($userAccess)?'<img src="images/edit.png" class="editH '.(($row['typeH']==2)?'remp':'modif').'" '.(($row['typeH']==2)?'id="'.$row['ID_Horaire'].'"':'').'/>'.(($row['typeH']==2)?CHtml::link(CHtml::image('images/delete.png','X',array('class'=>'editH')),array('deleteRemp','id'=>$row['ID_Horaire'], 'date'=>$dateJour->format('Ymd'), 'caserne'=>$caserne),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer cet item?')")):''):'');
				}
				if($row['typeH']!=2 && $row['Matricule_horaire']!==NULL && $userAccess){
					$caseHoraire .= '<img src="images/switch.png" class="editH remp" id="'.$row['ID_Horaire'].'"/>';
				}
				$caseHoraire .=  '<div style="width:109px; float:left;">'.((($row['hHeureDebut']=='00:00:00' && $row['hHeureFin']=='00:00:00')||($row['hHeureDebut']==NULL && $row['hHeureFin']==NULL))?(($row['phHeureDebut']=='00:00:00' && $row['phHeureFin']=='00:00:00')?substr($row['qHeureDebut'],0,5).' - '.substr($row['qHeureFin'],0,5):substr($row['phHeureDebut'],0,5).' - '.substr($row['phHeureFin'],0,5)):substr($row['hHeureDebut'],0,5).' - '.substr($row['hHeureFin'],0,5)).'</div>';
				if($parametres->horaireCalculHeure==0){
					$caseHoraire .= '</div>';
				}else{
					$strQuart = '';
					foreach($quarts as $q){
						$strQuart .= substr($q['nom'],0,1).'-'; 
					}
					foreach($quarts as $q){
						$strQuart .= 'T'.substr($q['nom'],0,1).'-';
					}
					$strQuart = substr($strQuart,0,strlen($strQuart)-1);
							
					$caseHoraire .=	'<div style="float:right;text-align:right;width:100px;">'.$strQuart.'</div></div>';
				}
				$indexCaseHoraire++;
				$row = $curseurHoraire->read();
			}while($row['idPoste']==$idPoste && $row['idQuart']==$idQuart);
			$quart[$idQuart]['ligne'] .= '<div class="label '.($pompierQuart?'pompierQuart':'').' '.($indexCaseHoraire>1?'plus':'').'">'
											.($modifQuart?'<span style="font-size:17px;">'.$pompierLabel.'</span>':$pompierLabel).'</div>'
											.$caseHoraire;
			$quart[$idQuart]['ligne'] .= '<div class="caseDispo"></div><div class="close">X</div></div></div>';
			$nbPoste++;
		}while($row['idQuart']==$idQuart);
		if($indiceJour==0 || $debut_du_mois){
			$quart[$idQuart]['nom'] .= '<div'.
										 ' style="height:'.(30*$nbPoste+$nbPoste-1).'px;'.
										 		' line-height:'.(30*$nbPoste+$nbPoste-1).'px;">'.$quart[$idQuart]['nomQuart'].'</div>';
		}
	}while($row['Jour']==$jour);
	$quart[$idQuart]['ligne'] .= '</td>';
	$indiceJour++;
	if($indiceJour>6){
		$indiceJour = 0;
		$indiceSemaine++;
		if($output_debut_du_mois){
			$output_debut_du_mois = false;
			for ($i=0; $i < $dateDebut->format('w'); $i++) { 
				foreach ($quart as $key => $value) {
					$quart[$key]['ligne'] = '<td></td>'.$quart[$key]['ligne'];
				}
			}
		}
		foreach($quart as $ligne){
			echo '<tr><td>'.$ligne['nom'].'</td><td>'.$ligne['poste'].'</td>'.$ligne['ligne'].'</tr>';
		}
	}
	$debut_du_mois = false;
}while($row!==false);
if($indiceJour>0){
	$indiceDebut = $indiceJour;
	foreach($quart as $ligne){
		echo '<tr><td>'.$ligne['nom'].'</td><td>'.$ligne['poste'].'</td>'.$ligne['ligne'];
		while($indiceJour<7){
			echo '<td></td>';
			$indiceJour++;
		}
		$indiceJour = $indiceDebut;
	}
	echo '</tr>';
}

$temps = array();
if($parametres->siCalculHeureHoraire == 1){
	do{
		$temps[$rowAutre['ID_quart'].'-'.$rowAutre['matricule']] = $rowAutre['HeureTotal'];
		$rowAutre = $curseurTempsAutre->read();
	}while($rowAutre!==false);
	
	do{
		if(isset($temps[$rowTemps['ID_quart'].'-'.$rowTemps['matricule']])){
			echo CHtml::hiddenField($rowTemps['ID_quart'].'-'.$rowTemps['matricule'], $rowTemps['HeureTotal']+$temps[$rowTemps['ID_quart'].'-'.$rowTemps['matricule']]);
		}else{
			echo CHtml::hiddenField($rowTemps['ID_quart'].'-'.$rowTemps['matricule'], $rowTemps['HeureTotal']);
		}
		$rowTemps = $curseurTemps->read();
	}while($rowTemps!==false);
}
?>
