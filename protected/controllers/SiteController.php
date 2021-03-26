<?php

class SiteController extends Controller
{
	public $defaultAction = 'login';
	
	public $pageTitle = "Accueil";
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}
	

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$parametres = Parametres::model()->findByPk(1);
		Yii::app()->language = 'fr';
		if(Yii::app()->user->isGuest)
		{
			$this->redirect(array('login'));
		}
		
		$usager = Usager::model()->findByPk(Yii::app()->user->id);
		
		$dateDebut = new DateTime(date('Y-m-d')."T00:00:00",new DateTimeZone($parametres->timezone));
		$dateFin = date_add(clone $dateDebut,new DateInterval("P7D"));
		
		/*$sql = 
		"SELECT 
			ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) AS Jour, 
			c.nom AS caserne,
			q.heureDebut AS qHeureDebut, 
			q.heureFin AS qHeureFin,
			time_to_sec(IF(subtime(q.heureFin,q.HeureDebut)>=0,subtime(q.heureFin,q.HeureDebut),addtime(subtime(q.heureFin,q.HeureDebut),'24:00:00')))/3600 AS qHeureReel,
			p.nom AS Poste,
			ph.heureDebut AS phHeureDebut, 
			ph.heureFin AS phHeureFin, 
			time_to_sec(IF(subtime(ph.heureFin,ph.heureDebut)>=0,subtime(ph.heureFin,ph.heureDebut),addtime(subtime(ph.heureFin,ph.heureDebut),'24:00:00')))/3600 AS phHeureReel,
			e.couleur AS couleur_garde,
			h.type AS typeH, 
			h.heureDebut AS hHeureDebut, 
			h.heureFin AS hHeureFin,
			time_to_sec(IF(subtime(h.heureFin,h.heureDebut)>=0,subtime(h.heureFin,h.heureDebut),addtime(subtime(h.heureFin,h.heureDebut),'24:00:00')))/3600 AS hHeureReel
		FROM 
			((numbers i1, numbers i2), (tbl_quart q LEFT JOIN (tbl_poste_horaire ph INNER JOIN tbl_poste p ON p.id=ph.tbl_poste_id) ON ph.tbl_quart_id=q.id))
			LEFT JOIN tbl_horaire h ON h.tbl_poste_horaire_id=ph.id AND h.date=ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) AND h.type IN (0,2) 
			LEFT JOIN tbl_usager u ON u.id=h.tbl_usager_id 
			LEFT JOIN tbl_horaire h2 ON h2.parent_id = h.id AND h2.dateModif=(SELECT MAX(m.dateModif) FROM tbl_horaire m WHERE m.parent_id=h.id) AND h2.type = 1 
			LEFT JOIN tbl_usager u2 ON u2.id=h2.tbl_usager_id 
			LEFT JOIN tbl_equipe_garde eg ON q.id=eg.tbl_quart_id AND eg.modulo=MOD((UNIX_TIMESTAMP(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i)) DIV 86400),28) 
			LEFT JOIN tbl_equipe e on e.id=eg.tbl_equipe_id
			LEFT JOIN tbl_caserne c on qc.tbl_caserne_id = c.id
		WHERE 
			(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) < '".$dateFin->format('Y-m-d')."')
			AND h.tbl_usager_id = ".Yii::app()->user->id."
			AND (ph.dateFin >= '".$dateFin->format('Y-m-d')."' OR ph.dateFin IS NULL) 
		ORDER BY Jour, q.heureDebut, p.id, ph.heureDebut, h.heureDebut";
		
		$cn = Yii::app()->db;		
		$cm = $cn->createCommand($sql);
		
		$curseurHoraire = $cm->query();*/

		$casernes = $usager->getCaserne();
		
		$conditionCaserne = 'tbl_caserne_id IN ('.$casernes.')';
		
		$documents = DocumentCaserne::model()->findAll($conditionCaserne);
		
		$criteria = new CDbCriteria;
		$ids = '(';
		foreach($documents as $document){
			$ids .= $document->tbl_document_id.', ';
		}
		if($ids != '('){
			$ids = substr($ids,0,strlen($ids)-2);
			$ids .= ')';

			$criteria->condition .= 'id IN '.$ids;
			$criteria->limit = '3';
			$criteria->order = 'date DESC';
		}else{
			$criteria->condition = 'id = 0';
		}		
		
		$dataDocument=new CActiveDataProvider('Document');
		$dataDocument->setPagination(false);
		$dataDocument->setCriteria($criteria);
		
		$dateJour = date('Y-m-d');
		
		$notices = NoticeCaserne::model()->findAll($conditionCaserne);

		$criteria = new CDbCriteria;
		$criteria->condition = 'dateDebut<="'.$dateJour.'" AND dateFin>="'.$dateJour.'" AND ';
		$ids = '(';
		foreach($notices as $notice){
			$ids .= $notice->tbl_notice_id.', ';
		}
		if($ids != '('){
			$ids = substr($ids,0,strlen($ids)-2);
			$ids .= ')';

			$criteria->condition .= 'id IN '.$ids;
			$criteria->order = 'dateDebut DESC';
		}else{
			$criteria->condition .= 'id = 0';
		}
		
		$dataNotice=new CActiveDataProvider('Notice');
		$dataNotice->setPagination(false);
		$dataNotice->setCriteria($criteria);
		
		/******* À FAIRE QUAND ON MIGRE*************
		$usagers = Usager::model()->findAll(array('condition'=>'tbl_equipe_id IS NOT NULL'));
		foreach($usagers as $usager){
			$equipe = new EquipeUsager;
			
			$equipe->tbl_equipe_id = $usager->tbl_equipe_id;
			$equipe->tbl_usager_id = $usager->id;
			$equipe->save();
		}*/
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'

		$casernesActifUsager = Caserne::model()->findAll(array('condition'=>'id IN ('.$casernes.') AND siActif = 1'));
		$casernesNbr = Caserne::model()->count(array('condition'=>'id IN ('.$casernes.') AND siActif = 1'));
		$tblCaserne = CHtml::listData($casernesActifUsager,'id','nom');	

		$nombreQuartAfficher = 2;
		$curseurALLForceFrappe = array();
		$ALLminimum = array();
		$dispo = array();
		$garde = Garde::model()->findByPk($parametres->garde_fdf);
		
		foreach($casernesActifUsager as $key => $caserneActifUsager)
		{
			if($caserneActifUsager->siGrandEcran==1){
				$sqlQuart = 
					'SELECT
						q.id AS QuartID,
						q.nom AS Quart,
						c.id AS CaserneID,
						c.nom AS Caserne,
						IFNULL(
							(
								SELECT h.dispo 
								FROM tbl_dispo_jour h
								WHERE 
									h.date = CURDATE()
									AND h.tbl_quart_id = q.id
									AND h.tbl_usager_id = '.Yii::app()->user->id.'
									AND h.tbl_caserne_id = '.$caserneActifUsager->id.'
								ORDER BY h.date DESC
								LIMIT 1
							),
							IF((SELECT defaut_fdf FROM tbl_parametres)=1,0,1)
						) AS Dispo,
						CURDATE() as DateQuart,
						e.Couleur
					FROM tbl_quart q
					LEFT JOIN tbl_dispo_jour dj ON dj.tbl_quart_id = q.id AND dj.tbl_usager_id = 1 AND dj.date = CURDATE()
					LEFT JOIN tbl_poste_horaire ph ON ph.tbl_quart_id = q.id
					LEFT JOIN tbl_poste_horaire_caserne phc ON phc.tbl_poste_horaire_id = ph.id
					LEFT JOIN tbl_caserne c ON c.id = phc.tbl_caserne_id
					LEFT JOIN tbl_equipe_garde eg ON eg.tbl_quart_id = q.id AND eg.tbl_caserne_id = '.$caserneActifUsager->id.' AND eg.modulo = '.(strtotime(date('Y-m-d'))/86400)%$garde->nbr_jour_periode.' AND eg.tbl_garde_id = '.$garde->id.'
					LEFT JOIN tbl_equipe e ON e.id  = eg.tbl_equipe_id,
					(
						SELECT DISTINCT q.*, 
							IF(q.heureDebut <= CURTIME(), 
								IF(q.heureFin > CURTIME(), 
									q.heureFin,
									IF(q.heureFin < q.heureDebut, 
										ADDTIME(q.heureFin,"24:00:00"), 
										IF(q.heureFin > CURTIME(), 
											q.heureFin,
											ADDTIME(q.heureFin,"24:00:00")
										)
									)
								),
								IF(q.heureFin > CURTIME(), 
									q.heureFin,
									IF(q.heureFin < q.heureDebut, 
										ADDTIME(q.heureFin,"24:00:00"), 
										q.heureFin
									)
								)
							) AS heureTri
						FROM tbl_poste_horaire_caserne phc 
						INNER JOIN tbl_poste_horaire ph ON ph.id=phc.tbl_poste_horaire_id 
						INNER JOIN tbl_quart q ON q.id=ph.tbl_quart_id
						WHERE phc.tbl_caserne_id = '.$caserneActifUsager->id.'
						ORDER BY heureTri
						LIMIT 2
					) AS valideQuart
					WHERE valideQuart.id = q.id AND phc.tbl_caserne_id = '.$caserneActifUsager->id.'
					GROUP BY q.id
					ORDER BY valideQuart.heureTri
				';
				$cnQuart = Yii::app()->db;		
				$cmQuart = $cnQuart->createCommand($sqlQuart);	
				$curseurQuart = $cmQuart->query();
				$curseurALLQuart[$caserneActifUsager->nom] = $curseurQuart;
			}

			$sql = 
			"	SELECT 
	CURDATE() AS DateQuart, 
	q.id AS QuartID, 
	q.nom AS Quart, 
	q.heureDebut, 
	eu.tbl_equipe_id AS Equipe, 
	e.couleur,
	e.nom AS NomEquipe,
	c.id AS CaserneID, 
	c.nom AS Caserne, 
	SUM(IFNULL(
			(SELECT 
				h.dispo 
			FROM 
				tbl_dispo_jour h 
			WHERE 
				h.date = DATE(CURDATE()) 
				AND h.tbl_quart_id = q.id 
				AND h.tbl_usager_id = u.id 
				AND h.tbl_caserne_id = ".$caserneActifUsager->id." 
			ORDER BY 
				h.date DESC LIMIT 1 
			), 
			IF(
				(SELECT 
					defaut_fdf 
				FROM tbl_parametres
				)=1,0,1)
			)) AS Dispo, 
	(IF(e.ordre < (
		SELECT DISTINCT tb1.ordre 
		FROM tbl_equipe_garde eg1 
		LEFT JOIN tbl_equipe tb1 ON tb1.id = tbl_equipe_id 
		WHERE 
			eg1.tbl_quart_id = q.id 
			AND eg1.tbl_caserne_id = ".$caserneActifUsager->id."
			AND eg1.modulo = ".(strtotime(date('Y-m-d'))/86400)%$garde->nbr_jour_periode."
			AND eg1.tbl_garde_id = ".$garde->id.")
		,(e.ordre + 100), e.ordre )) 
		AS Ordre,
	(IF(q.id = (SELECT q_first.id FROM tbl_quart q_first ORDER BY q_first.heureDebut ASC LIMIT 1), 
		IF(
			(SELECT 
				m.minimum 
			FROM tbl_minimum m 
			WHERE jourSemaine = (DAYOFWEEK(CURDATE())-1) 
			ORDER BY dateHeure DESC 
			LIMIT 1) 
				> 
			SUM(IFNULL(
				(SELECT 
					h.dispo 
				FROM 
					tbl_dispo_jour h 
				WHERE 
					h.date = DATE(CURDATE()) 
					AND h.tbl_quart_id = q.id 
					AND h.tbl_usager_id = u.id 
					AND h.tbl_caserne_id = 1 
				ORDER BY 
					h.date DESC LIMIT 1 
				), 
				IF(
					(SELECT 
						defaut_fdf 
					FROM tbl_parametres
					)=1,0,1
				)
			))
			,'true','false'), 
		IF(
			(SELECT 
					m.minimum 
				FROM tbl_minimum m 
				WHERE jourSemaine = (DAYOFWEEK(CURDATE())-1) 
				ORDER BY dateHeure DESC 
				LIMIT 1) 
					> 			
			(
				SUM(IFNULL(
					(SELECT 
						h.dispo 
					FROM 
						(tbl_quart q_actuel,
						tbl_dispo_jour h)
						INNER JOIN tbl_quart quarts ON quarts.id = h.tbl_quart_id AND quarts.heureDebut < q_actuel.heureDebut
					WHERE 
						h.date = DATE(CURDATE()) 
						AND h.tbl_usager_id = u.id 
						AND h.tbl_caserne_id = 1
						AND q_actuel.id = q.id
					ORDER BY 
						h.date DESC LIMIT 1 
					), 
					IF(
						(SELECT 
							defaut_fdf 
						FROM tbl_parametres
						)=1,0,1
					)
				))
				+
				SUM(IFNULL(
					(SELECT 
						h.dispo 
					FROM 
						tbl_dispo_jour h 
					WHERE 
						h.date = DATE(CURDATE()) 
						AND h.tbl_quart_id = q.id 
						AND h.tbl_usager_id = u.id 
						AND h.tbl_caserne_id = 1 
					ORDER BY 
						h.date DESC LIMIT 1 
					), 
					IF(
						(SELECT 
							defaut_fdf 
						FROM tbl_parametres
						)=1,0,1
					)
				))				
			),'true','false'
		))) AS Minimum
FROM 
	(numbers i1, numbers i2, numbers i3, numbers i4),
	tbl_quart q, 
	tbl_usager u 
	LEFT JOIN tbl_equipe_usager eu ON eu.tbl_usager_id = u.id 
	LEFT JOIN tbl_equipe e ON e.id = eu.tbl_equipe_id 
	LEFT JOIN tbl_caserne c ON c.id = e.tbl_caserne_id, 
	(SELECT 
		DISTINCT q.*, 
		IF(
			q.heureDebut <= CURTIME(), 
			IF(q.heureFin > CURTIME(), 
				q.heureFin, 
				IF(q.heureFin < q.heureDebut, 
					ADDTIME(q.heureFin,'24:00:00'), 
					IF(q.heureFin > CURTIME(), 
						q.heureFin, ADDTIME(q.heureFin,'24:00:00')
					)
				)
			), 
			IF(q.heureFin > CURTIME(), q.heureFin, IF(q.heureFin < q.heureDebut, ADDTIME(q.heureFin,'24:00:00'), q.heureFin ))
		) AS heureTri 
	FROM 
		tbl_poste_horaire_caserne phc 
		INNER JOIN tbl_poste_horaire ph ON ph.id=phc.tbl_poste_horaire_id 
		INNER JOIN tbl_quart q ON q.id=ph.tbl_quart_id 
	WHERE phc.tbl_caserne_id = ".$caserneActifUsager->id."
	ORDER BY heureTri 
	LIMIT 2 ) AS valideQuart 
WHERE 
	(ADDDATE(CURDATE(), i4.i*1000+i3.i*100+i2.i*10+i1.i) <= ADDDATE(CURDATE(), INTERVAL 0 DAY)) 
	AND e.siFDF = 1 
	AND e.tbl_caserne_id = ".$caserneActifUsager->id."
	AND u.actif = 1 
	AND u.enService = 1 
	AND valideQuart.id = q.id 
GROUP BY DateQuart, q.id, eu.tbl_equipe_id 
ORDER BY valideQuart.heureTri, DateQuart, q.heureDebut, ordre
			";
			$cn = Yii::app()->db;		
			$cm = $cn->createCommand($sql);
			$curseurForceFrappe = $cm->query();
			$curseurALLForceFrappe[$caserneActifUsager->nom] = $curseurForceFrappe;
			
			$minimum = Minimum::model()->findBySql(
					'SELECT (SELECT minimum FROM tbl_minimum WHERE jourSemaine = 0 AND tbl_caserne_id = '.$caserneActifUsager->id.' ORDER BY dateHeure DESC LIMIT 0,1) as dimancheMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 0 AND tbl_caserne_id = '.$caserneActifUsager->id.' ORDER BY dateHeure DESC LIMIT 0,1) as dimancheNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 1 AND tbl_caserne_id = '.$caserneActifUsager->id.' ORDER BY dateHeure DESC LIMIT 0,1) as lundiMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 1 AND tbl_caserne_id = '.$caserneActifUsager->id.' ORDER BY dateHeure DESC LIMIT 0,1) as lundiNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 2 AND tbl_caserne_id = '.$caserneActifUsager->id.' ORDER BY dateHeure DESC LIMIT 0,1) as mardiMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 2 AND tbl_caserne_id = '.$caserneActifUsager->id.' ORDER BY dateHeure DESC LIMIT 0,1) as mardiNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 3 AND tbl_caserne_id = '.$caserneActifUsager->id.' ORDER BY dateHeure DESC LIMIT 0,1) as mercrediMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 3 AND tbl_caserne_id = '.$caserneActifUsager->id.' ORDER BY dateHeure DESC LIMIT 0,1) as mercrediNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 4 AND tbl_caserne_id = '.$caserneActifUsager->id.' ORDER BY dateHeure DESC LIMIT 0,1) as jeudiMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 4 AND tbl_caserne_id = '.$caserneActifUsager->id.' ORDER BY dateHeure DESC LIMIT 0,1) as jeudiNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 5 AND tbl_caserne_id = '.$caserneActifUsager->id.' ORDER BY dateHeure DESC LIMIT 0,1) as vendrediMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 5 AND tbl_caserne_id = '.$caserneActifUsager->id.' ORDER BY dateHeure DESC LIMIT 0,1) as vendrediNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 6 AND tbl_caserne_id = '.$caserneActifUsager->id.' ORDER BY dateHeure DESC LIMIT 0,1) as samediMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 6 AND tbl_caserne_id = '.$caserneActifUsager->id.' ORDER BY dateHeure DESC LIMIT 0,1) as samediNiv 
					FROM `tbl_minimum` LIMIT 0,1');
			$tblMinimum = array();
			$tblMinimum[0]['minimum'] = $minimum->dimancheMin;
			$tblMinimum[0]['niveau'] = $minimum->dimancheNiv;
			$tblMinimum[1]['minimum'] = $minimum->lundiMin;
			$tblMinimum[1]['niveau'] = $minimum->lundiNiv;
			$tblMinimum[2]['minimum'] = $minimum->mardiMin;
			$tblMinimum[2]['niveau'] = $minimum->mardiNiv;
			$tblMinimum[3]['minimum'] = $minimum->mercrediMin;
			$tblMinimum[3]['niveau'] = $minimum->mercrediNiv;
			$tblMinimum[4]['minimum'] = $minimum->jeudiMin;
			$tblMinimum[4]['niveau'] = $minimum->jeudiNiv;
			$tblMinimum[5]['minimum'] = $minimum->vendrediMin;
			$tblMinimum[5]['niveau'] = $minimum->vendrediNiv;
			$tblMinimum[6]['minimum'] = $minimum->samediMin;
			$tblMinimum[6]['niveau'] = $minimum->samediNiv;

			$today = new DateTime('NOW', new DateTimeZone($parametres->timezone));
			for ($i=0; $i < 7; $i++) 
			{ 
				$testMinimum = $tblMinimum[$today->format("w")]['minimum'];
				$dateRequete = $today->format('Y-m-d');
				$exception = MinimumException::model()->find('dateDebut <= "'.$dateRequete.'" AND dateFin >= "'.$dateRequete.'" AND tbl_caserne_id = '.$caserneActifUsager->id, array('limit'=>1));
				if(count($exception) == 1){
					$tblMinimum[$today->format("w")]['minimum'] = $exception->minimum;
				}
				$today->add(new DateInterval('P1D'));
			}
			$ALLminimum[$caserneActifUsager->nom] = $tblMinimum;
		}
		$criteriaHoraireMobile = new CDbCriteria;
		$criteriaHoraireMobile->condition = 'tbl_usager_id ='.$usager->id.' AND date >= "'.date('Y-m-d').'" AND id NOT IN (SELECT parent_id FROM tbl_horaire WHERE parent_id = t.id)';
		$criteriaHoraireMobile->order = 'date';
		$criteriaHoraireMobile->limit = 3;
		$horaireMobile = Horaire::model()->findAll($criteriaHoraireMobile);

		$this->render('index',array(
			/*'curseurHoraire'=>$curseurHoraire,*/ 
			'parametres' =>$parametres,
			'dataDocument'=>$dataDocument,
			'dataNotice'=>$dataNotice,
			'casernesNbr' =>$casernesNbr,
			'curseurALLForceFrappe' => $curseurALLForceFrappe,
			'ALLminimum' => $ALLminimum,
			'curseurALLQuart' =>$curseurALLQuart,
			'horaireMobile' =>$horaireMobile,
		));
	}

	public function actionChangeMobileStatut($sendQuart, $caserne, $actif, $dateDemande)
	{
		if(Yii::app()->user->checkAccess('DispoFDF:index'))
		{
			$caserneActif = Caserne::model()->findAll(array('condition'=>'id IN ('.$caserne.') AND siActif = 1'));
			$quartInfo = Quart::model()->findByPk($sendQuart);
			$param = Parametres::model()->findByPk(1);
			$dateActuel = new DateTime('now', new DateTimeZone($param->timezone));
			$dateDemande = new DateTime(str_replace("'", "", $dateDemande), new DateTimeZone($param->timezone));
			$heureFin = new DateTime($quartInfo->heureFin, new DateTimeZone($param->timezone));
			if($quartInfo->heureDebut > $quartInfo->heureFin)
			{
				$heureFin->add(new DateInterval('P1D'));
			}
			$erreur = "";
			if($dateDemande < $heureFin)
			{
				$criteriaDispo = new CDbCriteria;
				$criteriaDispo->condition = '
					tbl_usager_id ='.Yii::app()->user->id.' 
					AND tbl_caserne_id ='.$caserne.' 
					AND date ="'.$dateDemande->format('Y-m-d').'" 
					AND tbl_quart_id ='.$sendQuart;
				$actualDispo = DispoFDF::model()->find($criteriaDispo);
				if($actualDispo === NUll)
				{
					if($param->defaut_fdf)
						$nouvelleDispo = 1;
					else
						$nouvelleDispo = 0;
					$dispo = new DispoFDF;
					$dispo->tbl_quart_id = $sendQuart;
					$dispo->date = $dateDemande->format('Y-m-d');
					$dispo->tbl_usager_id = Yii::app()->user->id;
					$dispo->heureDebut = $quartInfo->heureDebut;
					$dispo->heureFin = $quartInfo->heureFin;
					$dispo->tbl_usager_action = Yii::app()->user->id;
					$dispo->tbl_caserne_id = $caserne;
					$dispo->dispo = $nouvelleDispo;
					$dispo->dateDecoche = $dateActuel->format('Y-m-d H:i:s');
					$dispo->save();
				}
				else
				{
					if($actualDispo->dispo)
						$nouvelleDispo = 0;
					else
						$nouvelleDispo = 1;
					if($actif != $nouvelleDispo)
					{
						$actualDispo->dispo = $nouvelleDispo;
						$actualDispo->heureDebut = $quartInfo->heureDebut;
						$actualDispo->heureFin = $quartInfo->heureFin;
						$actualDispo->dateDecoche = $dateActuel->format('Y-m-d H:i:s');
						$actualDispo->save();
					}
					else
					{
						if($nouvelleDispo == 0)
						{
							$erreur = "Vous êtes déjà actif";
						}
						else
						{
							$erreur = "Vous êtes déjà inactif";
						}
					}
				}
			}
			else
			{
				$erreur = "Vous ne pouvez changer ce quart de travail. \n Veuillez rafraîchir la page.";
			}
			if($erreur == "")
			{
				$histo = new HistoFDF;
				$histo->tbl_quart_id = $sendQuart;
				$histo->date = date('Y-m-d');
				$histo->tbl_usager_id = Yii::app()->user->id;
				$histo->tbl_caserne_id = $caserne;
				$histo->action = $nouvelleDispo;
				$histo->dateAction = $dateActuel->format('Y-m-d H:i:s');
				$histo->usager_action = Yii::app()->user->id;
				$histo->heureDebut = $quartInfo->heureDebut;
				$histo->heureFin = $quartInfo->heureFin;
				$histo->save();		
				echo $nouvelleDispo;
			}
			else
			{
				echo $erreur;
			}
		}
	}

	public function actualQuart($casernes)
	{
		$actualsQuart = array();
		foreach ($casernes as $caserne) {
			$criteriaQuart = new CDbCriteria;
			$criteriaQuart->condition = 'heureDebut <= "'.date('H:i:s').'"
				AND IF (heureDebut < heureFin, 
						CONCAT("'.date('Y-m-d').'"," ", heureFin), 
						CONCAT(ADDDATE("'.date('Y-m-d').'", INTERVAL 1 DAY)," ", heureFin)
					) > "'.date('Y-m-d H:i:s').'"
				AND id IN (
					SELECT tbl_quart_id
					FROM tbl_poste_horaire horaire INNER JOIN tbl_poste_horaire_caserne caserne ON horaire.id = tbl_poste_horaire_id
					WHERE tbl_caserne_id = '.$caserne->id.'
				)'
			;
			$actualsQuart[$caserne->id] = Quart::model()->find($criteriaQuart);
		}
		return $actualsQuart;
	}
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}
	
	public function actionGrandEcran($caserne=""){
		$parametres = Parametres::model()->findByPk(1);
		$this->layout = 'gE';
		/*

		$tblQuart = Quart::model()->findAll(array('condition'=>'tbl_caserne_id = '.$caserne, 'order'=>'heureDebut'));
		$tblPosteHoraire = array();
		foreach($tblQuart as $quart){
			$tblPosteHoraire[$quart->nom] = $quart->posteHoraires; 
		}
		$listeEquipeGarde = EquipeGarde::model()->findAll();
		$tblEquipeGarde = array();
		foreach($listeEquipeGarde as $garde){
			$tblEquipeGarde[$garde->modulo][$garde->tbl_quart_id] = Equipe::model()->findByPk($garde->tbl_equipe_id);
		}

		$tblEquipeSP = Groupe::model()->findAll('tbl_caserne_id = :caserne AND siActif = 1', array(':caserne'=>$caserne));
		
		//Pour générer la liste des pompiers dispo
		Yii::app()->clientScript->registerScript('listePompier',
			'$(".listePompier").live("click",function(){'.
			CHtml::ajax(array(
				'update'=>'#listePompier',
				'type'=>'GET',
				'url'=>array('/dispoFDF/listePompier'),
				'cache'=>'false',
				'data'=>"js:{date:$(this).attr('date'),quart:$(this).attr('quart'),equipe:$(this).attr('equipe'),caserne:".$caserne."}",
			)).'});
			
			function updateHauteurTag(){
				$("#tagLigne").height($("#grilleData").height()+1*1);
			}
	
			updateHauteurTag();

			'	
		);
		*/
		


		$casernes = Caserne::model()->findAll('siActif = 1');
		if($caserne == ""){
			$caserne = $casernes[0]->id;
		}
		if($parametres->affichage_fdf == 0){
			$listeEquipe = new CActiveDataProvider('Equipe',array('criteria'=>array('condition'=>'siHoraire=1 AND siActif = 1 AND tbl_caserne_id = :caserne','params'=>array(':caserne'=>$caserne))));
		}elseif($parametres->affichage_fdf == 1){
			$caserneID = array();
			foreach($casernes as $cas){
				$caserneID[] = $cas->id;
			}
			$criteriaEquipe = new CDbCriteria;
			$criteriaEquipe->condition = 'siHoraire=1 AND siActif = 1';
			$criteriaEquipe->addInCondition('tbl_caserne_id',$caserneID);
			$criteriaEquipe->order = 'tbl_caserne_id ASC, nom ASC';
			$listeEquipe = new CActiveDataProvider('Equipe',array('criteria'=>$criteriaEquipe));
		}
		//Pour générer la liste des pompiers dispo
		Yii::app()->clientScript->registerScript('listePompier',
			'$(".listePompier").live("click",function(){'.
			CHtml::ajax(array(
				'update'=>'#listePompier',
				'type'=>'GET',
				'url'=>array('/dispoFDF/listePompier'),
				'cache'=>'false',
				'data'=>"js:{date:$(this).attr('date'),quart:$(this).attr('quart'),equipe:$(this).attr('equipe')}",
			)).'});'
		
		);
		
		Yii::app()->clientScript->registerScript('listePompierSP',
			'$(".listePompierSP").live("click",function(){'.
			CHtml::ajax(array(
				'update'=>'#listePompier',
				'type'=>'GET',
				'url'=>array('/dispoFDF/listePompierSP'),
				'cache'=>'false',
				'data'=>"js:{date:$(this).attr('date'),quart:$(this).attr('quart'),groupe:$(this).attr('groupe'),caserne:".$caserne."}",
			)).'});'
				
		);
		$listePoste = new CActiveDataProvider('Poste');
		$this->render('grandEcran',array(
			'caserne'=>$caserne,
			'listePoste'=>$listePoste,
			'listeEquipe'=>$listeEquipe,
			)
		);
	}
	
	public function actionAjaxHoraire($caserne="",$jours=7){
		$parametres = Parametres::model()->findByPk(1);
		$garde = Garde::model()->findByPk($parametres->garde_horaire);
		
		$jourSemaine = array('Dim','Lun','Mar','Mer','Jeu','Ven','Sam');
		$date = date("Ymd");
		if($parametres->grandEcran_horaire_dateDebut == 0){
			$dateDebut = new DateTime($date."T00:00:00",new DateTimeZone($parametres->timezone));
			$dateDebutSuivante = date_add(clone $dateDebut,new DateInterval("P".$jours."D"));
		}elseif($parametres->grandEcran_horaire_dateDebut == 1){
			$dateDebut = Horaire::debutPeriode($garde->nbr_jour_affiche,$parametres->moduloDebut,$date);
			$dateDebutSuivante = date_add(clone $dateDebut,new DateInterval("P".$jours."D"));
		}
		$criteria = new CDbCriteria;
		$criteria->condition = 'siActif = 1';
		$criteria->order = 'id ASC';
		$casernes = Caserne::model()->findAll('siActif = 1');
		if($caserne == ""){
			foreach($casernes as $c){
				$caserne = $c->id;
				break;
			}
		}
		
		$sql = "SELECT
	ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) AS Jour,
	q.nom AS nomQuart, q.id AS idQuart, q.heureDebut AS qHeureDebut, q.heureFin AS qHeureFin,
	p.nom AS Poste, p.id AS idPoste, p.diminutif AS diminutifPoste,
	ph.id AS phId, ph.heureDebut AS phHeureDebut, ph.heureFin AS phHeureFin,
	u.matricule AS Matricule_horaire, e.couleur AS couleur_garde, u2.matricule AS matricule_modification, h.type AS typeH,
	h.heureDebut AS hHeureDebut, h.heureFin AS hHeureFin, h.id  AS ID_Horaire
FROM
	((numbers i1, numbers i2),
	 (tbl_quart q LEFT JOIN (tbl_poste_horaire ph INNER JOIN tbl_poste p ON p.id=ph.tbl_poste_id) ON ph.tbl_quart_id=q.id))
 	  LEFT JOIN tbl_horaire h ON h.tbl_poste_horaire_id=ph.id AND h.date=ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) AND h.type IN (0,2) AND ((h.heureDebut = '00:00:00' AND h.heureFin = '00:00:00') OR h.heureDebut <> h.heureFin)
 	  		LEFT JOIN tbl_usager u ON u.id=h.tbl_usager_id
 	  LEFT JOIN tbl_horaire h2 ON h2.parent_id = h.id AND h2.dateModif=(SELECT MAX(m.dateModif) FROM tbl_horaire m WHERE m.parent_id=h.id) AND h2.type = 1 
 	  		LEFT JOIN tbl_usager u2 ON u2.id=h2.tbl_usager_id
 		LEFT JOIN tbl_equipe_garde eg ON q.id=eg.tbl_quart_id AND eg.modulo=MOD((UNIX_TIMESTAMP(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i)) DIV 86400),".$garde->nbr_jour_periode.") AND eg.tbl_garde_id = ".$parametres->garde_horaire."
	  LEFT JOIN tbl_equipe e on e.id=eg.tbl_equipe_id
	  INNER JOIN tbl_poste_horaire_caserne phc ON phc.tbl_poste_horaire_id = ph.id
WHERE
	(ADDDATE('".$dateDebut->format('Y-m-d')."', i2.i*10+i1.i) < '".$dateDebutSuivante->format('Y-m-d')."')
	AND phc.tbl_caserne_id = :caserne
	AND (ph.dateFin >= '".$dateDebutSuivante->format('Y-m-d')."' OR ph.dateFin IS NULL)
GROUP BY Poste, nomQuart, Jour
ORDER BY q.heureDebut, p.id, Jour, h.heureDebut, ph.heureDebut";
		
		$cn = Yii::app()->db;
		$cm = $cn->createCommand($sql);
		$cm->bindParam(':caserne',$caserne);
		$curseurHoraire = $cm->query();
		
		$nbrQuarts = Quart::model()->count();
		
		if($parametres->grandEcran_nbr_periode_horaire == 2){
			$dateDebut2 = clone $dateDebutSuivante;
			$dateDebutSuivante2 = date_add(clone $dateDebut2,new DateInterval("P".$garde->nbr_jour_affiche."D"));
			$criteria = new CDbCriteria;
			$criteria->condition = 'siActif = 1';
			$criteria->order = 'id ASC';
			
			$sql2 = "SELECT
	ADDDATE('".$dateDebut2->format('Y-m-d')."', i2.i*10+i1.i) AS Jour,
	q.nom AS nomQuart, q.id AS idQuart, q.heureDebut AS qHeureDebut, q.heureFin AS qHeureFin,
	p.nom AS Poste, p.id AS idPoste, p.diminutif AS diminutifPoste,
	ph.id AS phId, ph.heureDebut AS phHeureDebut, ph.heureFin AS phHeureFin,
	u.matricule AS Matricule_horaire, e.couleur AS couleur_garde, u2.matricule AS matricule_modification, h.type AS typeH,
	h.heureDebut AS hHeureDebut, h.heureFin AS hHeureFin, h.id  AS ID_Horaire
FROM
	((numbers i1, numbers i2),
	 (tbl_quart q LEFT JOIN (tbl_poste_horaire ph INNER JOIN tbl_poste p ON p.id=ph.tbl_poste_id) ON ph.tbl_quart_id=q.id))
 	  LEFT JOIN tbl_horaire h ON h.tbl_poste_horaire_id=ph.id AND h.date=ADDDATE('".$dateDebut2->format('Y-m-d')."', i2.i*10+i1.i) AND h.type IN (0,2) 
 	  		LEFT JOIN tbl_usager u ON u.id=h.tbl_usager_id
 	  LEFT JOIN tbl_horaire h2 ON h2.parent_id = h.id AND h2.dateModif=(SELECT MAX(m.dateModif) FROM tbl_horaire m WHERE m.parent_id=h.id) AND h2.type = 1 
 	  		LEFT JOIN tbl_usager u2 ON u2.id=h2.tbl_usager_id
	  LEFT JOIN tbl_equipe_garde eg ON q.id=eg.tbl_quart_id AND eg.modulo=MOD((UNIX_TIMESTAMP(ADDDATE('".$dateDebut2->format('Y-m-d')."', i2.i*10+i1.i)) DIV 86400),
	  		".$garde->nbr_jour_periode.")
	  LEFT JOIN tbl_equipe e on e.id=eg.tbl_equipe_id
	  INNER JOIN tbl_poste_horaire_caserne phc ON phc.tbl_poste_horaire_id = ph.id
WHERE
	(ADDDATE('".$dateDebut2->format('Y-m-d')."', i2.i*10+i1.i) < '".$dateDebutSuivante2->format('Y-m-d')."')
	AND phc.tbl_caserne_id = :caserne
	AND (ph.dateFin >= '".$dateDebutSuivante2->format('Y-m-d')."' OR ph.dateFin IS NULL)
GROUP BY Poste, nomQuart, Jour
ORDER BY q.heureDebut, p.id, Jour, h.heureDebut, ph.heureDebut";
			
			$cn = Yii::app()->db;
			$cm = $cn->createCommand($sql2);
			$cm->bindParam(':caserne',$caserne);
			$curseurHoraire2 = $cm->query();			
		}else{
			$curseurHoraire2 = NULL;
		}
		
		$this->renderPartial('_geHoraire',array(
			'curseurHoraire'=>$curseurHoraire,
			'curseurHoraire2'=>$curseurHoraire2,
			'jourSemaine'=>$jourSemaine,
			'nbrQuarts'=>$nbrQuarts,
			'parametres'=>$parametres,
		)
		);
	}
	
	public function actionAjaxFDF($caserne=""){
		if($caserne == ""){
			foreach($casernes as $c){
				$caserne = $c->id;
				break;
			}
		}
		$parametres = Parametres::model()->findByPk(1);
		$dateDebut = new DateTime(date("Ymd")."T00:00:00",new DateTimeZone($parametres->timezone));
		$jourSemaine = array('Dim','Lun','Mar','Mer','Jeu','Ven','Sam');
		$tblPompier = DispoFDF::getDispoJour(array('dateDebut'=>clone $dateDebut,'nombreJour'=>3),"",'',true, $caserne, false, true);
		$tblPompierGroupe = DispoFDF::getDispoJour(array('dateDebut'=>clone $dateDebut,'nombreJour'=>3),"",'',false, $caserne, false);
		$equipes = Equipe::model()->findAll('siActif = 1 AND tbl_caserne_id = '.$caserne);
		$ids = '';
		foreach($equipes as $equipe){
			$ids .= $equipe->id.', ';
		}
		$ids = substr($ids,0,strlen($ids)-2);
		$criteria = new CDbCriteria;
		$criteria->condition = 'tbl_equipe_id IN ('.$ids.')';
		$Garde = EquipeGarde::model()->findAll($criteria);
		
		$tblGarde = array();
		$tblMinimum = array();
		$caserneId = array();
		if($parametres->affichage_fdf==0){
			$criteriaGarde = new CDbCriteria;
			$criteriaGarde->condition = 'tbl_caserne_id = :caserne AND tbl_garde_id = :garde';
			$criteriaGarde->params = array(':caserne'=>$caserne, ':garde'=>$parametres->garde_fdf);
			$Garde = EquipeGarde::model()->findAll($criteriaGarde);
			
			foreach($Garde as $equipe){
				$tblGarde[0][$equipe->modulo.$equipe->tbl_quart_id] = $equipe->tblEquipe->couleur;
			}
			
			$minimum = Minimum::model()->findBySql(
					'SELECT (SELECT minimum FROM tbl_minimum WHERE jourSemaine = 0 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as dimancheMin,
							(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 0 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as dimancheNiv,
							(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 1 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as lundiMin,
							(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 1 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as lundiNiv,
							(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 2 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as mardiMin,
							(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 2 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as mardiNiv,
							(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 3 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as mercrediMin,
							(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 3 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as mercrediNiv,
							(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 4 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as jeudiMin,
							(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 4 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as jeudiNiv,
							(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 5 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as vendrediMin,
							(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 5 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as vendrediNiv,
							(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 6 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as samediMin,
							(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 6 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as samediNiv
							FROM `tbl_minimum` LIMIT 0,1');
			
			$tblMinimum[$caserne][0]['minimum'] = $minimum->dimancheMin;
			$tblMinimum[$caserne][0]['niveau'] = $minimum->dimancheNiv;
			$tblMinimum[$caserne][1]['minimum'] = $minimum->lundiMin;
			$tblMinimum[$caserne][1]['niveau'] = $minimum->lundiNiv;
			$tblMinimum[$caserne][2]['minimum'] = $minimum->mardiMin;
			$tblMinimum[$caserne][2]['niveau'] = $minimum->mardiNiv;
			$tblMinimum[$caserne][3]['minimum'] = $minimum->mercrediMin;
			$tblMinimum[$caserne][3]['niveau'] = $minimum->mercrediNiv;
			$tblMinimum[$caserne][4]['minimum'] = $minimum->jeudiMin;
			$tblMinimum[$caserne][4]['niveau'] = $minimum->jeudiNiv;
			$tblMinimum[$caserne][5]['minimum'] = $minimum->vendrediMin;
			$tblMinimum[$caserne][5]['niveau'] = $minimum->vendrediNiv;
			$tblMinimum[$caserne][6]['minimum'] = $minimum->samediMin;
			$tblMinimum[$caserne][6]['niveau'] = $minimum->samediNiv;
			
			$caserneId[] = $caserne;
		}elseif($parametres->affichage_fdf==1){
			$critereCaserne = new CDbCriteria;
			$critereCaserne->condition = 'siActif = 1 AND siGrandEcran = 1';
			$critereCaserne->order = 'nom ASC';
			$casernes = Caserne::model()->findAll($critereCaserne);
			$i = 0;
			
			$casernesID = array();
			foreach($casernes as $cas){
				$criteriaGarde = new CDbCriteria;
				$criteriaGarde->condition = 'tbl_caserne_id = :caserne AND tbl_garde_id = :garde';
				$criteriaGarde->params = array(':caserne'=>$cas->id, ':garde'=>$parametres->garde_fdf);
				$Garde = EquipeGarde::model()->findAll($criteriaGarde);
				$casernesID[] = $cas->id;
				foreach($Garde as $equipe){
					$tblGarde[$i][$equipe->modulo.$equipe->tbl_quart_id] = $equipe->tblEquipe->couleur;
				}
				$i++;
				$minimum = Minimum::model()->findBySql(
						'SELECT (SELECT minimum FROM tbl_minimum WHERE jourSemaine = 0 AND tbl_caserne_id = '.$cas->id.' ORDER BY dateHeure DESC LIMIT 0,1) as dimancheMin,
							(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 0 AND tbl_caserne_id = '.$cas->id.' ORDER BY dateHeure DESC LIMIT 0,1) as dimancheNiv,
							(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 1 AND tbl_caserne_id = '.$cas->id.' ORDER BY dateHeure DESC LIMIT 0,1) as lundiMin,
							(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 1 AND tbl_caserne_id = '.$cas->id.' ORDER BY dateHeure DESC LIMIT 0,1) as lundiNiv,
							(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 2 AND tbl_caserne_id = '.$cas->id.' ORDER BY dateHeure DESC LIMIT 0,1) as mardiMin,
							(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 2 AND tbl_caserne_id = '.$cas->id.' ORDER BY dateHeure DESC LIMIT 0,1) as mardiNiv,
							(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 3 AND tbl_caserne_id = '.$cas->id.' ORDER BY dateHeure DESC LIMIT 0,1) as mercrediMin,
							(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 3 AND tbl_caserne_id = '.$cas->id.' ORDER BY dateHeure DESC LIMIT 0,1) as mercrediNiv,
							(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 4 AND tbl_caserne_id = '.$cas->id.' ORDER BY dateHeure DESC LIMIT 0,1) as jeudiMin,
							(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 4 AND tbl_caserne_id = '.$cas->id.' ORDER BY dateHeure DESC LIMIT 0,1) as jeudiNiv,
							(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 5 AND tbl_caserne_id = '.$cas->id.' ORDER BY dateHeure DESC LIMIT 0,1) as vendrediMin,
							(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 5 AND tbl_caserne_id = '.$cas->id.' ORDER BY dateHeure DESC LIMIT 0,1) as vendrediNiv,
							(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 6 AND tbl_caserne_id = '.$cas->id.' ORDER BY dateHeure DESC LIMIT 0,1) as samediMin,
							(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 6 AND tbl_caserne_id = '.$cas->id.' ORDER BY dateHeure DESC LIMIT 0,1) as samediNiv
							FROM `tbl_minimum` LIMIT 0,1');
				
				$tblMinimum[$cas->id][0]['minimum'] = $minimum->dimancheMin;
				$tblMinimum[$cas->id][0]['niveau'] = $minimum->dimancheNiv;
				$tblMinimum[$cas->id][1]['minimum'] = $minimum->lundiMin;
				$tblMinimum[$cas->id][1]['niveau'] = $minimum->lundiNiv;
				$tblMinimum[$cas->id][2]['minimum'] = $minimum->mardiMin;
				$tblMinimum[$cas->id][2]['niveau'] = $minimum->mardiNiv;
				$tblMinimum[$cas->id][3]['minimum'] = $minimum->mercrediMin;
				$tblMinimum[$cas->id][3]['niveau'] = $minimum->mercrediNiv;
				$tblMinimum[$cas->id][4]['minimum'] = $minimum->jeudiMin;
				$tblMinimum[$cas->id][4]['niveau'] = $minimum->jeudiNiv;
				$tblMinimum[$cas->id][5]['minimum'] = $minimum->vendrediMin;
				$tblMinimum[$cas->id][5]['niveau'] = $minimum->vendrediNiv;
				$tblMinimum[$cas->id][6]['minimum'] = $minimum->samediMin;
				$tblMinimum[$cas->id][6]['niveau'] = $minimum->samediNiv;
					
				$caserneId[] = $cas->id;
			}
		}
		$criteria = new CDbCriteria;
		$criteria->condition = 'tbl_caserne_id = :caserne';
		$criteria->params = array(':caserne'=>$caserne);
		$posteHC = PosteHoraireCaserne::model()->findAll($criteria);
		$posteId = array();
		foreach($posteHC as $postehc){
			if(!in_array($postehc->tbl_poste_horaire_id,$posteId)){
				$posteId[]=$postehc->tbl_poste_horaire_id;
			}
		}
		$criteria = new CDbCriteria;
		$criteria->addInCondition('id',$posteId);
		$postesH = PosteHoraire::model()->findAll($criteria);
		$quartId = array();
		foreach($postesH as $postes){
			if(!in_array('$postes->tbl_quart_id', $quartId)){
				$quartId[] = $postes->tbl_quart_id;
			}
		}
		$criteria = new CDbCriteria;
		$criteria->addInCondition('id',$quartId);
		
		$Nbrquarts = Quart::model()->count($criteria);

		$critereEquipe = new CDbCriteria;
		$critereEquipe->condition = "siFDF=1 AND siActif=1 AND tbl_caserne_id = ".$caserne;
		
		if($parametres->fdf_equipe_spe==0){			
			$criteriaGroupe = new CDbCriteria;
			$criteriaGroupe->alias = 't';
			$criteriaGroupe->join = 'LEFT JOIN tbl_caserne c ON c.id = t.tbl_caserne_id';
			$criteriaGroupe->condition = 'c.siActif = 1';
			$criteriaGroupe->order = 'c.nom ASC';
			$tblEquipeSP = Groupe::model()->findAll($criteriaGroupe);
		}else{
			$tblEquipeSP = Groupe::model()->findAll('tbl_caserne_id = '.$caserne);
		}
		
		$garde = Garde::model()->findByPk($parametres->garde_fdf);
		$nbrEquipe = array();
		$caserneNom = array();
		$critereEquipe = new CDbCriteria;
		if($parametres->affichage_fdf == 0){
			$critereEquipe->condition = "siFDF=1 AND siActif=1 AND tbl_caserne_id = ".$caserne;
			$nbrEquipe[] = Equipe::model()->count($critereEquipe);
		}elseif($parametres->affichage_fdf == 1){
			$casID = array();
			foreach($casernes as $cas){
				$caserneNom[] = $cas->nom; 
				$critereEquipe->condition = "siFDF =1 AND siActif = 1 AND tbl_caserne_id = ".$cas->id;
				$nbrEquipe[] = Equipe::model()->count($critereEquipe);
			}			
		}
		$this->renderPartial('_geFDF',array(
				'parametres'=>$parametres,
				'tblPompier'=>$tblPompier,
				'tblPompierGroupe'=>$tblPompierGroupe,
				'jourSemaine'=>$jourSemaine,
				'tblGarde'=>$tblGarde,
				'Nbrquarts'=>$Nbrquarts,
				'tblMinimum'=>$tblMinimum,
				'nbrEquipe'=>$nbrEquipe,
				'tblEquipeSP'=>$tblEquipeSP,
				'garde'=>$garde,
				'caserneNom'=>$caserneNom,
				'caserneId'=>$caserneId,
		)
		);
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		//Redirige les user authentifié à la page de base
		if(!Yii::app()->user->isGuest)
		{
			$this->redirect(Yii::app()->homeUrl);
		}
		
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	public function actionInstall(){
		if(isset(Yii::app()->params['install']) && Yii::app()->params['install']==1) {
		$connection = Yii::app()->db;				
		$grade = new Grade;
		$grade->nom = "Administrateurs";
		if($grade->save()) 
		{
			$grade->refresh();
			echo "Grade créer.<br/>";
		}else{
			echo "Échec #2 dans la procédure. recommencer.<br/>";
		}
		
		$usager = new Usager;
		$usager->id = 1;
		$usager->nom = ".";
		$usager->prenom = "Administrateur";
		$usager->matricule = "999";
		$usager->pseudo = "admin";
		$usager->motdepasse = md5("sword2012");
		$usager->courriel = "info@swordware.com";
		$usager->tbl_grade_id = $grade->id;
		if($usager->save())
		{
			$usager->refresh();
			Yii::app()->authManager->assign("SuperAdmin",$usager->id);
				echo "Usager créer avec succès.<br/>";
		}else{
			echo "Échec de l'installation de l'usager.<br/>";
		}
		
		$caserne = new Caserne;
		$caserne->id = 1;
		$caserne->nom = 'À changer';
		$caserne->siActif = 1;
		$caserne->siGrandEcran = 1;
		if($caserne->save())
		{
			$caserne->refresh();
			echo "Caserne créer avec succès. <br/>";
		}else{
			echo "Échec de l'installation de la caserne.<br/>";
		}
		
		$equipe = new Equipe;
		$equipe->id = 1;
		$equipe->nom = '100';
		$equipe->couleur = 'ffffff';
		$equipe->siHoraire = 1;
		$equipe->siFDF = 1;
		$equipe->siActif = 1;
		$equipe->tbl_caserne_id = 1;
		if($equipe->save())
		{
			$equipe->refresh();
			$equipeUsager = new EquipeUsager;
			$equipeUsager->tbl_equipe_id = 1;
			$equipeUsager->tbl_usager_id = 1;
			if($equipeUsager->save())
			{
				$equipeUsager->refresh();
				echo "Equipe créer avec succès. <br/>";
			}else{
				echo "Échec de l'installation de l'équipe. Fin de l'installation.<br/>";
			}
		}else{
			echo "Échec de l'installation de l'équipe.<br/>";
		}
		
		$garde = new Garde;
		$garde->id = 1;
		$garde->nom = 'Garde';
		$garde->nbr_jour = 14;
		$garde->date_debut = "2011-12-25";
		$garde->nbr_jour_affiche = 14;
		$garde->nbr_jour_periode = 14;
		$garde->nbr_jour_depot = 14;
		if($garde->save()){
			echo "Garde créer avec succès. <br/>";
		}else{
			echo "Échec de l'installation de la garde.<br/>";
		}
	}//fin if install	
	}// fin actionInstall   
	
	public function actionDemo(){
		echo 'Modifications en cours.<br/>';
		$usagers = Usager::model()->findAll(array('condition'=>'id NOT IN (13, 14, 16)'));
		$nom = array('Côté', 'Lemieux', 'Morissette');
		foreach($usagers as $usager){
			$i = rand(0,2);
			$usager->nom = $nom[$i];
			$usager->telephone1 = '418-863-8284';
			$usager->telephone2 = '';
			$usager->courriel = 'info@swordware.com';
			$usager->adresseCivique = '1 Boulevard Swordware';
			$usager->ville = 'Rivière-du-Loup';
			
			$usager->save();
		}		
		echo 'Modifications terminées.';
	}
	
	
	public function actionsite_complet()
	{
		Yii::app()->theme = "";
		Yii::app()->session['mobile'] = FALSE;
		$this->redirect(array('index'));
	}
	public function actionsite_mobile()
	{
		Yii::app()->session['mobile'] = TRUE;
		Yii::app()->theme = "mobile";
		//Yii::log('Configuration du thème mobile : '.Yii::app()->theme->name,'info','Yii.theme');
		$this->redirect(array('index'));
	}
}