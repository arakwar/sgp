<?php

class DispoFDFController extends Controller
{
	public $pageTitle = 'Force de frappe';
	
	// Uncomment the following methods and override them if needed
	
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
				'accessControl'
		);
	}
	
	public function accessRules()
	{
		return array(
				array('allow',
						'actions'=>array('index','case','view','listePompier', 'listePompierSP'),
						'roles'=>array('DispoFDF:index'),
				),
				array('allow',
							
						'actions'=>array('rapportComplet', 'rapportDecoche', 'rapports', 'imprimerRapport'),
						'roles'=>array('GesEquipe'),
						
				),
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}	
	public function changeStatut()
	{
		$casernes = $usager->getCaserne();
		$caserne = $casernes[0];
		$param = Parametres::model()->findByPk(1);
		$criteria = new CDbCriteria;
		$criteria->join = 'INNER JOIN tbl_quart quart ON quart.id = t.tbl_quart_id';
		$criteria->condition = 't.tbl_usager_id ='.Yii::app()->user->id.' AND t.tbl_caserne_id ='.$caserne.' AND t.date ="'.date('Y-m-d').'" AND quart.heureDebut <= "'.date('H-i').'" AND quart.heureFin >= "'.date('H-i').'"';
		$actual = DispoFDF::model()->find($criteria);
		if($actual->dispo === NUll)
		{
			if($param->defaut_fdf == 0)
			{
				$nouvelleDispo = 1;
			}
			$dispo = new DispoFDF;
			$dispo->tbl_quart_id = $actual->tbl_quart_id;
			$dispo->date = date('Y-m-d');
			$dispo->tbl_usager_id = Yii::app()->user->id;
			$dispo->tbl_caserne_id = $caserne;
			$dispo->dispo = $nouvelleDispo;
			$dispo->dateDecoche = new DateTime('NOW', new DateTimeZone($parametres->timezone));
			$dispo->save();
			return 1;
		}
		else
		{
			return 0;
		}

	}
	public function actionCase($date,$tbl_quart_id,$usager,$caserne,$estDispo=0,$heureDebut=false, $heureFin=false){
		$event_number = time();
		$param = Parametres::model()->findByPk(1);
		$quart = Quart::model()->findByPk($tbl_quart_id);
		if($quart->heureFin <= $quart->heureDebut){
			$time = explode(':',$quart->heureFin);
			$time[0]+=24;
			$quart->heureFin = $time[0].':'.$time[1].':'.$time[2];
		}
		if($heureDebut===false){
			$quart1 = Quart::model()->findByPk($tbl_quart_id);
			$heureDebut = $quart1->heureDebut;
			$heureFin = $quart1->heureFin;
		}
		if(!($date == date('Y-m-d') && $quart->heureFin < date('H:i:s'))){
			//L'usager se met disponible
			if($estDispo==0){
				//modifier le reccord s'il existe
				$record = DispoFDF::model()->find('date=:date AND tbl_quart_id=:quart AND tbl_usager_id=:usager AND tbl_caserne_id=:caserne AND (heureDebut=:heureDebut OR heureFin=:heureFin)',
												array(':date'=>$date,':quart'=>$tbl_quart_id,':usager'=>$usager,':caserne'=>$caserne,':heureDebut'=>$heureDebut,':heureFin'=>$heureFin));
				if($record==NULL){
					$record = new DispoFDF;
					$record->date = $date;
					$record->tbl_quart_id = $tbl_quart_id;
					$record->tbl_usager_id = $usager;
					$record->tbl_caserne_id = $caserne;
				}
				$record->heureDebut = $heureDebut;
				$record->heureFin = $heureFin;				
				$record->dispo = 1;
				$record->tbl_usager_action = Yii::app()->user->id;
				
				if($record->save()){
					$estDispo = 1;
					
					/*Historique FDF*/
					$histo = HistoFDF::model()->find('action=1 AND date=:date AND tbl_quart_id=:quart AND tbl_usager_id=:usager AND tbl_caserne_id=:caserne AND (heureDebut=:heureDebut OR heureFin=:heureFin)',
													array(':date'=>$date,':quart'=>$tbl_quart_id,':usager'=>$usager,':caserne'=>$caserne,':heureDebut'=>$heureDebut,':heureFin'=>$heureFin));													
					
					if($histo===NULL || strtotime($histo->dateAction)-strtotime(date('Y-m-d H:i:s')) < 300){
						$histo = new HistoFDF;
					}
					
					$histo->date = $date;
					$histo->tbl_quart_id = $tbl_quart_id;
					$histo->tbl_usager_id = $usager;
					$histo->action = 1;
					$histo->usager_action = Yii::app()->user->id;
					$dateAction =  new DateTime('now',new DateTimeZone("America/Montreal"));
					$histo->dateAction = $dateAction->format('Y-m-d H:i:s');
					$histo->tbl_caserne_id = $caserne;
					$histo->heureDebut = $heureDebut;
					$histo->heureFin = $heureFin;
					
					$histo->save();
				
				} else {
					Yii::log("Erreur #1 lors de l'enregistrement de la dispo",'error','Tech');
					throw new CHttpException(500);
				}
			} else {
				//L'usager se met non-disponible
				$record = DispoFDF::model()->find('date=:date AND tbl_quart_id=:quart AND tbl_usager_id=:usager AND tbl_caserne_id=:caserne AND (heureDebut=:heureDebut OR heureFin=:heureFin)',
												array(':date'=>$date,':quart'=>$tbl_quart_id,':usager'=>$usager,':caserne'=>$caserne,':heureDebut'=>$heureDebut,':heureFin'=>$heureFin));
				if($record==NULL){
					$record = new DispoFDF;
					$record->date = $date;
					$record->tbl_quart_id = $tbl_quart_id;
					$record->tbl_usager_id = $usager;
					$record->tbl_caserne_id = $caserne;
				}
				$record->heureDebut = $heureDebut;
				$record->heureFin = $heureFin;
				$record->dateDecoche = new DateTime('NOW', new DateTimeZone($param->timezone));
				$record->dispo = 0;
				$record->tbl_usager_action = Yii::app()->user->id;
				
				if($record->save()) {			
					$estDispo = 0;
					
					/*Historique FDF*/
					$histo = new HistoFDF;
					
					$histo->date = $date;
					$histo->tbl_quart_id = $tbl_quart_id;
					$histo->tbl_usager_id = $usager;
					$histo->action = 0;
					$histo->usager_action = Yii::app()->user->id;
					$dateAction =  new DateTime('now',new DateTimeZone("America/Montreal"));
					$histo->dateAction = $dateAction->format('Y-m-d H:i:s');
					$histo->tbl_caserne_id = $caserne;
					$histo->heureDebut = $heureDebut;
					$histo->heureFin = $heureFin;
					
					$histo->save();
					
					$today = new DateTime('now',new DateTimeZone($param->timezone));
					if($date == $today->format('Y-m-d')){
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
						
						//On va voir si la date fait partie d'une exception
						$testMinimum = $tblMinimum[$today->format("w")]['minimum'];
						$dateRequete = $today->format('Y-m-d');
						$exception = MinimumException::model()->find('dateDebut <= "'.$dateRequete.'" AND dateFin >= "'.$dateRequete.'" AND tbl_caserne_id = '.$caserne, array('limit'=>1));
						if(count($exception) == 1){
							$testMinimum = $exception->minimum;
						}
						
						
						if($param->fdf_minimum_type==0){
							$garde = EquipeGarde::model()->find("tbl_quart_id=".$tbl_quart_id." AND modulo=".(($today->getTimestamp()/86400)%$param->nbJourPeriode)." AND tbl_caserne_id = ".$caserne." AND tbl_garde_id = ".$param->garde_fdf);
							$equipes = Equipe::model()->findAll('siFDF=1 AND tbl_caserne_id = '.$caserne);
							$equipeGarde = Equipe::model()->findByPk($garde->tbl_equipe_id);
							$ordreGarde = $equipeGarde->ordre;
							$ordreA = $ordreGarde;
							$ordre = "(";
							for($i=1;$i<=$tblMinimum[$today->format("w")]['niveau'];$i++){
								$ordre .= "'".$ordreA."',";
								$ordreA++;
								if($ordreA>count($equipes)){
									$ordreA = 1;
								}
							}
							//On enlève la dernière virgule
							$ordre = substr($ordre, 0, strlen($ordre)-1);
							$ordre .= ")";
												
							$critere = new CDbCriteria();
							$critere->alias = 'd';
							$critere->join = "INNER JOIN tbl_usager t ON t.id=d.tbl_usager_id ".
											"INNER JOIN tbl_equipe_usager equipeUsager ON equipeUsager.tbl_usager_id = t.id ".
											"INNER JOIN tbl_equipe equipe ON equipe.id = equipeUsager.tbl_equipe_id";
							$critere->condition = "d.tbl_quart_id=:qid AND d.date=:date AND t.enService = 1 
													AND equipe.siFDF=1 AND equipe.ordre IN ".$ordre;
							$critere->params = array(':qid'=>$tbl_quart_id,':date'=>$date);
							$nbrPompier = DispoFDF::model()->count($critere);
							
							$critere = new CDbCriteria;
							$critere->alias = 't';
							$critere->join = 'INNER JOIN tbl_equipe_usager eu ON eu.tbl_usager_id = t.id '.
												'INNER JOIN tbl_equipe e ON e.id = eu.tbl_equipe_id';
							$critere->condition = 'e.tbl_caserne_id = :caserne AND t.enService = 1 AND e.ordre IN '.$ordre;
							$critere->params = array(':caserne'=>$caserne);
							
							$nbrPEquipe = Usager::model()->count($critere);
							
							$nbrDispo = $nbrPEquipe-$nbrPompier;
		
							if($nbrDispo<$testMinimum){
								if($param->dernierAlerteFDF==NULL || $param->dernierAlerteFDF=="0000-00-00 00:00:00"){
									$dateEnvoi = new DateTime("now",new DateTimeZone($param->timezone));
									$dateEnvoi->sub(new DateInterval("P1D"));
								}else{
									$dateEnvoi = new DateTime($param->dernierAlerteFDF,new DateTimeZone($param->timezone));
								}
								//On regarde pour ne pas envoir plus qu'un mail toutes les 300 secondes
								if($today->getTimestamp() - $dateEnvoi->getTimestamp() > 300){
									/*
									 * Envoi du courriel d'alerte
									 * 1- On va chercher les usagers dont leur équipe est noté pour recevoir les alertes.
									 * 2- On crée le courriel
									 * 3- On ajoute les destinataires
									 * 4- On envoit.
									 */
									$critere = new CDbCriteria();
									$critere->alias = 't';
									$critere->join = "INNER JOIN tbl_equipe_usager eu ON t.id = eu.tbl_usager_id
													INNER JOIN tbl_equipe e ON eu.tbl_equipe_id = e.id";
									$critere->condition = "t.alerteFDF = 1 AND e.tbl_caserne_id = :caserne";
									$critere->params = array(':caserne'=>$caserne);
									$tblUsager = Usager::model()->findAll($critere);
									$message = new YiiMailMessage;
									$message->view = "alerteFDF";
									$message->setBody(array('today'=>clone $today), 'text/html');
									$message->subject = "Alerte de minimum de Force de Frappe";
									$message->from = Yii::app()->params['emailSysteme'];
									if($tblUsager){
										foreach($tblUsager as $dest){
											$message->addTo($dest->courriel);
										}
										Yii::app()->mail->send($message);
									}
									$param->dernierAlerteFDF = $today->format("Y-m-d H:i:s");
									$param->save();
								}
							}
							/*$sortieLog  = "Rapport du minimum FDF : \n";
							$sortieLog .= "Équipe de garde : ".$garde->tblEquipe->nom." (".$garde->tbl_equipe_id.")\n";
							$sortieLog .= "Usager #".$usager."\n";
							$sortieLog .= "nbrPompier : ".$nbrPompier."\n";
							$sortieLog .= "nbrEquipe  : ".$nbrEquipe."\n";
							$sortieLog .= "testMinimum : ".$testMinimum."\n";
							$sortieLog .= "Date dernier envoi : ".$dateEnvoi->format('Y-m-d H:i:s')."\n";
							$sortieLog .= "Heure actuelle     : ".$today->format('Y-m-d H:i:s');
							Yii::log($sortieLog,'trace','DispoFDF');*/
						}elseif($param->fdf_minimum_type == 1){
							/* 
							1- Chercher les équipes de l'usager
							2- Chercher le nombre de pompiers dispos par équipe
							3- Chercher le nombre de pompiers par équipe
							4- Vérifier le minimum
							*/
							
							$criteriaEquipe = new CDbCriteria();
							$criteriaEquipe->alias = 'e';
							$criteriaEquipe->join = 'LEFT JOIN tbl_equipe_usager u ON u.tbl_equipe_id = e.id';
							$criteriaEquipe->condition = 'u.tbl_usager_id = :usager';
							$criteriaEquipe->params = array(':usager'=>$usager);
							$equipes = Equipe::model()->findAll($criteriaEquipe);
							
							foreach($equipes as $equipe){
								//Nombre de pompier non-dispo
								$critere = new CDbCriteria();
								$critere->alias = 'd';
								$critere->join = "INNER JOIN tbl_usager t ON t.id=d.tbl_usager_id ".
												"INNER JOIN tbl_equipe_usager equipeUsager ON equipeUsager.tbl_usager_id = t.id ".
												"INNER JOIN tbl_equipe equipe ON equipe.id = equipeUsager.tbl_equipe_id";
								$critere->condition = "d.tbl_quart_id=:qid AND d.date=:date AND t.enService = 1 
														AND equipe.siFDF=1 AND equipe.id = :equipe";
								$critere->params = array(':qid'=>$tbl_quart_id,':date'=>$date, ':equipe'=>$equipe->id);
								$nbrPompier = DispoFDF::model()->count($critere);
								
								//Nombre de pompier par équipe
								$critere = new CDbCriteria;
								$critere->alias = 't';
								$critere->join = 'INNER JOIN tbl_equipe_usager eu ON eu.tbl_usager_id = t.id '.
													'INNER JOIN tbl_equipe e ON e.id = eu.tbl_equipe_id';
								$critere->condition = 'e.tbl_caserne_id = :caserne AND t.enService = 1 AND e.id = :equipe';
								$critere->params = array(':caserne'=>$caserne, ':equipe'=>$equipe->id);
								
								$nbrPEquipe = Usager::model()->count($critere);
								
								$nbrDispo = $nbrPEquipe-$nbrPompier;
								
								if($nbrDispo<$testMinimum){
									if($param->dernierAlerteFDF==NULL || $param->dernierAlerteFDF=="0000-00-00 00:00:00"){
										$dateEnvoi = new DateTime("now",new DateTimeZone($param->timezone));
										$dateEnvoi->sub(new DateInterval("P1D"));
									}else{
										$dateEnvoi = new DateTime($param->dernierAlerteFDF,new DateTimeZone($param->timezone));
									}
									//On regarde pour ne pas envoir plus qu'un mail toutes les 300 secondes
									if($today->getTimestamp() - $dateEnvoi->getTimestamp() > 300){
										/*
										 * Envoi du courriel d'alerte
										 * 1- On va chercher les usagers dont leur équipe est noté pour recevoir les alertes.
										 * 2- On crée le courriel
										 * 3- On ajoute les destinataires
										 * 4- On envoit.
										 */
										$critere = new CDbCriteria();
										$critere->alias = 't';
										$critere->join = "INNER JOIN tbl_equipe_usager eu ON t.id = eu.tbl_usager_id
														INNER JOIN tbl_equipe e ON eu.tbl_equipe_id = e.id";
										$critere->condition = "t.alerteFDF = 1 AND e.tbl_caserne_id = :caserne";
										$critere->params = array(':caserne'=>$caserne);
										$tblUsager = Usager::model()->findAll($critere);
										$message = new YiiMailMessage;
										$message->view = "alerteFDF";
										$message->setBody(array('today'=>clone $today), 'text/html');
										$message->subject = "Alerte de minimum de Force de Frappe";
										$message->from = Yii::app()->params['emailSysteme'];
										if($tblUsager){
											foreach($tblUsager as $dest){
												$message->addTo($dest->courriel);
											}
											Yii::app()->mail->send($message);
										}
										$param->dernierAlerteFDF = $today->format("Y-m-d H:i:s");
										$param->save();
									}
									break;
								}
							}
							
						}
					}					
				} else {
					Yii::log("Erreur #2 lors de l'enregistrement de la dispo",'error','Tech');
					throw new CHttpException(500);
				} 
			}
		}		
		//Retourne le div selon si le pompier est disponible ou non.
		echo $estDispo==1?'<img src="images/crochet.png"/>':'';		
		Yii::app()->end();
	}
	
	public function actionIndex($dateDebut="",$usager="",$caserne="0"){
		Yii::log('Parcours de la page de dispo de FDF','info');
		$parametres = Parametres::model()->findByPk(1);
		
		$user = Usager::model()->findByPk(Yii::app()->user->id);
		$casernesUsager = $user->getCaserne();
		$casernes = Caserne::model()->findAll(array('condition'=>'id IN ('.$casernesUsager.') AND siActif = 1 AND si_fdf'));
		$tblCaserne = CHtml::listData($casernes,'id','nom');		
		
		if($caserne=="0"){
			if(isset($parametres->caserne_defaut_fdf)){
				$caserne = $parametres->caserne_defaut_fdf;
			}else{
				//legacy code
				foreach($casernes as $cas){
					$caserne = $cas->id;
					break;
				}			
			}
		}
		
		if($dateDebut==""){
			$dD = new DateTime(date("Y-m-")."01T00:00:00",new DateTimeZone('America/Montreal'));
		} else {
			$dD = new DateTime($dateDebut."T00:00:00",new DateTimeZone('America/Montreal'));
		}
		$dF = new DateTime(date("Y-m-t",$dD->getTimestamp()));
		
		if($usager==""){
			$usager = Yii::app()->user->id;
		}
		
		$thisUsager = Usager::model()->findByPk(Yii::app()->user->id);
		
		//Pour savoir la date pour avancer/reculer dans les affichage
		$dateActuelle = new DateTime(date("Y-m-d",$dD->getTimestamp()));
		$datePrecedente = new DateTime(date("Y-m-d",$dD->getTimestamp()));
		$datePrecedente->sub(new DateInterval("P1D"));
		$datePrecedente->sub(new DateInterval("P".(date("t",$datePrecedente->getTimestamp())-1)."D"));
		$dateSuivante = new DateTime(date("Y-m-d",$dF->getTimestamp()));
		$dateSuivante->add(new DateInterval("P1D"));
		
		//le texte du mois
		$arrayMois = array("",
			Yii::t('generale','janvier'),
			Yii::t('generale','fevrier'),
			Yii::t('generale','mars'),
			Yii::t('generale','avril'),
			Yii::t('generale','mai'),
			Yii::t('generale','juin'),
			Yii::t('generale','juillet'),
			Yii::t('generale','aout'),
			Yii::t('generale','septembre'),
			Yii::t('generale','octobre'),
			Yii::t('generale','novembre'),
			Yii::t('generale','decembre'),
		); 
		$texteMois = $arrayMois[date("n",$dD->getTimestamp())]." ".date("Y",$dD->getTimestamp());
		
		//on recule la dateDebut et avance la dateFin pour couvrir des semaines complètes
		$dD->sub(new DateInterval("P".date("w",$dD->getTimestamp())."D"));
		$dF->add(new DateInterval("P".(6-date("w",$dF->getTimestamp()))."D"));

		//Sert à stocker les informations pour la vue
		$dataDispo = array();
		
		$jourSemaine = array(
			Yii::t('generale','dim'),
			Yii::t('generale','lun'),
			Yii::t('generale','mar'),
			Yii::t('generale','mer'),
			Yii::t('generale','jeu'),
			Yii::t('generale','ven'),
			Yii::t('generale','sam'),
		);
		
		//Cette liste sert à savoir quand est-ce que le pompier n'est pas disponible (quels enregistrements sont dans DispoFDF)
		//Le résultat est transposé dans un array pour être plus facile à utiliser.
		$criteriaFDF = new CDbcriteria;
		$criteriaFDF->condition = 'tbl_usager_id=:id AND tbl_caserne_id=:caserne AND  dispo = '.(($parametres->defaut_fdf==0)?'0':'1');
		$criteriaFDF->params = array(':id'=>$usager,':caserne'=>$caserne);
		$listeDispo = DispoFDF::model()->findAll($criteriaFDF);
		$tblDispo = array();
		foreach($listeDispo as $dispo){
			$tblDispo[$dispo->date][$dispo->tbl_quart_id] = 1;
		}
		
		//On va avoir besoin des quarts dans la boucle, j'évite de répéter des requête à la BD pour rien
		$criteria = new CDbCriteria;		
		$criteria->alias = 'q';
		$criteria->join = 'LEFT JOIN tbl_poste_horaire ph ON ph.tbl_quart_id = q.id '.
						'LEFT JOIN tbl_poste_horaire_caserne phc ON phc.tbl_poste_horaire_id = ph.id';
		$criteria->condition = 'phc.tbl_caserne_id = :caserne';		
		$criteria->params = array(':caserne'=>$caserne);
		$criteria->order = 'q.heureDebut';
		$criteria->group = 'q.id';
		
		$tblQuart = Quart::model()->findAll($criteria);
		while($dD->format("Ymd")<=$dF->format("Ymd")){
			foreach($tblQuart as $quart) {
				$dataDispo[$dD->getTimestamp()][$quart->id] = isset($tblDispo[date("Y-m-d",$dD->getTimestamp())][$quart->id])?0:1;
			}
			$dD->add(new DateInterval("P1D"));
		}
		
		$tblEquipe = Equipe::model()->findAll('siHoraire = 1 AND tbl_caserne_id = '.$caserne);
		
		$idsE = '';
		foreach($tblEquipe as $equipe){
			$idsE .= $equipe->id.' ,';
		}
		$idsE = substr($idsE, 0, strlen($idsE)-2);
		
		$listeEquipeGarde = EquipeGarde::model()->findAll('tbl_equipe_id IN ('.$idsE.') AND tbl_garde_id = '.$parametres->garde_fdf);
		$tblEquipeGarde = array();
		foreach($listeEquipeGarde as $garde){
			$tblEquipeGarde[$garde->modulo][$garde->tbl_quart_id] = Equipe::model()->findByPk($garde->tbl_equipe_id);
		}
		
		//on calcul le mois le plus loin qu'on peu afficher a afficher
		//$chAnnee = changement année
		$moisFDF = $parametres['moisFDF'];
		if((date('n')+$moisFDF)>12){
			$moisMax = $moisFDF-(12-date('n'));
			$chAnnee = true;
		}else{
			$moisMax = date('n')+$moisFDF;
			$chAnnee = false;
		}
		
		if($chAnnee){
			$moisFlag = date('n')-12;
			if($dateDebut!=""){
				$moisDebut = substr($dateDebut,5,2);
				$flag = $moisDebut+$moisMax;
				if($flag<12){
					$moisFlag = $moisDebut;
				}
			}
			$dateSuivantes = ($moisFlag>=$moisMax)?NULL:date("Y-m-d",$dateSuivante->getTimestamp());
		}else{
			$dateSuivantes = (date('n',$dateSuivante->getTimestamp())>=$moisMax)?NULL:date("Y-m-d",$dateSuivante->getTimestamp());
		}
		
		$tblUsager = array();
		$critere = new CDbCriteria;
		$critere->condition = "actif=1";
		$critere->order = "matricule ASC";
				
		if(Yii::app()->user->checkAccess('gesService')){
			$tblUsager = CHtml::listData(Usager::model()->findAll($critere),'id','matprenomnom');
		}elseif(Yii::app()->user->checkAccess('gesCaserne')){
			$tblEquipe = Equipe::model()->findAll('tbl_caserne_id IN ('.$usager->getCaserne().')');
			$strEquipes = '';
			foreach($tblEquipe as $equipe){
				$strEquipes .= $equipe->id.', ';
			}
			$strEquipes = substr($strEquipes,0,strlen($strEquipes)-2);
			$critere->join = 'INNER JOIN tbl_equipe_usager eu ON eu.tbl_usager_id = t.id';
			$critere->condition.= ' AND eu.tbl_equipe_id IN ('.$strEquipes.')';
			$tblUsager = CHtml::listData(Usager::model()->findAll($critere),'id','matprenomnom');
		}elseif(Yii::app()->user->checkAccess('gesEquipe')){
			$strEquipes = $usager->getEquipes();
			$critere->join = 'INNER JOIN tbl_equipe_usager eu ON eu.tbl_usager_id = t.id';
			$critere->condition.= ' AND eu.tbl_equipe_id IN ('.$strEquipes.')';
			$tblUsager = CHtml::listData(Usager::model()->findAll($critere),'id','matprenomnom');
		}
		
		$listeEquipe = new CActiveDataProvider('Equipe',array('criteria'=>array('condition'=>'siFDF=1 and tbl_caserne_id = :caserne','params'=>array(':caserne'=>$caserne))));
		$garde = Garde::model()->findByPk($parametres->garde_fdf);
		
		$this->render('index',array(
			'datePrecedente'=>(date('Ym',$datePrecedente->getTimestamp())<date('Ym',time()))?NULL:date("Y-m-d",$datePrecedente->getTimestamp()),
			'dateSuivante'=>$dateSuivantes,
			'dateActuelle'=>date("Y-m-d",$dateActuelle->getTimestamp()),
			'texteMois'=>$texteMois,
			'dataDispo'=>$dataDispo,
			'jourSemaine'=>$jourSemaine,
			'tblQuart'=>$tblQuart,
			'tblEquipeGarde'=>$tblEquipeGarde,
			'parametres'=>$parametres,
			'usager'=>$usager,
			'tblUsager'=>$tblUsager,
			'listeEquipe'=>$listeEquipe,
			'tblCaserne'=>$tblCaserne,
			'caserne'=>$caserne,
			'garde'=>$garde,
			)
		);
	}
	
	public function actionListePompier($date,$quart,$equipe){
		$parametres = Parametres::model()->findByPk(1);
		
		$equipeC = Equipe::model()->findByPk($equipe);
		
		$criteriaQuart = new CDbCriteria();
		$criteriaQuart->condition = 'heureDebut <= "'.date('H:i:s').'" AND IF(heureFin <= heureDebut, ADDTIME(heureFin,"24:00:00") >= "'.date('H:i:s').'",heureFin >= "'.date('H:i:s').'")';
		
		$quartActuel = Quart::model()->find($criteriaQuart);
		
		$memeQuart = false;
		/*if(date("Y-m-d",$date)==date("Y-m-d") && $quartActuel->id==$quart){
			$memeQuart=true;
		}*/
		
		$critereListeDispo = new CDbCriteria;
		$critereListeDispo->alias = 'dispo';
		$critereListeDispo->join = 'INNER JOIN tbl_usager u ON u.id=dispo.tbl_usager_id INNER JOIN tbl_equipe_usager eu ON eu.tbl_usager_id = u.id AND eu.tbl_equipe_id = '.$equipe;
		$critereListeDispo->condition = 'date ="'.date("Y-m-d",$date).'" AND tbl_quart_id='.$quart.' AND tbl_caserne_id = '.$equipeC->tbl_caserne_id;
		if($parametres->defaut_fdf==0){
			$critereListeDispo->condition .= (($memeQuart)?' AND (dispo = 0 OR (dispo = 1 AND NOT (heureDebut <= "'.date('H:i:s').'" AND IF(heureFin <= heureDebut, ADDTIME(heureFin,"24:00:00") >= "'.date('H:i:s').'",heureFin >= "'.date('H:i:s').'"))))':' AND dispo = 0');
		}else{
			$critereListeDispo->condition .= ' AND dispo = 1'.(($memeQuart)?' AND heureDebut <= "'.date('H:i:s').'" AND IF(heureFin <= heureDebut, ADDTIME(heureFin,"24:00:00") >= "'.date('H:i:s').'",heureFin >= "'.date('H:i:s').'")':'');
		}
		
		$listeDispo = DispoFDF::model()->findAll($critereListeDispo);
		$tblUsager = array();
		foreach($listeDispo as $value){
			if(!in_array($value->tbl_usager_id,$tblUsager))
			$tblUsager[] = $value->tbl_usager_id;
		}
		$critere = new CDbCriteria;
		$critere->alias = 't';
		$critere->order = 't.matricule';
		$critere->join = 'INNER JOIN tbl_equipe_usager eu ON eu.tbl_usager_id = t.id AND eu.tbl_equipe_id = '.$equipe;
		$critere->condition = 't.enService = 1';
		
		$criteriaNDispo = new CDbCriteria;
		if($parametres->defaut_fdf==0){
			$critere->addNotInCondition('t.id', $tblUsager);
			$criteriaNDispo->addInCondition('id',$tblUsager);
		}else{
			$critere->addInCondition('t.id', $tblUsager);
			$criteriaNDispo->alias = 't';
			$criteriaNDispo->join = 'INNER JOIN tbl_equipe_usager eu ON eu.tbl_usager_id = t.id AND eu.tbl_equipe_id = '.$equipe;;
			$criteriaNDispo->addNotInCondition('t.id',$tblUsager);
		}
					
		$dataUsager = new CActiveDataProvider('Usager',array(
			'criteria'=>$critere,
			'pagination'=>false
		));
		
		$dataUsagerNDispo = new CActiveDataProvider('Usager',array(
			'criteria'=>$criteriaNDispo,
			'pagination'=>false
		));
		
		$equipeN = Equipe::model()->findByPk($equipe);
		$quartN = Quart::model()->findByPk($quart);
		$this->renderPartial('_listePompier',array(
			'dataUsager'=>$dataUsager,
			'dataUsagerNDispo'=>$dataUsagerNDispo,
			'titre'=>CHtml::label(Yii::t('controller','dispoFDF._listePompier.date').' : ' .date('Y-m-d', $date).', '.Yii::t('controller','dispoFDF._listePompier.quart').' : '.$quartN->nom.', '.Yii::t('controller','dispoFDF._listePompier.equipe').' : '.$equipeN->nom,'', array('class'=>'titreListePompier')),
			'titre2'=>CHtml::label(Yii::t('controller','dispoFDF._listePompier.pompierNonDispo').' :','', array('class'=>'titreListePompier')),
		));
		Yii::app()->end();
	}
	
	public function actionListePompierSP($date,$quart,$groupe){
		$parametres = Parametres::model()->findByPk(1);		
		
		$tbl_groupeUsager = GroupeUsager::model()->findAll('tbl_groupe_id = "'.$groupe.'"');
		$in = '(';
		foreach($tbl_groupeUsager as $value){
			$in .= "'".$value->tbl_usager_id."',";
		}
		$critere = new CDbCriteria;
		$criteriaNDispo = new CDbCriteria;
		if(strlen($in) == 1){
						//on fait exprès pour que ca retourne 0
			$critere->condition = 'id = "a"';
			$criteriaNDispo->condition = 'id = "a"';
		}else{			
			$criteriaQuart = new CDbCriteria();
			$criteriaQuart->condition = 'heureDebut <= "'.date('H:i:s').'" AND IF(heureFin <= heureDebut, ADDTIME(heureFin,"24:00:00") >= "'.date('H:i:s').'",heureFin >= "'.date('H:i:s').'")';
			
			$quartActuel = Quart::model()->find($criteriaQuart);
			
			$memeQuart = false;
			/*if(date("Y-m-d",$date)==date("Y-m-d") && $quartActuel->id==$quart){
				$memeQuart=true;
			}*/			
			
			$in = substr($in, 0 , strlen($in)-1);
			$in .= ')';
			$critereListeDispo = new CDbCriteria;
			$critereListeDispo->alias = 'd';
			$critereListeDispo->join = 'INNER JOIN tbl_usager u ON u.id=d.tbl_usager_id';
			$critereListeDispo->condition = 'd.date ="'.date("Y-m-d",$date).'" AND d.tbl_quart_id='.$quart.' AND u.id IN '.$in;
			if($parametres->defaut_fdf==0){
				$critereListeDispo->condition .= (($memeQuart)?' AND (dispo = 0 OR (dispo = 1 AND NOT (heureDebut <= "'.date('H:i:s').'" AND IF(heureFin <= heureDebut, ADDTIME(heureFin,"24:00:00") >= "'.date('H:i:s').'",heureFin >= "'.date('H:i:s').'"))))':' AND dispo = 0');
			}else{
				$critereListeDispo->condition .= ' AND dispo = 1'.(($memeQuart)?' AND heureDebut <= "'.date('H:i:s').'" AND IF(heureFin <= heureDebut, ADDTIME(heureFin,"24:00:00") >= "'.date('H:i:s').'",heureFin >= "'.date('H:i:s').'")':'');
			}
			$listeDispo = DispoFDF::model()->findAll($critereListeDispo);
			$tblUsager = array();
			foreach($listeDispo as $value){
				$tblUsager[] = $value->tbl_usager_id;
			}
			$critere->order = 'matricule';
			$critere->condition = "actif=1 AND enService=1 AND id IN ".$in;
			
			if($parametres->defaut_fdf==0){
				$critere->addNotInCondition('id',$tblUsager);			
				$criteriaNDispo->addInCondition('id',$tblUsager);
			}else{
				$critere->addInCondition('id',$tblUsager);
				$criteriaNDispo->condition= 'id IN '.$in;
				$criteriaNDispo->addNotInCondition('id',$tblUsager);			
			}
		}
		
		$dataUsagerNDispo = new CActiveDataProvider('Usager',array(
				'criteria'=>$criteriaNDispo,
				'pagination'=>false
		));

		$dataUsager = new CActiveDataProvider('Usager',array(
			'criteria'=>$critere,
			'pagination'=>false
		));
				
		$groupeN = Groupe::model()->findByPk($groupe);
		$quartN = Quart::model()->findByPk($quart);
		
		$this->renderPartial('_listePompier',array(
			'dataUsager'=>$dataUsager,
			'dataUsagerNDispo'=>$dataUsagerNDispo,
			'titre'=>CHtml::label(Yii::t('controller','dispoFDF._listePompier.date'). ' : ' .date('Y-m-d', $date).', '.Yii::t('controller','_listePompier.quart').' : '.$quartN->nom.', '.Yii::t('controller','_listePompier.equipe').' : '.$groupeN->nom,'', array('class'=>'titreListePompier')),
			'titre2'=>CHtml::label(Yii::t('controller','dispoFDF._listePompier.pompierNonDispo').' :','', array('class'=>'titreListePompier')),
		));
		Yii::app()->end();
	}
	
	public function actionUpdateForm(){
		$parametres = Parametres::model()->findByPk(1);
		$critereEquipe = new CDbCriteria;
		$critereEquipe->distinct = true;
		$critereEquipe->order = "dateHeure DESC";
		
		$critereGroupe = new CDbCriteria;
		$critereGroupe->distinct = true;
		$critereGroupe->order = "dateHeure DESC";
		
		if(isset($_POST['dateDebut']) && $_POST['dateDebut']!=''){
			$dateDebut = new DateTime($_POST['dateDebut'],new DateTimeZone($parametres->timezone));
			$critereDebut = new CDbCriteria;
			$critereDebut->select = "tbl_equipe_id";
			$critereDebut->condition .= "typeAction=0 AND dateHeure<:dateDebut";
			$critereDebut->params = array(':dateDebut'=>$dateDebut->format('Y-m-d H:i:s'));
			$resultDebutEquipe = HistoEquipe::model()->findAll($critereDebut);
			$tblDebutEquipe = array();
			foreach($resultDebutEquipe as $value){
				$tblDebutEquipe[] = $value->tbl_equipe_id;
			}
			$critereEquipe->addNotInCondition('tbl_equipe_id',$tblDebutEquipe);
			$critereDebut->select = "tbl_groupe_id";
			$resultDebutGroupe = HistoGroupe::model()->findAll($critereDebut);
			$tblDebutGroupe = array();
			foreach($resultDebutGroupe as $value){
				$tblDebutGroupe[] = $value->tbl_groupe_id;
			}
			$critereGroupe->addNotInCondition('tbl_groupe_id',$tblDebutGroupe);
		}
		if(isset($_POST['dateFin']) && $_POST['dateFin']!=''){
			$dateFin = new DateTime($_POST['dateFin'],new DateTimeZone($parametres->timezone));
			$critereFin = new CDbCriteria;
			$critereFin->select = "tbl_equipe_id";
			$critereFin->condition .= "typeAction=2 AND dateHeure>=:dateFin";
			$critereFin->params = array(':dateFin'=>$dateFin->format('Y-m-d H:i:s'));
			$resultFinEquipe = HistoEquipe::model()->findAll($critereFin);
			$tblFinEquipe = array();
			foreach($resultFinEquipe as $value){
				$tblFinEquipe[] = $value->tbl_equipe_id;
			}
			$critereEquipe->addNotInCondition('tbl_equipe_id',$tblFinEquipe);
			$critereFin->select = "tbl_groupe_id";
			$resultFinGroupe = HistoGroupe::model()->findAll($critereFin);
			$tblFinGroupe = array();
			foreach($resultFinGroupe as $value){
				$tblFinGroupe[] = $value->tbl_groupe_id;
			}
			$critereGroupe->addNotInCondition('tbl_groupe_id',$tblFinGroupe);
		}
		
		$tblEquipe = HistoEquipe::model()->findAll($critereEquipe);
		
		$lstEquipe = array();
		foreach($tblEquipe as $key=>$equipe){
			if(!array_key_exists($equipe->tbl_equipe_id,$lstEquipe) AND $equipe->typeAction != 0){
				$lstEquipe[$equipe->tbl_equipe_id] = $equipe->nom;
			}elseif($equipe->typeAction != 0){
				if(strpos($lstEquipe[$equipe->tbl_equipe_id], $equipe->nom)=== false)
					$lstEquipe[$equipe->tbl_equipe_id] .= " / ".$equipe->nom;
			}
		}
		
		$tblGroupe = HistoGroupe::model()->findAll($critereGroupe);
		
		$lstGroupe = array();
		foreach($tblGroupe as $key=>$groupe){
			if(!array_key_exists($groupe->tbl_groupe_id,$lstGroupe) AND $groupe->typeAction != 0){
				$lstGroupe[$groupe->tbl_groupe_id] = $groupe->nom;
			}elseif($groupe->typeAction != 0){
				if(strpos($lstGroupe[$groupe->tbl_groupe_id], $groupe->nom)=== false)
					$lstGroupe[$groupe->tbl_groupe_id] .= " / ".$groupe->nom;
			}
		}
		
		$return['equipe'] = CHtml::checkBox('cmbEquipe_all',false,array('value'=>'all')).CHtml::label("Tous",'cmbEquipe_all',array('style'=>'display:inline;')).'<br/>';
		$return['equipe'] .= CHtml::checkBoxList('cmbEquipe',"",$lstEquipe,array(/*'checkAll'=>"Tous",*/'labelOptions'=>array('style'=>'display:inline;')));
		
		$return['groupe'] = CHtml::checkBox('cmbEquipeSpe_all',false,array('value'=>'all')).CHtml::label("Tous",'cmbEquipeSpe_all',array('style'=>'display:inline;')).'<br/>';
		$return['groupe'] .= CHtml::checkBoxList('cmbEquipeSpe',"",$lstGroupe,array(/*'checkAll'=>"Tous",*/'labelOptions'=>array('style'=>'display:inline;')));
		echo json_encode($return);
	}
	
	public function actionRapports(){
		$this->render('rapports',array()
		);		
	}
		
	public function loadModel($id)
	{
		$model=DispoFDF::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,Yii::t('erreur','erreur404'));
		return $model;
	}
	
	public function actionView($dateDebut="",$nombreJour=7, $cmbJour="", $choix="",$caserne="0"){
		$parametres = Parametres::model()->findByPk(1);
		$ajax = false;
		if(Yii::app()->request->isAjaxRequest){
			$ajax = true;
		}
		
		$user = Usager::model()->findByPk(Yii::app()->user->id);
		$casernesUsager = $user->getCaserne();
		$casernes = Caserne::model()->findAll(array('condition'=>'id IN ('.$casernesUsager.') AND siActif = 1 AND si_fdf'));
		$tblCaserne = CHtml::listData($casernes,'id','nom');		
		
		if($caserne=="0"){
			if(isset($parametres->caserne_defaut_fdf)){
				$caserne = $parametres->caserne_defaut_fdf;
			}else{
				//legacy code
				foreach($casernes as $cas){
					$caserne = $cas->id;
					break;
				}			
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
		
		if($cmbJour!=""){
			Yii::app()->session['nbJourFDF'] = $cmbJour;
		}elseif(!isset(Yii::app()->session['nbJourFDF'])){
			Yii::app()->session['nbJourFDF'] = 7;
		}
		if(!Yii::app()->request->isAjaxRequest){
			$nombreJour = Yii::app()->session['nbJourFDF'];
		}
		
		//On calcul la date maximum
		$dateMax = new DateTime('now',new DateTimeZone('America/Montreal'));
		$dateMax->add(new DateInterval('P6M'));	
		if($cmbJour!=""){			
			$dateMax->sub(new DateInterval('P'.Yii::app()->session['nbJourFDF'].'D'));		
		}else{
			$dateMax->sub(new DateInterval('P7D'));	
		}
		$max = false;
		$jourSemaine = array(
			Yii::t('generale','dim'),
			Yii::t('generale','lun'),
			Yii::t('generale','mar'),
			Yii::t('generale','mer'),
			Yii::t('generale','jeu'),
			Yii::t('generale','ven'),
			Yii::t('generale','sam'),
		);
		
		if($dateDebut=="" OR $dateDebut < date("Ymd")){
			$dateDebut = new DateTime('now',new DateTimeZone('America/Montreal'));
		} else {
			$dateDebut = new DateTime($dateDebut."T00:00:00",new DateTimeZone('America/Montreal'));
		}
		
		//On vérifie s'il y a un quart qui passe par-dessus minuit et si on est pendant ce quart
		//Si oui il faut reculer le dateDebut d'un jour pour afficher le quart de nuit dans la liste.
		$quartCriteria = new CDbCriteria;
		$quartCriteria->alias = 'q';
		$quartCriteria->join = "";
		$quartCriteria->condition = "heureFin <= heureDebut AND heureFin <> '00:00:00'";
		$quartCriteria->addInCondition('id',$quartId);
		
		$quartMinuit = Quart::model()->find($quartCriteria);
		
		$dateLive = new DateTime(date("Y-m-d H:i:s"),new DateTimeZone('America/Montreal'));
		if($quartMinuit !== NULL){			
			if($dateLive->getTimestamp()>= strtotime('00:00:00') && $dateLive->getTimestamp() <= strtotime($quartMinuit->heureFin)){
				$dateDebut->sub(new DateInterval('P1D'));
				$dateLive->sub(new DateInterval('P1D'));
			}
		}
	
		$tblPompier = DispoFDF::getDispoJour(array('dateDebut'=>clone $dateDebut,'nombreJour'=>($max)?Yii::app()->session['nbJourFDF']:$nombreJour),"","",true,$caserne, $ajax);
		
		$criteriaGroupe = new CDbCriteria;
		$criteriaGroupe->alias = 't';
		$criteriaGroupe->join = 'LEFT JOIN tbl_caserne c ON c.id = t.tbl_caserne_id';
		$criteriaGroupe->condition = 'c.siActif = 1';
		$criteriaGroupe->order = 'c.nom ASC';
		$listeGroupe = Groupe::model()->findAll($criteriaGroupe);
		
		if($nombreJour == 1 && $choix=="last"){
			$dateDebut->sub(new DateInterval('P4D'));
		}
		
		$tblPompierGroupe = DispoFDF::getDispoJour(array('dateDebut'=>clone $dateDebut,'nombreJour'=>(($max)?Yii::app()->session['nbJourFDF']:$nombreJour)),"",$listeGroupe,false,$caserne, $ajax);
		
		$tblGarde = array();
		$tblMinimum = array();
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
			
		}elseif($parametres->affichage_fdf==1){
			$critereCaserne = new CDbCriteria;
			$critereCaserne->condition = 'siActif = 1';
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
				
				$i++;			
			}			
		}

		if($parametres->fdf_equipe_spe ==0){
			$tblEquipeSP = Groupe::model()->findAll($criteriaGroupe);
		}else{
			$tblEquipeSP = Groupe::model()->findAll('tbl_caserne_id = '.$caserne);			
		}
		
		$nbrEquipe = array();
		$caserneNom = array();
		$caserneId = array();
		$critereEquipe = new CDbCriteria;
		if($parametres->affichage_fdf == 0){
			$critereEquipe->condition = "siFDF=1 AND siActif=1 AND tbl_caserne_id = ".$caserne;
			$nbrEquipe[] = Equipe::model()->count($critereEquipe);
			$caserneId[] = $caserne;
		}elseif($parametres->affichage_fdf == 1){
			$casID = array();
			foreach($casernes as $cas){
				$caserneNom[] = $cas->nom; 
				$caserneId[] = $cas->id; 
				$critereEquipe->condition = "siFDF =1 AND siActif = 1 AND tbl_caserne_id = ".$cas->id;
				$nbrEquipe[] = Equipe::model()->count($critereEquipe);
			}			
		}
		
		$garde = Garde::model()->findByPk($parametres->garde_fdf);
		
		if($ajax){
			$this->renderPartial('_view',array(
				'tblPompierGroupe' => $tblPompierGroupe,
				'tblPompier'=>$tblPompier,
				'tblMinimum'=>$tblMinimum,
				'dateDebut'=>$dateDebut,
				'jourSemaine'=>$jourSemaine,
				'tblGarde'=>$tblGarde,
				'Nbrquarts'=>$Nbrquarts,
				'nbrEquipe'=>$nbrEquipe,
				'caserneNom'=>$caserneNom,
				'caserneId'=>$caserneId,
				'garde'=>$garde,
				'ajax'=>true,
				)
			);
			echo '£';
			$this->renderPartial('_viewSP',array(
					'tblPompier'=>$tblPompier, 
					'tblPompierGroupe'=>$tblPompierGroupe, 
					'jourSemaine'=>$jourSemaine,
					'parametres'=>$parametres, 
					'tblEquipeSP'=>$tblEquipeSP,
					'Ajax'=>$choix,
					'Nbrquarts'=>$Nbrquarts,
					)
			);
		}else{
			$criteriaLEquipe = new CDbCriteria;
			$criteriaLEquipe->condition = 'siFDF=1';
			if($parametres->affichage_fdf==0){
				$criteriaLEquipe->condition .= ' AND tbl_caserne_id = '.$caserne;
			}elseif($parametres->affichage_fdf==1){
				$criteriaLEquipe->addInCondition('tbl_caserne_id', $casernesID);
			}
			$criteriaLEquipe->order = 'tbl_caserne_id ASC, nom ASC';
			$listeEquipe = new CActiveDataProvider('Equipe',array('criteria'=>$criteriaLEquipe));
			
			//Pour générer la liste des pompiers dispo
			Yii::app()->clientScript->registerScript('listePompier',
				'$(".listePompier").live("click",function(){'.
				CHtml::ajax(array(
					'update'=>'#listePompier',
					'type'=>'GET',
					'url'=>array('listePompier'),
					'cache'=>'false',
					'data'=>"js:{date:$(this).attr('date'),quart:$(this).attr('quart'),equipe:$(this).attr('equipe')}",
				)).'});'
				
			);
			
			//Pour générer la liste des pompiers dispo dans les équipes spécialisées
			Yii::app()->clientScript->registerScript('listePompierSP',
				'$(".listePompierSP").live("click",function(){'.
				CHtml::ajax(array(
					'update'=>'#listePompier',
					'type'=>'GET',
					'url'=>array('listePompierSP'),
					'cache'=>'false',
					'data'=>"js:{date:$(this).attr('date'),quart:$(this).attr('quart'),groupe:$(this).attr('groupe')}",
				)).'});'
				
			);
			
			$this->render('view',array(
				'tblPompier'=>$tblPompier,
				'tblPompierGroupe' => $tblPompierGroupe,
				'dateDebut'=>$dateDebut,
				'jourSemaine'=>$jourSemaine,
				'listeEquipe'=>$listeEquipe,
				'tblGarde'=>$tblGarde,
				'nbrEquipe'=>$nbrEquipe,
				'tblEquipeSP'=>$tblEquipeSP,
				'parametres'=>$parametres,
				'dateMax'=>$dateMax->format('dmY'),
				'caserne'=>$caserne,
				'tblCaserne'=>$tblCaserne,
				'tblMinimum'=>$tblMinimum,
				'Nbrquarts'=>$Nbrquarts,
				'caserneNom'=>$caserneNom,
				'caserneId'=>$caserneId,
				'garde'=>$garde,
				'dateLive'=>$dateLive,
				)
			);
		}
	}

	public function actionRapportDecoche($dateDebut=NULL, $dateFin=NULL){
		$quarts = Quart::model()->findAll();
		$tblQuarts = array();
		foreach($quarts as $quart){
			$tblQuarts[$quart->id] = $quart->nom;
		}
		if(Yii::app()->request->isAjaxRequest){
			if($dateDebut!=NULL && $dateFin!=NULL){
				$criteria = new CDbCriteria;
				$criteria->condition = 'date BETWEEN :dateDebut AND :dateFin ';
				$criteria->params = array(':dateDebut'=>$dateDebut, ':dateFin'=>$dateFin);
				if(isset($_GET['Quarts'])){
					$criteria->addInCondition('tbl_quart_id', $_GET['Quarts'], 'AND');
				}else{
					$quarts = array();
				}
				$criteria->order = 'date ASC, dateDecoche ASC';
	
				$dataDispo = new CActiveDataProvider('DispoFDF',array(
						'criteria'=>$criteria,
						'pagination'=>array('pageSize'=>15)
				));
			}else{
				$dataDispo = new CActiveDataProvider('DispoFDF',array(
						'criteria'=>array('condition'=>'id=0'),
				));
			}
				
			$this->renderPartial('_resultDecoche',array(
					'dataDispo'=>$dataDispo,
			));
		}else{
			$dataDispo = new CActiveDataProvider('DispoFDF',array(
					'criteria'=>array('condition'=>'id=0'),
			));
			$this->render("rapportDecoche",array(
					'DateDebut'=>NULL,
					'DateFin'=>NULL,
					'ChkSelected'=>NULL,
					'tblQuarts'=>$tblQuarts,
					'dataDispo'=>$dataDispo,
			));
		}
	}
	
	public function actionRapportComplet($dateDebut=NULL, $dateFin=NULL){
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
				$inCondition = '';
				if(isset($_GET['quarts'])){
					$in = '';
					foreach($_GET['quarts'] as $quart){
						$in .= $quart.',';
					}
					$in = substr($in,0,strlen($in)-1);
					$inCondition = ' AND q.id IN('.$in.')';
				}
				if(isset($_GET['equipes'])){
					$in = '';
					foreach($_GET['equipes'] as $equipe){
						$in .= $equipe.',';
					}
					$in = substr($in,0,strlen($in)-1);
					$inCondition .= ' AND e.id IN('.$in.')';
				}
				if($_GET['regroupement']=='0'){
					$sql =
					"SELECT
							c.nom AS Caserne,
							ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) AS Temps,
							e.nom AS Equipe,
							e.couleur AS Couleur,
							SUM(IFNULL((SELECT h.action
										FROM tbl_histo_fdf h
										WHERE
											h.date = DATE(Temps)
											AND h.tbl_quart_id = q.id
											AND h.tbl_usager_id = u.id
											AND h.dateAction <= Temps
										ORDER BY h.dateAction DESC
										LIMIT 1
										),
										IF((SELECT defaut_fdf FROM tbl_parametres)=1,0,1))
							) AS Dispo
						FROM (numbers i1, numbers i2, numbers i3, numbers i4),
						tbl_caserne c,
						tbl_quart q,
						tbl_usager u
						LEFT JOIN tbl_equipe_usager eu ON eu.tbl_usager_id = u.id
						LEFT JOIN tbl_equipe e ON e.id = eu.tbl_equipe_id
						WHERE
							(ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) <= '".$dateFin." 23:00:00')
							AND u.actif = 1
							AND ((
								q.heureDebut <= TIME(ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')))
								AND 
									ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) <
									IF(q.heureDebut>=q.heureFin,
										CONCAT(ADDDATE(DATE(ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),INTERVAL 1 DAY),' ',q.heureFin),
										CONCAT(DATE(ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),' ',q.heureFin))
								)
							OR
								(
								ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) >=
								IF(q.heureDebut>=q.heureFin,
										CONCAT(SUBDATE(DATE(ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),INTERVAL 1 DAY),' ',q.heureDebut),
										CONCAT(DATE(ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),' ',q.heureDebut))
								AND q.heureFin > TIME(ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')))
								)
							)
							AND e.siFDF = 1
							AND c.id = e.tbl_caserne_id
							".$inCondition."
							GROUP BY  c.id, Temps, q.id, eu.tbl_equipe_id
							ORDER BY  c.id, Temps, q.heureDebut, eu.tbl_equipe_id";
				}else{
					$sql =
					"SELECT
							c.nom AS Caserne,
							CONCAT(u.matricule,' ',u.prenom,' ',u.nom) AS Usager,
							ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) AS Temps,
							e.nom AS Equipe,
							SUM(IFNULL((SELECT h.action
										FROM tbl_histo_fdf h
										WHERE
											h.date = DATE(Temps)
											AND h.tbl_quart_id = q.id
											AND h.tbl_usager_id = u.id
											AND h.dateAction <= Temps
										ORDER BY h.dateAction DESC
										LIMIT 1
										),
										IF((SELECT defaut_fdf FROM tbl_parametres)=1,0,1))
							) AS Dispo
						FROM (numbers i1, numbers i2, numbers i3, numbers i4),
						tbl_caserne c,
						tbl_quart q,
						tbl_usager u
						LEFT JOIN tbl_equipe_usager eu ON eu.tbl_usager_id = u.id
						LEFT JOIN tbl_equipe e ON e.id = eu.tbl_equipe_id
						WHERE
							(ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) <= '".$dateFin." 23:00:00')
							AND u.actif = 1
							AND ((
								q.heureDebut <= TIME(ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')))
								AND 
									ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) <
									IF(q.heureDebut>=q.heureFin,
										CONCAT(ADDDATE(DATE(ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),INTERVAL 1 DAY),' ',q.heureFin),
										CONCAT(DATE(ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),' ',q.heureFin))
								)
							OR
								(
								ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) >=
								IF(q.heureDebut>=q.heureFin,
										CONCAT(SUBDATE(DATE(ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),INTERVAL 1 DAY),' ',q.heureDebut),
										CONCAT(DATE(ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),' ',q.heureDebut))
								AND q.heureFin > TIME(ADDTIME('".$dateDebut." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')))
								)
							)
							AND e.siFDF = 1
							AND c.id = e.tbl_caserne_id
							".$inCondition."
							GROUP BY  c.id, Temps, q.id, u.id
							ORDER BY  c.id, e.nom, Temps, q.heureDebut, u.matricule";
				}
				$cn = Yii::app()->db;
				$cm = $cn->createCommand($sql);
				$dataDispo = $cm->query();
				$this->renderPartial('_resultComplet',array(
						'dataDispo'=>$dataDispo,
						'dateDebut'=>$_GET['dateDebut'],
						'dateFin'=>$_GET['dateFin'],
						'groupe'=>$_GET['regroupement'],
				),false,true);
			}else{
				echo 'date';
			}
			Yii::app()->end();
		}
		$this->render("rapportComplet",array(
				'parametres' => $parametres,
				'DateDebut'=>NULL,
				'DateFin'=>NULL,
				'ChkSelectedQ'=>NULL,
				'ChkSelectedE'=>NULL,
				'ChkSelectedR'=>'0',
				'tblQuarts'=>$tblQuarts,
				'tblEquipes'=>$tblEquipes,
				'tblRegroup'=>$tblRegroup,
				'dataDispo'=>FALSE,
		));				
	}
	
	public function actionImprimerRapport(){
		if($_GET['dateDebut']!='' && $_GET['dateFin']!=''){
			$inCondition = '';
			if(isset($_GET['quarts'])){
				$in = '';
				foreach($_GET['quarts'] as $quart){
					$in .= $quart.',';
				}
				$in = substr($in,0,strlen($in)-1);
				$inCondition = ' AND q.id IN('.$in.')';
			}
			if(isset($_GET['equipes'])){
				$in = '';
				foreach($_GET['equipes'] as $equipe){
					$in .= $equipe.',';
				}
				$in = substr($in,0,strlen($in)-1);
				$inCondition .= ' AND e.id IN('.$in.')';
			}
			$parametres = Parametres::model()->findByPk(1);
			$dateDebut = new DateTime($_GET['dateDebut'],new DateTimeZone($parametres->timezone));
			$dateFin = new DateTime($_GET['dateFin'],new DateTimeZone($parametres->timezone));
			$nbrJour = $dateDebut->diff($dateFin);
			if($_GET['regroupement']=='0'){
				$sql =
				"SELECT
								c.nom AS Caserne,
								ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) AS Temps,
								e.nom AS Equipe,
								e.couleur AS Couleur,
								SUM(IFNULL((SELECT h.action
											FROM tbl_histo_fdf h
											WHERE
												h.date = DATE(Temps)
												AND h.tbl_quart_id = q.id
												AND h.tbl_usager_id = u.id
												AND h.dateAction <= Temps
											ORDER BY h.dateAction DESC
											LIMIT 1
											),
											IF((SELECT defaut_fdf FROM tbl_parametres)=1,0,1))
								) AS Dispo
							FROM (numbers i1, numbers i2, numbers i3, numbers i4),
							tbl_caserne c,
							tbl_quart q,
							tbl_usager u
							LEFT JOIN tbl_equipe_usager eu ON eu.tbl_usager_id = u.id
							LEFT JOIN tbl_equipe e ON e.id = eu.tbl_equipe_id
							WHERE
								(ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) <= '".$_GET['dateFin']." 23:00:00')
								AND u.actif = 1
								AND ((
									q.heureDebut <= TIME(ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')))
									AND ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) <
										IF(q.heureDebut>=q.heureFin,
											CONCAT(ADDDATE(DATE(ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),INTERVAL 1 DAY),' ',q.heureFin),
											CONCAT(DATE(ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),' ',q.heureFin))
									)
								OR
									(
									ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) >=
									IF(q.heureDebut>=q.heureFin,
											CONCAT(SUBDATE(DATE(ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),INTERVAL 1 DAY),' ',q.heureDebut),
											CONCAT(DATE(ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),' ',q.heureDebut))
									AND q.heureFin > TIME(ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')))
									)
								)
								AND e.siFDF = 1
								AND c.id = e.tbl_caserne_id
								".$inCondition."
								GROUP BY  c.id, Temps, q.id, eu.tbl_equipe_id
								ORDER BY  c.id, Temps, q.heureDebut, eu.tbl_equipe_id";
			}else{
				$sql =
				"SELECT
					c.nom AS Caserne,
					CONCAT(u.matricule,' ',u.prenom,' ',u.nom) AS Usager,
					ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) AS Temps,
					e.nom AS Equipe,
					IFNULL((SELECT h.action
								FROM tbl_histo_fdf h
								WHERE
									h.date = DATE(Temps)
									AND h.tbl_quart_id = q.id
									AND h.tbl_usager_id = u.id
									AND h.dateAction <= Temps
								ORDER BY h.dateAction DESC
								LIMIT 1
								),
								IF((SELECT defaut_fdf FROM tbl_parametres)=1,0,1)
					) AS Dispo
				FROM (numbers i1, numbers i2, numbers i3, numbers i4),
				tbl_caserne c,
				tbl_quart q,
				tbl_usager u
				LEFT JOIN tbl_equipe_usager eu ON eu.tbl_usager_id = u.id
				LEFT JOIN tbl_equipe e ON e.id = eu.tbl_equipe_id
				WHERE
					(ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) <= '".$_GET['dateFin']." 23:00:00')
					AND u.actif = 1
					AND ((
						q.heureDebut <= TIME(ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')))
						AND ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) <
							IF(q.heureDebut>=q.heureFin,
								CONCAT(ADDDATE(DATE(ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),INTERVAL 1 DAY),' ',q.heureFin),
								CONCAT(DATE(ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),' ',q.heureFin))
						)
					OR
						(
						ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')) >=
						IF(q.heureDebut>=q.heureFin,
								CONCAT(SUBDATE(DATE(ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),INTERVAL 1 DAY),' ',q.heureDebut),
								CONCAT(DATE(ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00'))),' ',q.heureDebut))
						AND q.heureFin > TIME(ADDTIME('".$_GET['dateDebut']." 00:00:00', CONCAT('0 ',i4.i*1000+i3.i*100+i2.i*10+i1.i,':00:00')))
						)
					)
					AND e.siFDF = 1
					AND c.id = e.tbl_caserne_id
					".$inCondition."
					GROUP BY  c.id, Temps, q.id, u.id
					ORDER BY  c.id, e.nom, Temps, q.heureDebut, u.matricule";
			}
			$cn = Yii::app()->db;
			$cm = $cn->createCommand($sql);
			$dataDispo = $cm->query();			
			//On traite les données
			$row = $dataDispo->read();
			$dispos = array();
			if($_GET['regroupement']=='0'){
				//On lit le curseur SQL et on sort le data
				$i=0;
				$j=0;
				$casernes = array();
				$equipes = array();
				do{
					$caserne = $row['Caserne'];
					$casernes[$caserne] = array();
					$equipes[$caserne] = array();
					do{
						$temps = $row['Temps'];
						$date = substr($temps,0,10);
						$heure = substr($temps,11,5);
						if(isset($casernes[$caserne])){
						if(!in_array($date,$casernes[$caserne]))$casernes[$caserne][] = $date;
							do{
								if(!isset($dispos[$j])){
									$dispos[$j] = array();
								}
								if(!in_array($heure,$dispos[$j])){
									$dispos[$j][]=$heure;
								}
								if(!in_array($row['Equipe'],$equipes[$caserne])){
									$equipes[$caserne][] = $row['Equipe'];
								}
								$dispos[$j][] = $row['Dispo'];
								$row = $dataDispo->read();
							}while($temps == $row['Temps']);
							$j++;
						}else{
							$row = $dataDispo->read();
						}
					}while($caserne = $row['Caserne']);
				}while($row!==FALSE);
			}else{
				do{
					$caserne = $row['Caserne'];
					do{
						$equipe = $row['Equipe'];
						do{
							$temps = $row['Temps'];
							do{
								$usager = $row['Usager'];
								do{
									$dispos[$caserne][substr($temps,0,10)][$equipe][substr($temps,11,5)][$usager]['dispo'] = $row['Dispo'];
									$row = $dataDispo->read();
								}while($usager==$row['Usager']);
							}while($temps == $row['Temps']);
						}while($equipe == $row['Equipe']);
					}while($caserne = $row['Caserne']);
				}while($row!==FALSE);
				//echo '<pre>';print_r($dispo);echo '</pre>';
			}

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
			$objPHPExcel->getProperties()
				->setCreator("Studio Swordware Inc.")
				->setLastModifiedBy("Studio Swordware Inc.")
				->setTitle("Rapport")
				->setSubject("Rapport Force de Frappe")
				->setDescription("")
				->setKeywords("")
				->setCategory("");
			
			if($_GET['regroupement']=='0')
			{
				$graph = $objPHPExcel->getSheet(0);
				$graph->setTitle('Graphiques');
				
				$data = new PHPExcel_Worksheet($objPHPExcel, 'Données');			
				$objPHPExcel->addSheet($data, 1);
				
				$data->setCellValue('A1', 'Heure');
				$data->setCellValue('B1', 'Équipes');
				$data->fromArray($dispos, ' ', 'A2');
				
				$i=0;
				$k=0;
				$labelx = array(new PHPExcel_Chart_DataSeriesValues('String', 'Données!$A$2:$A$25', NULL, 24));
				foreach($casernes as $nom=>$dates){
					$data->setCellValue('A'.chr(65+$i).($i+1), $nom);
					$data->fromArray($equipes[$nom], ' ', 'AA'.($i+2));
					$nbrEquipe = count($dispos[$i])-1;
					$labelLegende = array();
					for($j=0;$j<$nbrEquipe;$j++){
						$labelLegende[] = new PHPExcel_Chart_DataSeriesValues('String', 'Données!$A'.chr(65+$j).'$'.($i+2), NULL, 1);
					}					
					foreach($dates as $date){
						$dataGraph = array();
						for($j=0;$j<$nbrEquipe;$j++){
							$dataGraph[] = new PHPExcel_Chart_DataSeriesValues('Number', 'Données!$'.chr(66+$j).'$'.($k*24+2).':$'.chr(66+$j).'$'.($k*24+25), NULL, 24);
						}
						$dataSerie=new PHPExcel_Chart_DataSeries(
								PHPExcel_Chart_DataSeries::TYPE_LINECHART,
								PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
								range(0, count($dataGraph)-1),
								$labelLegende,
								$labelx,
								$dataGraph
						);
						$plotArea=new PHPExcel_Chart_PlotArea(NULL, array($dataSerie));
						$legend=new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
						$title=new PHPExcel_Chart_Title($nom.' - '.$date);
						$chart= new PHPExcel_Chart(
								'chart',
								$title,
								$legend,
								$plotArea,
								true,
								0,
								NULL,
								NULL
						);
						$chart->setTopLeftPosition('A'.(1+$k*20));
						$chart->setBottomRightPosition('V'.(20+20*$k));
						$graph->addChart($chart);
						$k++;				
					}
					$i++;
				}
			}else{				
				$data = $objPHPExcel->getSheet(0);
				$data->setTitle('Rapport');
				$y = 1;
				$x = 0;
				foreach ($dispos as $nomCaserne => $caserne)
				{
					$data->SetCellValue(PHPExcel_Cell::stringFromColumnIndex('0').$y, $nomCaserne);
					$y ++;
					$showCaserne = true;
					foreach ($caserne as $dateJour => $jour)
					{
						$data->SetCellValue(PHPExcel_Cell::stringFromColumnIndex('0').$y, $dateJour);
						$y ++;
						$showDate = true;
						foreach ($jour as $nomEquipe => $equipe)
						{
							$yDebut = $y;
							$data->SetCellValue(PHPExcel_Cell::stringFromColumnIndex('0').$y, $nomEquipe);
							$y ++;
							$showPompier = true;
							foreach ($equipe as $heureJour => $heure)
							{
								if($showPompier)
								{
									$y ++;
								}
								$data->SetCellValue(PHPExcel_Cell::stringFromColumnIndex('0').$y, $heureJour);
								$x = 1;
								foreach ($heure as $nomPomier => $dispo)
								{
										
									if($showPompier)
									{
										$data->SetCellValue(PHPExcel_Cell::stringFromColumnIndex($x).($y-1), $nomPomier);
										$nbrPompierMax = count($heure);
									}
									if($dispo['dispo'])
									{
										$resultat = 'X';
									}
									else
									{
										$resultat = '';
									}
									$data->SetCellValue(PHPExcel_Cell::stringFromColumnIndex($x).$y, $resultat);
									$x ++;
								}
								$showPompier = false;
								$y ++;
							}
							$colonneMax=PHPExcel_Cell::stringFromColumnIndex($nbrPompierMax);
							$style = array(
									'alignment' => array(
											'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
									)
							);
							$data->getStyle('A'.($yDebut+1).':'.$colonneMax.($y-1))->applyFromArray($style);
							foreach(range('B',$colonneMax) as $columnID)
							{
								$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
								->setAutoSize(true);
							}
							$data->mergeCells('A'.$yDebut.':'.$colonneMax.$yDebut);
							$style = array(
									'alignment' => array(
											'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
									),
									'fill' => array(
											'type' => PHPExcel_Style_Fill::FILL_SOLID,
											'color' => array('rgb' => 'FF0000'),
									),
									'font'  => array(
											'bold'  => true,
											'color' => array('rgb' => 'FFFFFF'),
									),
							);
							$BStyle = array(
									'borders' => array(
											'inside' => array(
													'style' => PHPExcel_Style_Border::BORDER_THIN
											),
											'outline' => array(
													'style' => PHPExcel_Style_Border::BORDER_THICK
											)
									)
							);
							$data->getStyle('A'.$yDebut.':'.'A'.$yDebut)->applyFromArray($style);
							$data->getStyle('A'.$yDebut.':'.$colonneMax.($yDebut))->applyFromArray($BStyle);
							$data->getStyle('A'.$yDebut.':'.'A'.$yDebut)->getFont()->setBold(true);
							$data->getStyle('A'.($yDebut+1).':'.$colonneMax.($yDebut+25))->applyFromArray($BStyle);
							if($showDate)
							{
								$data->getStyle('A'.($yDebut-1))->getFont()->setSize(16);
								$data->getStyle('A'.($yDebut-1))->getFont()->setBold(true);
								$data->mergeCells('A'.($yDebut-1).':'.$colonneMax.($yDebut-1));
								$style = array(
										'alignment' => array(
												'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
										)
								);
								$data->getStyle('A'.($yDebut-1).':'.'A'.($yDebut-1))->applyFromArray($style);
								$showDate = false;
							}
							if($showCaserne)
							{
								$data->getStyle('A'.($yDebut-2))->getFont()->setSize(20);
								$data->getStyle('A'.($yDebut-2))->getFont()->setBold(true);
								$data->mergeCells('A'.($yDebut-2).':'.$colonneMax.($yDebut-2));
								$style = array(
										'alignment' => array(
												'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
										)
								);
								$data->getStyle('A'.($yDebut-2).':'.'A'.($yDebut-2))->applyFromArray($style);
								$showCaserne = false;
							}
						}
					}
				}				
			}
			
			header("Pragma: public");
			header("Expires: 0");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");;
			header("Content-Disposition: attachment;filename=rapportFDF.xlsx");
			header("Content-Transfer-Encoding: binary ");

			spl_autoload_register(array('YiiBase','autoload'));
			
			//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->setOffice2003Compatibility(true);
			$objWriter->setIncludeCharts(true);
			$objWriter->save('php://output');
			
			Yii::app()->end();
		}else{
			echo 'date';
		}
	}
}