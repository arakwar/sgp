<?php

class HoraireController extends Controller
{
	public $pageTitle = 'Horaire';
	public $avisNV = '';

	// Uncomment the following methods and override them if needed
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
				'accessControl', // perform access control for CRUD operations
		);
	}
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	
	public function accessRules()
	{
		return array(
				array('allow',
						'actions'=>array('listeDispo', 'listeDispoPart', 'pdfHoraire', 'index', 'dispo', 
							'defautDispo', 'cochePeriode', 'coche', 'decoche','conge', 'congeView','congeCreate', 
								'congeUpdate', 'congeArchive'),
						'roles'=>array('Horaire:index'),
				),
				array('allow',
						'actions'=>array('valider', 'valider2', 'change', 'modif', 'createModif', 'remp', 
							'createRemp', 'deleteRemp', 'indexTP', 'importerTP', 'fermer', 'heureTempsPartiel',
							'congeValider', 'rapportRemp', 'congeArchiver', 'imprimerConge', 'rapports', 
							'rapportAbsence','congeGestion'),
						'roles'=>array('Horaire:create'),
				),
				array('allow',
						'actions'=>array('typeConge', 'typeCreate', 'typeModif', 'typeDelete', 'typeView', 
							'congeAppel', 'timestamp', 'temps'),
						'roles'=>array('Admin'),
				),
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}
	
	public function actionIndex($date="",$caserne="",$idEquipe="", $ajax=""){
		$parametres = Parametres::model()->findByPk(1);
		
		$usager = Usager::model()->findByPk(Yii::app()->user->id);
		
		$criteriaCaserne = new CDbCriteria;
		$criteriaCaserne->condition = 'siActif = 1 && si_horaire';
		if($parametres->caserne_horaire==1){
			$criteriaCaserne->addInCondition('id', $usager->getCaserne());
		}

		
		$casernes = Caserne::model()->findAll($criteriaCaserne);
		$tblCaserne = CHtml::listData($casernes,'id','nom');		
		
		if($caserne==""){
			if(isset($parametres->caserne_defaut_horaire)){
				$caserne = $parametres->caserne_defaut_horaire;
			}else{
				//legacy code
				foreach($casernes as $cas){
					$caserne = $cas->id;
					break;
				}			
			}
		}
		
		$equipes = Equipe::model()->findAll('siActif = 1 AND tbl_caserne_id = '.$caserne);
		$ids = '';
		foreach($equipes as $equipe){
			$ids .= $equipe->id.', ';
		}
		$ids = substr($ids,0,strlen($ids)-2);
		
		if($ajax=='lstUsager'){
			$listeEquipe = new CActiveDataProvider('Equipe',array(
					'pagination'=>false,
					'criteria'=>array('condition'=>'siActif=1 AND siHoraire = 1 AND id IN ('.$ids.')'),
			));
				
			$criteria = new CDbCriteria();
			$criteria->order = $parametres->colonne.' '.$parametres->ordre;
			$idU = '';
			if($idEquipe!=""){
				$idE = $idEquipe;
		
				$equipesU = EquipeUsager::model()->findAll('tbl_equipe_id IN ('.$idE.')');
				//$ids devient les id des usagers
				foreach($equipesU as $equipe){
					$idU .= $equipe->tbl_usager_id.', ';
				}
				$idU = substr($idU,0,strlen($idU)-2);
		
			}else{
				$equipesU = EquipeUsager::model()->findAll('tbl_equipe_id IN ('.$ids.')');
				//$ids devient les id des usagers
				foreach($equipesU as $equipe){
					$idU .= $equipe->tbl_usager_id.', ';
				}
				$idU = substr($idU,0,strlen($idU)-2);
					
			}
			if($idU == ''){
				$idU = '0';
			}
				
			$criteria->condition .= "id IN (".$idU.")";
				
			$dataUsager=new CActiveDataProvider('Usager',array(
					'pagination'=>array(
							'pageSize'=>15
					),
					'criteria'=>$criteria
			));
		
			$this->renderPartial('_viewUsager',array(
					'dataUsager'=>$dataUsager,
					'parametres'=>$parametres,
			));
		}else{
			
			$garde = Garde::model()->findByPk($parametres->garde_horaire);
			$dateDebut = Horaire::debutPeriode($garde->nbr_jour_affiche,$parametres->moduloDebut,$date);

			if(!empty($parametres->horaire_mensuel)){
				$dateDebutPrecedente = clone $dateDebut;
				$dateDebutPrecedente->modify('first day of last month');
				$dateDebutSuivante = clone $dateDebut;
				$dateDebutSuivante->modify('first day of next month');
				$dateDebutSuivanteSuivante = clone $dateDebutSuivante;
				$dateDebutSuivanteSuivante->modify('first day of next month');
				$dateFin = clone $dateDebut;
				$dateFin->modify('last day of this month');
			}else{
				$dateDebutPrecedente = date_sub(clone $dateDebut,new DateInterval("P".$garde->nbr_jour_affiche."D"));
				$dateDebutSuivante = date_add(clone $dateDebut,new DateInterval("P".$garde->nbr_jour_affiche."D"));
				$dateDebutSuivanteSuivante = date_add(clone $dateDebut,new DateInterval("P".($garde->nbr_jour_affiche*2)."D"));
				$dateFin = date_add(clone $dateDebut,new DateInterval("P".$garde->nbr_jour_affiche."D"));
			}
	
			$criteria=new CDbCriteria;
			$criteria->alias='th';
			$criteria->join='LEFT JOIN tbl_poste_horaire AS tph ON tph.id=th.tbl_poste_horaire_id
							LEFT JOIN tbl_poste AS tp ON tp.id = tph.tbl_poste_id '.
							'INNER JOIN tbl_poste_horaire_caserne tphc ON tphc.tbl_poste_horaire_id = tph.id';
			$criteria->condition='th.date >= :dateDebutPrecedente AND th.date < :dateDebut AND th.statut = 1 AND th.tbl_caserne_id = :caserne';
			$criteria->params = array(':dateDebutPrecedente'=>$dateDebutPrecedente->format("Y-m-d"), ':dateDebut'=>$dateDebut->format("Y-m-d"), ':caserne'=>$caserne);
			$horairePrecedent = Horaire::model()->count($criteria);
			
			$criteria=new CDbCriteria;
			$criteria->alias='th';
			$criteria->join='LEFT JOIN tbl_poste_horaire AS tph ON tph.id=th.tbl_poste_horaire_id
							LEFT JOIN tbl_poste AS tp ON tp.id = tph.tbl_poste_id '.
							'INNER JOIN tbl_poste_horaire_caserne tphc ON tphc.tbl_poste_horaire_id = tph.id';
			$criteria->condition='th.date >= :dateDebutSuivante AND th.date < :dateDebutSuivanteSuivante AND th.statut = 1 AND th.tbl_caserne_id = :caserne';
			$criteria->params = array(':dateDebutSuivante'=>$dateDebutSuivante->format("Y-m-d"), ':dateDebutSuivanteSuivante'=>$dateDebutSuivanteSuivante->format("Y-m-d"), ':caserne'=>$caserne);
			$horaireSuivant = Horaire::model()->count($criteria);
			
			if($horairePrecedent >= 1){
				$siPeriodePrecedente = true;
			}else{
				$siPeriodePrecedente = false;
			}
			if(Yii::app()->user->checkAccess('GesHoraire')){
				$periodeSuivante = true;
			}elseif((!Yii::app()->user->checkAccess('GesHoraire')) && $horaireSuivant >= 1){
				$periodeSuivante = true;
			}else{
				$periodeSuivante = false;
			}
			
			$criteria = new CDbCriteria;
			$criteria->alias='th';
			$criteria->join='LEFT JOIN tbl_poste_horaire AS tph ON tph.id=th.tbl_poste_horaire_id
							LEFT JOIN tbl_poste AS tp ON tp.id = tph.tbl_poste_id '.
						'LEFT JOIN tbl_quart q ON tph.tbl_quart_id = q.id '.
						'INNER JOIN tbl_poste_horaire_caserne phc ON phc.tbl_poste_horaire_id = tph.id';
			$criteria->limit=1;
			$criteria->condition='th.date >= :dateDebut AND th.date < :dateFin AND th.statut = 1 AND phc.tbl_caserne_id = :caserne AND th.tbl_caserne_id = :caserne';
			$criteria->params = array(':dateDebut'=>$dateDebut->format("Y-m-d"), ':dateFin'=>$dateFin->format("Y-m-d"), ':caserne'=>$caserne);
			$horaire = Horaire::model()->find($criteria);
			
			if($horaire != NULL){
				$valide = $horaire->statut;
			}else{
				$valide = 0;
			}
	
			$tblEquipe = Equipe::model()->findAll('siHoraire = 1 AND tbl_caserne_id = '.$caserne);
			
			$idsE = '';
			foreach($tblEquipe as $equipe){
				$idsE .= $equipe->id.' ,';
			}
			$idsE = substr($idsE, 0, strlen($idsE)-2);
			
			$listeEquipeGarde = EquipeGarde::model()->findAll('tbl_equipe_id IN ('.$idsE.') AND tbl_garde_id = '.$parametres->garde_horaire);
			$jourSemaine = array(
				Yii::t('generale','dim'),
				Yii::t('generale','lun'),
				Yii::t('generale','mar'),
				Yii::t('generale','mer'),
				Yii::t('generale','jeu'),
				Yii::t('generale','ven'),
				Yii::t('generale','sam'),
				);
	$sql = 
	"SELECT 
		ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) AS Jour, 
		q.nom AS nomQuart,	q.id AS idQuart, q.heureDebut AS qHeureDebut, q.heureFin AS qHeureFin,". 
		//Calcule le nombre d'heure de travail du quart
		"time_to_sec(IF(subtime(q.heureFin,q.HeureDebut)>=0,subtime(q.heureFin,q.HeureDebut),addtime(subtime(q.heureFin,q.HeureDebut),'24:00:00')))/3600 AS qHeureReel, 
		UNIX_TIMESTAMP(CONCAT_WS(' ',ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),q.heureDebut)) AS tsQDebut,
	    UNIX_TIMESTAMP(CONCAT_WS(' ',IF(q.heureFin>=q.heureDebut,ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),ADDDATE(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),1)),q.heureFin)) AS tsQFin,
		p.nom AS Poste, p.id AS idPoste, p.diminutif AS diminutifPoste, 
		ph.id AS phId, ph.heureDebut AS phHeureDebut, ph.heureFin AS phHeureFin, 
		time_to_sec(IF(subtime(ph.heureFin,ph.heureDebut)>=0,subtime(ph.heureFin,ph.heureDebut),addtime(subtime(ph.heureFin,ph.heureDebut),'24:00:00')))/3600 AS phHeureReel,
	    UNIX_TIMESTAMP(CONCAT_WS(' ',ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),ph.heureDebut)) AS tsPHDebut,
	    UNIX_TIMESTAMP(CONCAT_WS(' ',IF(ph.heureFin>=ph.heureDebut,ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),ADDDATE(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),1)),ph.heureFin)) AS tsPHFin,
		u.matricule AS Matricule_horaire, u.id AS ID_pompier_horaire, e.nom AS nom_equipe_garde, IF(ph.couleur IS NOT NULL, ph.couleur,e.couleur) AS couleur_garde, u2.matricule AS matricule_modification, h.type AS typeH,
		h.heureDebut AS hHeureDebut, h.heureFin AS hHeureFin, h.id  AS ID_Horaire,
		time_to_sec(IF(subtime(h.heureFin,h.heureDebut)>=0,subtime(h.heureFin,h.heureDebut),addtime(subtime(h.heureFin,h.heureDebut),'24:00:00')))/3600 AS hHeureReel,
	    UNIX_TIMESTAMP(CONCAT_WS(' ',ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),h.heureDebut)) AS tsHDebut,
	    UNIX_TIMESTAMP(CONCAT_WS(' ',IF(h.heureFin>=h.heureDebut,ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),ADDDATE(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),1)),h.heureFin)) AS tsHFin,
		ab.id AS absence
	FROM 
		((numbers i1, numbers i2), 
		 (tbl_quart q LEFT JOIN (tbl_poste_horaire ph INNER JOIN tbl_poste p ON p.id=ph.tbl_poste_id) ON ph.tbl_quart_id=q.id)) 
	 	  LEFT JOIN tbl_horaire h ON h.tbl_poste_horaire_id=ph.id AND h.date=ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) AND h.type IN (0,2) AND h.tbl_caserne_id = ".$caserne." LEFT JOIN tbl_usager u ON u.id=h.tbl_usager_id 
	 	  LEFT JOIN tbl_horaire h2 ON h2.parent_id = h.id AND h2.dateModif=(SELECT MAX(m.dateModif) FROM tbl_horaire m WHERE m.parent_id=h.id) AND h2.type = 1 LEFT JOIN tbl_usager u2 ON u2.id=h2.tbl_usager_id 
		  LEFT JOIN tbl_equipe_garde eg ON q.id=eg.tbl_quart_id AND eg.modulo=MOD((UNIX_TIMESTAMP(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i)) DIV 86400),".$garde->nbr_jour_periode.") AND eg.tbl_garde_id = ".$parametres->garde_horaire." 
		  LEFT JOIN tbl_equipe e on e.id=eg.tbl_equipe_id
		  LEFT JOIN tbl_absence ab ON ab.dateConge = h.date AND ab.tbl_usager_id = h.tbl_usager_id AND ab.statut = 2 
			  AND(
				(CONCAT(ADDDATE('2015-05-10', i2.i*10+i1.i),' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut)))
				>=
				IF(
					ab.heureDebut > ab.heureFin,
					CONCAT(ADDDATE(ADDDATE('2015-05-10', i2.i*10+i1.i),INTERVAL 1 DAY),' ',ab.heureFin),
					CONCAT(ADDDATE('2015-05-10', i2.i*10+i1.i),' ',ab.heureFin)
				)
				AND		
				IF(
					IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut))>
					IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin)),
					CONCAT(ADDDATE(ADDDATE('2015-05-10', i2.i*10+i1.i),INTERVAL 1 DAY),' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin))),
					CONCAT(ADDDATE('2015-05-10', i2.i*10+i1.i),' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin)))
				)
				>=
				IF(
					ab.heureDebut >= ab.heureFin,
					CONCAT(ADDDATE(ADDDATE('2015-05-10', i2.i*10+i1.i),INTERVAL 1 DAY),' ',ab.heureDebut),
					CONCAT(ADDDATE('2015-05-10', i2.i*10+i1.i),' ',ab.heureDebut)
				))
				OR
				(CONCAT(ADDDATE('2015-05-10', i2.i*10+i1.i),' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut)))
				<=
				IF(
					ab.heureDebut >= ab.heureFin,
					CONCAT(ADDDATE(ADDDATE('2015-05-10', i2.i*10+i1.i),INTERVAL 1 DAY),' ',ab.heureFin),
					CONCAT(ADDDATE('2015-05-10', i2.i*10+i1.i),' ',ab.heureFin)
				)
				AND		
				IF(
					IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut))>
					IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin)),
					CONCAT(ADDDATE(ADDDATE('2015-05-10', i2.i*10+i1.i),INTERVAL 1 DAY),' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin))),
					CONCAT(ADDDATE('2015-05-10', i2.i*10+i1.i),' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin)))
				)
				>=
				IF(
					ab.heureDebut >= ab.heureFin,
					CONCAT(ADDDATE(ADDDATE('2015-05-10', i2.i*10+i1.i),INTERVAL 1 DAY),' ',ab.heureFin),
					CONCAT(ADDDATE('2015-05-10', i2.i*10+i1.i),' ',ab.heureFin)
				))
			  )
		  INNER JOIN tbl_poste_horaire_caserne phc ON phc.tbl_poste_horaire_id = ph.id
	WHERE 
		(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) < '".$dateDebutSuivante->format('Y-m-d')."')
		AND phc.tbl_caserne_id = ".$caserne." AND eg.tbl_caserne_id = ".$caserne."
		AND e.id IN (".$ids.")
		AND (ph.dateFin >= '".$dateDebutSuivante->format('Y-m-d')."' OR ph.dateFin IS NULL)
		AND IF(h.heureDebut <> '00:00:00' AND h.heureFin <> '00:00:00' AND h.heureDebut = h.heureFin,false,true)";
	if(Yii::app()->params['poste_horaire_couleur'] === 1) {
		  $sql .= "ORDER BY Jour, q.heureDebut, ph.order, p.id, ph.heureDebut, h.heureDebut";
		} else {
			$sql .= "ORDER BY Jour, q.heureDebut, p.id, ph.heureDebut, h.heureDebut";
		}
	
	/*AND q.tbl_caserne_id = ".$caserne." AND p.tbl_caserne_id = ".$caserne." AND e.id IN (".$ids.")*/
			$cn = Yii::app()->db;		
			$cm = $cn->createCommand($sql);
			
			$curseurHoraire = $cm->query();
			
			$dateAuj = Horaire::debutPeriode($garde->nbr_jour_periode,$parametres->moduloDebut,date('Y-m-d'));
			
			if($parametres->siCalculHeureHoraire == 1){
				$dateNow = new DateTime('now', new DateTimeZone('America/Montreal'));
				$dateDebutTemps = new DateTime(date('Y').'-'.substr($parametres->dateDebutCalculTemps, -5), new DateTimeZone('America/Montreal'));
				if($dateDebutTemps->getTimestamp()>$dateNow->getTimestamp()){
					$dateDebutTemps = new DateTime((date('Y')-1).'-'.substr($parametres->dateDebutCalculTemps, -5), new DateTimeZone('America/Montreal'));
				}
				/*$dateDebutTemps = clone $dateDebut;
				$dateDebutTemps->sub(new DateInterval("P".($parametres->nbJourPeriode*52)."D"));*/
				$dateFinTemps = clone $dateDebut;
				$dateFinTemps->sub((new DateInterval("P1D")));
				
				$sql =
				"SELECT
					IFNULL(u2.id,u.id) AS ID_pompier,
					IFNULL(u2.matricule,u.matricule) AS matricule,
					SUM(IF(time_to_sec(IF(subtime(h.heureFin,h.heureDebut)>=0,subtime(h.heureFin,h.heureDebut),addtime(subtime(h.heureFin,h.heureDebut),'24:00:00')))/3600 <> 0.0000,
								time_to_sec(IF(subtime(h.heureFin,h.heureDebut)>=0,subtime(h.heureFin,h.heureDebut),addtime(subtime(h.heureFin,h.heureDebut),'24:00:00')))/3600,
								IF(time_to_sec(IF(subtime(ph.heureFin,ph.heureDebut)>=0,subtime(ph.heureFin,ph.heureDebut),addtime(subtime(ph.heureFin,ph.heureDebut),'24:00:00')))/3600 <> 0.0000,
									time_to_sec(IF(subtime(ph.heureFin,ph.heureDebut)>=0,subtime(ph.heureFin,ph.heureDebut),addtime(subtime(ph.heureFin,ph.heureDebut),'24:00:00')))/3600,
									time_to_sec(IF(subtime(q.heureFin,q.heureDebut)>=0,subtime(q.heureFin,q.heureDebut),addtime(subtime(q.heureFin,q.heureDebut),'24:00:00')))/3600
							)
						)) AS HeureTotal,
					q.id AS ID_quart
				FROM
					((numbers i1, numbers i2, numbers i3),
					 (tbl_quart q LEFT JOIN tbl_poste_horaire ph ON ph.tbl_quart_id=q.id LEFT JOIN tbl_poste_horaire_caserne phc ON phc.tbl_poste_horaire_id = ph.id))
				 	  LEFT JOIN tbl_horaire h ON h.tbl_poste_horaire_id=ph.id AND h.date=ADDDATE('".$dateDebutTemps->format('Y-m-d')."', i3.i*100+i2.i*10+i1.i) AND h.type IN (0,2) LEFT JOIN tbl_usager u ON u.id=h.tbl_usager_id
				 	  LEFT JOIN tbl_horaire h2 ON h2.parent_id = h.id AND h2.dateModif=(SELECT MAX(m.dateModif) FROM tbl_horaire m WHERE m.parent_id=h.id) AND h2.type = 1 LEFT JOIN tbl_usager u2 ON u2.id=h2.tbl_usager_id
				WHERE
					(ADDDATE('".date('Y').$dateDebutTemps->format('-m-d')."', i3.i*100+i2.i*10+i1.i) < '".$dateFinTemps->format('Y-m-d')."')
					AND (u2.id IS NOT NULL OR u.id IS NOT NULL)
					AND phc.tbl_caserne_id <> ".$caserne."
							
				GROUP BY q.id, ID_pompier
				ORDER BY ID_pompier";
				
				
				$cn = Yii::app()->db;
				$cm = $cn->createCommand($sql);
				
				//Curseur pour le temps de la période en cours dans les autres casernes
				$curseurTempsAutre = $cm->query();
				
				$sql = 
				"SELECT
					IFNULL(u2.id,u.id) AS ID_pompier,
					IFNULL(u2.matricule,u.matricule) AS matricule,
					SUM(IF(time_to_sec(IF(subtime(h.heureFin,h.heureDebut)>=0,subtime(h.heureFin,h.heureDebut),addtime(subtime(h.heureFin,h.heureDebut),'24:00:00')))/3600 <> 0.0000, 
								time_to_sec(IF(subtime(h.heureFin,h.heureDebut)>=0,subtime(h.heureFin,h.heureDebut),addtime(subtime(h.heureFin,h.heureDebut),'24:00:00')))/3600, 
								IF(time_to_sec(IF(subtime(ph.heureFin,ph.heureDebut)>=0,subtime(ph.heureFin,ph.heureDebut),addtime(subtime(ph.heureFin,ph.heureDebut),'24:00:00')))/3600 <> 0.0000,
									time_to_sec(IF(subtime(ph.heureFin,ph.heureDebut)>=0,subtime(ph.heureFin,ph.heureDebut),addtime(subtime(ph.heureFin,ph.heureDebut),'24:00:00')))/3600,
									time_to_sec(IF(subtime(q.heureFin,q.heureDebut)>=0,subtime(q.heureFin,q.heureDebut),addtime(subtime(q.heureFin,q.heureDebut),'24:00:00')))/3600
							)				
						)) AS HeureTotal,
					q.id AS ID_quart
				FROM 
					((numbers i1, numbers i2, numbers i3), 
					 (tbl_quart q LEFT JOIN tbl_poste_horaire ph ON ph.tbl_quart_id=q.id)) 
				 	  LEFT JOIN tbl_horaire h ON h.tbl_poste_horaire_id=ph.id AND h.date=ADDDATE('".$dateDebutTemps->format('Y-m-d')."', i3.i*100+i2.i*10+i1.i) AND h.type IN (0,2) LEFT JOIN tbl_usager u ON u.id=h.tbl_usager_id 
				 	  LEFT JOIN tbl_horaire h2 ON h2.parent_id = h.id AND h2.dateModif=(SELECT MAX(m.dateModif) FROM tbl_horaire m WHERE m.parent_id=h.id) AND h2.type = 1 LEFT JOIN tbl_usager u2 ON u2.id=h2.tbl_usager_id 
				WHERE 
					(ADDDATE('".date('Y').$dateDebutTemps->format('-m-d')."', i3.i*100+i2.i*10+i1.i) <= '".$dateFinTemps->format('Y-m-d')."')
					AND (u2.id IS NOT NULL OR u.id IS NOT NULL)
				GROUP BY q.id, ID_pompier
				ORDER BY ID_pompier";
				
				
				$cn = Yii::app()->db;
				$cm = $cn->createCommand($sql);
			
				//Curseur pour le temps des X périodes précédentes
				$curseurTemps = $cm->query();
			}else{
				$curseurTempsAutre = NULL;
				$curseurTemps = NULL;
			}
	
			$criteriaQuart = new CDbCriteria;
			$criteriaQuart->order = 'heureDebut ASC';
			$quarts = Quart::model()->findAll($criteriaQuart);
			
			if(Yii::app()->request->isAjaxRequest && $date!=""){
				$userAccess = Yii::app()->user->checkAccess('GesHoraire');
				$arrayMois = array("","janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre");
				
				$this->renderPartial('_horaire',array(
					'curseurTempsAutre' => $curseurTempsAutre,
					'curseurTemps'	=>$curseurTemps,
					'jourSemaine'	  =>$jourSemaine,
					'parametres'	  =>$parametres,
					'siPeriodePrecedente'=>$siPeriodePrecedente,
					'periodeSuivante' => $periodeSuivante,
					'curseurHoraire'  => $curseurHoraire,
					'caserne'		  => $caserne,
					'dateAuj'		=> $dateAuj,
					'valide'		=> $valide,
					'quarts'		=>$quarts,
					'userAccess'	=>$userAccess,
					'dateDebut'	=>$dateDebut,
					'dateFin'   => $dateFin,
					'arrayMois'	=> $arrayMois,
				));
			}else{
				$dataEquipe=new CActiveDataProvider('Equipe',array(
					'pagination'=>array(
						'pageSize'=>10
					),
					'criteria'=>array('condition'=>'siHoraire=1 AND siActif=1 AND id IN ('.$ids.')')
				));
				
				
				$listeEquipe = new CActiveDataProvider('Equipe',array(
					'pagination'=>false,
					'criteria'=>array('condition'=>'siActif=1 AND siHoraire = 1 AND id IN ('.$ids.')'),
				));
				
				$criteria = new CDbCriteria();
				$criteria->order = $parametres->colonne.' '.$parametres->ordre;
				$idU = '';
				if($idEquipe!=""){
					$idE = $idEquipe;
					
					$equipesU = EquipeUsager::model()->findAll('tbl_equipe_id IN ('.$idE.')');
					//$ids devient les id des usagers
					foreach($equipesU as $equipe){
						$idU .= $equipe->tbl_usager_id.', ';
					}
					$idU = substr($idU,0,strlen($idU)-2);				
					
				}else{
					$equipesU = EquipeUsager::model()->findAll('tbl_equipe_id IN ('.$ids.')');
					//$ids devient les id des usagers
					foreach($equipesU as $equipe){
						$idU .= $equipe->tbl_usager_id.', ';
					}
					$idU = substr($idU,0,strlen($idU)-2);
				
				}
				if($idU == ''){
					$idU = '0';
				}
				
				$criteria->condition .= "id IN (".$idU.")";
				
				$dataUsager=new CActiveDataProvider('Usager',array(
					'pagination'=>array(
						'pageSize'=>15
					),
					'criteria'=>$criteria
				));
	
				$this->render('index',array(
					'curseurTempsAutre' => $curseurTempsAutre,
					'curseurTemps'	=>$curseurTemps,
					'curseurHoraire' => $curseurHoraire,
					'jourSemaine'    => $jourSemaine,
					'parametres'	 => $parametres,
					'dataEquipe'	 => $dataEquipe,
					'dataUsager'	 => $dataUsager,
					'listeEquipe'	 => $listeEquipe,
					'siPeriodePrecedente'=>$siPeriodePrecedente,
					'periodeSuivante' => $periodeSuivante,
					'caserne'		  => $caserne,
					'tblCaserne'	  => $tblCaserne,	
					'dateDebut'		=> $dateDebut,
					'dateFin'       => $dateFin,
					'valide'		=> $valide,
					'dateAuj'		=> $dateAuj,
					'quarts'		=>$quarts,
				));
			}
		}
	}
	
	public function actionIndexTP($caserne="",$date="",$idEquipe=""){
		$this->pageTitle = 'Horaire fixe';
		$parametres = Parametres::model()->findByPk(1);
		$usager = Usager::model()->findByPk(Yii::app()->user->id);
		$casernesUsager = $usager->getCaserne();
		$casernes = Caserne::model()->findAll(array('condition'=>'id IN ('.$casernesUsager.') AND siActif = 1 && si_horaire'));
		$tblCaserne = CHtml::listData($casernes,'id','nom');		
		
		if($caserne==""){
			if(isset($parametres->caserne_defaut_horaire)){
				$caserne = $parametres->caserne_defaut_horaire;
			}else{
				//legacy code
				foreach($casernes as $cas){
					$caserne = $cas->id;
					break;
				}			
			}
		}
		
		$equipes = Equipe::model()->findAll('siActif = 1 AND tbl_caserne_id = '.$caserne);
		$ids = '';
		foreach($equipes as $equipe){
			$ids .= $equipe->id.', ';
		}
		$ids = substr($ids,0,strlen($ids)-2);
		
		$garde = Garde::model()->findByPk($parametres->garde_horaire);
		
		$date = '1990-01-01';
		
		$dateDebut = Horaire::debutPeriode($parametres->nbJourHoraireFixe,$parametres->moduloDebut,$date);
		
		//$dateDebutPrecedente = date_sub(clone $dateDebut,new DateInterval("P".$parametres->nbJourHoraireFixe."D"));
		$dateDebutSuivante = date_add(clone $dateDebut,new DateInterval("P".$parametres->nbJourHoraireFixe."D"));
		//$dateDebutSuivanteSuivante = date_add(clone $dateDebut,new DateInterval("P".($parametres->nbJourHoraireFixe*2)."D"));
		//$dateFin = date_add(clone $dateDebut,new DateInterval("P".$parametres->nbJourHoraireFixe."D"));

		$tblEquipe = Equipe::model()->findAll('siHoraire = 1 AND tbl_caserne_id = '.$caserne);
		
		$idsE = '';
		foreach($tblEquipe as $equipe){
			$idsE .= $equipe->id.' ,';
		}
		$idsE = substr($idsE, 0, strlen($idsE)-2);
		
		$listeEquipeGarde = EquipeGarde::model()->findAll('tbl_equipe_id IN ('.$idsE.') AND tbl_garde_id = '.$garde->id.' AND tbl_caserne_id = '.$caserne);
		$jourSemaine = array(
			Yii::t('generale','dim'),
			Yii::t('generale','lun'),
			Yii::t('generale','mar'),
			Yii::t('generale','mer'),
			Yii::t('generale','jeu'),
			Yii::t('generale','ven'),
			Yii::t('generale','sam'),
		);

		$garde = Garde::model()->findByPk($parametres->garde_horaire);
		
$sql = 
"SELECT 
	ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) AS Jour, 
	q.nom AS nomQuart,	q.id AS idQuart, q.heureDebut AS qHeureDebut, q.heureFin AS qHeureFin,". 
	//Calcule le nombre d'heure de travail du quart
	"time_to_sec(IF(subtime(q.heureFin,q.HeureDebut)>=0,subtime(q.heureFin,q.HeureDebut),addtime(subtime(q.heureFin,q.HeureDebut),'24:00:00')))/3600 AS qHeureReel, 
	UNIX_TIMESTAMP(CONCAT_WS(' ',ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),q.heureDebut)) AS tsQDebut,
    UNIX_TIMESTAMP(CONCAT_WS(' ',IF(q.heureFin>=q.heureDebut,ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),ADDDATE(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),1)),q.heureFin)) AS tsQFin,
	p.nom AS Poste, p.id AS idPoste, p.diminutif AS diminutifPoste, 
	ph.id AS phId, ph.heureDebut AS phHeureDebut, ph.heureFin AS phHeureFin, 
	time_to_sec(IF(subtime(ph.heureFin,ph.heureDebut)>=0,subtime(ph.heureFin,ph.heureDebut),addtime(subtime(ph.heureFin,ph.heureDebut),'24:00:00')))/3600 AS phHeureReel,
    UNIX_TIMESTAMP(CONCAT_WS(' ',ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),ph.heureDebut)) AS tsPHDebut,
    UNIX_TIMESTAMP(CONCAT_WS(' ',IF(ph.heureFin>=ph.heureDebut,ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),ADDDATE(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),1)),ph.heureFin)) AS tsPHFin,
	u.matricule AS Matricule_horaire, u.id AS ID_pompier_horaire, e.nom AS nom_equipe_garde, IF(ph.couleur IS NOT NULL, ph.couleur,e.couleur) AS couleur_garde, h.type AS typeH,
	h.heureDebut AS hHeureDebut, h.heureFin AS hHeureFin, h.id  AS ID_Horaire,
	time_to_sec(IF(subtime(h.heureFin,h.heureDebut)>=0,subtime(h.heureFin,h.heureDebut),addtime(subtime(h.heureFin,h.heureDebut),'24:00:00')))/3600 AS hHeureReel,
    UNIX_TIMESTAMP(CONCAT_WS(' ',ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),h.heureDebut)) AS tsHDebut,
    UNIX_TIMESTAMP(CONCAT_WS(' ',IF(h.heureFin>=h.heureDebut,ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),ADDDATE(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i),1)),h.heureFin)) AS tsHFin
FROM 
	((numbers i1, numbers i2), 
	 (tbl_quart q LEFT JOIN (tbl_poste_horaire ph INNER JOIN tbl_poste p ON p.id=ph.tbl_poste_id) ON ph.tbl_quart_id=q.id)) 
 	  LEFT JOIN tbl_horaire h ON h.tbl_poste_horaire_id=ph.id AND h.date=ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) AND h.type IN (0,2) AND h.tbl_caserne_id = ".$caserne." LEFT JOIN tbl_usager u ON u.id=h.tbl_usager_id 
	  LEFT JOIN tbl_equipe_garde eg ON q.id=eg.tbl_quart_id AND eg.modulo=MOD((UNIX_TIMESTAMP(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i)) DIV 86400),".$garde->nbr_jour_periode.") AND eg.tbl_garde_id = ".$parametres->garde_horaire." AND eg.tbl_caserne_id = ".$caserne." 
	  LEFT JOIN tbl_equipe e on e.id=eg.tbl_equipe_id
	  INNER JOIN tbl_poste_horaire_caserne phc ON phc.tbl_poste_horaire_id = ph.id
WHERE 
	(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) < '".$dateDebutSuivante->format('Y-m-d')."')
	AND phc.tbl_caserne_id = ".$caserne." AND e.id IN (".$ids.")
	AND (ph.dateFin >= '".date('Y-m-d')."' OR ph.dateFin IS NULL)
GROUP BY Jour, phId, ID_Horaire";

if(Yii::app()->params['poste_horaire_couleur'] === 1) {
	$sql .= " ORDER BY Jour, q.heureDebut, ph.order, p.id, ph.heureDebut, h.heureDebut";
} else {
	$sql .= " ORDER BY Jour, q.heureDebut, p.id, ph.heureDebut, h.heureDebut";
}


/*AND q.tbl_caserne_id = ".$caserne." AND p.tbl_caserne_id = ".$caserne." AND e.id IN (".$ids.")*/
		$cn = Yii::app()->db;		
		$cm = $cn->createCommand($sql);	
		
		$curseurHoraire = $cm->query();


		$dataEquipe=new CActiveDataProvider('Equipe',array(
			'pagination'=>array(
				'pageSize'=>10
			),
			'criteria'=>array('condition'=>'siHoraire=1 AND siActif=1 AND id IN ('.$ids.')')
		));
		
		
		$listeEquipe = new CActiveDataProvider('Equipe',array(
			'pagination'=>false,
			'criteria'=>array('condition'=>'siActif=1 AND siHoraire = 1 AND id IN ('.$ids.')'),
		));
		
		$criteria = new CDbCriteria();
		$criteria->order = $parametres->colonne.' '.$parametres->ordre;
		$idU = '';
		if($idEquipe!=""){
			$idE = $idEquipe;
			
			$equipesU = EquipeUsager::model()->findAll('tbl_equipe_id IN ('.$idE.')');
			//$ids devient les id des usagers
			foreach($equipesU as $equipe){
				$idU .= $equipe->tbl_usager_id.', ';
			}
			$idU = substr($idU,0,strlen($idU)-2);				
			
		}else{
			$equipesU = EquipeUsager::model()->findAll('tbl_equipe_id IN ('.$ids.')');
			//$ids devient les id des usagers
			foreach($equipesU as $equipe){
				$idU .= $equipe->tbl_usager_id.', ';
			}
			$idU = substr($idU,0,strlen($idU)-2);
		
		}	
		if($idU == ''){
			$idU = '0';
		}
		
		$criteria->condition .= "id IN (".$idU.")";
		
		$dataUsager=new CActiveDataProvider('Usager',array(
			'pagination'=>array(
				'pageSize'=>15
			),
			'criteria'=>$criteria
		));
		
		$sqlEG = "
				SELECT eq.modulo, e.couleur, eq.tbl_quart_id
				FROM tbl_equipe_garde eq
				LEFT JOIN tbl_equipe e ON eq.tbl_equipe_id = e.id
				WHERE 
				eq.tbl_caserne_id =1
				AND eq.tbl_garde_id = ".$parametres->garde_horaire." 
				ORDER BY modulo ASC 
				";
		
		$cn = Yii::app()->db;
		$cm = $cn->createCommand($sqlEG);
		
		$curseurEG = $cm->query();
		
		$row = $curseurEG->read();
		
		$tblEquipeGarde = array();
		do{
			$tblEquipeGarde[$row['modulo']][$row['tbl_quart_id']] = $row['couleur'];
			$row = $curseurEG->read();
		}while($row!==false);
		
		$this->render('indexTP',array(
			'tblEquipeGarde' => $tblEquipeGarde,
			'curseurHoraire' => $curseurHoraire,
			'jourSemaine'    => $jourSemaine,
			'parametres'	 => $parametres,
			'dataEquipe'	 => $dataEquipe,
			'dataUsager'	 => $dataUsager,
			'listeEquipe'	 => $listeEquipe,
			'caserne'		  => $caserne,
			'tblCaserne'	  => $tblCaserne,	
			'dateDebut'		=> $dateDebut,
			'garde'			=>$garde,
		));
	}
	
	public function actionPdfHoraire($date, $caserne){
		$parametres = Parametres::model()->findByPk(1);
		
		$equipes = Equipe::model()->findAll('siActif = 1 AND tbl_caserne_id = '.$caserne);
		$ids = '';
		foreach($equipes as $equipe){
			$ids .= $equipe->id.', ';
		}
		$ids = substr($ids,0,strlen($ids)-2);
		
		$dateDebut = new DateTime($date."T00:00:00",new DateTimeZone($parametres->timezone));
		$dateDebutSuivante = date_add(clone $dateDebut,new DateInterval("P".$parametres->nbJourPeriode."D"));
		
		$sql = "SELECT
	IF((UNIX_TIMESTAMP(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i)) DIV 86400) - (UNIX_TIMESTAMP('".$dateDebut->format('Y-m-d').
		"') DIV 86400)<=6,'0',(((UNIX_TIMESTAMP(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i)) DIV 86400) - (UNIX_TIMESTAMP('".
				$dateDebut->format('Y-m-d')."') DIV 86400)) DIV 7)) AS semaine,
	ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) AS Jour,
	q.nom AS nomQuart, q.id AS idQuart, q.heureDebut AS qHeureDebut, q.heureFin AS qHeureFin,
	p.nom AS Poste, p.id AS idPoste, p.diminutif AS diminutifPoste,
	ph.id AS phId, ph.heureDebut AS phHeureDebut, ph.heureFin AS phHeureFin,
	u.matricule AS Matricule_horaire, IF(ph.couleur IS NOT NULL, ph.couleur,e.couleur) AS couleur_garde, u2.matricule AS matricule_modification, h.type AS typeH,
	h.heureDebut AS hHeureDebut, h.heureFin AS hHeureFin, h.id  AS ID_Horaire
FROM
	((numbers i1, numbers i2),
	 (tbl_quart q LEFT JOIN (tbl_poste_horaire ph INNER JOIN tbl_poste p ON p.id=ph.tbl_poste_id) ON ph.tbl_quart_id=q.id))
 	  LEFT JOIN tbl_horaire h ON h.tbl_poste_horaire_id=ph.id AND h.date=ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) AND h.type IN (0,2) AND h.tbl_caserne_id = ".$caserne." LEFT JOIN tbl_usager u ON u.id=h.tbl_usager_id
 	  LEFT JOIN tbl_horaire h2 ON h2.parent_id = h.id AND h2.dateModif=(SELECT MAX(m.dateModif) FROM tbl_horaire m WHERE m.parent_id=h.id) AND h2.type = 1 LEFT JOIN tbl_usager u2 ON u2.id=h2.tbl_usager_id
	  LEFT JOIN tbl_equipe_garde eg ON q.id=eg.tbl_quart_id AND eg.modulo=MOD((UNIX_TIMESTAMP(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i)) DIV 86400),".$parametres->nbJourPeriode.")
	  LEFT JOIN tbl_equipe e on e.id=eg.tbl_equipe_id
	  INNER JOIN tbl_poste_horaire_caserne phc ON phc.tbl_poste_horaire_id = ph.id
WHERE
	(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) < '".$dateDebutSuivante->format('Y-m-d')."')
	AND phc.tbl_caserne_id = ".$caserne."
GROUP BY Jour, phId, ID_Horaire
ORDER BY semaine, q.heureDebut, p.id, Jour, h.heureDebut, ph.heureDebut";
		$cn = Yii::app()->db;		
		$cm = $cn->createCommand($sql);
		
		
		$curseurHoraire = $cm->query();
		
		$jourSemaine = array(
			Yii::t('generale','dim'),
			Yii::t('generale','lun'),
			Yii::t('generale','mar'),
			Yii::t('generale','mer'),
			Yii::t('generale','jeu'),
			Yii::t('generale','ven'),
			Yii::t('generale','sam'),
		);
		// get a reference to the path of PHPExcel classes 
		$phpExcelPath = Yii::getPathOfAlias('system.ext.PHPExcel.Classes');
		
		// Turn off our amazing library autoload 
		spl_autoload_unregister(array('YiiBase','autoload'));        
		
		// making use of our reference, include the main class
		// when we do this, phpExcel has its own autoload registration
		// procedure (PHPExcel_Autoloader::Register();)
		include($phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
			 
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
			    
		// Set properties
		$objPHPExcel->getProperties()->setCreator("Studio Swordware Inc.")
			->setLastModifiedBy("Studio Swordware Inc.")
			->setTitle(Yii::t('controller','horaire.pdfHoraire.setTitle'))
			->setSubject(Yii::t('controller','horaire.pdfHoraire.setSubject'))
			->setDescription(Yii::t('controller','horaire.pdfHoraire.setDescription', array('{dateDebut}'=>$dateDebut->format("d-m-Y"))))
			->setKeywords(Yii::t('controller','horaire.pdfHoraire.setKeywords'))
			->setCategory("");
			
		//Cache la grille à l'impression
		$objPHPExcel->getActiveSheet()->setShowGridlines(true);
		
		$objPHPExcel->setActiveSheetIndex(0)
			->mergeCells('A1:J1')
			->setCellValue('A1', Yii::t('controller','horaire.pdfHoraire.setCellValue', array('{dateDebut}'=>$dateDebut->format("d-m-Y"))));
		
		// tentative de centrer la feuille qui marche plus ou moins
		$objPHPExcel->getActiveSheet()->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
		$indiceSemaine = 1;
		$indiceJour = 0;
		$row = $curseurHoraire->read();
		$poste = "";
		$caseHoraire = "";
		$col[0] = "C";	$col[1] = "D";	$col[2] = "E";	$col[3] = "F";	$col[4] = "G";	$col[5] = "H";	$col[6] = "I";
		$default_border = array( 
			'style' => PHPExcel_Style_Border::BORDER_THIN, 
			'color' => array('rgb'=>'1006A3') ); 
		$style_header = array( 'borders' => 
			array( 'bottom' => $default_border, 
				   'left' => $default_border, 
				   'top' => $default_border, 
				   'right' => $default_border, 
			), 
			'fill' => array( 
				'type' => PHPExcel_Style_Fill::FILL_SOLID, 
				'color' => array('rgb'=>'E1E0F7'), 
			), 
			'font' => array( 'bold' => false, ) );
		
		$premiereLigne = true;
		$indiceLigne = 2;
		$ajustementIndiceLigne = 2;
		do{
			$semaine = $row['semaine'];
			$premierQuart = true;
			$nbPoste = 0;
			do{
				$nbrPosteHoraireMax = 0;
				$poste = $row['idPoste'];
				$diminutifPoste = $row['diminutifPoste'];
				$quart = $row['idQuart'];
				$nomQuart = $row['nomQuart'];
				$indiceJour = 0;
				do{
					$jour = $row['Jour'];
					$nbrPosteHoraire = 0;
					if($premierQuart){
						$style_header['fill']['color']['rgb'] = "FFFFFF";
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col[$indiceJour].$indiceLigne,$jourSemaine[date('w',strtotime($row['Jour']))].' '.date('d',strtotime($row['Jour'])))
							->getStyle($col[$indiceJour].$indiceLigne)->applyFromArray($style_header);
						$indiceLigne++;
					}
					$style_header['fill']['color']['rgb'] = $row['couleur_garde'];
					do{
						
						$pompierLabel = $row['matricule_modification']===NULL?$row['Matricule_horaire']:$row['matricule_modification'];
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col[$indiceJour].$indiceLigne,($pompierLabel))
							->getStyle($col[$indiceJour].$indiceLigne)->applyFromArray($style_header);
						$indiceLigne++;
						$nbrPosteHoraire++;
						$row = $curseurHoraire->read();
					}while($jour == $row['Jour']);
					$indiceJour++;
					if($nbrPosteHoraireMax < $nbrPosteHoraire){
						$nbrPosteHoraireMax = $nbrPosteHoraire;
					}
					$indiceLigne = $ajustementIndiceLigne;
				}while($poste == $row['idPoste']);
				if($premierQuart){
					$ajustementIndiceLigne++;
					$indiceLigne++;
					$premierQuart = false;
				}
				$style_header['fill']['color']['rgb'] = "FFFFFF";
				//permet de recopier la couleur din cases vides quand il y a plus que 1 case horaire pour un poste sur un des jour dla semaine
				if($nbrPosteHoraireMax > 1){ 
					$objPHPExcel->getActiveSheet()->mergeCells('B'.($indiceLigne).':B'.($indiceLigne+$nbrPosteHoraireMax-1));
					$objPHPExcel->getActiveSheet()->getStyle("B".$indiceLigne)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					foreach($col as $indexColonne){
						$objPHPExcel->getActiveSheet()->duplicateStyle($objPHPExcel->getActiveSheet()->getStyle($indexColonne.$indiceLigne), 
																			$indexColonne.($indiceLigne+1).':'.$indexColonne.($indiceLigne+$nbrPosteHoraireMax));
					}
				}
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue("B".$indiceLigne,$diminutifPoste);
				$objPHPExcel->getActiveSheet()->getStyle("B".$indiceLigne)->applyFromArray($style_header);
				$ajustementIndiceLigne += $nbrPosteHoraireMax;
				$indiceLigne += $nbrPosteHoraireMax;
				$nbPoste += $nbrPosteHoraireMax;
				if($quart != $row['idQuart']){
					$style_header['fill']['color']['rgb'] = "FFFFFF";
					$objPHPExcel->getActiveSheet()->mergeCells('A'.($indiceLigne-$nbPoste).':A'.($indiceLigne-1));
					$objPHPExcel->getActiveSheet()->setCellValue('A'.($indiceLigne-$nbPoste),$nomQuart);
					$objPHPExcel->getActiveSheet()->getStyle('A'.($indiceLigne-$nbPoste))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('A'.($indiceLigne-$nbPoste))->applyFromArray($style_header);
					$nbPoste = 0;
				}
			}while($semaine == $row['semaine']);
		}while($row!==false);
		
		
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/msexcel');
		header('Content-Disposition: attachment;filename="horaire.xls"');
		header('Cache-Control: max-age=0');
		 
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		Yii::app()->end();
			 
		// Once we have finished using the library, give back the 
		// power to Yii... 
		// spl_autoload_register(array('YiiBase','autoload'));
	}
	
	public function actionValider($date, $caserne){
		$validation = new Validation;
		$tblReponse = $validation->validerHoraire($date, $caserne);
		echo json_encode($tblReponse);
		Yii::app()->end();
	}
	
	public function actionFermer($date, $caserne){
		$parametres = Parametres::model()->findByPk(1);
		$dateDebut = new DateTime($date."T00:00:00",new DateTimeZone($parametres->timezone));
		$dateDebutSuivante = date_add(clone $dateDebut,new DateInterval("P".$parametres->nbJourPeriode."D"));
		
		try{
			$transaction = Yii::app()->db->beginTransaction();
			$criteria = new CDbCriteria;
			$criteria->alias = 'h';
			$criteria->join = 'LEFT JOIN tbl_poste_horaire ph ON h.tbl_poste_horaire_id = ph.id '.
					'INNER JOIN tbl_poste_horaire_caserne phc ON phc.tbl_poste_horaire_id = ph.id';
			$criteria->condition = 'h.date >= :dateDebut AND h.date < :dateDebutSuivante AND phc.tbl_caserne_id = :caserne AND h.tbl_caserne_id = :caserne';
			$criteria->params = array(':dateDebut'=>$dateDebut->format('Y-m-d'), ':dateDebutSuivante'=>$dateDebutSuivante->format('Y-m-d'), ':caserne'=>$caserne);
			
			$horaires = Horaire::model()->findAll($criteria);
			
			foreach($horaires as $horaire){
				$horaire->statut = 1;
			
				if(!$horaire->save()){
					echo Yii::t('erreur','horaire.fermer');
					$transaction->rollback();
					Yii::app()->end();
				}
			}
			$transaction->commit();
			echo '1';
		}catch(Exception $e){
			$transaction->rollback();
			echo Yii::t('erreur','horaire.fermer');
		}
		Yii::app()->end();
	}
	
	public function actionChange(){
		$caseHoraire = Horaire::model()->findByAttributes(array('date'=>$_POST['date'],'tbl_poste_horaire_id'=>$_POST['poste'],'type'=>0,'tbl_caserne_id'=>$_POST['caserne']));
		$usager = Usager::model()->findByAttributes(array('matricule'=>$_POST['usager'], 'actif'=>1));
		
		if($_POST['usager']==''){
			$caseHoraire->delete();
		}elseif($usager===NULL){
			if($caseHoraire===NULL){
				echo "";
			}else{
				$reponse = array();
				$reponse['matricule'] = $caseHoraire->Usager->matricule;
				echo json_encode($reponse);
			}
		}else{
			if($caseHoraire==NULL){
				$caseHoraire = new Horaire();
				$caseHoraire->tbl_poste_horaire_id = $_POST['poste'];
				$caseHoraire->date = $_POST['date'];
				$caseHoraire->type = 0;
				$caseHoraire->statut = 0;
				$caseHoraire->tbl_caserne_id = $_POST['caserne'];
			}
			$caseHoraire->tbl_usager_id = $usager->id;
			if($caseHoraire->save()){
				$reponse = array();
				$reponse['matricule'] = $usager->matricule;
				echo json_encode($reponse);
			}
		}
		
		Yii::app()->end();
	}
	
	public function actionListeDispo(){
			if(isset($_POST['poste'])&&isset($_POST['quart'])){
			$parametres = Parametres::model()->findByPk(1);
			
			$poste = Poste::model()->findByPk($_POST['poste']);
			$quart = Quart::model()->findByPk($_POST['quart']);
			$date = $_POST['date'];
			$datetimeD = new DateTime($date.$quart->heureDebut,new DateTimeZone($parametres->timezone));
			if($quart->heureFin < $quart->heureDebut){
				$nouvelleDate = new DateTime($date,new DateTimeZone($parametres->timezone));
				$nouvelleDate->add(new DateInterval('P1D'));
				$date = $nouvelleDate->format('Y-m-d');
			}
			$datetimeF = new DateTime($date.$quart->heureFin,new DateTimeZone($parametres->timezone));
			$critere = new CDbCriteria();
			$critere->select = 't.nom, prenom, matricule, t.id, dispo.tsDebut AS dHeureDebut, dispo.tsFin AS dHeureFin, q.id AS id_quart';
			$critere->alias = 't';
			$critere->order = 't.dateEmbauche';
			$critere->join = 'INNER JOIN tbl_dispo_horaire dispo ON dispo.tbl_usager_id=t.id '.
							 'INNER JOIN tbl_usager_poste poste ON poste.tbl_usager_id=t.id '.
							'INNER JOIN tbl_quart q ON q.id = dispo.tbl_quart_id';
			$critere->condition = 't.enService=1 AND poste.tbl_poste_id=:poste AND ((dispo.tsDebut>=:debut AND dispo.tsDebut<:fin) OR (dispo.tsFin>:debut AND dispo.tsFin<=:fin)) AND dispo.dispo=0';
			$critere->params = array(':poste'=>$poste->id,':debut'=>$datetimeD->format('Y-m-d H:i:s'), ':fin'=>$datetimeF->format('Y-m-d H:i:s'));
			$critere->group = 'matricule';
			
			$critereQuart = new CDbCriteria;
			$critereQuart->order = 'heureDebut DESC';
			$quarts = Quart::model()->findAll($critereQuart);
			if($parametres->listeDispo_Equipe == 1){
				$critereEquipe = new CDbCriteria;
				$critereEquipe->condition = 'siActif = 1';
				$critereEquipe->condition .= ' AND tbl_caserne_id = '.$_POST['caserne'];
				$equipes = Equipe::model()->findAll($critereEquipe);
				$ids = '';
				foreach($equipes as $equipe){
					$ids .= $equipe->id.', ';
				}
				$ids = substr($ids,0,strlen($ids)-2);			
				$critere->join .=  ' INNER JOIN tbl_equipe_usager equipe ON equipe.tbl_usager_id=t.id';
				$critere->condition .= ' AND equipe.tbl_equipe_id IN ('.$ids.')';
			}						
			$liste = Usager::model()->findAll($critere);
			$retour = "";
			$tblUsagerPoste = array();
			foreach($liste as $usager){
				$usagerDHeureDebut = new DateTime($usager->dHeureDebut,new DateTimeZone($parametres->timezone));
				$usagerDHeureFin = new DateTime($usager->dHeureFin,new DateTimeZone($parametres->timezone));
				if(($usagerDHeureDebut->format('H:i:s') != $quart->heureDebut && $usagerDHeureFin->format('H:i:s') != $quart->heureFin)){
					$dispoP = true;
					$criteria = new CDbCriteria;
					$criteria->alias = 'dispo';
					$criteria->join = 'INNER JOIN tbl_usager t ON t.id = dispo.tbl_usager_id';
					$criteria->condition = 't.matricule = :pompier AND ((dispo.tsDebut>=:debut AND dispo.tsDebut<:fin) OR (dispo.tsFin>:debut AND dispo.tsFin<=:fin)) AND dispo.dispo=0';
					$criteria->params = array(':pompier'=>$usager->matricule,':debut'=>$datetimeD->format('Y-m-d H:i:s'), ':fin'=>$datetimeF->format('Y-m-d H:i:s'));
					
					$dispos = DispoHoraire::model()->findAll($criteria);
					
					$dispoPart = '';
					foreach($dispos as $dispo){
						$dispoPart .= $dispo->heureDebut.' - '.$dispo->heureFin.'<br/>';
					}
					$dispoPart = substr($dispoPart,0,strlen($dispoPart)-5);
				}else{
					$dispoP = false;
				}
				$retour = $retour.'<div class="ddedit_item" semaine="'.$_POST['semaine'].'" valeur="'.$usager->matricule.'" quart="'.$usager->id_quart.'">'.$usager->matprenomnom;
				if($parametres->horaireCalculHeure==0){
					$retour .= '<div class="heuresPeriode">0</div>';
					$retour .= '<div class="heures">0</div>';
				}elseif($parametres->horaireCalculHeure==1){
					foreach($quarts as $q){
						$retour = $retour.'<div class="heures" quart="'.$q->id.'">0</div>';
					}
					foreach($quarts as $q){
						$retour = $retour.'<div class="heuresPeriode" quart="'.$q->id.'">0</div>';
					}
				}
				$retour = $retour.(($dispoP)?CHtml::image(Yii::app()->baseUrl.'/images/clock.png', 'Dispo',array('class'=>'dispoPartielle', 'dispo'=>$dispoPart)):'').'</div>';
				
				$tblUsagerPoste[] = $usager->id;
			}
			if($poste->formationObli == 0){
				$critere = new CDbCriteria();
				$critere->select = 't.nom, prenom, matricule, t.id, dispo.tsDebut AS dHeureDebut, dispo.tsFin AS dHeureFin, q.id AS id_quart';
				$critere->alias = 't';
				$critere->order = 't.dateEmbauche';
				$critere->join = 'INNER JOIN tbl_dispo_horaire dispo ON dispo.tbl_usager_id=t.id '.
								'INNER JOIN tbl_quart q ON q.id = dispo.tbl_quart_id';
				$critere->condition = 't.enService=1 AND ((dispo.tsDebut>=:debut AND dispo.tsDebut<:fin) OR (dispo.tsFin>:debut AND dispo.tsFin<=:fin)) AND dispo.dispo=0';
				$critere->params = array(':debut'=>$datetimeD->format('Y-m-d H:i:s'), ':fin'=>$datetimeF->format('Y-m-d H:i:s'));
				$critere->addNotInCondition('t.id',$tblUsagerPoste,'AND');
				$critere->group = 'matricule';
				if($parametres->listeDispo_Equipe == 1){
					$critereEquipe = new CDbCriteria;
					$critereEquipe->condition = 'siActif = 1';
					$critereEquipe->condition .= ' AND tbl_caserne_id = '.$_POST['caserne'];
					$equipes = Equipe::model()->findAll($critereEquipe);
					$ids = '';
					foreach($equipes as $equipe){
						$ids .= $equipe->id.', ';
					}
					$ids = substr($ids,0,strlen($ids)-2);
					$critere->join .=  ' INNER JOIN tbl_equipe_usager equipe ON equipe.tbl_usager_id=t.id';
					$critere->condition .= ' AND equipe.tbl_equipe_id IN ('.$ids.')';
				}
				$liste = Usager::model()->findAll($critere);
					foreach($liste as $usager){
						$usagerDHeureDebut = new DateTime($usager->dHeureDebut,new DateTimeZone($parametres->timezone));
						$usagerDHeureFin = new DateTime($usager->dHeureFin,new DateTimeZone($parametres->timezone));
						if(($usagerDHeureDebut->format('H:i:s') != $quart->heureDebut && $usagerDHeureFin->format('H:i:s') != $quart->heureFin)){
							$dispoP = true;
							$criteria = new CDbCriteria;
							$criteria->alias = 'dispo';
							$criteria->join = 'INNER JOIN tbl_usager t ON t.id = dispo.tbl_usager_id';
							$criteria->condition = 't.matricule = :pompier AND ((dispo.tsDebut>=:debut AND dispo.tsDebut<:fin) OR (dispo.tsFin>:debut AND dispo.tsFin<=:fin)) AND dispo.dispo=0';
							$criteria->params = array(':pompier'=>$usager->matricule, ':debut'=>$datetimeD->format('Y-m-d H:i:s'), ':fin'=>$datetimeF->format('Y-m-d H:i:s'));
							
							$dispos = DispoHoraire::model()->findAll($criteria);
							
							$dispoPart = '';
							foreach($dispos as $dispo){
								$dispoPart .= $dispo->heureDebut.' - '.$dispo->heureFin.'<br/>';
							}
							$dispoPart = substr($dispoPart,0,strlen($dispoPart)-5);				
						}else{
							$dispoP = false;
						}
					$retour = $retour.'<div class="ddedit_item nonPoste" semaine="'.$_POST['semaine'].'" valeur="'.$usager->matricule.'" quart="'.$usager->id_quart.'">'.$usager->matprenomnom;
					if($parametres->horaireCalculHeure==0){
						$retour .= '<div class="heuresPeriode">0</div>';
						$retour .= '<div class="heures">0</div>';
					}elseif($parametres->horaireCalculHeure==1){
						foreach($quarts as $q){
							$retour = $retour.'<div class="heures" quart="'.$q->id.'">0</div>';
						}
						foreach($quarts as $q){
							$retour = $retour.'<div class="heuresPeriode" quart="'.$q->id.'">0</div>';
						}
					}			
					$retour = $retour.(($dispoP)?CHtml::image(Yii::app()->baseUrl.'/images/clock.png', 'Dispo',array('class'=>'dispoPartielle', 'dispo'=>$dispoPart)):'').'</div>';
					}
			}
			echo $retour;
		}
	}
	
	public function actionDispo($date="",$idUsager="",$caserne=""){
		
		$parametres = Parametres::model()->findByPk(1);
		$usager = Usager::model()->findByPk(Yii::app()->user->id);
		$usagerConnecter = Usager::model()->findByPk(Yii::app()->user->id);
		$casernesUsager = $usager->getCaserne();
		
		if($idUsager!=""){
			if(!Usager::peutGerer($idUsager)){
				throw new CHttpException(403,Yii::t('erreur','erreur403'));
				Yii::app()->end();
			}
			$usager = Usager::model()->findByPk($idUsager);
		}
		
		$casernes = Caserne::model()->findAll(array('condition'=>'id IN ('.$casernesUsager.') AND siActif = 1 && si_horaire'));
		$tblCaserne = CHtml::listData($casernes,'id','nom');
		
		if($caserne==""){
			if(isset($parametres->caserne_defaut_horaire)){
				$caserne = $parametres->caserne_defaut_horaire;
			}else{
				//legacy code
				foreach($casernes as $cas){
					$caserne = $cas->id;
					break;
				}			
			}
		}
		
		//Sort les équipes de garde de la caserne
		$tblEquipe = Equipe::model()->findAll('siHoraire = 1 AND tbl_caserne_id = :caserne ',array(':caserne'=>$caserne));
		
		$idsE = '';
		foreach($tblEquipe as $equipe){
			$idsE .= $equipe->id.' ,';
		}
		$idsE = substr($idsE, 0, strlen($idsE)-2);
		
		$garde = Garde::model()->findByPk($parametres->garde_horaire);
		
		$jourSemaine = array(
			Yii::t('generale','dim'),
			Yii::t('generale','lun'),
			Yii::t('generale','mar'),
			Yii::t('generale','mer'),
			Yii::t('generale','jeu'),
			Yii::t('generale','ven'),
			Yii::t('generale','sam'),
		);
		
		$dateDebut = Horaire::debutPeriode($garde->nbr_jour_depot,$parametres->moduloDebut,$date);

		if(!empty($parametres->horaire_mensuel)){
			$datePrecedente = clone $dateDebut;
			$datePrecedente->modify('first day of last month');
			$dateMax = date_add(new DateTime('now',new DateTimeZone($parametres->timezone)),new DateInterval('P'.$parametres->moisFDF.'M'));
			$dateSuivante = clone $dateDebut;
			$dateSuivante->modify('first day of next month');
		}else{
			$datePrecedente = date_sub(clone $dateDebut,new DateInterval('P'.$garde->nbr_jour_depot.'D'));
			$dateMax = date_add(new DateTime('now',new DateTimeZone($parametres->timezone)),new DateInterval('P'.$parametres->moisFDF.'M'));
			$dateMax->sub(new DateInterval('P'.$garde->nbr_jour_depot.'D'));
			$dateSuivante = date_add(clone $dateDebut, new DateInterval('P'.$garde->nbr_jour_affiche.'D'));
			$dateFinDispo = date_add(clone $dateDebut,new DateInterval("P".$garde->nbr_jour_affiche."D"));
		}
		$siMax = false;
		if($dateDebut >= $dateMax){ $siMax = true; }

		$strDateDebut = $dateDebut->format('Y-m-d');
		
		$sql = 
		"SELECT
			ADDDATE('".$strDateDebut."',i2.i*10+i1.i) AS Jour,
			q.nom as Quart,
			q.id AS quart_id,
			dh.dispo as dispo,
			dh.modulo as modulo,
			dh.id AS dispo_id,
			TIME(dh.tsDebut) AS dhHeureDebut,
			TIME(dh.tsFin) AS dhHeureFin,
			dh.tbl_usager_id AS usager_id,
			if(q.heureDebut<=TIME(dh.tsDebut),TIME(dh.tsDebut),addtime(TIME(dh.tsDebut),'24:00:00')) as heureDebutTri,
			e.couleur as couleur,
			q.heureDebut AS qHeureDebut,
			q.heureFin AS qHeureFin
		FROM 
			(numbers i1, numbers i2, tbl_quart q)
			LEFT JOIN tbl_dispo_horaire dh ON
				(dh.date = ADDDATE('".$strDateDebut."',i2.i*10+i1.i) AND dh.tbl_usager_id=".$usager->id.")
				AND (dh.tsDebut>=CONCAT(ADDDATE('".$strDateDebut."',i2.i*10+i1.i), ' ', q.heureDebut) 
				AND dh.tsDebut < IF(q.heureFin<=q.heureDebut, ADDTIME(CONCAT(ADDDATE('".$strDateDebut."',i2.i*10+i1.i), ' ', q.heureFin),'24:00:00'),
					CONCAT(ADDDATE('".$strDateDebut."',i2.i*10+i1.i), ' ', q.heureFin)))
				AND dh.dispo = 0
			LEFT JOIN tbl_equipe_garde eg ON q.id=eg.tbl_quart_id
				AND eg.modulo=MOD((UNIX_TIMESTAMP(ADDDATE('".$strDateDebut."', i2.i*10+i1.i)) DIV 86400),".$garde->nbr_jour_periode.") AND eg.tbl_garde_id = ".$parametres->garde_horaire."
			INNER JOIN tbl_equipe e ON e.id=eg.tbl_equipe_id AND e.tbl_caserne_id = ".$caserne."
		WHERE
			ADDDATE('".$strDateDebut."',i2.i*10+i1.i) < '".$dateSuivante->format('Y-m-d')."'
		ORDER BY Jour,q.heureDebut, heureDebutTri";
		
		/*$sql = "SELECT ADDDATE('".$strDateDebut."',i2.i*10+i1.i) AS Jour, q.nom as Quart, q.id AS quart_id, dh.dispo as dispo, dh.modulo as modulo, dh.id AS dispo_id,
						TIME(dh.tsDebut) AS dhHeureDebut, TIME(dh.tsFin) AS dhHeureFin, dh.tbl_usager_id AS usager_id,
						if(q.heureDebut<=TIME(dh.tsDebut),TIME(dh.tsDebut),addtime(TIME(dh.tsDebut),'24:00:00')) as heureDebutTri, e.couleur as couleur,
						q.heureDebut AS qHeureDebut, q.heureFin AS qHeureFin
				FROM (numbers i1, numbers i2, tbl_quart q) 
					LEFT JOIN tbl_dispo_horaire dh ON
					(DATE(dh.tsDebut)=ADDDATE('".$strDateDebut."',i2.i*10+i1.i) AND dh.tbl_usager_id=".$usager->id.") AND ((q.heureDebut<=TIME(dh.tsDebut) AND TIME(dh.tsDebut)<IF(q.heureFin<q.heureDebut,addtime(q.heureFin,'24:00:00'),q.heureFin)) OR (q.heureDebut<IF(TIME(dh.tsFin)<TIME(dh.tsDebut),addtime(TIME(dh.tsFin),'24:00:00'),TIME(dh.tsFin)) AND IF(TIME(dh.tsFin)<TIME(dh.tsDebut),addtime(TIME(dh.tsFin),'24:00:00'),TIME(dh.tsFin))<=q.heureFin))
				    AND dh.dispo = 0
					LEFT JOIN tbl_equipe_garde eg ON q.id=eg.tbl_quart_id AND eg.modulo=MOD((UNIX_TIMESTAMP(ADDDATE('".$strDateDebut."', i2.i*10+i1.i)) DIV 86400),".$garde->nbr_jour.") AND eg.tbl_garde_id = ".$parametres->garde_horaire."
					LEFT JOIN tbl_equipe e ON e.id=eg.tbl_equipe_id
				    INNER JOIN tbl_quart_caserne qc ON qc.tbl_quart_id = q.id
				WHERE 
					ADDDATE('".$strDateDebut."',i2.i*10+i1.i) < date_add('".$strDateDebut."',INTERVAL ".$parametres->nbJourPeriode." DAY)
					AND qc.tbl_caserne_id = ".$caserne."
				ORDER BY Jour,q.heureDebut, heureDebutTri";*/
		 
		$cn = Yii::app()->db;
		$cm = $cn->createCommand($sql);
		$curseurDispo = $cm->query();
		
		$criteria = new CDbCriteria;
		$criteria->alias='th';
		$criteria->join='LEFT JOIN tbl_poste_horaire AS tph ON tph.id=th.tbl_poste_horaire_id
						LEFT JOIN tbl_poste AS tp ON tp.id = tph.tbl_poste_id '.
					'LEFT JOIN tbl_quart q ON tph.tbl_quart_id = q.id '.
					'INNER JOIN tbl_poste_horaire_caserne phc ON phc.tbl_poste_horaire_id = tph.id';
		$criteria->limit=1;
		$criteria->condition='th.date >= :dateDebut AND th.statut = 1 AND phc.tbl_caserne_id = :caserne AND th.tbl_caserne_id = :caserne';
		$criteria->params = array(':dateDebut'=>$dateDebut->format("Y-m-d"), ':caserne'=>$caserne);
		$horaire = Horaire::model()->find($criteria);
		
		if($horaire != NULL){
			$valide = $horaire->statut;
		}else{
			$valide = 0;
		}
		
		$date = '1990-01-01';
		$dateDebut = Horaire::debutPeriode($garde->nbr_jour_periode,$parametres->moduloDebut,$date);
		$dateFin = date_add(clone $dateDebut,new DateInterval("P".$garde->nbr_jour_affiche."D"));
		$strDateDebut = $dateDebut->format('Y-m-d');
		$sql = "SELECT
			ADDDATE('".$strDateDebut."',i2.i*10+i1.i) AS Jour,
			q.nom as Quart,
			q.id AS quart_id,
			dh.dispo as dispo,
			dh.modulo as modulo,
			dh.id AS dispo_id,
			TIME(dh.tsDebut) AS dhHeureDebut,
			TIME(dh.tsFin) AS dhHeureFin,
			dh.tbl_usager_id AS usager_id,
			if(q.heureDebut<=TIME(dh.tsDebut),TIME(dh.tsDebut),addtime(TIME(dh.tsDebut),'24:00:00')) as heureDebutTri,
			e.couleur as couleur,
			q.heureDebut AS qHeureDebut,
			q.heureFin AS qHeureFin
		FROM 
			(numbers i1, numbers i2, tbl_quart q)
			LEFT JOIN tbl_dispo_horaire dh ON
				(dh.date = ADDDATE('".$strDateDebut."',i2.i*10+i1.i) AND dh.tbl_usager_id=".$usager->id.")
				AND (dh.tsDebut>=CONCAT(ADDDATE('".$strDateDebut."',i2.i*10+i1.i), ' ', q.heureDebut) AND dh.tsDebut < IF(q.heureFin<=q.heureDebut, ADDTIME(CONCAT(ADDDATE('".$strDateDebut."',i2.i*10+i1.i), ' ', q.heureFin),'24:00:00'), CONCAT(ADDDATE('".$strDateDebut."',i2.i*10+i1.i), ' ', q.heureFin)))
				AND dh.dispo = 0
			LEFT JOIN tbl_equipe_garde eg ON q.id=eg.tbl_quart_id
				AND eg.modulo=MOD((UNIX_TIMESTAMP(ADDDATE('".$strDateDebut."', i2.i*10+i1.i)) DIV 86400),".$garde->nbr_jour_depot.") AND eg.tbl_garde_id = ".$parametres->garde_horaire."
			INNER JOIN tbl_equipe e ON e.id=eg.tbl_equipe_id AND e.tbl_caserne_id = ".$caserne."
		WHERE
			ADDDATE('".$strDateDebut."',i2.i*10+i1.i) < date_add('".$strDateDebut."',INTERVAL ".$garde->nbr_jour_depot." DAY)
		ORDER BY Jour,q.heureDebut, heureDebutTri";
			
		$cn = Yii::app()->db;
		$cm = $cn->createCommand($sql);
		$curseurDispoDefaut = $cm->query();
		
		$tblUsager = array();
		$critere = new CDbCriteria;
		$critere->condition = "actif=1";
		$critere->order = "matricule ASC";
		
		if(Yii::app()->user->checkAccess('gesService')){
			$tblUsager = CHtml::listData(Usager::model()->findAll($critere),'id','matprenomnom');
		}elseif(Yii::app()->user->checkAccess('gesCaserne')){
			$tblEquipe = Equipe::model()->findAll('tbl_caserne_id IN ('.$usagerConnecter->getCaserne().')');
			$strEquipes = '';
			foreach($tblEquipe as $equipe){
				$strEquipes .= $equipe->id.', ';
			}
			$strEquipes = substr($strEquipes,0,strlen($strEquipes)-2);
			$critere->join = 'INNER JOIN tbl_equipe_usager eu ON eu.tbl_usager_id = t.id';
			$critere->condition.= ' AND eu.tbl_equipe_id IN ('.$strEquipes.')';
			$tblUsager = CHtml::listData(Usager::model()->findAll($critere),'id','matprenomnom');
		}elseif(Yii::app()->user->checkAccess('gesEquipe')){
			$strEquipes = $usagerConnecter->getEquipes();
			$critere->join = 'INNER JOIN tbl_equipe_usager eu ON eu.tbl_usager_id = t.id';
			$critere->condition.= ' AND eu.tbl_equipe_id IN ('.$strEquipes.')';
			$tblUsager = CHtml::listData(Usager::model()->findAll($critere),'id','matprenomnom');
		}
		
		$arrCaserneU = explode(',',$usager->getCaserne());
		$arrCaserneUC = explode(',',$usagerConnecter->getCaserne());
		
		$arrCaserneDisabled = array_diff($arrCaserneUC,$arrCaserneU);
		
		$tblCasernesDisabled = array();
		
		foreach($arrCaserneDisabled as $id){
			$tblCasernesDisabled[intval($id)] = array('disabled'=>true);
		}


		
		$this->render('dispo',array(
				'valide'          => $valide,
				'curseurDispoDefaut' => $curseurDispoDefaut,
				'curseurDispo' 	  => $curseurDispo,
				'tblUsager'		  => $tblUsager,
				'parametres'      => $parametres,
				'jourSemaine'     => $jourSemaine,
				'usager'		  => $usager,
				'siMax'			  => $siMax,
				'tblCaserne'	  => $tblCaserne,
				'caserne'		  => $caserne,
				'tblCasernesDisabled' => $tblCasernesDisabled,
				'garde'				=> $garde,
				'dateSuivante' => $dateSuivante,
				'datePrecedente' => $datePrecedente
		));
		
	}
	
	public function actionCoche($date, $idQuart, $idUsager, $heureDebut="", $heureFin="", $idDispo="", $idAncienDispo=""){
		$parametres = Parametres::model()->findByPk(1);
		if(!Usager::peutGerer($idUsager)){
			throw new CHttpException(403,Yii::t('erreur','erreur403'));
			Yii::app()->end();
		}	
		$transaction = Yii::app()->db->beginTransaction();
		$quart = Quart::model()->findByPk($idQuart);
		if($idDispo==""){
			$dispoCriteria = new CDbCriteria;
			$dispoCriteria->condition = 'tbl_quart_id = :quart AND date = :date AND tbl_usager_id = :usager';
			$dispoCriteria->params = array(':quart'=>$idQuart, ':date'=>$date, ':usager'=>$idUsager);
			$dispo = DispoHoraire::model()->find($dispoCriteria);
			
			if($dispo === NULL){			
				$dispo = new DispoHoraire;
				$dispo->tbl_usager_id = $idUsager;
				$dispo->date = $date;
				$dispo->tbl_quart_id = $quart->id;
			}
			if($heureDebut!=""){
				$heureDebutDispo = date("H:i:s",strtotime($quart->heureDebut)+($heureDebut*180)); //car 3600secondes / 20px / 2 (demi-heures) = 180
				$heureFinDispo = date("H:i:s",strtotime($quart->heureDebut)+(($heureDebut+$heureFin)*180));
					
				if(substr($heureDebutDispo,-5) != '00:00' AND substr($heureDebutDispo,-5) != '30:00'){
					if(substr($heureDebutDispo,-5) == '59:59'){
						$h = substr($heureDebutDispo,0,strpos($heureDebutDispo, ':'));
						$h++;
						$heureDebutDispo = $h.':00:00';
					}else{
						$h = substr($heureDebutDispo,0,strpos($heureDebutDispo, ':'));
						$heureDebutDispo = $h.':30:00';
					}
				}
					
				if(substr($heureFinDispo,-5) != '00:00' AND substr($heureFinDispo,-5) != '30:00'){
					if(substr($heureFinDispo,-5) == '59:59'){
						$h = substr($heureFinDispo,0,strpos($heureFinDispo, ':'));
						$h++;
						$heureFinDispo = $h.':00:00';
					}else{
						$h = substr($heureFinDispo,0,strpos($heureFinDispo, ':'));
						$heureFinDispo = $h.':30:00';
					}
				}
				
				if(!($dispo->heureDebut==$heureDebutDispo || $dispo->heureFin==$heureFinDispo)){
					$dispo = new DispoHoraire;
					$dispo->tbl_usager_id = $idUsager;
					$dispo->date = $date;
					$dispo->tbl_quart_id = $quart->id;
				}				
			
				$dispo->heureDebut = $heureDebutDispo;
				$dispo->heureFin = $heureFinDispo;
			}else{
				$dispo->heureDebut = $quart->heureDebut;
				$dispo->heureFin = $quart->heureFin;				
			}
			$dispo->dateDecoche = NULL;
			$dispo->dispo = 0;
			$dateF = $date;
			if($quart->heureFin < $quart->heureDebut AND $dispo->heureDebut < $quart->heureFin){
				$nouvelleDate = new DateTime($date,new DateTimeZone($parametres->timezone));
				$nouvelleDate->add(new DateInterval('P1D'));
				$dateF = $nouvelleDate->format('Y-m-d');				
			}
			$dispo->tsDebut = $dateF.' '.$dispo->heureDebut;			
			$dateF = $date;
			if($quart->heureFin < $quart->heureDebut){
				$nouvelleDate = new DateTime($date,new DateTimeZone($parametres->timezone));
				$nouvelleDate->add(new DateInterval('P1D'));
				$dateF = $nouvelleDate->format('Y-m-d');
			}
			$dispo->tsFin = $dateF.' '.$dispo->heureFin;
			if($dispo->save()){
				if($idAncienDispo!=""){
					$commit = true;
					$idAncienDispo = explode(',',$idAncienDispo);
					foreach($idAncienDispo as $idAD){
						$ancienDispo = DispoHoraire::model()->find('id=:id',array(':id'=>$idAD));
						if(!$ancienDispo->delete()){
							$commit=false;
						}
					}
					if($commit){
						echo $dispo->id;
						$transaction->commit();
					}else{
						$transaction->rollback();
					}
				}else{
					echo $dispo->id;
					$transaction->commit();
				}
			}else{
				foreach($dispo->getErrors() as $err){
					Yii::log(json_encode($err),'info','Horaire.dispo');
				}
				throw new CHttpException(500, Yii::t('erreur','erreur500'));
			}
		}else{
			$dispo = DispoHoraire::model()->find('id=:id',array(":id"=>$idDispo));
			
			$heureDebutDispo = date("H:i:s",strtotime($quart->heureDebut)+($heureDebut*180)); //car 3600secondes / 20px / 2 (demi-heures) = 180
			$heureFinDispo = date("H:i:s",strtotime($quart->heureDebut)+(($heureDebut+$heureFin)*180));
			
			if(substr($heureDebutDispo,-5) != '00:00' AND substr($heureDebutDispo,-5) != '30:00'){
				if(substr($heureDebutDispo,-5) == '59:59'){
					$h = substr($heureDebutDispo,0,strpos($heureDebutDispo, ':'));
					$h++;
					$heureDebutDispo = $h.':00:00';
				}else{
					$h = substr($heureDebutDispo,0,strpos($heureDebutDispo, ':'));
					$heureDebutDispo = $h.':30:00';					
				}
			}
			
			if(substr($heureFinDispo,-5) != '00:00' AND substr($heureFinDispo,-5) != '30:00'){
				if(substr($heureFinDispo,-5) == '59:59'){
					$h = substr($heureFinDispo,0,strpos($heureFinDispo, ':'));
					$h++;
					$heureFinDispo = $h.':00:00';
				}else{
					$h = substr($heureFinDispo,0,strpos($heureFinDispo, ':'));
					$heureFinDispo = $h.':30:00';
				}
			}
			
			if(!($dispo->heureDebut==$heureDebutDispo || $dispo->heureFin==$heureFinDispo)){
				$dispo = new DispoHoraire;
			}
			
			$dispo->heureDebut = $heureDebutDispo;
			$dispo->heureFin   = $heureFinDispo;
			$dateF = $date;
			if($quart->heureFin < $quart->heureDebut AND $dispo->heureDebut < $quart->heureFin){
				$nouvelleDate = new DateTime($date,new DateTimeZone($parametres->timezone));
				$nouvelleDate->add(new DateInterval('P1D'));
				$dateF = $nouvelleDate->format('Y-m-d');				
			}
			$dispo->tsDebut = $dateF.' '.$dispo->heureDebut;			
			$dateF = $date;
			if($quart->heureFin < $quart->heureDebut){
				$nouvelleDate = new DateTime($date,new DateTimeZone($parametres->timezone));
				$nouvelleDate->add(new DateInterval('P1D'));
				$dateF = $nouvelleDate->format('Y-m-d');
			}
			$dispo->tsFin = $dateF.' '.$dispo->heureFin;
			$dispo->tbl_quart_id = $quart->id;
			$dispo->dispo = 0;
			$dispo->dateDecoche = NULL;
			
			if($dispo->save()){
				if($idAncienDispo!=""){
					$ancienDispo = DispoHoraire::model()->find('id=:id',array(':id'=>$idAncienDispo));
					if($ancienDispo->delete()){
						echo "1";
						$transaction->commit();
					}else{
						$transaction->rollback();
					}
				}else{
					echo "1";
					$transaction->commit();
				}
			}
		}
		echo "0";
		Yii::app()->end();
	}
	
	public function actionDecoche($strDispoId){
		$parametres = Parametres::model()->findByPk(1);
		$garde = Garde::model()->findByPk($parametres->garde_horaire);
		
		$tblDispoId = json_decode($strDispoId);
		$transaction = Yii::app()->db->beginTransaction();
		$dispoTest = DispoHoraire::model()->find("id=:id",array(':id'=>$tblDispoId[0]));
		$retour = '';
		if(!Usager::peutGerer($dispoTest->tbl_usager_id)){
			throw new CHttpException(403, Yii::t('erreur','erreur403'));
			Yii::app()->end();
		}
		foreach($tblDispoId as $id){
			$date = '1990-01-01';
			$dateDebut = Horaire::debutPeriode($garde->nbr_jour_periode,$parametres->moduloDebut,$date);
			$dateFin = date_add(clone $dateDebut,new DateInterval('P'.($garde->nbr_jour_periode-1).'D'));

			$defaut = false;
			if($dispoTest->date >= $dateDebut->format('Y-m-d') AND $dispoTest->date <=$dateFin->format('Y-m-d')){
				$defaut = true;
			}
			
			$dispo = DispoHoraire::model()->find("id=:id",array(':id'=>$id));
			if(!$defaut){
				$dispo->dispo = 1;
				$dateDecoche = new DateTime(date('Y-m-d H:i:s'),new DateTimeZone($parametres->timezone));
				$dispo->dateDecoche = $dateDecoche->format('Y-m-d H:i:s');
				if($dispo->save()){
					$retour.= $id.';';
				}else{
					$transaction->roollback;
					$transaction = null;
					break;
				}
			}else{
				if($dispo->delete()){
					$retour.= $id.';';
				}else{
					$transaction->roollback;
					$transaction = null;
					break;
				}				
			}
		}
		if($transaction!=null){
			$transaction->commit();
		}
		echo $retour."0";
		Yii::app()->end();
	}
		
	public function actionDefautDispo($caserne,$date,$usagerid){
		
		if(!Usager::peutGerer($usagerid)){
			throw new CHttpException(403, Yii::t('erreur','erreur403'));
			Yii::app()->end();
		}
		
		//Vérifie si on a le droit de faire la copie
		$parametres = Parametres::model()->findByPk(1);
		$garde = Garde::model()->findByPk($parametres->garde_horaire);
		$tsNow = new DateTime(date("Y-m-d"),new DateTimeZone($parametres->timezone));
		$diff = $garde->nbr_jour_affiche - $parametres->moduloDepotDispo;
		$tsDebut = date_sub(new DateTime(date("Y-m-d"),new DateTimeZone($parametres->timezone)),new DateInterval("P".$diff."D"));
		
		//On récupère les données de base
		$quarts = Quart::model()->findAll();
		$usager = Usager::model()->findByPk($usagerid);
		$dateDebut = Horaire::debutPeriode($garde->nbr_jour_affiche,$parametres->moduloDebut,$date)->format('Y-m-d');
		$dateFin = date('Y-m-d', strtotime($date." +".($garde->nbr_jour_affiche-1)." days"));
		
		$transaction = Yii::app()->db->beginTransaction();
				
		//On supprime les vielles données
		$query = "DELETE dh.* FROM tbl_dispo_horaire AS dh ".
				"WHERE dh.tbl_usager_id = :uid AND dh.date BETWEEN :dateDebut AND :dateFin";
		$command = Yii::app()->db->createCommand($query);
		$command->execute(array(':uid'=>$usager->id,':dateDebut'=>$dateDebut,':dateFin'=>$dateFin));
		
		$dateD = '1990-01-01';
		$dateDefaut = Horaire::debutPeriode($garde->nbr_jour_periode,$parametres->moduloDebut,$dateD);
		$strDateDebut = $dateDefaut->format('Y-m-d');
		
		//On va chercher les dispos par défaut
		$sql = "SELECT dh.dispo as dispo, dh.date AS date,
					dh.heureDebut AS dhHeureDebut, dh.heureFin AS dhHeureFin, dh.tbl_quart_id AS tbl_quart_id
				FROM tbl_dispo_horaire dh".
			" WHERE dh.tbl_usager_id = :uid AND dh.date BETWEEN '".$strDateDebut."' AND adddate('".$strDateDebut."', INTERVAL :nbJour DAY)";
			
			
		$cn = Yii::app()->db;
		$cm = $cn->createCommand($sql);
		$curseurDispoDefaut = $cm->query(array(':uid'=>$usager->id,':nbJour'=>$garde->nbr_jour_periode-1));
		
		//On boucle pour entrer les nouvelle données
		while($row=$curseurDispoDefaut->read()){
			$dispo = new DispoHoraire();
			$dispo->tbl_quart_id = $row['tbl_quart_id'];
			$quart = Quart::model()->findByPk($dispo->tbl_quart_id);
			$jourAjout =  (strtotime($row['date'])/86400)%$garde->nbr_jour_periode-$parametres->moduloDebut;
			if($jourAjout<0) $jourAjout += $garde->nbr_jour_periode;
			$jourDispo =  date('Y-m-d', strtotime($dateDebut." +".($jourAjout)." days"));
			$dispo->date = $jourDispo;
			$dispo->tbl_usager_id = $usager->id;
			$dispo->dispo = $row['dispo'];
			$dispo->heureDebut = $row['dhHeureDebut'];
			$dispo->heureFin = $row['dhHeureFin'];
			$jourDispoF =  date('Y-m-d', strtotime($jourDispo));
			if($quart->heureFin<$quart->heureDebut && $row['dhHeureDebut']<$row['dhHeureFin']){
				$jourDispo = date('Y-m-d', strtotime($jourDispo. ' + 1 days'));
			}
			$dispo->tsDebut = $jourDispoF.' '.$row['dhHeureDebut'];
			$jourDispoF =  date('Y-m-d', strtotime($jourDispo));
			if($quart->heureFin<$quart->heureDebut){
				$jourDispo = date('Y-m-d', strtotime($jourDispo. ' + 1 days'));
			}
			$dispo->tsFin = $jourDispo.' '.$row['dhHeureFin'];
			if(!$dispo->save()){
				$transaction->rollback();
				throw new CHttpException(500, Yii::t('erreur','erreur500'));
				Yii::app()->end();
			}
			unset($dispo);
		}
		$transaction->commit();
		$this->redirect(array('dispo&idUsager='.$usager->id.'&caserne='.$caserne.'&date='.substr($dateDebut,0,4).substr($dateDebut,5,2).substr($dateDebut,8,2)));
	}

	public function actionModif($date,$posteHoraire,$caserne){
		$critere = new CDbCriteria();
		$critere->condition = "date=:date AND tbl_poste_horaire_id=:posteId AND type = 0 AND tbl_caserne_id = :caserne";
		$critere->params = array(':date'=>$date,':posteId'=>$posteHoraire, ':caserne'=>$caserne);
		$caseHoraire = Horaire::model()->find($critere);
		$PH = PosteHoraire::model()->findByPk($posteHoraire);
		$quart = Quart::model()->findByPk($PH->tbl_quart_id);
		$listeModif = array();
		if($caseHoraire) {
			$critereModif = new CDbCriteria();
			$critereModif->condition = "parent_id=:horaireId AND type = 1";
			$critereModif->params = array(":horaireId"=>$caseHoraire->id);
			$critereModif->order = "dateModif DESC";
			$listeModif = Horaire::model()->findAll($critereModif);	
		}else{
			$caseHoraire = new Horaire;
			$caseHoraire->date = $date;
			$caseHoraire->tbl_poste_horaire_id = $posteHoraire;
			$caseHoraire->tbl_caserne_id = $caserne;
		}
		
		$poste = PosteHoraire::model()->findByPk($posteHoraire);
		$critere = new CDbCriteria();
		$critere->select = 'nom, prenom, matricule, t.id, telephone1, telephone2, dispo.heureDebut AS dHeureDebut, dispo.heureFin AS dHeureFin';
		$critere->alias = 't';
		$critere->join = 'INNER JOIN tbl_dispo_horaire dispo ON dispo.tbl_usager_id=t.id '.
						 'INNER JOIN tbl_usager_poste poste ON poste.tbl_usager_id=t.id';
		$critere->condition = 't.actif = 1 AND t.enService=1 AND dispo.date=\''.$date.'\' AND dispo.tbl_quart_id='.$poste->tbl_quart_id.' AND poste.tbl_poste_id='.$poste->tbl_poste_id;
		$critere->group = 'matricule';
		$listeDispo = Usager::model()->findAll($critere);
		$tblUsagerPoste = array();
		foreach($listeDispo as $usager){
			$tblUsagerPoste[] = $usager->id;
		}
		$critere = new CDbCriteria();
		$critere->select = 'nom, prenom, matricule, t.id, telephone1, dispo.heureDebut AS dHeureDebut, dispo.heureFin AS dHeureFin';
		$critere->alias = 't';
		$critere->join = 'INNER JOIN tbl_dispo_horaire dispo ON dispo.tbl_usager_id=t.id ';
		$critere->condition = 't.actif = 1 AND t.enService=1 AND dispo.date=\''.$date.'\' AND dispo.tbl_quart_id='.$poste->tbl_quart_id.' ';
		$critere->addNotInCondition('t.id',$tblUsagerPoste,'AND');
		$critere->group = 'matricule';
		$listeDispoNonPoste = Usager::model()->findAll($critere);
		foreach($listeDispoNonPoste as $usager){
			$tblUsagerPoste[] = $usager->id;
		}
		
		$criteria = new CDbCriteria;
		$criteria->addInCondition('tbl_usager_id', $tblUsagerPoste, 'AND');
		$criteria->condition = 'tbl_quart_id = :quart AND date = :date AND heureDebut IS NOT NULL AND heureFin IS NOT NULL';
		$criteria->params = array(':quart'=>$quart->id, ':date'=>$date);
		
		$dispoPart = DispoHoraire::model()->findAll($criteria);
		
		$this->renderPartial('_formModif',array(
			'listeModif'=>$listeModif,
			'caseHoraire'=>$caseHoraire,
			'listeDispo'=>$listeDispo,
			'listeDispoNonPoste'=>$listeDispoNonPoste,
			'dispoPart'=>$dispoPart,
			'qHeureDebut'=>$quart->heureDebut,
			'qHeureFin'=>$quart->heureFin,
		), false, true);
	}
	
	public function actionCreateModif($usager,$poste,$date,$caserne){
		$parametres = Parametres::model()->findByPk(1);
		$garde = Garde::model()->findByPk($parametres->garde_horaire);
		$usager = Usager::model()->find('matricule=:mat AND actif = 1',array(':mat'=>$usager));
		$auteur = Usager::model()->findByPk(Yii::app()->user->id);
		$posteHoraire = PosteHoraire::model()->findByPk($poste);
		$quart = Quart::model()->findByPk($posteHoraire->tbl_quart_id);
		$posteT = Poste::model()->findByPk($posteHoraire->tbl_poste_id);
		if(!$usager){
			echo Yii::t('erreur','usagerInvalide');
			Yii::app()->end();
		}
		$critere = new CDbCriteria();
		$critere->condition = 'date=:date AND tbl_poste_horaire_id=:pid AND type = 0 AND tbl_caserne_id = :caserne';
		$critere->params = array(':date'=>$date,':pid'=>$poste, ':caserne'=>$caserne);
		$caseHoraire = Horaire::model()->find($critere);		
		if($caseHoraire){
			//si on fait effectivement une modification
			$modif = new Horaire;
			$date = new DateTime(NULL,new DateTimeZone($parametres->timezone));
			$modif->date = $caseHoraire->date;
			$modif->tbl_poste_horaire_id = $poste;
			$modif->dateModif = $date->format('Y-m-d H:i:s');
			$modif->heureModif = $date->format('H:i:s');
			$modif->heureDebut = $caseHoraire->heureDebut;
			$modif->heureFin = $caseHoraire->heureFin;
			$modif->modifLu = 0;
			$modif->tbl_usager_id = $usager->id;
			$modif->modif_usager_id = Yii::app()->user->id;
			$modif->parent_id = $caseHoraire->id;
			$modif->type = 1;
			$modif->statut = $caseHoraire->statut;
			$modif->tbl_caserne_id = $caserne;
			if($modif->save()){
				$critere = new CDbCriteria();
				$critere->condition = 'type = 2 AND parent_id =:parent';
				$critere->params = array(':parent'=>$caseHoraire->id);
				$remplacements = Horaire::model()->findAll($critere);
				foreach($remplacements as $remp){
					$remp->parent_id = $modif->id;
					
					$remp->save();
				}				
				//envoie du courriel lors de la modification				
				if(!filter_var($usager->courriel, FILTER_VALIDATE_EMAIL) === false){
					$message = new YiiMailMessage;
					$message->view = "horaireModif";
					$message->setBody(array("message"=>$auteur->prenom." ".$auteur->nom." vous a ajouté à l'horaire de garde pour le quart de ".$quart->nom." du ".$caseHoraire->date." au poste de ".$posteT->nom), 'text/html');
					$message->subject = "Changement à l'horaire";
					if(!filter_var($auteur->courriel, FILTER_VALIDATE_EMAIL) === false){
						$message->from = $auteur->courriel;
					}else{
						$message->from = Yii::app()->params['emailSysteme'];
					}
					$message->addTo($usager->courriel);
					Yii::app()->mail->send($message);
				}else{
					$notification = new Notification;
					$notification->tbl_usager_id = $usager->id;
					$notification->dateCreation = date('Y-m-d');
					$notification->categorie = "Controller";
					$notification->message = "horaire.createModif.notification";
					$notification->details = json_encode(array(
						'{auteur}'=>$auteur->getMatPrenomNom(),
						'{nomQuart}'=>$quart->nom,
						'{dateCaseHoraire}'=>$caseHoraire->date,
						'{nomPosteT}'=>$posteT->nom,
					));
					$notification->save();
				}
				echo "1";
			}else{
				echo "0m".json_encode($modif->getErrors());
			}
		}else{
			//si la case était vide en startant
			$dateDebut = Horaire::debutPeriode($garde->nbr_jour_periode,$parametres->moduloDebut,$date);
			$dateFin = date_add(clone $dateDebut,new DateInterval("P".$garde->nbr_jour_periode."D"));
			
			$critere = new CDbCriteria();
			$critere->condition = 'date BETWEEN :dateDebut AND :dateFin AND tbl_caserne_id = :caserne';
			$critere->params = array(':dateDebut'=>$dateDebut->format('Y-m-d'), ':dateFin'=>$dateFin->format('Y-m-d'), ':caserne'=>$caserne);
			$caseHoraireO = Horaire::model()->find($critere);	
			
			if($caseHoraireO === NULL){
				$valide = 0;
			}else{
				$valide = $caseHoraireO->statut;
			}
			
			$caseHoraire = new Horaire;
			$caseHoraire->date = $date;
			$caseHoraire->tbl_poste_horaire_id = $poste;
			$caseHoraire->tbl_usager_id = $usager->id;
			$caseHoraire->type = 0;
			$caseHoraire->tbl_caserne_id = $caserne;
			$caseHoraire->statut = $valide;
			
			if($caseHoraire->save()){
				//envoie du courriel lors de la modification				
				if(!filter_var($usager->courriel, FILTER_VALIDATE_EMAIL) === false){
					$message = new YiiMailMessage;
					$message->view = "horaireModif";
					$message->setBody(array("message"=>$auteur->prenom." ".$auteur->nom." vous a ajouté à l'horaire de garde pour le quart de ".$quart->nom." du ".$caseHoraire->date." au poste de ".$posteT->nom), 'text/html');
					$message->subject = "Changement à l'horaire";
					if(!filter_var($auteur->courriel, FILTER_VALIDATE_EMAIL) === false){
						$message->from = $auteur->courriel;
					}else{
						$message->from = Yii::app()->params['emailSysteme'];
					}
					$message->addTo($usager->courriel);
					Yii::app()->mail->send($message);
				}else{
					$notification = new Notification;
					$notification->tbl_usager_id = $usager->id;
					$notification->dateCreation = date('Y-m-d');
					$notification->categorie = "Controller";
					$notification->message = "horaire.createModif.notification";
					$notification->details = json_encode(array(
						'{auteur}'=>$auteur->getMatPrenomNom(),
						'{nomQuart}'=>$quart->nom,
						'{dateCaseHoraire}'=>$caseHoraire->date,
						'{nomPosteT}'=>$posteT->nom,
					));
					$notification->save();
				}
				echo "1";
			}else{
				echo "0c".json_encode($caseHoraire->getErrors());
			}
		}
		Yii::app()->end();
	}
	
	public function actionRemp($id,$date,$posteHoraire,$type,$caserne){
		$critere = new CDbCriteria();
		$critere->condition = "id=:id";
		$critere->params = array(':id'=>$id);
		$caseHoraire = Horaire::model()->find($critere);
		$poste = PosteHoraire::model()->findByPk($posteHoraire);
		$quart = Quart::model()->findByPk($poste->tbl_quart_id);		
		
		$critere = new CDbCriteria();
		$critere->select = 'nom, prenom, matricule, t.id, telephone1, telephone2, dispo.heureDebut AS dHeureDebut, dispo.heureFin AS dHeureFin';
		$critere->alias = 't';
		$critere->join = 'INNER JOIN tbl_dispo_horaire dispo ON dispo.tbl_usager_id=t.id '.
						 ' INNER JOIN tbl_usager_poste poste ON poste.tbl_usager_id=t.id';
		$critere->condition = 't.enService=1 AND dispo.date=\''.$date.'\' AND dispo.tbl_quart_id='.$poste->tbl_quart_id.' AND poste.tbl_poste_id='.$poste->tbl_poste_id;		
		$critere->group = 'matricule';
		$listeDispo = Usager::model()->findAll($critere);
		$tblUsagerPoste = array();
		foreach($listeDispo as $usager){
			$tblUsagerPoste[] = $usager->id;
		}
		$critere = new CDbCriteria();
		$critere->select = 'nom, prenom, matricule, t.id, telephone1, dispo.heureDebut AS dHeureDebut, dispo.heureFin AS dHeureFin';
		$critere->alias = 't';
		$critere->join = 'INNER JOIN tbl_dispo_horaire dispo ON dispo.tbl_usager_id=t.id ';
		$critere->condition = 't.enService=1 AND dispo.date=\''.$date.'\' AND dispo.tbl_quart_id='.$poste->tbl_quart_id.' ';
		$critere->addNotInCondition('t.id',$tblUsagerPoste,'AND');
		$critere->group = 'matricule';
		$listeDispoNonPoste = Usager::model()->findAll($critere);
		
		$sql = 
"SELECT a.id, a.tbl_usager_id, a.dateEmis,	a.dateConge, a.heureDebut, a.heureFin, a.tbl_type_id, a.tbl_quart_id, a.statut, a.chef_id, a.dateRecu, a.heureRecu, a.note
FROM tbl_absence a, tbl_horaire h 
LEFT JOIN tbl_poste_horaire ph ON ph.id = h.tbl_poste_horaire_id
LEFT JOIN tbl_quart q ON q.id = ph.tbl_quart_id
WHERE h.id = :tbl_horaire_id AND a.tbl_usager_id = :usager AND a.dateConge = :date AND a.statut = 2 AND 
    (IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut))<=a.heureDebut AND
     IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin))>=a.heureDebut
     OR
     IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut))<=a.heureFin AND
     IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin))>=a.heureFin
    )";
		
		$absence = Absence::model()->findBySql($sql, array(':usager'=>$caseHoraire->tbl_usager_id, ':date'=>$date, ':tbl_horaire_id'=>$caseHoraire->id));
		
		if($absence != NULL){
			$absenceid = $absence->id;
			$heureDebut = $absence->heureDebut;
			$heureFin = $absence->heureFin;
			$typeC = TypeConge::model()->findByPk($absence->tbl_type_id);
			$raison = $typeC->nom;
			
			$criteria = new CDbCriteria;
			$criteria->condition = 'tbl_absence_id = :absence';
			$criteria->params = array(':absence'=>$absence->id);
			$criteria->order = 'dateAppel ASC, heure ASC';
			
			$listeAppels = AbsenceAppel::model()->findAll($criteria);
			if($listeAppels === NULL){
				$listeAppels = array();
			}
		}else{			
			$absenceid = 0;
			$raison = '';
			$listeAppels = array();
			if($caseHoraire->heureDebut=='00:00:00' && $caseHoraire->heureFin=='00:00:00'){
				if($poste->heureDebut=='00:00:00' && $poste->heureFin=='00:00:00'){
					$heureDebut = $quart->heureDebut;
					$heureFin = $quart->heureFin;
				}else{
					$heureDebut = $poste->heureDebut;
					$heureFin = $poste->heureFin;				
				}
			}else{
				$heureDebut = $caseHoraire->heureDebut;
				$heureFin = $caseHoraire->heureFin;
			}
		}
		$heureDebut = substr($heureDebut,0,5);
		$heureFin = substr($heureFin,0,5);
		
		if($type==2){
			$usagerR = Usager::model()->findByPk($caseHoraire->tbl_usager_id);
			$matriculeR = $usagerR->matricule;
			$id = $caseHoraire->id;
			
			$caseHoraireO = Horaire::model()->findByPk($caseHoraire->parent_id);
			$usager = Usager::model()->findByPk($caseHoraireO->tbl_usager_id);
			$type = $caseHoraireO->type;
			
		}else{
			$usager = Usager::model()->findByPk($caseHoraire->tbl_usager_id);
			$matriculeR = NULL;
			$id = 0;
		}
		
		$listeUsager = Usager::model()->findAll();
		
		$criteria = new CDbCriteria;
		$criteria->addInCondition('tbl_usager_id', $tblUsagerPoste, 'AND');
		$criteria->condition = 'tbl_quart_id = :quart AND date = :date AND heureDebut IS NOT NULL AND heureFin IS NOT NULL';
		$criteria->params = array(':quart'=>$quart->id, ':date'=>$date);
		
		$dispoPart = DispoHoraire::model()->findAll($criteria);
		
		$this->renderPartial('_formRemplacement',array(
			'usager'=>$usager->getMatPrenomNom(),
			'usagerR'=>$matriculeR,
			'caseHoraire'=>$caseHoraire,
			'listeDispo'=>$listeDispo,
			'listeDispoNonPoste'=>$listeDispoNonPoste,
			'heureDebut'=>$heureDebut,
			'heureFin'=>$heureFin,
			'id'=>$id,
			'type'=>$type,
			'dispoPart'=>$dispoPart,
			'qHeureDebut'=>$quart->heureDebut,
			'qHeureFin'=>$quart->heureFin,
			'listeAppels'=>$listeAppels,
			'listeUsager' => $listeUsager,
			'absence' => $absenceid,
			'raison' => $raison,
		), false, true);
	}
	
	public function actionCreateRemp($usager,$poste,$date,$heureDebut,$heureFin,$id,$type,$caserne){
		$parametres = Parametres::model()->findByPk(1);
		if($heureDebut!=$heureFin){
			$heureDebut .= ':00';
			$heureFin .= ':00';
			$usager = Usager::model()->find('matricule=:mat AND actif = 1',array(':mat'=>$usager));
			$posteHoraire = PosteHoraire::model()->findByPk($poste);
			$quart = Quart::model()->findByPk($posteHoraire->tbl_quart_id);
			if(!$usager){
				echo Yii::t('erreur','usagerInvalide');
				Yii::app()->end();
			}
			$critere = new CDbCriteria();
			$critere->condition = 'date=:date AND tbl_poste_horaire_id=:pid AND type =:type AND tbl_caserne_id = :caserne';
			$critere->params = array(':date'=>$date,':pid'=>$poste,':type'=>$type, ':caserne'=>$caserne);
			$caseHoraire = Horaire::model()->find($critere);		
			if($caseHoraire){
				//On vérifie que les heure debut et fin du remplacement entre ne dépasse pas les heures du posteHoraire/Quart
				$pass = 0;
				if($posteHoraire->heureDebut!='00:00:00' && $posteHoraire->heureFin!='00:00:00'){
					if($heureDebut<$posteHoraire->heureDebut || $heureFin>$posteHoraire->heureFin){
						$pass=1;
					}
				}else{
					if($heureDebut<$quart->heureDebut || $heureFin>$quart->heureFin){
						$pass=1;
					}					
				}
				if($pass ==0){
					//On vérifie que les remplacements ne se chevauchent pas
					$criteria = new CDbCriteria;
					$criteria->condition = 'type = 2 AND parent_id=:parent AND (((heureDebut<:heureFin AND heureFin>:heureFin) OR (heureDebut<:heureDebut AND heureFin>:heureDebut)) OR (heureDebut>:heureDebut AND heureFin<:heureFin) OR (heureDebut<:heureDebut AND heureFin>:heureFin)) AND id <> :id';
					$criteria->params = array(':parent'=>$caseHoraire->id, ':heureFin'=>$heureFin, ':heureDebut'=>$heureDebut, ':id'=>$id);
					$remps = Horaire::model()->count($criteria);
					if($remps == 0){
						$rempInfo = new RemplacementInfo;
						if($caseHoraire->parent_id==NULL){
							$parent = $caseHoraire->id;
						}else{
							$parent = $caseHoraire->parent_id;
						}
						$rempInfo->tbl_horaire_parent = $parent;
						if($id == 0){
							$remp = new Horaire;
							$rempInfo->type = '0';
						}else{
							$remp = Horaire::model()->findByPk($id);
							$rempInfo->type = '1';
						}
						$date = new DateTime(NULL,new DateTimeZone($parametres->timezone));
						$changeHeure = true;
						if($remp->heureDebut==$heureDebut && $remp->heureFin == $heureFin){
							$changeHeure = false;
						}
						$sql =
						"SELECT a.id, a.tbl_usager_id, a.dateEmis,	a.dateConge, a.heureDebut, a.heureFin, a.tbl_type_id, a.tbl_quart_id, a.statut, a.chef_id, a.dateRecu, a.heureRecu, a.note
						FROM tbl_absence a, tbl_horaire h
						LEFT JOIN tbl_poste_horaire ph ON ph.id = h.tbl_poste_horaire_id
						LEFT JOIN tbl_quart q ON q.id = ph.tbl_quart_id
						WHERE h.id = :tbl_horaire_id AND a.tbl_usager_id = :usager AND a.dateConge = :date AND a.statut = 2 AND
						    (IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut))<=a.heureDebut AND
						     IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin))>=a.heureDebut
						     OR
						     IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut))<=a.heureFin AND
						     IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin))>=a.heureFin
						    )";
						
						$absence = Absence::model()->findBySql($sql, array(':usager'=>$caseHoraire->tbl_usager_id, ':date'=>$caseHoraire->date, ':tbl_horaire_id'=>$caseHoraire->id));
						
						$rempInfo->tbl_usager_modif = Yii::app()->user->id;
						$rempInfo->dateHoraire = $caseHoraire->date;
						$rempInfo->tbl_usager_horaire = $caseHoraire->tbl_usager_id;
						$rempInfo->tbl_absence_id = (($absence!==NULL)?$absence->id:$absence);
						$remp->date = $caseHoraire->date;
						$remp->tbl_poste_horaire_id = $poste;
						$remp->dateModif = $date->format('Y-m-d H:i:s');
						$remp->heureModif = $date->format('H:i:s');
						$remp->heureDebut = $heureDebut;
						$remp->heureFin = $heureFin;
						$remp->tbl_usager_id = $usager->id;
						$remp->modif_usager_id = Yii::app()->user->id;
						$remp->parent_id = $parent;
						$remp->type = 2;
						$remp->statut = $caseHoraire->statut;
						$remp->tbl_caserne_id = $caserne;
						if($remp->save()){
							$remp->refresh();
							$rempInfo->tbl_horaire_remp = $remp->id;
							$rempInfo->tbl_usager_remp = $remp->tbl_usager_id;
							$rempInfo->heureDebut = $remp->heureDebut;
							$rempInfo->heureFin = $remp->heureFin;
							$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
							$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
							$rempInfo->save();
							if($changeHeure){
								if($caseHoraire->heureDebut!='00:00:00' && $caseHoraire->heureFin!='00:00:00'){
									if($heureDebut == $caseHoraire->heureDebut){
										$heureHoraireDebut = $heureFin;
										$heureHoraireFin = $caseHoraire->heureFin;
									}elseif($heureFin == $caseHoraire->heureFin){
										$heureHoraireDebut = $caseHoraire->heureDebut;
										$heureHoraireFin = $heureDebut;						
									}else{
										$heureHoraireDebut = $caseHoraire->heureDebut;
										$heureHoraireFin = $heureDebut;
										$caseHoraire2 = new Horaire;
										$caseHoraire2->attributes = $caseHoraire->attributes;
										$caseHoraire2->parent_id = $parent;
										$caseHoraire2->heureDebut = $heureFin;
										$caseHoraire2->heureFin = $caseHoraire->heureFin;
										$caseHoraire2->tbl_caserne_id;
										
										if(!$caseHoraire2->save()){
											echo "0m".json_encode($caseHoraire2->getErrors());
											Yii::app()->end();
										}
									}							
								}elseif($posteHoraire->heureDebut!='00:00:00' && $posteHoraire->heureFin!='00:00:00'){
									if($heureDebut == $posteHoraire->heureDebut){
										$heureHoraireDebut = $heureFin;
										$heureHoraireFin = $posteHoraire->heureFin;
									}elseif($heureFin == $posteHoraire->heureFin){
										$heureHoraireDebut = $posteHoraire->heureDebut;
										$heureHoraireFin = $heureDebut;						
									}else{
										$heureHoraireDebut = $posteHoraire->heureDebut;
										$heureHoraireFin = $heureDebut;
										$caseHoraire2 = new Horaire;
										$caseHoraire2->attributes = $caseHoraire->attributes;
										$caseHoraire2->parent_id = $parent;
										$caseHoraire2->heureDebut = $heureFin;
										$caseHoraire2->heureFin = $posteHoraire->heureFin;
										$caseHoraire2->tbl_caserne_id;
										
										if(!$caseHoraire2->save()){
											echo "0m".json_encode($caseHoraire2->getErrors());
											Yii::app()->end();
										}
									}
								}else{
									if($heureDebut == $quart->heureDebut){
										$heureHoraireDebut = $heureFin;
										$heureHoraireFin = $quart->heureFin;
									}elseif($heureFin == $quart->heureFin){
										$heureHoraireDebut = $quart->heureDebut;
										$heureHoraireFin = $heureDebut;						
									}else{
										$heureHoraireDebut = $quart->heureDebut;
										$heureHoraireFin = $heureDebut;
										$caseHoraire2 = new Horaire;
										$caseHoraire2->attributes = $caseHoraire->attributes;
										$caseHoraire2->parent_id = $parent;
										$caseHoraire2->heureDebut = $heureFin;
										$caseHoraire2->heureFin = $quart->heureFin;
										$caseHoraire2->tbl_caserne_id;
										
										if(!$caseHoraire2->save()){
											echo "0Erreur d'enregistrement : ".json_encode($caseHoraire2->getErrors());
											Yii::app()->end();
										}
									}					
								}
								$caseHoraire->heureDebut = $heureHoraireDebut;
								$caseHoraire->heureFin = $heureHoraireFin;
							}
							if($caseHoraire->save()){
								echo "1";
							}else{
								echo "0Erreur d'enregistrement : ".json_encode($caseHoraire->getErrors());
							}
						}else{
							echo "0Erreur d'enregistrement : ".json_encode($remp->getErrors());
						}
					}else{
						echo "0Les heures chevauchent un autre remplacement pour cette case horaire";
						Yii::app()->end();					
					}
				}else{
					echo "0Les heures de remplacement ne cadre pas dans le posteHoraire/Quart";
					Yii::app()->end();						
				}
			}else{
				echo "0Case horaire invalide";
				Yii::app()->end();
			}
			Yii::app()->end();
		}
		echo "0Heures invalide";
		Yii::app()->end();
	}
	
	public function actionDeleteRemp($id, $date, $caserne){
		$parametres = Parametres::model()->findByPk(1);
		$remp = Horaire::model()->findByPk($id);
		if($remp){
			$caseHoraire = Horaire::model()->findByPk($remp->parent_id);
			if($caseHoraire){
				$rempInfo = new RemplacementInfo;
				$rempInfo->dateHoraire = $caseHoraire->date;
				$rempInfo->tbl_usager_modif = Yii::app()->user->id;
				$rempInfo->tbl_usager_remp = $remp->tbl_usager_id;
				$rempInfo->tbl_usager_horaire = $caseHoraire->tbl_usager_id;
				$rempInfo->tbl_horaire_parent = $caseHoraire->id;
				$rempInfo->tbl_horaire_remp = $remp->id;
				$rempInfo->type = '2';
				$horaires = Horaire::model()->findAll('parent_id = :parent AND id <> :id', array(':parent'=>$remp->parent_id, ':id'=>$id));
				if(count($horaires)==0){
					$caseHoraire->heureDebut = '00:00:00';
					$caseHoraire->heureFin = '00:00:00';
					if($caseHoraire->save()){
						$remp->delete();
						$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
						$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
						$rempInfo->save();
						$this->redirect(array('index',array(
							'date'=>$date,
							'caserne'=>$caserne,
						)));
					}
				}else{
					$heureDebutRemp = $remp->heureDebut;
					$heureFinRemp = $remp->heureFin;
					$posteHoraire = PosteHoraire::model()->findByPk($caseHoraire->tbl_poste_horaire_id);
					if($posteHoraire->heureDebut!='00:00:00' && $posteHoraire->heureFin!='00:00:00'){
						if($heureDebutRemp == $posteHoraire->heureDebut){
							if($caseHoraire->heureDebut == $heureFinRemp){
								$caseHoraire->heureDebut = $heureDebutRemp;
								if($caseHoraire->save()){
									$remp->delete();
									$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
									$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
									$rempInfo->save();
									$this->redirect(array('index',array(
										'date'=>$date,
										'caserne'=>$caserne,
									)));
								}
							}else{
								foreach($horaires as $horaire){
									if($horaire->heureDebut == $heureFinRemp){
										$horaire->heureDebut == $heureDebutRemp;
										if($horaire->save()){
											$remp->delete();
											$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
											$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
											$rempInfo->save();
											$this->redirect(array('index',array(
												'date'=>$date,
												'caserne'=>$caserne,
											)));
											break;
										}
									}
								}
							}
						}elseif($heureFinRemp == $posteHoraire->heureFin){
							if($caseHoraire->heureFin == $heureDebutRemp){
								$caseHoraire->heureFin = $heureFinRemp;
								if($caseHoraire->save()){
									$remp->delete();
									$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
									$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
									$rempInfo->save();
									$this->redirect(array('index',array(
										'date'=>$date,
										'caserne'=>$caserne,
									)));
								}
							}else{
								foreach($horaires as $horaire){
									echo $horaire->id.'-';
									if($horaire->heureFin == $heureDebutRemp){
										$horaire->heureFin = $heureFinRemp;
										if($horaire->save()){
											$remp->delete();
											$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
											$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
											$rempInfo->save();
											$this->redirect(array('index',array(
												'date'=>$date,
												'caserne'=>$caserne,
											)));
											break;
										}
									}
								}
							}							
						}else{
							if($caseHoraire->heureDebut == $heureFinRemp){
								foreach($horaires as $horaire){
									if($horaire->heureFin == $heureDebutRemp){
										if($horaire->tbl_usager_id == $caseHoraire->tbl_usager_id && $horaire->type!=2){
											if(count($horaires)>=2){
												$caseHoraire->heureDebut = $horaire->heureDebut;
											}else{
												$caseHoraire->heureDebut == '00:00:00';
												$caseHoraire->heureFin == '00:00:00';
											}
											if($caseHoraire->save()){
												if($horaire->delete()){
													$remp->delete();
													$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
													$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
													$rempInfo->save();
													$this->redirect(array('index',array(
														'date'=>$date,
														'caserne'=>$caserne,
													)));
													break;
												}
											}
										}else{
											$caseHoraire->heureDebut = $heureDebutRemp;
											if($caseHoraire->save()){
												$remp->delete();
												$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
												$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
												$rempInfo->save();
												$this->redirect(array('index',array(
													'date'=>$date,
													'caserne'=>$caserne,
												)));
												break;
											}
										}
									}
								}
							}elseif($caseHoraire->heureFin == $heureDebutRemp){
								foreach($horaires as $horaire){
									if($horaire->heureDebut == $heureFinRemp){
										if($horaire->tbl_usager_id == $caseHoraire->tbl_usager_id && $horaire->type!=2){
											if(count($horaires)>=2){
												$caseHoraire->heureFin = $horaire->heureFin;
											}else{
												$caseHoraire->heureDebut == '00:00:00';
												$caseHoraire->heureFin == '00:00:00';
											}
											if($caseHoraire->save()){
												if($horaire->delete()){
													$remp->delete();
													$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
													$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
													$rempInfo->save();
													$this->redirect(array('index',array(
														'date'=>$date,
														'caserne'=>$caserne,
													)));
													break;
												}
											}
										}else{
											$caseHoraire->heureFin = $heureFinRemp;
											if($caseHoraire->save()){
												$remp->delete();
												$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
												$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
												$rempInfo->save();
												$this->redirect(array('index',array(
													'date'=>$date,
													'caserne'=>$caserne,
												)));
												break;
											}
										}
									}
								}								
							}else{
								foreach($horaires as $horaire1){
									if($horaire1->heureFin == $heureDebutRemp){
										foreach($horaires as $horaire2){
											if($horaires2->heureDebut == $heureFinRemp){
												if($horaire1->tbl_usager_id == $horaire2->tbl_usager_id && ($horaire1->type!=2 || $horaire2->type!=2)){
													$horaire1->heureFin = $horaire2->heureFin;
													if($horaire1->save()){
														if($horaire2->delete()){
															$remp->delete();
															$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
															$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
															$rempInfo->save();
															$this->redirect(array('index',array(
																'date'=>$date,
																'caserne'=>$caserne,
															)));
															break;
														}
													}
												}else{
													if(($horaire1->type == 2 && $horaire2->type == 2) || $horaire1->tbl_usager_id != $horaire2->tbl_usager_id){
														$remp->type = 0;
														$remp->tbl_usager_id = $caseHoraire->tbl_usager_id;
														if($remp->save()){
															$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
															$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
															$rempInfo->save();
															$this->redirect(array('index',array(
																'date'=>$date,
																'caserne'=>$caserne,
															)));
															break;
														}
													}elseif($horaire1->type == 2){
														$horaire2->heureDebut = $remp->heureDebut;
														if($horaire2->save()){
															$remp->delete();
															$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
															$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
															$rempInfo->save();
															$this->redirect(array('index',array(
																'date'=>$date,
																'caserne'=>$caserne,
															)));
															break;														
														}
													}else{
														$horaire1->heureFin = $remp->heureFin;
														if($horaire1->save()){
															$remp->delete();
															$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
															$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
															$rempInfo->save();
															$this->redirect(array('index',array(
																'date'=>$date,
																'caserne'=>$caserne,
															)));
															break;														
														}
													}								
												}
											}
										}
									}
								}
							}
						}
					}else{
						$quart = Quart::model()->findByPk($posteHoraire->tbl_quart_id);
						if($heureDebutRemp == $quart->heureDebut){
							if($caseHoraire->heureDebut == $heureFinRemp){
								$caseHoraire->heureDebut = $heureDebutRemp;
								if($caseHoraire->save()){
									$remp->delete();
									$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
									$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
									$rempInfo->save();
									$this->redirect(array('index',array(
										'date'=>$date,
										'caserne'=>$caserne,
									)));
								}
							}else{
								foreach($horaires as $horaire){
									if($horaire->heureDebut == $heureFinRemp){
										$horaire->heureDebut == $heureDebutRemp;
										if($horaire->save()){
											$remp->delete();
											$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
											$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
											$rempInfo->save();
											$this->redirect(array('index',array(
												'date'=>$date,
												'caserne'=>$caserne,
											)));
											break;
										}
									}
								}
							}
						}elseif($heureFinRemp == $quart->heureFin){
							if($caseHoraire->heureFin == $heureDebutRemp){
								$caseHoraire->heureFin = $heureFinRemp;
								if($caseHoraire->save()){
									$remp->delete();
									$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
									$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
									$rempInfo->save();
									$this->redirect(array('index',array(
										'date'=>$date,
										'caserne'=>$caserne,
									)));
								}
							}else{
								foreach($horaires as $horaire){
									if($horaire->heureFin == $heureDebutRemp){
										$horaire->heureFin == $heureFinRemp;
										if($horaire->save()){
											$remp->delete();
											$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
											$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
											$rempInfo->save();
											$this->redirect(array('index',array(
												'date'=>$date,
												'caserne'=>$caserne,
											)));
											break;
										}
									}
								}
							}							
						}else{
							if($caseHoraire->heureDebut == $heureFinRemp){
								foreach($horaires as $horaire){
									if($horaire->heureFin == $heureDebutRemp){
										if($horaire->tbl_usager_id == $caseHoraire->tbl_usager_id && $horaire->type!=2){
											if(count($horaires)>=2){
												$caseHoraire->heureDebut = $horaire->heureDebut;
											}else{
												$caseHoraire->heureDebut == '00:00:00';
												$caseHoraire->heureFin == '00:00:00';
											}
											if($caseHoraire->save()){
												if($horaire->delete()){
													$remp->delete();
													$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
													$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
													$rempInfo->save();
													$this->redirect(array('index',array(
														'date'=>$date,
														'caserne'=>$caserne,
													)));
													break;
												}
											}
										}else{
											$caseHoraire->heureDebut = $heureDebutRemp;
											if($caseHoraire->save()){
												$remp->delete();
												$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
												$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
												$rempInfo->save();
												$this->redirect(array('index',array(
													'date'=>$date,
													'caserne'=>$caserne,
												)));
												break;
											}
										}
									}
								}
							}elseif($caseHoraire->heureFin == $heureDebutRemp){
								foreach($horaires as $horaire){
									if($horaire->heureDebut == $heureFinRemp){
										if($horaire->tbl_usager_id == $caseHoraire->tbl_usager_id && $horaire->type!=2){
											if(count($horaires)>=2){
												$caseHoraire->heureFin = $horaire->heureFin;
											}else{
												$caseHoraire->heureDebut == '00:00:00';
												$caseHoraire->heureFin == '00:00:00';
											}
											if($caseHoraire->save()){
												if($horaire->delete()){
													$remp->delete();
													$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
													$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
													$rempInfo->save();
													$this->redirect(array('index',array(
														'date'=>$date,
														'caserne'=>$caserne,
													)));
													break;
												}
											}
										}else{
											$caseHoraire->heureFin = $heureFinRemp;
											if($caseHoraire->save()){
												$remp->delete();
												$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
												$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
												$rempInfo->save();
												$this->redirect(array('index',array(
													'date'=>$date,
													'caserne'=>$caserne,
												)));
												break;
											}
										}
									}
								}								
							}else{
								foreach($horaires as $horaire1){
									if($horaire1->heureFin == $heureDebutRemp){
										foreach($horaires as $horaire2){
											if($horaires2->heureDebut == $heureFinRemp){
												if($horaire1->tbl_usager_id == $horaire2->tbl_usager_id && ($horaire1->type!=2 || $horaire2->type!=2)){
													$horaire1->heureFin == $horaire2->heureFin;
													if($horaire1->save()){
														if($horaire2->delete()){
															$remp->delete();
															$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
															$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
															$rempInfo->save();
															$this->redirect(array('index',array(
																'date'=>$date,
																'caserne'=>$caserne,
															)));
															break;
														}
													}
												}else{
													if(($horaire1->type == 2 && $horaire2->type == 2) || $horaire1->tbl_usager_id != $horaire2->tbl_usager_id){
														$remp->type = 0;
														$remp->tbl_usager_id = $caseHoraire->tbl_usager_id;
														if($remp->save()){
															$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
															$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
															$rempInfo->save();
															$this->redirect(array('index',array(
																'date'=>$date,
																'caserne'=>$caserne,
															)));
															break;
														}
													}elseif($horaire1->type == 2){
														$horaire2->heureDebut = $remp->heureDebut;
														if($horaire2->save()){
															$remp->delete();
															$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
															$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
															$rempInfo->save();
															$this->redirect(array('index',array(
																'date'=>$date,
																'caserne'=>$caserne,
															)));
															break;														
														}
													}else{
														$horaire1->heureFin = $remp->heureFin;
														if($horaire1->save()){
															$remp->delete();
															$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
															$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
															$rempInfo->save();
															$this->redirect(array('index',array(
																'date'=>$date,
																'caserne'=>$caserne,
															)));
															break;														
														}
													}								
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}else{
				$remp->delete();
				$dateTime = new DateTime(date('Y-m-d')."T".date('H:i:s'),new DateTimeZone($parametres->timezone));
				$rempInfo->dateModif = $dateTime->format('Y-m-d H:i:s');
				$rempInfo->save();
				$this->redirect(array('index',array(
					'date'=>$date,
					'caserne'=>$caserne,
				)));
			}
		}
	}
	
	public function actionRapportRemp($choix=NULL, $dateDebut=NULL, $dateFin=NULL){
		$lstChoixDate = array();
		$lstChoixDate['0']['value']='dateHoraire';$lstChoixDate['0']['label']='Date de la case horaire';
		$lstChoixDate['1']['value']='dateModif';$lstChoixDate['1']['label']='Date de la modification';
		$listeChoixDate = CHtml::listData($lstChoixDate, 'value', 'label');
		
		if(Yii::app()->request->isAjaxRequest){
			if($dateDebut!==NULL && $dateFin!==NULL){
				$criteria = new CDbCriteria;
				if($choix == 'dateHoraire'){
					$condition = '(r.dateHoraire >= "'.$dateDebut.'" AND r.dateHoraire < "'.$dateFin.'")
											OR (r.dateHoraire > "'.$dateDebut.'" AND r.dateHoraire <= "'.$dateFin.'")';
				}else{
					$condition = '(DATE_FORMAT(FROM_UNIXTIME(r.dateModif), "%e %b %Y") >= "'.$dateDebut.'" AND DATE_FORMAT(FROM_UNIXTIME(r.dateModif), "%e %b %Y") < "'.$dateFin.'")
											OR (DATE_FORMAT(FROM_UNIXTIME(r.dateModif), "%e %b %Y") > "'.$dateDebut.'" AND DATE_FORMAT(FROM_UNIXTIME(r.dateModif), "%e %b %Y") <= "'.$dateFin.'")';
				}
				$sql =
			"SELECT
				r.tbl_horaire_parent AS ID_Horaire,
				r.dateModif AS DateModif,
				CONCAT(um.matricule,' - ',um.prenom,' ', um.nom) AS UsagerModif,
				CONCAT(ur.matricule,' - ',ur.prenom,' ', ur.nom) AS UsagerRemp,
				r.heureDebut AS heureDebutRemp,
				r.heureFin AS heureFinRemp,
				h.date AS DateHoraire,
				CONCAT(uh.matricule,' - ',uh.prenom,' ', uh.nom) AS UsagerHoraire,
				q.nom AS Quart,
				a.id AS Absence,
				CONCAT(a.heureDebut,' à ',a.heureFin) AS HeureDemandee,
				tc.nom AS Raison,
				r.type AS Type
			FROM
				tbl_remplacement_info r
				LEFT JOIN tbl_usager um ON um.id = r.tbl_usager_modif
				LEFT JOIN tbl_usager ur ON ur.id = r.tbl_usager_remp
				LEFT JOIN tbl_horaire h ON h.id = r.tbl_horaire_parent
				LEFT JOIN tbl_usager uh ON uh.id = h.tbl_usager_id
				LEFT JOIN tbl_poste_horaire ph ON ph.id = h.tbl_poste_horaire_id
				LEFT JOIN tbl_quart q ON q.id = ph.tbl_quart_id
				LEFT JOIN tbl_absence a ON a.tbl_usager_id = h.tbl_usager_id AND a.statut = 2 AND
				(
					IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut))<=a.heureDebut AND
					IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin))>=a.heureDebut
					OR
					IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut))<=a.heureFin AND
					IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin))>=a.heureFin
				)
				LEFT JOIN tbl_type_conge tc ON tc.id = a.tbl_type_id
			WHERE ".$condition."
			ORDER BY r.tbl_horaire_parent, r.dateModif ASC
			";				
				$cn = Yii::app()->db;
				$cm = $cn->createCommand($sql);
				$dataRemp = $cm->query();
					
				$this->renderPartial('_resultRemp',array(
										'dataRemp'=>$dataRemp,
									));
			}else{
				echo 'date';
			}
			Yii::app()->end();
		}

		$this->render("rapportRemp",array(
				'DateDebut'=>NULL,
				'DateFin'=>NULL,
				'lstSelected'=>'dateHoraire',
				'listeChoixDate'=>$listeChoixDate,
				'dataRemp'=>NULL,
		));		
	}
	
	public function actionImporterTP($date, $caserne){	
		$parametres = Parametres::model()->findByPk(1);
		
		$dateFixe = "1990-01-01";
		
		$dateDebut = new DateTime($date."T00:00:00",new DateTimeZone('America/Montreal'));		
		$dateDebutFixe = Horaire::debutPeriode($parametres->nbJourHoraireFixe,$parametres->moduloDebut,$dateFixe);
		
		$dateFinFixe = date_add(clone $dateDebutFixe,new DateInterval("P".$parametres->nbJourHoraireFixe."D"));
		$TSdd = $dateDebut->getTimestamp();
		$TSddF = $dateDebutFixe->getTimestamp();
		
		$criteria = new CDbCriteria;
		$criteria->alias = 'dh';
		$criteria->join = 'INNER JOIN tbl_poste_horaire_caserne phc ON dh.tbl_poste_horaire_id = phc.tbl_poste_horaire_id';
		$criteria->condition = 'dh.date>=:dateDebut AND dh.date<:dateFin AND phc.tbl_caserne_id = :caserne AND dh.tbl_caserne_id = :caserne';
		$criteria->params = array(':dateDebut'=>$dateDebutFixe->format('Y-m-d'),':dateFin'=>$dateFinFixe->format('Y-m-d'),':caserne'=>$caserne);
		
		$horairesFixe = Horaire::model()->findAll($criteria);
		foreach($horairesFixe as $horaireFixe){
			$dateFixe = new DateTime($horaireFixe->date."T00:00:00",new DateTimeZone('America/Montreal'));
			$TSdf = $dateFixe->getTimestamp();
			
			$difTS = $TSdf - $TSddF;
			$TSddN = $TSdd + $difTS;
			$dateR = date('Y-m-d',$TSddN);
			$dateRe = new DateTime($dateR."T00:00:00",new DateTimeZone('America/Montreal'));	
			
			$criteria = new CDbCriteria;
			$criteria->condition = 'date =:date AND type = 0 AND tbl_poste_horaire_id = :posteH AND (parent_id IS NULL OR parent_id = 0) AND tbl_caserne_id = :caserne';
			$criteria->params = array(':date'=>$dateRe->format('Y-m-d'),':posteH'=>$horaireFixe->tbl_poste_horaire_id, ':caserne'=>$caserne);
			$horaire = Horaire::model()->find($criteria);
			if($horaire===NULL){
				$horaire = new Horaire;
				$horaire->setAttributes($horaireFixe->getAttributes());
				$horaire->date = $dateRe->format('Y-m-d');
				
				if(!$horaire->save()){
					echo '0mUn enregistrement a échoué.';
					Yii::app()->end();
				}
			}
		}
		echo '1';
		Yii::app()->end();
	}
	
	public function actionListeDispoPart($pompier, $quart, $date){	
		$criteria = new CDbCriteria;
		$criteria->alias = 'dispo';
		$criteria->join = 'INNER JOIN tbl_usager usager ON usager.id = dispo.tbl_usager_id';
		$criteria->condition = 'usager.matricule = :pompier AND usager.actif = 1 AND dispo.date = :date AND dispo.quart = :quart';
		$criteria->params = array(':pompier'=>$pompier, ':date'=>$date, ':quart'=>$quart);
		
		$dispos = DispoHoraire::model()->findAll($criteria);
		
		$retour = array();
		$i=0;
		foreach($dispo as $dispo){
			$retour[$i] = $dispo->heureDebut.' - '.$dispo->heureFin;
			$i++;
		}
		
		echo json_encode($retour);
		Yii::app()->end();
	}
	
	public function actionConge($filtre = 'a.dateConge DESC'){
		$this->pageTitle = 'Avis d\'absence';
		$this->layout='//layouts/column2';
		
		$criteria = new CDbCriteria();
		$criteria->alias = 'a';
		$criteria->join = 'INNER JOIN tbl_usager u ON u.id = a.tbl_usager_id';
		$criteria->order = $filtre.', u.dateEmbauche ASC';		
		$criteria->condition = 'a.tbl_usager_id = :usager AND a.archive = 0';
		$criteria->params = array(':usager'=>Yii::app()->user->id);
		
		$dataProviderPerso=new CActiveDataProvider('Absence',array('criteria'=>$criteria));
		
		$criteria = new CDbCriteria();
		$criteria->alias = 'a';
		$criteria->join = 'INNER JOIN tbl_usager u ON u.id = a.tbl_usager_id';
		$criteria->order = $filtre.', u.dateEmbauche ASC';		
		$criteria->condition = 'a.tbl_usager_id <> :usager AND a.statut = 1 AND a.archive = 0';
		$criteria->params = array(':usager'=>Yii::app()->user->id);		
		
		$dataProviderFermer=new CActiveDataProvider('Absence',array('criteria'=>$criteria));
		
		$criteria = new CDbCriteria();
		$criteria->alias = 'a';
		$criteria->join = 'INNER JOIN tbl_usager u ON u.id = a.tbl_usager_id';
		$criteria->order = $filtre.', u.dateEmbauche ASC';		
		$criteria->condition = 'a.tbl_usager_id <> :usager AND a.statut = 2 AND a.archive = 0';
		$criteria->params = array(':usager'=>Yii::app()->user->id);
		
		$dataProviderAccepter=new CActiveDataProvider('Absence',array('criteria'=>$criteria));
		
		$criteria = new CDbCriteria();
		$criteria->alias = 'a';
		$criteria->join = 'INNER JOIN tbl_usager u ON u.id = a.tbl_usager_id';
		$criteria->order = $filtre.', u.dateEmbauche ASC';		
		$criteria->condition = 'a.tbl_usager_id <> :usager AND a.statut = 3 AND a.archive = 0';
		$criteria->params = array(':usager'=>Yii::app()->user->id);		
		
		$dataProviderRefuser=new CActiveDataProvider('Absence',array('criteria'=>$criteria));
		
		$SiAdmin = Yii::app()->user->checkAccess('Admin');
		
		$tblFiltre = array('a.dateConge ASC'=>'Date du congé - Croissant','a.dateConge DESC'=>'Date du congé - Décroissant','a.dateEmis ASC'=>'Date de la demande - Croissant','a.dateEmis DESC'=>'Date de la demande - Décroissant');
		
		$this->render('conge',array(
			'dataProviderPerso'=>$dataProviderPerso,
			'dataProviderFermer'=>$dataProviderFermer,
			'dataProviderAccepter'=>$dataProviderAccepter,
			'dataProviderRefuser'=>$dataProviderRefuser,
			'siAdmin'=>$SiAdmin,
			'filtre'=>$filtre,
			'tblFiltre'=>$tblFiltre,
		));		
	}
	
	public function actionCongeView($id){
		$this->pageTitle = 'Avis d\'absence';
		$this->layout='//layouts/column2';
		
		$conge = Absence::model()->findByPk($id);
		
		$this->render('congeView',array(
			'model'=>$conge,
		));	
	}
	
	public function actionCongeCreate($id=0){
		$this->pageTitle = 'Avis d\'absence';
		
		$model = new Absence;
		$model->tbl_usager_id = Yii::app()->user->id;
		$model->statut = 0;
		
		if($id != 0){
			$OriModel = Absence::model()->findByPk($id);
			$model->dateConge = $OriModel->dateConge;
			$model->heureDebut = $OriModel->heureDebut;
			$model->heureFin = $OriModel->heureFin;
			$model->tbl_type_id = $OriModel->tbl_type_id;
			$model->tbl_quart_id = $OriModel->tbl_quart_id;
			$model->note = $OriModel->note;
		}		
		
		if(isset($_POST['Absence'])){
			$model->attributes=$_POST['Absence'];
			$model->statut = 1;
			$model->tbl_usager_id = Yii::app()->user->id;
			$model->dateEmis = date('Y-m-d');
			$model->heureDebut .= ':00';
			$model->heureFin .= ':00';
			if($model->save())
				$types = TypeConge::model()->findAll();
			$listType = array();
			foreach($types as $type){
				$listType[$type->id] = (($type->abrev !== NULL)?$type->abrev.' - ':'').$type->nom;
			}
			$notificationUsers = Usager::model()->findAllBySql(
				'SELECT * 
				FROM tbl_usager u
				INNER JOIN AuthAssignment aa ON aa.userid=u.id
				WHERE aa.itemname="GesHoraire"'
			);
			$usager = Usager::model()->findByPk(Yii::app()->user->id);
			foreach ($notificationUsers as $user) 
			{
				$notification = new Notification;
				$notification->tbl_usager_id = $user->id;
				$notification->dateCreation = date('Y-m-d');
				$notification->categorie = 'controller';
				$notification->message = "horaire.congeCreate.avisAbsence.notification";
				$notification->details = json_encode(array(
					'{PrenomNomPompier}'=>$usager->getMatPrenomNom(),
					'{dateHeureAvis}'=>date('Y-m-d H:i:s'),
					'{dateDebut}'=>$model->dateConge,
					'{heureDebut}'=>$model->heureDebut,
					'{heureFin}'=>$model->heureFin,
					'{typeConge}'=>$listType[$model->tbl_type_id],
					'{avisLien}'=>CHtml::link('ici',array('horaire/congeUpdate', 'id'=>$model->id))
				));
				$notification->save();
			}
			$this->redirect(array('congeView','id'=>$model->id));			
		}
		$siAdmin = Yii::app()->user->checkAccess('Admin');
		
		$avisNV = '';
		if($siAdmin){
			$criteria = new CDbCriteria();
			$criteria->alias = 'a';
			$criteria->join = 'INNER JOIN tbl_usager u ON u.id = a.tbl_usager_id';
			$criteria->order = 'a.dateConge DESC, u.dateEmbauche ASC';
			$criteria->condition = 'statut = 1';

			$avisNV = Absence::model()->findAll($criteria);
			$this->layout='//layouts/columnAvis';
		}else{
			$this->layout='//layouts/column2';
		}
		
		$types = TypeConge::model()->findAll();
		$listType = array();
		foreach($types as $type){
			$listType[$type->id] = (($type->abrev !== NULL)?$type->abrev.' - ':'').$type->nom;
		}
		$this->render('congeCreate',array(
			'model'=>$model,
			'siAdmin'=>$siAdmin,
			'avisNV'=>$avisNV,
			'listType'=>$listType,
		));	
	}
	
	public function actionCongeUpdate($id){
		$this->pageTitle = 'Avis d\'absence';
		
		$siAdmin = Yii::app()->user->checkAccess('Admin');
		
		$model = Absence::model()->findByPk($id);
		
		$criteria = new CDbCriteria();
		$criteria->alias = 'a';
		$criteria->join = 'INNER JOIN tbl_usager u ON u.id = a.tbl_usager_id';
		$criteria->order = 'a.dateConge DESC, u.dateEmbauche ASC';
		if(!$siAdmin){
			$criteria->condition = 'a.tbl_usager_id = :id';
			$criteria->params = array(':id'=>Yii::app()->user->id);
		}
		
		$absences = Absence::model()->findAll($criteria);
		
		$avisNV = '';
		if($siAdmin){
			$criteria = new CDbCriteria();
			$criteria->alias = 'a';
			$criteria->join = 'INNER JOIN tbl_usager u ON u.id = a.tbl_usager_id';
			$criteria->order = 'a.dateConge DESC, u.dateEmbauche ASC';
			$criteria->condition = 'statut = 1';

			$avisNV = Absence::model()->findAll($criteria);
			$this->layout='//layouts/columnAvis';
		}else{
			$this->layout='//layouts/column2';
		}
		
		$idP = 0;$idS = 0;$trouver = false;
		foreach($absences as $absence){
			if($absence->id == $id ){
				$trouver = true;
			}else{
				if($trouver){
					$idS = $absence->id;
					break;
				}else{
					$idP = $absence->id;
				}
			}
		}
		
		$types = TypeConge::model()->findAll();
		$listType = array();
		foreach($types as $type){
			$listType[$type->id] = (($type->abrev !== NULL)?$type->abrev.' - ':'').$type->nom;
		}
		
		$criteria = new CDbCriteria;
		$criteria->order = 'matricule ASC';
		$usagers = Usager::model()->findAll($criteria);
		
		$listUsager = array('0'=>'- Aucun -');
		foreach($usagers as $usager){
			$listUsager[$usager->id] = $usager->getMatPrenomNom();
		}
		
		$listRadioButton = array();
		$listRadioButton['0'] = 'Oui';
		$listRadioButton['1'] = 'Non';
		
		
		$this->render('congeUpdate',array(
			'idS' => $idS,
			'idP' => $idP,
			'model'=>$model,
			'listType'=>$listType,
			'listUsager'=>$listUsager,
			'listRadioButton'=>$listRadioButton,
			'siAdmin'=>$siAdmin,
			'avisNV'=>$avisNV,
		));	
	}
	
	public function actionCongeValider($id, $statut=0, $raison='', $dir=''){
		$parametres = Parametres::model()->findByPk(1);
		$this->pageTitle = 'Avis d\'absence';
			
		$model = Absence::model()->findByPk($id);
		if($statut != 0){	
			$model->statut = $statut;
			$model->chef_id = Yii::app()->user->id;
			$date = new DateTime('now',new DateTimeZone($parametres->timezone));
			$model->dateRecu = $date->format('Y-m-d');
			$model->heureRecu = $date->format('H:i:s');
			$model->raison = $raison;
			
			if($model->save()){
				if($dir==''){
					$this->redirect(array('congeUpdate','id'=>$id));
				}else{
					$this->redirect(array('conge'));
				}
			}
		}else{
			$this->renderPartial('_formCongeValider',array(
					'model'=>$model
			), false, true);			
		}
	}
	
	public function actionCongeGestion($filtre = "dateConge DESC"){
		$this->pageTitle = 'Avis d\'absence';
		$this->layout='//layouts/column2';

		$criteria = new CDbCriteria();
		$criteria->alias = 'a';
		$criteria->condition = 'a.tbl_usager_id <> :usager AND a.statut = 1 AND a.archive = 0';
		$criteria->params = array(':usager'=>Yii::app()->user->id);
		$criteria->group = ((strpos($filtre,'ASC')!==false)?substr($filtre,0,strlen($filtre)-4):substr($filtre,0,strlen($filtre)-5));
		$criteria->order = $filtre;
		
		$absences = Absence::model()->findAll($criteria);
		
		$tblFiltre = array('a.dateConge ASC'=>'Date du congé - Croissant','a.dateConge DESC'=>'Date du congé - Décroissant','a.dateEmis ASC'=>'Date de la demande - Croissant','a.dateEmis DESC'=>'Date de la demande - Décroissant');
		
		$this->render('congeGestion',array(
			'filtre'=>$filtre,
			'absences'=>$absences,
			'tblFiltre'=>$tblFiltre,
		));			
	}
	
	public function actionTypeConge(){
		$this->pageTitle = 'Avis d\'absence';
		$this->layout='//layouts/column2';
		$criteria = new CDbCriteria();
		
		$dataProvider=new CActiveDataProvider('TypeConge',array('criteria'=>$criteria));
		$this->render('typeConge',array(
			'dataProvider'=>$dataProvider,
		));			
	}
	
	public function actionTypeView($id){
		$this->pageTitle = 'Avis d\'absence';
		$this->layout='//layouts/column2';
		$type = TypeConge::model()->findByPk($id);
		
		$this->render('typeView',array(
			'model'=>$type,
		));			
	}
	
	public function actionTypeCreate(){
		$this->pageTitle = 'Avis d\'absence';
		$this->layout='//layouts/column2';
		$model = new TypeConge;
		
		if(isset($_POST['TypeConge']))
		{
			$model->attributes=$_POST['TypeConge'];
			if($model->save())
				$this->redirect(array('typeView','id'=>$model->id));
		}

		$this->render('typeCreate',array(
			'model'=>$model,
		));
	}
	
	public function actionTypeModif($id){
		$this->pageTitle = 'Avis d\'absence';
		$this->layout='//layouts/column2';
		$model = TypeConge::model()->findByPk($id);
		
		if(isset($_POST['TypeConge']))
		{
			$model->attributes=$_POST['TypeConge'];
			if($model->save())
				$this->redirect(array('typeView','id'=>$model->id));
		}

		$this->render('typeUpdate',array(
			'model'=>$model,
		));
	}
	
	public function actionTypeDelete($id){
		$this->pageTitle = 'Avis d\'absence';
		$this->layout='//layouts/column2';
		$model = TypeConge::model()->findByPk($id);
		
		$conges = Conge::model()->count(array('condition'=>'tbl_type_id = :type', 'params'=>array(':type'=>$id)));
		
		if($conges == 0){
			$model->delete();
		}
		
		$criteria = new CDbCriteria();
		$criteria->condition = 'siActif = 1';
		
		$dataProvider=new CActiveDataProvider('TypeConge',array('criteria'=>$criteria));
		$this->render('typeConge',array(
			'dataProvider'=>$dataProvider,
		));			
	}
	
	public function actionCongeAppel(){
		$parametres = Parametres::model()->findByPk(1);
		if($_POST['matricule']!=''){
			$dateA = date('Y-m-d');
			$date = new DateTime($dateA."T00:00:00",new DateTimeZone($parametres->timezone));
				
			$app = new AbsenceAppel;
			
			$criteriaUsager = new CDbCriteria;
			$criteriaUsager->condition = 'matricule = :usager';
			$criteriaUsager->params = array(':usager'=>$_POST['matricule']);
			$usager = Usager::model()->find($criteriaUsager);
			
			if($usager!=NULL){				
				$app->tbl_absence_id = $_POST['id'];
				$app->tbl_usager_id = $usager->id;
				$app->reponse = $_POST['reponse'];
				$app->dateAppel = $date->format('Y-m-d');
				$app->heure = $date->format('H:i:s');
				
				$app->save();
			}
		}
		$criteria = new CDbCriteria;
		$criteria->condition = 'tbl_absence_id = :absence';
		$criteria->params = array(':absence'=>$_POST['id']);
		$criteria->order = 'dateAppel ASC, heure ASC';
		
		$appels = AbsenceAppel::model()->findAll($criteria);
		
		$retour = '<tr><th>Pompier</th><th>Réponse</th></tr>';
		foreach($appels as $appel){
			$usager = Usager::model()->findByPk($appel->tbl_usager_id);
			
			$retour .= '<tr>';
			$retour .= '<td>'.$usager->matricule.'</td>';
			$retour .= '<td>'.(($appel->reponse==0)?'Oui':'Non').'</td>';
			$retour .= '</tr>';
		}		
		$retour .= '<tr>';
		$retour .= '<td>'.CHtml::textField('appel_matricule', '').'</td>';
		$retour .= '<td>'.CHtml::radioButtonList('appel_reponse', '0', array('0'=>'Oui', '1'=>'Non')).'</td>';
		$retour .= '</tr>';
		
		echo $retour;
	}
	
	public function actionCongeArchiver($id){
		$model = Absence::model()->findByPk($id);
		
		if($model->statut == '2' || $model->statut == '3'){
			$model->archive = 1;
			$model->save();
		}
		
		$this->redirect(array('conge'));
	}
	
	public function actionCongeArchive(){
		$this->pageTitle = 'Avis d\'absence';
		$this->layout='//layouts/column2';
		
		$criteria = new CDbCriteria();
		$criteria->alias = 'a';
		$criteria->join = 'INNER JOIN tbl_usager u ON u.id = a.tbl_usager_id';
		$criteria->order = 'a.dateConge DESC, u.dateEmbauche ASC';
		$criteria->condition = 'a.tbl_usager_id = :usager AND a.archive = 1 AND (a.statut = 2 OR a.statut = 3)';
		$criteria->params = array(':usager'=>Yii::app()->user->id);
		
		$dataProviderPerso=new CActiveDataProvider('Absence',array('criteria'=>$criteria));
		
		$criteria = new CDbCriteria();
		$criteria->alias = 'a';
		$criteria->join = 'INNER JOIN tbl_usager u ON u.id = a.tbl_usager_id';
		$criteria->order = 'a.dateConge DESC, u.dateEmbauche ASC';
		$criteria->condition = 'a.tbl_usager_id <> :usager AND a.statut = 2 AND a.archive = 1';
		$criteria->params = array(':usager'=>Yii::app()->user->id);
		
		$dataProviderAccepter=new CActiveDataProvider('Absence',array('criteria'=>$criteria));
		
		$criteria = new CDbCriteria();
		$criteria->alias = 'a';
		$criteria->join = 'INNER JOIN tbl_usager u ON u.id = a.tbl_usager_id';
		$criteria->order = 'a.dateConge DESC, u.dateEmbauche ASC';
		$criteria->condition = 'a.tbl_usager_id <> :usager AND a.statut = 3 AND a.archive = 1';
		$criteria->params = array(':usager'=>Yii::app()->user->id);
		
		$dataProviderRefuser=new CActiveDataProvider('Absence',array('criteria'=>$criteria));
		
		$SiAdmin = Yii::app()->user->checkAccess('Admin');
		
		$this->render('congeArchive',array(
				'dataProviderPerso'=>$dataProviderPerso,
				'dataProviderAccepter'=>$dataProviderAccepter,
				'dataProviderRefuser'=>$dataProviderRefuser,
				'siAdmin'=>$SiAdmin,
		));		
	}
	
	public function actionImprimerConge($id){
		$model = Absence::model()->findByPk($id);;
		//$this->renderPartial('imprimer', array('model'=>$model),true);
		# mPDF
		$mPDF1 = Yii::app()->ePdf->mpdf('','Letter');
		# You can easily override default constructor's params
		# Load a stylesheet
		/*$stylesheet = file_get_contents(Yii::getPathOfAlias('webroot.css') . '/main.css');
		 $mPDF1->WriteHTML($stylesheet, 1);*/
		$stylesheet = file_get_contents(Yii::getPathOfAlias('webroot.css') . '/print.css');
		$mPDF1->WriteHTML($stylesheet, 1);
		// Set a simple Footer including the page number
		//$mPDF1->setFooter('{PAGENO}');
		//$mPDF1->WriteHTML(CHtml::image(Yii::getPathOfAlias('webroot.images') . '/LogoSwordwareBase.png' ));
		# renderPartial (only 'view' of current controller)
		$mPDF1->WriteHTML($this->renderPartial('imprimerConge', array('model'=>$model), true));
		# Renders image
		
		# Outputs ready PDF
		$mPDF1->Output($model->id.'-'.$model->tblUsager->matricule.'_'.$model->tblUsager->prenom.'_'.$model->tblUsager->nom.'.pdf','I');		
	}
	
	public function actionTimestamp(){
		$dispos = DispoHoraire::model()->findAll();
		$i=0;
		foreach($dispos as $dispo){
			if($dispo->heureDebut!==NULL){
				$datetimed = new DateTime($dispo->date.$dispo->heureDebut);
				$dispo->tsDebut = $datetimed->format('Y-m-d H:i:s');
				$datetimef = new DateTime($dispo->date.$dispo->heureFin);
				if($datetimef->getTimestamp() < $datetimed->getTimestamp()){
					$datetimef->add(new DateInterval('P1D'));
				}
				$dispo->tsFin = $datetimef->format('Y-m-d H:i:s');
			}else{
				$quart = Quart::model()->findByPk($dispo->tbl_quart_id);
				$datetimed = new DateTime($dispo->date.$quart->heureDebut);
				$dispo->tsDebut = $datetimed->format('Y-m-d H:i:s');
				$datetimef = new DateTime($dispo->date.$quart->heureFin);
				if($datetimef->getTimestamp() < $datetimed->getTimestamp()){
					$datetimef->add(new DateInterval('P1D'));
				}
				$dispo->tsFin = $datetimef->format('Y-m-d H:i:s');			
			}
			$dispo->save();	
		}
		echo 'terminer';
	}
	
	public function actionHeureTempsPartiel(){
		echo Horaire::heureTempsPartiel('');
	}
	
	public function actionRapports(){
		$this->render("rapports");
	}
	
	//Rapport de temps de garde
	public function actionTemps($dateDebut=NULL, $dateFin=NULL){
		$parametres = Parametres::model()->findByPk(1);
		$dataDispo = NULL;
		$usagers = Usager::model()->findAll();
		$tblUsagers = array();
		$quarts = Quart::model()->findAll();
		$tblQuarts = array();
		foreach($quarts as $quart){
			$tblQuarts[$quart->id] = $quart->nom;
		}
		$equipes = Equipe::model()->findAll(array('condition'=>'siActif = 1 AND siFDF = 1'));
		$tblEquipes = array();
		foreach($equipes as $equipe){
			$tblEquipes[$equipe->id] = $equipe->nom;
		}
		
		$lstRegroup = array();
		$lstRegroup[0]['label'] = Yii::t('controller','dispoFDFRaportComplet.parEquipe'); $lstRegroup[0]['value'] = '0';
		$lstRegroup[1]['label'] = Yii::t('controller','dispoFDFRaportComplet.parPompier'); $lstRegroup[1]['value'] = '1';
		
		$tblRegroup = CHtml::listData($lstRegroup, 'value', 'label', '');
		
		if(Yii::app()->request->isAjaxRequest){
			//$_GET['dateDebut']='2015-04-01';
			//$_GET['dateFin']='2015-04-02';
			if($_GET['dateDebut']!='' && $_GET['dateFin']!=''){
				$sql =
				"SELECT 
					CONCAT(IFNULL(u2.matricule,u.matricule),' ',IFNULL(u2.prenom,u.prenom),' ',IFNULL(u2.nom,u.nom)) AS Usager,
					IFNULL(u2.id,u.id) AS ID_pompier,  
					SUM(
						IF(time_to_sec(IF(subtime(h.heureFin,h.heureDebut)>=0,subtime(h.heureFin,h.heureDebut),addtime(subtime(h.heureFin,h.heureDebut),'24:00:00')))/3600 <> 0.0000, 
							time_to_sec(IF(subtime(h.heureFin,h.heureDebut)>=0,subtime(h.heureFin,h.heureDebut),addtime(subtime(h.heureFin,h.heureDebut),'24:00:00')))/3600, 
							IF(time_to_sec(IF(subtime(ph.heureFin,ph.heureDebut)>=0,subtime(ph.heureFin,ph.heureDebut),addtime(subtime(ph.heureFin,ph.heureDebut),'24:00:00')))/3600 <> 0.0000, 
								time_to_sec(IF(subtime(ph.heureFin,ph.heureDebut)>=0,subtime(ph.heureFin,ph.heureDebut),addtime(subtime(ph.heureFin,ph.heureDebut),'24:00:00')))/3600, 
								time_to_sec(IF(subtime(q.heureFin,q.heureDebut)>=0,subtime(q.heureFin,q.heureDebut),addtime(subtime(q.heureFin,q.heureDebut),'24:00:00')))/3600 
							) 
						)
					) AS HeureTotal
				FROM 
					((numbers i1, numbers i2, numbers i3), 
					(tbl_quart q LEFT JOIN tbl_poste_horaire ph ON ph.tbl_quart_id=q.id)) 
					LEFT JOIN tbl_horaire h ON h.tbl_poste_horaire_id=ph.id AND h.date=ADDDATE('".$_GET['dateDebut']."', i3.i*100+i2.i*10+i1.i) AND h.type IN (0,2) 
					LEFT JOIN tbl_usager u ON u.id=h.tbl_usager_id 
					LEFT JOIN tbl_horaire h2 ON h2.parent_id = h.id AND h2.dateModif=(SELECT MAX(m.dateModif) FROM tbl_horaire m WHERE m.parent_id=h.id) AND h2.type = 1 
					LEFT JOIN tbl_usager u2 ON u2.id=h2.tbl_usager_id 
				WHERE 
					(ADDDATE('".$_GET['dateDebut']."', i3.i*100+i2.i*10+i1.i) <= '".$_GET['dateFin']."') AND 
					(u2.id IS NOT NULL OR u.id IS NOT NULL) 
				GROUP BY ID_pompier 
				ORDER BY ID_pompier";
				$cn = Yii::app()->db;
				$cm = $cn->createCommand($sql);
				$dataDispo = $cm->query();
				$this->renderPartial('_resultTemps',array(
						'dataDispo'=>$dataDispo,
						'dateDebut'=>$_GET['dateDebut'],
						'dateFin'=>$_GET['dateFin'],
				),false,true);
			}else{
				echo 'date';
			}
			Yii::app()->end();
		}
		$this->render("rapportTemps",array(
				'parametres' => $parametres,
				'DateDebut'=>NULL,
				'DateFin'=>NULL,
				'dataDispo'=>FALSE,
		));		
			
	}
	
	public function actionRapportAbsence($annee = ''){
		$parametres = Parametres::model()->findByPk(1);
		$sql =
		"
			SELECT
				YEAR(a.dateConge) as Annee
			FROM
				tbl_absence a
			WHERE 
				a.statut = 2
			GROUP BY
				YEAR(a.dateConge)
			";
		$cn = Yii::app()->db;
		$cm = $cn->createCommand($sql);
		$dataAnnee = $cm->query();
		
		$row = $dataAnnee->read();
		$annees = array();$i=0;
		do{
			$annees[$i]['label'] = $row['Annee'];
			$annees[$i]['value'] = $row['Annee'];
			$i++;
			$row = $dataAnnee->read();
		}while($row!==FALSE);
		
		$listeAnnee = CHtml::listData($annees, 'value', 'label');
		
		if($annee != ''){
			$sql = 
			"
			SELECT
				CONCAT(u.matricule,\" - \",u.prenom, \" \", u.nom) as Usager,
				SUM(a.heureFin - a.heureDebut)/10000 as Heure,
				MONTH(a.dateConge) as Mois
			FROM
				tbl_usager u
			LEFT JOIN 
				tbl_absence a ON u.id = a.tbl_usager_id
			WHERE
				YEAR(a.dateConge) = '".$annee."' AND
				a.statut = 2
			GROUP BY
				a.tbl_usager_id, MONTH(a.dateConge)
			ORDER BY 
				u.matricule ASC,
				MONTH(a.dateConge) ASC
			";
			$cn = Yii::app()->db;
			$cm = $cn->createCommand($sql);
			$dataAbsence = $cm->query();
			$this->renderPartial('_resultAbsence',array(
				'annee'=>$annee,
				'dataAbsence'=>$dataAbsence,
				'banque'=>$parametres->congeHeureMax,
			));
			Yii::app()->end();
		}else{
			$dataAbsence = FALSE;
		}
		$this->render("rapportAbsence",array(
				'listeAnnee' => $listeAnnee,
				'parametres' => $parametres,
				'annee'=>$annee,
				'dataAbsence'=>$dataAbsence,
		));
	}
}