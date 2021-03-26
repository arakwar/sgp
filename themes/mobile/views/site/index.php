<?php
	Yii::app()->clientScript->registerScript('evenement','
		$(".dispo").live("click touchstart",function()
		{
			parent = this;
			'.
				(CHtml::ajax(array(
					'type'=>'GET',
					'url' =>array('site/changeMobileStatut'),
					'cache'=>false,
					'data'=>"js:{
						sendQuart:$(parent).attr('quartID'),
						caserne:$(parent).attr('caserne'),
						actif:$(parent).attr('dispo'),
						dateDemande: $(parent).attr('dateQuart'),
					}",
					'success'=>'function(result){
						if(result == 0)
						{
							$(parent).empty();
							$(parent).attr("dispo",result);
						}
						else if(result == 1)
						{
							$(parent).prepend(\''.CHtml::image('images/crochet.png').'\');
							$(parent).attr("dispo",result);
						}
						else
						{
							alert(result);
						}
					}'
				)))
			.'
			
		});
	');
	$showTitre = TRUE;
	echo '<h3>'.Yii::t('mobile','index.dateActuel').': <br />'.date('Y-m-d H:i').'</h3>';
	echo '<div class="forceFrappeDisponnibilite">';
	foreach ($curseurALLQuart as $key => $curseurQuart)
	{
		$row = $curseurQuart->read();
		do
		{
			if(!isset($forceFrappeDispo))
			{
				$date = $row['DateQuart'];
				$quart = $row['Quart'];
				$debut = TRUE;
				$newQuart = false;
				$nbrQuart = 1;
				$forceFrappeDispo = array();
			}
			if($date != $row['DateQuart'])
			{
				$tempo = array() ;
				$tempo['Date'] = $date;
				$tempo['NbrQuart'] = $nbrQuart;
				$forceFrappeDispo['body']['Date'][] = $tempo;
				$date = $row['DateQuart'];
				$nbrQuart = 1;
			}
			if($quart != $row['Quart'])
			{
				$tempo = array();
				$tempo['Couleur'] = $row['Couleur'];
				$tempo['Nom'] = $row['Quart'];
				$forceFrappeDispo['body']['Quart'][] = $tempo;
				$nbrQuart++;
				$quart = $row['Quart'];
				$newQuart = TRUE;
			}
			if($debut)
			{
				$forceFrappeDispo['head']['Nom'] = $row['Caserne'];
				$forceFrappeDispo['head']['ID'] = $row['CaserneID'];
				$tempo = array();
				$tempo['Couleur'] = $row['Couleur'];
				$tempo['Nom'] = $row['Quart'];
				$forceFrappeDispo['body']['Quart'][] = $tempo;
				$debut = False;
			}
			$tempo = array();
			$tempo['Caserne'] = $row['CaserneID'];
			$tempo['DateQuart'] = $row['DateQuart'];
			$tempo['QuartID'] = $row['QuartID'];
			$tempo['Dispo'] = $row['Dispo'];
			$forceFrappeDispo['body']['Dispo'][] = $tempo;
			$quart = $row['Quart'];
			$row = $curseurQuart->read();
		}while($row !== false);
		$tempo = array() ;
		$tempo['Date'] = $date;
		$tempo['NbrQuart'] = $nbrQuart;
		$forceFrappeDispo['body']['Date'][] = $tempo;

		//----- affichage ---->
		echo (($showTitre)? '<h3>'.Yii::t('mobile','index.forceFrappeDisponnibilite').'</h3>': '').
		(($casernesNbr>1)?'<h4>'.$forceFrappeDispo['head']['Nom'].'</h4>':'').
		'<table id="'.$forceFrappeDispo['head']['ID'].'" class="forceFrappeDispo"><tbody>'.
			'<tr><td class="forceFrappeDispo head">'.Yii::t('mobile','index.quart').'</td>';
				foreach ($forceFrappeDispo['body']['Quart'] as $value) 
				{
					echo '<td style="background-color:#'.$value['Couleur'].'; background-image:url(images/degrade.png)">'.$value['Nom'].'</td>';
				}
			echo '</tr>'.
			'<tr><td class="forceFrappeDispo head">'.Yii::t('mobile','index.dispo').'</td>';
				foreach ($forceFrappeDispo['body']['Dispo'] as $value) 
				{
					$elements = 'class="dispo" caserne="'.$value['Caserne'].'" dateQuart="'.$value['DateQuart'].'" quartID="'.$value['QuartID'].'" dispo="'.$value['Dispo'].'"';
					echo '<td><a '.$elements.'>'.($value['Dispo']? CHtml::image('images/crochet.png'): '').'</a></td>';
				}
			echo '</tr>'.
		'</tbody></table>';
		unset($forceFrappeDispo);
		$showTitre = false;
	}
	echo '</div>';

	if(Yii::app()->params['moduleHoraire']){
		echo '<div id="Quart"><h3>'.Yii::t('mobile','index.prochainsQuart').'</h3>';
		$show = TRUE;
		foreach($horaireMobile as $horaire)
		{
			$class = ($show) ? 'first' : '';
			echo
				'<div class="horaireCaserneQuart '.$class.'">'.
					'<div class="caseHoraire">'.
						'<div class="horaireDate" ><p><strong>'.Yii::t('mobile','index.date').' : </strong>'.$horaire->date.'</p></div>'.
						'<div class="horaireQuart"><p><strong>'.Yii::t('mobile','index.quart').' : </strong>'.$horaire->PosteHoraire->Quart->nom.' ('.substr($horaire->PosteHoraire->heureDebut,0,strlen($horaire->PosteHoraire->heureFin)-3).' à '.substr($horaire->PosteHoraire->heureFin,0,strlen($horaire->PosteHoraire->heureDebut)-3).')</p></div>'.
						'<div class="horairePoste"><p><strong>'.Yii::t('mobile','index.poste').': </strong>'.$horaire->PosteHoraire->poste->nom.'</p></div>'.
						(($casernesNbr>1)?'<div class="horaireLieux"><p><strong>'.Yii::t('mobile','index.caserne').' : </strong>'.$horaire->Caserne->nom.'</p></div>':'').
					'</div>'.
				'</div>'
			;
			$show = false;
		}

		if($show)
			echo'<div class="horaireCaserneQuart aucun"> <p> '.Yii::t('mobile','index.aucunQuart').' </p></div>';
		echo '</div>';
	}

	echo '<div id="forceFrappe">';
	$showTitre = TRUE;
		foreach ($curseurALLForceFrappe as $key => $curseurForceFrappe)
		{
			foreach ($ALLminimum as $key1 => $minimum)
				if($key == $key1)
					break;
			$row = $curseurForceFrappe->read();
			$testMinimum = array();$nomEquipe = array();
			do 
			{
				$testMinimum[$row['DateQuart']][$row['QuartID']][$row['Equipe']] = $row['Minimum'];
				if(!isset($forceFrappe))
				{
					$date = $row['DateQuart'];
					$quart = $row['Quart'];
					$debut = TRUE;
					$newQuart = false;
					$nbrQuart = 1;
					$nbrEquipe = 1;
					$nbrMinimum[$row['DateQuart']] = $minimum[date('w', strtotime($row['DateQuart']))]['minimum'];
					$niveau[$row['DateQuart']] = $minimum[date('w', strtotime($row['DateQuart']))]['niveau'];
					$minimumCompteur[$row['DateQuart']] = 0;
					$niveauCompteur[$row['DateQuart']] = 0;
					$forceFrappe = array();
				}
				if($date != $row['DateQuart'])
				{
					$tempo = array() ;
					$tempo['Date'] = $date;
					$tempo['NbrQuart'] = $nbrQuart;
					$forceFrappe['body']['Date'][] = $tempo;
					$date = $row['DateQuart'];
					$nbrMinimum[$row['DateQuart']] = $minimum[date('w', strtotime($row['DateQuart']))]['minimum'];
					$niveau[$row['DateQuart']] = $minimum[date('w', strtotime($row['DateQuart']))]['niveau'];
					$minimumCompteur[$row['DateQuart']] = 0;
					$niveauCompteur[$row['DateQuart']] = 0;
					$nbrEquipe = 1;
					$nbrQuart = 1;
				}
				if($quart != $row['Quart'])
				{
					$tempo = array();
					$tempo['Couleur'] = $row['couleur'];
					$tempo['Nom'] = $row['Quart'];
					$forceFrappe['body']['Quart'][] = $tempo;
					$nbrEquipe = 1;
					$nbrQuart++;
					$quart = $row['Quart'];
					$newQuart = TRUE;
				}
				if($debut)
				{
					$forceFrappe['head']['Nom'] = $row['Caserne'];
					$forceFrappe['head']['ID'] = $row['CaserneID'];
					$tempo = array();
					$tempo['Couleur'] = $row['couleur'];
					$tempo['Nom'] = $row['Quart'];
					$forceFrappe['body']['Quart'][] = $tempo;
					$tempo = array();
					$tempo['Date'] = $row['DateQuart'];
					$tempo['Dispo'] = $row['Dispo'];
					$tempo['QuartID'] = $row['QuartID'];
					$tempo['EquipeID'] = $row['Equipe'];
					$tempo['NomEquipe'] = $row['NomEquipe'];
					$forceFrappe['body']['Garde'][] = $tempo;
					if($niveau[$row['DateQuart']] > $niveauCompteur[$row['DateQuart']])
					{
						$minimumCompteur[$row['DateQuart']] = $row['Dispo'];
						$niveauCompteur[$row['DateQuart']]++;
					}
					$debut = False;
				}
				elseif($newQuart)
				{
					$tempo = array();
					$tempo['Date'] = $row['DateQuart'];
					$tempo['Dispo'] = $row['Dispo'];
					$tempo['QuartID'] = $row['QuartID'];
					$tempo['EquipeID'] = $row['Equipe'];
					$nomGarde = $row['NomEquipe'];
					$forceFrappe['body']['Garde'][] = $tempo; 
					if($niveau[$row['DateQuart']] > $niveauCompteur[$row['DateQuart']])
					{
						$minimumCompteur[$row['DateQuart']] .= $row['Dispo'];
						$niveauCompteur[$row['DateQuart']]++;
					}
					$newQuart = False;
				}
				else
				{
					$tempo = array();
					$tempo['Date'] = $row['DateQuart'];
					$tempo['Dispo'] = $row['Dispo'];
					$tempo['QuartID'] = $row['QuartID'];
					$tempo['EquipeID'] = $row['Equipe'];
					$nomEquipe[] = $row['NomEquipe'];
					$forceFrappe['body']['Equipe'][$nbrEquipe][] = $tempo;
					if($niveau[$row['DateQuart']] > $niveauCompteur[$row['DateQuart']])
					{
						$minimumCompteur[$row['DateQuart']] .= $row['Dispo'];
						$niveauCompteur[$row['DateQuart']]++;
					}
				}
				$quart = $row['Quart'];
				$nbrEquipe ++;
				$row = $curseurForceFrappe->read();
			}while($row !== false);
			$tempo = array() ;
			$tempo['Date'] = $date;
			$tempo['NbrQuart'] = $nbrQuart;
			$forceFrappe['body']['Date'][] = $tempo;

			//----- affichage ---->
			echo (($showTitre)? '<h3>'.Yii::t('mobile','index.forceFrappeActuelle').'</h3>':'').
			(($casernesNbr>1)?'<h4>'.$forceFrappe['head']['Nom'].'</h4>':'').
			'<table id="'.$forceFrappe['head']['ID'].'" class="tableForceFrappe"><tbody>'.
				'<tr><td class="tableForceFrappe head">'.Yii::t('mobile','index.quart').'</td>';
					foreach ($forceFrappe['body']['Quart'] as $value) 
					{
						echo '<td style="background-color:#'.$value['Couleur'].'; background-image:url(images/degrade.png)">'.$value['Nom'].'</td>';
					}
				echo '</tr>'.
				'<tr><td class="tableForceFrappe head">'.(($parametres->affichage_fdf_equipe==0)?Yii::t('mobile','index.garde'):$nomGarde).'</td>';
					$totalQuart = array();
					foreach ($forceFrappe['body']['Garde'] as $quart => $value) 
					{
						$totalQuart[$quart] = $value['Dispo'];
						echo '<td style="background-color:#'.($testMinimum[$value['Date']][$value['QuartID']][$value['EquipeID']] == 'true' ? 'F00' :'0F0').';background-image:url(images/degrade.png);background-position:top;background-repeat:repeat-x;">'.$value['Dispo'].'</td>';
					}
				echo '</tr>';
				if(isset($forceFrappe['body']['Equipe'])){
					foreach ($forceFrappe['body']['Equipe'] as $numEquipe => $value) 
					{
						echo'<tr><td class="tableForceFrappe head">'.(($parametres->affichage_fdf_equipe==0)?Yii::t('mobile','index.equipe').''.$numEquipe:$nomEquipe[$numEquipe-2]).'</td>';
						foreach ($value as $quart => $value) 
						{
							if(!isset($totalQuart[$quart]))
							{
								$totalQuart[$quart] = $value['Dispo'];
							}
							else
							{
								$totalQuart[$quart] += $value['Dispo'];
							}
							echo '<td style="background-color:#'.($testMinimum[$value['Date']][$value['QuartID']][$value['EquipeID']] == 'true'? 'F00' :'0F0').'; background-image:url(images/degrade.png);background-position:top;background-repeat:repeat-x;">'.$value['Dispo'].'</td>';
						}
					}
				}
				echo '</tr>'.
				'<tr><td class="tableForceFrappe head">'.Yii::t('mobile','index.total').'</td>';
					foreach ($totalQuart as $total) 
					{
						echo '<td style="background-color:#0F0; background-image:url(images/degrade.png)">'.$total.'</td>';
					}
				echo '</tr>';

			echo '</tbody></table>';
			unset($forceFrappe);
			$showTitre = false;
		}
	echo '</div>';