<?php
class ValidationService extends CBehavior{

	public function valider($date, $caserne){
		/**
		  * Liste des paramètres utilisés dans la validation
		  * $heureMaxSemaine : heures max des pompiers temporaires
		  * $heureMaxPeriode : heures max des pompiers temporaire par période
		  * $heureMaxRemplacement : heures max des pompiers temporaires s'ils remplacent le même pompier permanent
		  * $tempsRepos : temps entre 2 quart de travail
		  */
		$heureMaxSemaine = 48;
		$heureMaxPeriode = 160;
		$heureMaxRemplacement = 66;
		$tempsRepos = 0;
		$parametres = Parametres::model()->findByPk(1);

		$dateDebut = new DateTime($date."T00:00:00");
		$dateDebutSuivante = date_add(clone $dateDebut,new DateInterval("P".$parametres->nbJourPeriode."D"));
		$equipes = Equipe::model()->findAll('siActif = 1 AND tbl_caserne_id = '.$caserne);
		$ids = '';
		foreach($equipes as $equipe){
			$ids .= $equipe->id.', ';
		}
		$ids = substr($ids,0,strlen($ids)-2);
		$criteria = new CDbCriteria;
		$criteria->alias = 'h';
		$criteria->join = 'LEFT JOIN tbl_poste_horaire ph ON h.tbl_poste_horaire_id = ph.id '.
						  'LEFT JOIN tbl_poste_horaire_caserne phc ON ph.id = phc.tbl_poste_horaire_id';
		$criteria->condition = 'phc.tbl_caserne_id = :caserne AND h.date >= :dateDebut AND h.date < :dateSuivante';
		$criteria->params = array(':caserne'=>$caserne, ':dateDebut'=>$dateDebut->format('Y-m-d'), ':dateSuivante'=>$dateDebutSuivante->format('Y-m-d'));
		$Choraires = Horaire::model()->count($criteria);

		$tblReponse[0][0] = 2;
		Yii::log(json_encode($tblReponse),'info','Validation');
		/*décommenter pour ne pas permettre la validation d'un horaire vide
		 if($Choraires == 0){
			return $tblReponse;
		}*/
		
		/*Heures max des pompiers temporaires pour 1 semaine*/
		$sql =
		"SELECT
			IF((UNIX_TIMESTAMP(h.date) DIV 86400) - (UNIX_TIMESTAMP('".$dateDebut->format('Y-m-d')."') DIV 86400)<=6,'0',(((UNIX_TIMESTAMP(h.date) DIV 86400) - (UNIX_TIMESTAMP('".$dateDebut->format('Y-m-d')."') DIV 86400)) DIV 7)) AS semaine,
			IFNULL(u2.id,u.id) AS ID_pompier_horaire,
			IFNULL(u2.matricule,u.matricule) AS matricule,
			SUM(IF(time_to_sec(IF(subtime(h.heureFin,h.heureDebut)>=0,subtime(h.heureFin,h.heureDebut),addtime(subtime(h.heureFin,h.heureDebut),'24:00:00')))/3600 <> 0.0000, 
						time_to_sec(IF(subtime(h.heureFin,h.heureDebut)>=0,subtime(h.heureFin,h.heureDebut),addtime(subtime(h.heureFin,h.heureDebut),'24:00:00')))/3600, 
						IF(time_to_sec(IF(subtime(ph.heureFin,ph.heureDebut)>=0,subtime(ph.heureFin,ph.heureDebut),addtime(subtime(ph.heureFin,ph.heureDebut),'24:00:00')))/3600 <> 0.0000,
							time_to_sec(IF(subtime(ph.heureFin,ph.heureDebut)>=0,subtime(ph.heureFin,ph.heureDebut),addtime(subtime(ph.heureFin,ph.heureDebut),'24:00:00')))/3600,
							time_to_sec(IF(subtime(q.heureFin,q.heureDebut)>=0,subtime(q.heureFin,q.heureDebut),addtime(subtime(q.heureFin,q.heureDebut),'24:00:00')))/3600
					)				
				)) AS HeureTotal,
			h.parent_id AS parent
		FROM 
			((numbers i1, numbers i2), 
			 (tbl_quart q LEFT JOIN (tbl_poste_horaire ph INNER JOIN tbl_poste p ON p.id=ph.tbl_poste_id) ON ph.tbl_quart_id=q.id)) 
		 	  LEFT JOIN tbl_horaire h ON h.tbl_poste_horaire_id=ph.id AND h.date=ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) AND h.type IN (0,2) LEFT JOIN tbl_usager u ON u.id=h.tbl_usager_id 
		 	  LEFT JOIN tbl_horaire h2 ON h2.parent_id = h.id AND h2.dateModif=(SELECT MAX(m.dateModif) FROM tbl_horaire m WHERE m.parent_id=h.id) AND h2.type = 1 LEFT JOIN tbl_usager u2 ON u2.id=h2.tbl_usager_id 
			  LEFT JOIN tbl_equipe_garde eg ON q.id=eg.tbl_quart_id AND eg.modulo=MOD((UNIX_TIMESTAMP(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i)) DIV 86400),".$parametres->nbJourPeriode.") 
			  LEFT JOIN tbl_equipe e on e.id=eg.tbl_equipe_id
		WHERE 
			(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) < '".$dateDebutSuivante->format('Y-m-d')."')
			AND e.id IN (".$ids.")
			AND (u2.id IS NOT NULL OR u.id IS NOT NULL)
			AND (u2.tempsPlein = 0 OR u.tempsPlein = 0 )
		GROUP BY semaine, ID_pompier_horaire
		ORDER BY semaine, ID_pompier_horaire";
		
		$cn = Yii::app()->db;		
		$cm = $cn->createCommand($sql);		
		$curseurHoraire = $cm->query();

		//Dépasse le 66 heures
		$tbl_id = array(); // Usagers déja en warning, pour éviter les répétitions
		$compteur = 0;
		while($row= $curseurHoraire->read()){
			if($row['HeureTotal'] > $heureMaxRemplacement){
				$tblReponse[1][] = array('semaine'=>$row['semaine']+1,'pompier'=>$row['matricule'],'type'=>'warning');
				$compteur++;
				$tbl_id[] = $row['matricule'];			
			}
		}
		Yii::log(json_encode($tblReponse),'info','Validation');
		if($compteur>0){
			$tblReponse[0][] = Yii::t('sgp',"Il y a un pompier qui dépasse les ".$heureMaxRemplacement." heures dans une semaine.
					|Il y a {n} pompiers qui dépassent les ".$heureMaxRemplacement." heures dans une semaine.",
					array($compteur));
		}
		
		$curseurHoraire = $cm->query();
		
		//Dépasse le 48 heures sans remplacer le même pompier
		$compteur = 0;
		while($row= $curseurHoraire->read()){
			if($row['HeureTotal'] > $heureMaxSemaine){
				if(!in_array($row['matricule'],$tbl_id)){
					$ssql = "SELECT
								r.matricule AS mat_remp
							FROM 
								((numbers i1, numbers i2), 
								 (tbl_quart q LEFT JOIN (tbl_poste_horaire ph INNER JOIN tbl_poste p ON p.id=ph.tbl_poste_id) ON ph.tbl_quart_id=q.id)) 
							 	  LEFT JOIN tbl_horaire h ON h.tbl_poste_horaire_id=ph.id AND h.date=ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) AND h.type = 2 LEFT JOIN tbl_usager u ON u.id=h.tbl_usager_id
								  LEFT JOIN tbl_horaire r ON r.tbl_horaire_id = h.parent_id
							WHERE 
								(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) < '".$dateDebutSuivante->format('Y-m-d')."')
								AND (u.matricule = ".$row['matricule'].")";

					$cn = Yii::app()->db;
					$cm = $cn->createCommand($ssql);
					$curseurRemplacement = $cm->query();
					
					$mat_remp = 0;
					$i = 0;
					$rempMemePomp = true;
					while($srow = $curseurRemaplcement->read()){
						if($mat_remp == 0){
							$mat_remp = $srow['mat_remp'];
						}else{
							if($mat_remp!=$srow['mat_remp']){
								$rempMemePomp = false;
							}
						}
						$i ++;
					}
					if(!$rempMemePomp || $i<3){					
						$tblReponse[1][] = array('semaine'=>$row['semaine']+1,'pompier'=>$row['matricule'],'type'=>'warning');
						$compteur++;
					}
				}
			}
		}
		Yii::log(json_encode($tblReponse),'info','Validation');
		if($compteur>0){
			$tblReponse[0][] = Yii::t('sgp',"Il y a un pompier qui dépasse les heures maximales dans une semaine.
					|Il y a {n} pompiers qui dépassent les heures maximales dans une semaine.",
					array($compteur));
		}
		Yii::log(json_encode($tblReponse),'info','Validation');


		/*Heures max des pompiers temporaires pour la période*/
		$sql =
		"SELECT
			IFNULL(u2.id,u.id) AS ID_pompier_horaire,
			IFNULL(u2.matricule,u.matricule) AS matricule,
			SUM(IF(time_to_sec(IF(subtime(h.heureFin,h.heureDebut)>=0,subtime(h.heureFin,h.heureDebut),addtime(subtime(h.heureFin,h.heureDebut),'24:00:00')))/3600 <> 0.0000, 
						time_to_sec(IF(subtime(h.heureFin,h.heureDebut)>=0,subtime(h.heureFin,h.heureDebut),addtime(subtime(h.heureFin,h.heureDebut),'24:00:00')))/3600, 
						IF(time_to_sec(IF(subtime(ph.heureFin,ph.heureDebut)>=0,subtime(ph.heureFin,ph.heureDebut),addtime(subtime(ph.heureFin,ph.heureDebut),'24:00:00')))/3600 <> 0.0000,
							time_to_sec(IF(subtime(ph.heureFin,ph.heureDebut)>=0,subtime(ph.heureFin,ph.heureDebut),addtime(subtime(ph.heureFin,ph.heureDebut),'24:00:00')))/3600,
							time_to_sec(IF(subtime(q.heureFin,q.heureDebut)>=0,subtime(q.heureFin,q.heureDebut),addtime(subtime(q.heureFin,q.heureDebut),'24:00:00')))/3600
					)				
				)) AS HeureTotal
		FROM 
			((numbers i1, numbers i2), 
			 (tbl_quart q LEFT JOIN (tbl_poste_horaire ph INNER JOIN tbl_poste p ON p.id=ph.tbl_poste_id) ON ph.tbl_quart_id=q.id)) 
		 	  LEFT JOIN tbl_horaire h ON h.tbl_poste_horaire_id=ph.id AND h.date=ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) AND h.type IN (0,2) LEFT JOIN tbl_usager u ON u.id=h.tbl_usager_id 
		 	  LEFT JOIN tbl_horaire h2 ON h2.parent_id = h.id AND h2.dateModif=(SELECT MAX(m.dateModif) FROM tbl_horaire m WHERE m.parent_id=h.id) AND h2.type = 1 LEFT JOIN tbl_usager u2 ON u2.id=h2.tbl_usager_id 
			  LEFT JOIN tbl_equipe_garde eg ON q.id=eg.tbl_quart_id AND eg.modulo=MOD((UNIX_TIMESTAMP(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i)) DIV 86400),".$parametres->nbJourPeriode.") 
			  LEFT JOIN tbl_equipe e on e.id=eg.tbl_equipe_id
		WHERE 
			(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) < '".$dateDebutSuivante->format('Y-m-d')."')
			AND e.id IN (".$ids.")
			AND (u2.id IS NOT NULL OR u.id IS NOT NULL)
			AND (u2.tempsPlein = 0 OR u.tempsPlein = 0 )
		GROUP BY ID_pompier_horaire
		ORDER BY ID_pompier_horaire";
		
		$cn = Yii::app()->db;		
		$cm = $cn->createCommand($sql);		
		$curseurHoraire = $cm->query();

		$compteur = 0;
		while($row= $curseurHoraire->read()){
			if($row['HeureTotal'] > $heureMaxPeriode){					
				$tblReponse[1][] = array('semaine'=>$row['semaine']+1,'pompier'=>$row['matricule'],'type'=>'warning');
				$compteur++;
			}
		}
		Yii::log(json_encode($tblReponse),'info','Validation');
		if($compteur>0){
			$tblReponse[0][] = Yii::t('sgp',"Il y a un pompier qui dépasse les heures maximales pour la période.
					|Il y a {n} pompiers qui dépassent les heures maximales pour la période.",
					array($compteur));
		}
		Yii::log(json_encode($tblReponse),'info','Validation');

		/* Temps de repos */
/*$sql = " SELECT
	h.date as date, h.tbl_poste_horaire_id as poste_horaire_id, u.matricule as usager, h1.date as date2, h1.tbl_poste_horaire_id as ph_id2,
	IF(subtime(h.heureFin,h.heureDebut)<>0 ,
		IF(subtime(h.heureDebut,q.heureDebut)<0,
			UNIX_TIMESTAMP(CONCAT(h.date,' ',h.heureDebut))+86400,
			UNIX_TIMESTAMP(CONCAT(h.date,' ',h.heureDebut))),
		IF(subtime(ph.heureFin,ph.heureDebut)<>0 ,
			IF(subtime(ph.heureDebut,q.heureDebut)<0,
				UNIX_TIMESTAMP(CONCAT(h.date,' ',ph.heureDebut))+86400,
				UNIX_TIMESTAMP(CONCAT(h.date,' ',ph.heureDebut))),
			IF(subtime(q.heureDebut,q.heureDebut)<0,
				UNIX_TIMESTAMP(CONCAT(h.date,' ',q.heureDebut))+86400,
				UNIX_TIMESTAMP(CONCAT(h.date,' ',q.heureDebut))))) as h_heureDebut,
	IF(subtime(h.heureFin,h.heureDebut)<>0 ,
		IF(subtime(h.heureFin,q.heureDebut)<0,
			UNIX_TIMESTAMP(CONCAT(h.date,' ',h.heureFin))+86400,
			UNIX_TIMESTAMP(CONCAT(h.date,' ',h.heureFin))),
		IF(subtime(ph.heureFin,ph.heureDebut)<>0 ,
			IF(subtime(ph.heureFin,q.heureDebut)<0,
				UNIX_TIMESTAMP(CONCAT(h.date,' ',ph.heureFin))+86400,
				UNIX_TIMESTAMP(CONCAT(h.date,' ',ph.heureFin))),
			IF(subtime(q.heureFin,q.heureDebut)<0,
				UNIX_TIMESTAMP(CONCAT(h.date,' ',q.heureFin))+86400,
				UNIX_TIMESTAMP(CONCAT(h.date,' ',q.heureFin))))) as h_heureFin,
	IF(subtime(h1.heureFin,h1.heureDebut)<>0 ,
		IF(subtime(h1.heureDebut,q1.heureDebut)<0,
			UNIX_TIMESTAMP(CONCAT(h1.date,' ',h1.heureDebut))+86400,
			UNIX_TIMESTAMP(CONCAT(h1.date,' ',h1.heureDebut))),
		IF(subtime(ph1.heureFin,ph1.heureDebut)<>0 ,
			IF(subtime(ph1.heureDebut,q1.heureDebut)<0,
				UNIX_TIMESTAMP(CONCAT(h1.date,' ',ph1.heureDebut))+86400,
				UNIX_TIMESTAMP(CONCAT(h1.date,' ',ph1.heureDebut))),
			IF(subtime(q1.heureDebut,q1.heureDebut)<0,
				UNIX_TIMESTAMP(CONCAT(h1.date,' ',q1.heureDebut))+86400,
				UNIX_TIMESTAMP(CONCAT(h1.date,' ',q1.heureDebut))))) as h1_heureDebut,
	IF(subtime(h1.heureFin,h1.heureDebut)<>0 ,
		IF(subtime(h1.heureFin,q1.heureDebut)<0,
			UNIX_TIMESTAMP(CONCAT(h1.date,' ',h1.heureFin))+86400,
			UNIX_TIMESTAMP(CONCAT(h1.date,' ',h1.heureFin))),
		IF(subtime(ph1.heureFin,ph1.heureDebut)<>0 ,
			IF(subtime(ph1.heureFin,q1.heureDebut)<0,
				UNIX_TIMESTAMP(CONCAT(h1.date,' ',ph1.heureFin))+86400,
				UNIX_TIMESTAMP(CONCAT(h1.date,' ',ph1.heureFin))),
			IF(subtime(q1.heureFin,q1.heureDebut)<0,
				UNIX_TIMESTAMP(CONCAT(h1.date,' ',q1.heureFin))+86400,
				UNIX_TIMESTAMP(CONCAT(h1.date,' ',q1.heureFin))))) as h1_heureFin
FROM
	tbl_horaire h
	INNER JOIN tbl_poste_horaire ph
		ON ph.id=h.tbl_poste_horaire_id
	INNER JOIN tbl_quart q
		ON q.id = ph.tbl_quart_id
	INNER JOIN tbl_usager u ON u.id = h.tbl_usager_id
	INNER JOIN (tbl_horaire h1 
		INNER JOIN tbl_poste_horaire ph1 ON h1.tbl_poste_horaire_id = ph1.id
		INNER JOIN tbl_quart q1 ON q1.id=ph1.tbl_quart_id	
	)
		ON
		    h1.date BETWEEN h.date AND DATE_ADD(h.date, INTERVAL 1 DAY)
		    AND h.tbl_usager_id=h1.tbl_usager_id 
		    AND h.id<>h1.id
			
WHERE
	h.date >= '".(date_sub(clone $dateDebut, new DateInterval("P1D"))->format('Y-m-d'))."' AND h.date < '".$dateDebutSuivante->format('Y-m-d')."'
	AND h.type <> 1 AND h1.type <> 1	
HAVING
	h_heureFin+18000 >= h1_heureDebut
    AND h_heureDebut < h1_heureDebut
ORDER BY h.date";

		$cn = Yii::app()->db;		
		$cm = $cn->createCommand($sql);		
		$curseurHoraire = $cm->query();

		$compteur = 0 ;
		while($row = $curseurHoraire->read()){
			$tblReponse[1][] = array('jour'=>$row['date'],'posteHoraire'=>$row['poste_horaire_id'],'pompier'=>$row['usager'],'type'=>'erreur');
			$compteur++;
		}
		Yii::log(json_encode($tblReponse),'info','Validation');

		if($compteur>0){
			$tblReponse[0][0] = 0;
			$tblReponse[0][] = Yii::t('sgp',"Il y a un pompier qui n'a pas assez d'heures de repos après un quart de travail.
				|Il y a {n} pompiers n'ont pas assez d'heures de repos après un quart de travail.",
					array($compteur));
		}else{
			if($tblReponse[0][0] <> 0) $tblReponse[0][0] = 1;
		}
		Yii::log(json_encode($tblReponse),'info','Validation');*/

		/* Postes obligatoires */
		/*	$sql =
		"SELECT h.date as date, h.tbl_poste_horaire_id as poste_horaire_id, u.matricule as usager
		 FROM tbl_horaire h INNER JOIN tbl_usager u ON u.id=h.tbl_usager_id
			INNER JOIN tbl_poste_horaire ph ON ph.id = h.tbl_poste_horaire_id
			INNER JOIN tbl_poste p ON p.id = ph.tbl_poste_id
			LEFT JOIN tbl_usager_poste up ON up.tbl_usager_id = u.id AND up.tbl_poste_id = p.id
		 WHERE up.tbl_usager_id IS NULL
			AND h.date BETWEEN '".$dateDebut->format('Y-m-d')."' AND '".$dateDebutSuivante->format('Y-m-d')."'";
		
		$cn = Yii::app()->db;		
		$cm = $cn->createCommand($sql);		
		$curseurHoraire = $cm->query();

		$compteur = 0 ;
		while($row = $curseurHoraire->read()){
			$tblReponse[1][] = array('jour'=>$row['date'],'posteHoraire'=>$row['poste_horaire_id'],'pompier'=>$row['usager'],'type'=>'warning');
			$compteur++;
		}
		Yii::log(json_encode($tblReponse),'info','Validation');

		if($compteur>0){
			$tblReponse[0][] = Yii::t('sgp',"Il y a un pompier assigné à un poste dont il n'a pas la formation nécessaire.
				|Il y a {n} pompiers assignés à des postes dont ils n'ont pas la formation nécessaire.",
					array($compteur));
		}
		Yii::log(json_encode($tblReponse),'info','Validation');*/


		/* Heures qui se chevauche */
		$sql =
		"SELECT
		h.date as date, h.tbl_poste_horaire_id as poste_horaire_id, u.matricule as usager
		FROM
			tbl_horaire h
			INNER JOIN tbl_poste_horaire ph
			ON ph.id=h.tbl_poste_horaire_id
			INNER JOIN tbl_quart q
			ON q.id = ph.tbl_quart_id
			INNER JOIN tbl_usager u ON u.id = h.tbl_usager_id
			INNER JOIN (tbl_horaire h1 
						INNER JOIN tbl_poste_horaire ph1 ON h1.tbl_poste_horaire_id = ph1.id
						INNER JOIN tbl_quart q1 ON q1.id=ph1.tbl_quart_id	
			) ON
		    h.date=h1.date 
		    AND h.tbl_usager_id=h1.tbl_usager_id 
		    AND q.id = q1.id
		    AND h.id<>h1.id
			
		WHERE
			h.date >= '".$dateDebut->format('Y-m-d')."' AND h.date < '".$dateDebutSuivante->format('Y-m-d')."'
			AND h.type <> 1 AND h1.type <> 1
		AND NOT (
				(
					IF(subtime(h.heureFin,h.heureDebut)<>0 ,
						IF(subtime(h.heureFin,h.heureDebut)<0,
							UNIX_TIMESTAMP(CONCAT(h.date,' ',h.heureFin))+86400,
							UNIX_TIMESTAMP(CONCAT(h.date,' ',h.heureFin))),
						IF(subtime(ph.heureFin,ph.heureDebut)<>0 ,
							IF(subtime(ph.heureFin,ph.heureDebut)<0,
								UNIX_TIMESTAMP(CONCAT(h.date,' ',ph.heureFin))+86400,
								UNIX_TIMESTAMP(CONCAT(h.date,' ',ph.heureFin))),
							IF(subtime(q.heureFin,q.heureDebut)<0,
								UNIX_TIMESTAMP(CONCAT(h.date,' ',q.heureFin))+86400,
								UNIX_TIMESTAMP(CONCAT(h.date,' ',q.heureFin))))) 
					<=
			        IF(subtime(h1.heureFin,h1.heureDebut)<>0,
			        	UNIX_TIMESTAMP(CONCAT(h.date,' ',h1.heureDebut)),
			        	IF(subtime(ph1.heureFin,ph1.heureDebut)<>0,
			        		UNIX_TIMESTAMP(CONCAT(h.date,' ',ph1.heureDebut)),
			        		UNIX_TIMESTAMP(CONCAT(h.date,' ',q1.heureDebut))))
			        OR
			        /*si heureFin1 <= heureDebut*/
			        IF(subtime(h1.heureFin,h1.heureDebut)<>0 ,
			        	IF(subtime(h1.heureFin,h1.heureDebut)<0,
			        		UNIX_TIMESTAMP(CONCAT(h.date,' ',h1.heureFin))+86400,
			        		UNIX_TIMESTAMP(CONCAT(h.date,' ',h1.heureFin))),
						IF(subtime(ph1.heureFin,ph1.heureDebut)<>0 ,
							IF(subtime(ph1.heureFin,ph1.heureDebut)<0,
								UNIX_TIMESTAMP(CONCAT(h.date,' ',ph1.heureFin))+86400,
								UNIX_TIMESTAMP(CONCAT(h.date,' ',ph1.heureFin))),
							IF(subtime(q1.heureFin,q1.heureDebut)<0,
								UNIX_TIMESTAMP(CONCAT(h.date,' ',q1.heureFin))+86400,
								UNIX_TIMESTAMP(CONCAT(h.date,' ',q1.heureFin))))) 
					<=
			        IF(subtime(h.heureFin,h.heureDebut)<>0,
			        	UNIX_TIMESTAMP(CONCAT(h.date,' ',h.heureDebut)),
			        	IF(subtime(ph.heureFin,ph.heureDebut)<>0,
			        		UNIX_TIMESTAMP(CONCAT(h.date,' ',ph.heureDebut)),
			        		UNIX_TIMESTAMP(CONCAT(h.date,' ',q.heuredebut))))
				)
				)
		ORDER BY h.date;";
		
		$cn = Yii::app()->db;		
		$cm = $cn->createCommand($sql);		
		$curseurHoraire = $cm->query();

		$compteur = 0 ;
		while($row = $curseurHoraire->read()){
			$tblReponse[1][] = array('jour'=>$row['date'],'posteHoraire'=>$row['poste_horaire_id'],'pompier'=>$row['usager'],'type'=>'erreur');
			$compteur++;
		}
		Yii::log(json_encode($tblReponse),'info','Validation');

		if($compteur>0){
			$tblReponse[0][0] = 1;
			$tblReponse[0][] = Yii::t('sgp',"Il y a un pompier dont les heures se chevauche dans un ou plusieurs quarts.
				|Il y a {n} pompiers dont les heures se chevauche dans un ou plusieurs quarts.",
					array($compteur));
		}else{
			if($tblReponse[0][0] <> 0) $tblReponse[0][0] = 1;
		}
		Yii::log(json_encode($tblReponse),'info','Validation');
		

		
		
		
		return $tblReponse;
	}
}