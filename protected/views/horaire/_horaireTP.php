<?php 
$siPrec = true;
$indiceSemaine = 1;
$indiceJour = 0;
$row = $curseurHoraire->read();
$poste = "";
$caseHoraire = "";
$i=0;
$eg = $parametres->moduloDebut;

/**
 * @var $quart String : Permet d'enregistrer le HTML de chaque quart pour ensuite le monter dans le tableau HTML dans le bon ordre
 * Ce array est réinitialisé à chaque nouvelle semaine
 */
$quart = array();
do{
	$jour = $row['Jour'];
	$dateJour = new DateTime($row['Jour']);
	$siPremierDuJour = true;
	do{
		$idQuart = $row['idQuart'];
		if($indiceJour==0){
			 $quart[$idQuart]['ligne'] = '';
			 $quart[$idQuart]['nom'] = '';
			 if($siPremierDuJour){
			 	$quart[$idQuart]['nom'] .= '<div>';
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
				$quart[$idQuart]['ligne'] .= '<div>'.++$i.'</div>';
				$quart[$idQuart]['poste'] .= '<div></div>';
				$siPremierDuJour = false;
			}
			$quart[$idQuart]['poste'] .= '<div class="cursor" title="'.$row['Poste'].'">'.$row['diminutifPoste'].'</div>';
			$quart[$idQuart]['ligne'] .= '<div'.
											' class="case"'.
											' style="background-color:#'.$tblEquipeGarde[$eg][$idQuart].
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
			$pompierLabel = $row['Matricule_horaire'];
			$pompierQuart = false;
			$modifQuart = false;
			$indexCaseHoraire = 0;
			do{
				// ------ CASE HORAIRE -------
					if($row['typeH']==2){
						$modifQuart = true;
					}
					if($row['ID_pompier_horaire']==Yii::app()->user->id){
						$pompierQuart = true;
					}
					$caseHoraire .= '<div'.
									  ' style="width:301px"'.
									  ' semaine="'.$indiceSemaine.
									  '" posteH="'.$row['phId'].
					                  '" heures="'.($row['hHeureReel']>0?$row['hHeureReel']:($row['phHeureReel']>0?$row['phHeureReel']:$row['qHeureReel'])).
					                  '" pompier="'.$row['Matricule_horaire'].
									  '" tsDebut="'.($row['hHeureReel']>0?$row['tsHDebut']:($row['phHeureReel']>0?$row['tsPHDebut']:$row['tsQDebut'])).
									  '" tsFin="'.($row['hHeureReel']>0?$row['tsHFin']:($row['phHeureReel']>0?$row['tsPHFin']:$row['tsQFin'])).
									  '" tsFin="'.($row['phHeureReel']>0?$row['tsQFin']:$row['tsPHFin']).
									  '" typeH="'.($row['typeH']===NULL?'0':$row['typeH']).
									  '">'.CHtml::textField('horaire',$row['Matricule_horaire'],array('class'=>'textTransparent',
																								'indexCaseHoraire'=>$indexCaseHoraire,
																								'style'=> 'float:left',
																								)).
									  (($row['typeH']==2)?'<img src="images/edit.png" class="editH remp" />':'').(($row['typeH']==2)?CHtml::link(CHtml::image('images/delete.png','X',array('class'=>'editH')),array('deleteRemp','id'=>$row['ID_Horaire'], 'date'=>$dateJour->format('Ymd'), 'caserne'=>$caserne),array('onClick'=>"return confirm('Êtes-vous sûr de vouloir supprimer cet item?')")):'');
				if($row['typeH']!=2 && $row['Matricule_horaire']!==NULL){
					$caseHoraire .= '<img src="images/switch.png" class="editH remp" />';
				}
				$caseHoraire .=  '<div style="width:109px; float:left;">'.((($row['hHeureDebut']=='00:00:00' && $row['hHeureFin']=='00:00:00')||($row['hHeureDebut']==NULL && $row['hHeureFin']==NULL))?(($row['phHeureDebut']=='00:00:00' && $row['phHeureFin']=='00:00:00')?substr($row['qHeureDebut'],0,5).' - '.substr($row['qHeureFin'],0,5):substr($row['phHeureDebut'],0,5).' - '.substr($row['phHeureFin'],0,5)):substr($row['hHeureDebut'],0,5).' - '.substr($row['hHeureFin'],0,5)).'</div>
									</div>';
				$indexCaseHoraire++;
				$row = $curseurHoraire->read();
			}while($row['idPoste']==$idPoste && $row['idQuart']==$idQuart);
			$quart[$idQuart]['ligne'] .= '<div class="label '.($pompierQuart?'pompierQuart':'').' '.($indexCaseHoraire>1?'plus':'').'">'
											.($modifQuart?'<span style="font-size:17px;">'.$pompierLabel.'</span>':$pompierLabel).'</div>'
											.$caseHoraire;
			$quart[$idQuart]['ligne'] .= '<div class="caseDispo"></div><div class="close">X</div></div></div>';
			$nbPoste++;
		}while($row['idQuart']==$idQuart);
		if($indiceJour==0){
			$quart[$idQuart]['nom'] .= '<div'.
										 ' style="height:'.(30*$nbPoste+$nbPoste-1).'px;'.
										 		' line-height:'.(30*$nbPoste+$nbPoste-1).'px;">'.$quart[$idQuart]['nomQuart'].'</div>';
		}
	}while($row['Jour']==$jour);
	$eg++;
	if($eg >= $garde->nbr_jour_periode){
		$eg = 0;
	}
	$quart[$idQuart]['ligne'] .= '</td>';
	$indiceJour++;
	if($indiceJour>6){
		$indiceJour = 0;
		$indiceSemaine++;
		foreach($quart as $ligne){
			echo '<tr><td>'.$ligne['nom'].'</td><td>'.$ligne['poste'].'</td>'.$ligne['ligne'].'</tr>';
		}
	}
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
?>
