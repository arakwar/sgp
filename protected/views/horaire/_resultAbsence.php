<?php
	if($dataAbsence !==FALSE){
		$row = $dataAbsence->read();
		//echo '<pre>';print_r($row);echo '</pre>';
		$mois = array();$nbrHeure = array();
		$mois[1] = Yii::t('generale','janvier');
		$mois[2] = Yii::t('generale','fevrier');
		$mois[3] = Yii::t('generale','mars');
		$mois[4] = Yii::t('generale','avril');
		$mois[5] = Yii::t('generale','mai');
		$mois[6] = Yii::t('generale','juin');
		$mois[7] = Yii::t('generale','juillet');
		$mois[8] = Yii::t('generale','aout');
		$mois[9] = Yii::t('generale','septembre');
		$mois[10] = Yii::t('generale','octobre');
		$mois[11] = Yii::t('generale','novembre');
		$mois[12] = Yii::t('generale','decembre');
		$table = '<table><tr><td colspan="3"><h2>'.$annee.'</h2></td></tr>';
		do{
			$index = $row['Mois'];
			$table .= '<tr><td colspan="3"><h3>'.$mois[$index].'</h3></td></tr>';
			$table .= '<tr><td><strong>Pompier</strong></td><td><strong>Heures acceptées</strong></td><td><strong>Heures restantes</strong></td></tr>';
			do{
				if(array_key_exists($row['Usager'], $nbrHeure)){
					$nbrHeure[$row['Usager']] += $row['Heure'];
				}else{
					$nbrHeure[$row['Usager']] = $row['Heure'];					
				}
				$table .= '<tr><td>'.$row['Usager'].'</td><td>'.substr($row['Heure'],0,strlen($row['Heure']-2)).'</td><td>'.($banque-$nbrHeure[$row['Usager']]).'</td></tr>';
				$row = $dataAbsence->read();		
			}while($index==$row['Mois']);
		}while($row!==FALSE);
		$table .= '</table>';
		echo $table;
	}
?>