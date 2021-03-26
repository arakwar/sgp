<?php

class EvenementController extends Controller
{
	public $pageTitle = 'Événement';
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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
			array('allow', // Permet à tous les usagers de voir le calendrier des évènements
				'actions'=>array('index','dispo','view','coche','decoche','getEvenementInfo'),
				'roles'=>array('Evenement:index'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','delete','usagerGroupe','validation'),
				'roles'=>array('Evenement:create'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	private function compteEvenement(&$tE,$key,$suivi,$original){
		$retour = array();
		foreach ($tE as $k2 => $val) {
			if($val['dateDebut'] < $tE[$key]['dateFin'] &&
				 $val['dateFin']   > $tE[$key]['dateDebut'] &&
				 $val['id'] != $tE[$key]['id']              &&
				 $val['aujourdhui'] == $tE[$key]['aujourdhui'] &&
				 $val['dateDebut'] < $tE[$original]['dateFin'] &&
				 $val['dateFin']   > $tE[$original]['dateDebut'] &&
				 !in_array($k2, $suivi)
				){
				$suivi[] = $k2;
				$retour[] = 1+$this->compteEvenement($tE,$k2,$suivi,$original);
			}
		}
		if(count($retour)>0){
			rsort($retour,SORT_NUMERIC);
			return $retour[0];
		}else{
			return 0;
		}
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		if(Yii::app()->params['moduleEvenement']){
			$usagers = EvenementUsager::model()->findAllByAttributes(array('tbl_evenement_id'=>$id));
			$lstUsagers = array();
			$lstResultats = array();
			foreach($usagers as $usager){
				$usa = Usager::model()->findByPk($usager->tbl_usager_id);
				$lstUsagers[] = $usa->getMatPrenomNom();
				if($usager->resultat==1)
					$lstResultats[] = $usa->getMatPrenomNom();
			}
			sort($lstUsagers);
						
			
			$this->render('view',array(
				'model'=>$this->loadModel($id),
				'lstUsagers'=>$lstUsagers,
				'lstResultats'=>$lstResultats,
			));
		}else{
			$this->redirect(array('site/index'));
		}
	}
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionDispo($date="",$idUsager="", $caserne="")
	{
		if(Yii::app()->params['moduleEvenement']){
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
			
			$casernes = Caserne::model()->findAll(array('condition'=>'id IN ('.$casernesUsager.') AND siActif = 1'));
			$tblCaserne = CHtml::listData($casernes,'id','nom');
			
			if($caserne==""){
				foreach($casernes as $cas){
						$caserne = $cas->id;
						break;
					}
			}
			
			//Sort les équipes de garde de la caserne
			$tblEquipe = Equipe::model()->findAll('siHoraire = 1 AND tbl_caserne_id = :caserne ',array(':caserne'=>$caserne));
			
			$idsE = '';
			foreach($tblEquipe as $equipe){
				$idsE .= $equipe->id.' ,';
			}
			$idsE = substr($idsE, 0, strlen($idsE)-2);
			
			$parametres = Parametres::model()->findByPk(1);
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
			
			$dateDebut = Horaire::debutPeriode($parametres->nbJourPeriode,$parametres->moduloDebut,$date);
			$datePrecedente = date_sub(clone $dateDebut,new DateInterval('P'.$parametres->nbJourPeriode.'D'));
			$dateMax = date_add(new DateTime('now',new DateTimeZone($parametres->timezone)),new DateInterval('P'.$parametres->moisFDF.'M'));
			$dateMax->sub(new DateInterval('P'.$parametres->nbJourPeriode.'D'));
	
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
				LEFT JOIN tbl_dispo_evenement dh ON
					(dh.date = ADDDATE('".$strDateDebut."',i2.i*10+i1.i) AND dh.tbl_usager_id=".$usager->id.")
					AND (dh.tsDebut>=CONCAT(ADDDATE('".$strDateDebut."',i2.i*10+i1.i), ' ', q.heureDebut) AND dh.tsDebut < IF(q.heureFin<q.heureDebut, ADDTIME(CONCAT(ADDDATE('".$strDateDebut."',i2.i*10+i1.i), ' ', q.heureFin),'24:00:00'), CONCAT(ADDDATE('".$strDateDebut."',i2.i*10+i1.i), ' ', q.heureFin)))
					AND dh.dispo = 0
				LEFT JOIN tbl_equipe_garde eg ON q.id=eg.tbl_quart_id
					AND eg.modulo=MOD((UNIX_TIMESTAMP(ADDDATE('".$strDateDebut."', i2.i*10+i1.i)) DIV 86400),".$garde->nbr_jour.") AND eg.tbl_garde_id = ".$parametres->garde_horaire."
				LEFT JOIN tbl_equipe e ON e.id=eg.tbl_equipe_id
			WHERE
				ADDDATE('".$strDateDebut."',i2.i*10+i1.i) < date_add('".$strDateDebut."',INTERVAL ".$parametres->nbJourPeriode." DAY)
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
					'curseurDispo' 	  => $curseurDispo,
					'tblUsager'		  => $tblUsager,
					'parametres'      => $parametres,
					'jourSemaine'     => $jourSemaine,
					'usager'		  => $usager,
					'tblCaserne'	  => $tblCaserne,
					'caserne'		  => $caserne,
					'tblCasernesDisabled' => $tblCasernesDisabled,
					'garde'				=> $garde,
			));
		}else{
			$this->redirect(array('site/index'));
		}		
	}
	
	public function actionCoche($date, $idQuart, $idUsager, $heureDebut="", $heureFin="", $idDispo="", $idAncienDispo=""){
		$parametres = Parametres::model()->findByPk(1);
		if(!Usager::peutGerer($idUsager)){
			throw new CHttpException(403, Yii::t('erreur','erreur403'));
			Yii::app()->end();
		}
		$transaction = Yii::app()->db->beginTransaction();
		$quart = Quart::model()->findByPk($idQuart);
		if($idDispo==""){
			$dispoCriteria = new CDbCriteria;
			$dispoCriteria->condition = 'tbl_quart_id = :quart AND date = :date AND tbl_usager_id = :usager';
			$dispoCriteria->params = array(':quart'=>$idQuart, ':date'=>$date, ':usager'=>$idUsager);
			$dispo = DispoEvenement::model()->find($dispoCriteria);
				
			if($dispo === NULL){
				$dispo = new DispoEvenement;
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
					$dispo = new DispoEvenement;
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
						$ancienDispo = DispoEvenement::model()->find('id=:id',array(':id'=>$idAD));
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
			$dispo = DispoEvenement::model()->find('id=:id',array(":id"=>$idDispo));
				
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
				$dispo = new DispoEvenement;
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
					$ancienDispo = DispoEvenement::model()->find('id=:id',array(':id'=>$idAncienDispo));
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
	
		$tblDispoId = json_decode($strDispoId);
		$transaction = Yii::app()->db->beginTransaction();
		$dispoTest = DispoEvenement::model()->find("id=:id",array(':id'=>$tblDispoId[0]));
		$retour = '';
		if(!Usager::peutGerer($dispoTest->tbl_usager_id)){
			throw new CHttpException(403,Yii::t('erreur','erreur403'));
			Yii::app()->end();
		}
		foreach($tblDispoId as $id){
			$date = '1990-01-01';
			$dateDebut = Horaire::debutPeriode($parametres->nbJourPeriode,$parametres->moduloDebut,$date);
			$dateFin = date_add(clone $dateDebut,new DateInterval('P'.($parametres->nbJourPeriode-1).'D'));
	
			$defaut = false;
			if($dispoTest->date >= $dateDebut->format('Y-m-d') AND $dispoTest->date <=$dateFin->format('Y-m-d')){
				$defaut = true;
			}
				
			$dispo = DispoEvenement::model()->find("id=:id",array(':id'=>$id));
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

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */

	public function actionCreate(){
		if(Yii::app()->params['moduleEvenement']){
			$model=new Evenement;
	
			// Uncomment the following line if AJAX validation is needed
			// $this->performAjaxValidation($model);
	
			if(isset($_POST['Evenement']))
			{
				$model->attributes=$_POST['Evenement'];
				$model->tbl_formation_id = '0';
				if($model->save()){
					$this->redirect(array('update','id'=>$model->id));
				}
			}
	
			$this->render('create',array(
				'model'=>$model,
				'lstGroupeF'=>array(),
				'lstUsagers'=>array(),
				'lstUsagersDispo'=>array(),
				'lstPreRequis'=>array(),
			));
		}else{
			$this->redirect(array('site/index'));
		}
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id){
		if(Yii::app()->params['moduleEvenement']){
			$model=$this->loadModel($id);		
	
			$parametres = Parametres::model()->findByPk(1);
	
			// Uncomment the following line if AJAX validation is needed
			// $this->performAjaxValidation($model);
	
			$lstUsagersDispo = array();
			
			$dateDebutTS = date('Y-m-d H:i:s',strtotime($model->dateDebut));
			$dateFinTS = date('Y-m-d H:i:s',strtotime($model->dateFin));
			
			$dateDebut = new DateTime($dateDebutTS,new DateTimeZone('America/Montreal'));
			$dateFin = new DateTime($dateFinTS,new DateTimeZone('America/Montreal'));
			
			$lstUsagers = array();
			$usagers = Usager::model()->findAll(array('condition'=>'tempsPlein<>2 AND enService = 1', 'order'=>'matricule ASC'));
			foreach($usagers as $usager){
				$lstUsagers[$usager->id] = $usager->getMatPrenomNom();
			}
			
			$lstInvites = array();
			$invites = Usager::model()->findAll(array('condition'=>'tempsPlein=2 AND enService = 1', 'order'=>'matricule ASC'));
			foreach($invites as $invite){
				$lstInvites[$invite->id] = $invite->getMatPrenomNom();
			}
			
			$lstPreRequis = array();		
			if($model->tbl_formation_id !=0){
				$prerequis = FormationPreRequis::model()->findAllByAttributes(array('tbl_formation_id'=>$model->tbl_formation_id));
				$idprerequis = array();
				foreach($prerequis as $pre){
					$idprerequis[]=$pre->tbl_formation_pre;
				}
				if(count($idprerequis)>0){
					foreach($usagers as $usager){
						$criteria = new CDbCriteria;
						$criteria->alias = 'eu';
						$criteria->condition = 'eu.tbl_usager_id = '.$usager->id.' AND eu.resultat = 1';
						$criteria->join = 'LEFT JOIN tbl_evenement e ON eu.tbl_evenement_id = e.id LEFT JOIN tbl_formation f ON f.id = e.tbl_formation_id';
						$criteria->addInCondition('f.id', $idprerequis);	
						$retour = EvenementUsager::model()->findAll($criteria);
						if(empty($retour)){
							$lstPreRequis[]=$usager->id;
						}
					}
					foreach($invites as $invite){
						$criteria = new CDbCriteria;
						$criteria->alias = 'eu';
						$criteria->condition = 'eu.tbl_usager_id = '.$invite->id.' AND eu.resultat = 1';
						$criteria->join = 'LEFT JOIN tbl_evenement e ON eu.tbl_evenement_id = e.id LEFT JOIN tbl_formation f ON f.id = e.tbl_formation_id';
						$criteria->addInCondition('f.id', $idprerequis);
						$retour = EvenementUsager::model()->findAll($criteria);
						if(empty($retour)){
							$lstPreRequis[]=$usager->id;
						}
					}
				}
			}
			
			$phpdate = strtotime($model->dateDebut);
			$date = date('Y-m-d', $phpdate );
			$heureDebut = date('H:i:s', $phpdate);
			$phpdate = strtotime($model->dateFin);
			$heureFin = date('H:i:s', $phpdate);
			
			/*Ce SQL sort les usagers qui ne sont pas de garde pendant l'évènement*/
			$sql = "
	SELECT * 
	FROM tbl_usager t 
	WHERE 
		t.id NOT IN (
		SELECT h.tbl_usager_id 
		FROM `tbl_horaire` h 
		LEFT JOIN tbl_poste_horaire ph ON ph.id = h.tbl_poste_horaire_id 
		LEFT JOIN tbl_quart q ON q.id = ph.tbl_quart_id 
		WHERE 
			h.date = '".$dateDebut->format('Y-m-d')."'
			AND(
				(
					CONCAT(
						IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',
							IF(ph.heureDebut<ph.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
								IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',
									IF(ph.heureDebut<ph.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
										IF(q.heureDebut<q.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."')
								)
						)
						,' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut))
					)
					<=
					CONCAT('".$dateDebut->format('Y-m-d')."',' ', '".$heureDebut."')
					AND
					CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin)))
					>
					CONCAT('".$dateDebut->format('Y-m-d')."',' ', '".$heureDebut."')		
				)
				OR
				(
					CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut)))
					<
					CONCAT('".$dateDebut->format('Y-m-d')."',' ', '".$heureFin."')
					AND
					CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin)))
					>=
					CONCAT(					
						IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',
							IF(ph.heureDebut<ph.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
								IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',
									IF(ph.heureDebut<ph.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
										IF(q.heureDebut<q.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."')
								)
						),' ', '".$heureFin."')
				)
				OR(
					CONCAT('".$dateDebut->format('Y-m-d')."',' ','".$heureDebut."')<=CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut)))
					AND
					CONCAT(IF('".$heureFin."'<'".$heureDebut."',DATE_ADD('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY),'".$dateDebut->format('Y-m-d')."'),' ','".$heureFin."')
					>=
					CONCAT(IF(q.heureDebut<q.heureFin, DATE_ADD('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin)))
				)
			)
			AND h.id NOT IN(
			SELECT h2.parent_id
			FROM tbl_horaire h2
			LEFT JOIN tbl_poste_horaire ph2 ON ph2.id = h2.tbl_poste_horaire_id 
			LEFT JOIN tbl_quart q2 ON q2.id = ph2.tbl_quart_id
			WHERE
				h2.type <> 0
				AND h2.date = '".$dateDebut->format('Y-m-d')."'
				AND(
					(
						CONCAT(
							IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',
								IF(ph2.heureDebut<ph2.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
									IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',
										IF(ph2.heureDebut<ph2.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
											IF(q2.heureDebut<q2.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."')
									)
							)
							,' ',IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',h2.heureDebut,IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',ph2.heureDebut,q2.heureDebut))
						)
						<=
						CONCAT('".$dateDebut->format('Y-m-d')."',' ', '".$heureDebut."')
						AND
						CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',h2.heureFin,IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',ph2.heureFin,q2.heureFin)))
						>
						CONCAT('".$dateDebut->format('Y-m-d')."',' ', '".$heureDebut."')		
					)
					OR
					(
						CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',h2.heureDebut,IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',ph2.heureDebut,q2.heureDebut)))
						<
						CONCAT('".$dateDebut->format('Y-m-d')."',' ', '".$heureFin."')
						AND
						CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',h2.heureFin,IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',ph2.heureFin,q2.heureFin)))
						>=
						CONCAT(					
							IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',
								IF(ph2.heureDebut<ph2.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
									IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',
										IF(ph2.heureDebut<ph2.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
											IF(q2.heureDebut<q2.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."')
									)
							),' ', '".$heureFin."')
					)
					OR(
						CONCAT('".$dateDebut->format('Y-m-d')."',' ','".$heureDebut."')<=CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',h2.heureDebut,IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',ph2.heureDebut,q2.heureDebut)))
						AND
						CONCAT(IF('".$heureFin."'<'".$heureDebut."',DATE_ADD('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY),'".$dateDebut->format('Y-m-d')."'),' ','".$heureFin."')
						>=
						CONCAT(IF(q2.heureDebut<q2.heureFin, DATE_ADD('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),' ',IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',h2.heureFin,IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',ph2.heureFin,q2.heureFin)))
					)
				)		
			
			)
		)
		AND t.id IN (
			SELECT d.tbl_usager_id
			FROM ".(($parametres->eve_dispo==0)?"tbl_dispo_evenement":"tbl_dispo_horaire")." d 
			WHERE
				('".$dateDebut->getTimestamp()."' >= UNIX_TIMESTAMP(d.tsDebut) AND '".$dateDebut->getTimestamp()."' < UNIX_TIMESTAMP(d.tsFin))
				OR
				('".$dateFin->getTimestamp()."' > UNIX_TIMESTAMP(d.tsDebut) AND '".$dateFin->getTimestamp()."' <= UNIX_TIMESTAMP(d.tsFin))
				AND d.dispo = 0		
		)
		AND t.actif = 1 
	ORDER BY t.matricule ASC";
			$usagers = Usager::model()->findAllBySql($sql);
			foreach($usagers as $usager){
				$lstUsagersDispo[$usager->id] = $usager->id;
			}
			
			/*$criteria = new CDbCriteria;
			$criteria->alias = 't';
			$criteria->join = 'LEFT JOIN '.(($parametres->eve_dispo==0)?'tbl_dispo_evenement':'tbl_dispo_horaire').' d ON t.id = d.tbl_usager_id';
			$criteria->condition = 'UNIX_TIMESTAMP(d.tsDebut) <='.$dateDebut->getTimestamp().' AND UNIX_TIMESTAMP(d.tsFin) >='.$dateFin->getTimestamp().' AND d.dispo = 0';
			$criteria->order = 'matricule ASC';
			$usagers = Usager::model()->findAll($criteria);
			foreach($usagers as $usager){
				if(!in_array($usager->id,$lstUsagersDispo)){
					$lstUsagersDispo[$usager->id] = $usager->id;
				}
			}*/
			
			$criteria = new CDbCriteria;
			$criteria->order = 'nom ASC';
			
			$groupeF = GroupeFormation::model()->findAll($criteria);
			$lstGroupeF = CHtml::listData($groupeF, 'id', 'nom');
			
			$criteria = new CDbCriteria;
			$criteria->condition = 'siActif = 1';
			$criteria->order = 'nom ASC';
			
			$groupeE = Equipe::model()->findAll($criteria);
			$lstGroupeE = CHtml::listData($groupeE, 'id', 'nom');
			
			if(isset($_POST['Evenement']))
			{
				$model->attributes=$_POST['Evenement'];
				if($model->save()){
					EvenementUsager::model()->deleteAll(array('condition'=>'tbl_evenement_id='.$model->id));
					$pass = 0;
					if(isset($_POST['tblUsagers'])){
						foreach($_POST['tblUsagers'] as $usager){
							$eveUser = new EvenementUsager;
				
							$eveUser->tbl_evenement_id = $id;
							$eveUser->tbl_usager_id = $usager;
				
							$eveUser->save();
							if(in_array($usager,$lstPreRequis) || !in_array($usager, $lstUsagersDispo)){
								$pass = 1;
							}
						}
					}
					if(isset($_POST['tblInvites'])){
						foreach($_POST['tblInvites'] as $usager){
							$eveUser = new EvenementUsager;
								
							$eveUser->tbl_evenement_id = $id;
							$eveUser->tbl_usager_id = $usager;
								
							$eveUser->save();
							if(in_array($usager,$lstPreRequis) || !in_array($usager, $lstUsagersDispo)){
								$pass = 1;
							}
						}
					}
					if($pass==0){
						$this->redirect(array('view','id'=>$model->id));
					}elseif($pass==1){
						$this->redirect(array('validation','id'=>$model->id));
					}
				}
			}
	
			$this->render('update',array(
				'model'=>$model,
				'lstGroupeF' => $lstGroupeF,
				'lstGroupeE' => $lstGroupeE,
				'lstUsagers'=>$lstUsagers,
				'lstInvites'=>$lstInvites,
				'lstUsagersDispo'=>$lstUsagersDispo,
				'lstPreRequis'=>$lstPreRequis,
			));
		}else{
			$this->redirect(array('site/index'));
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->params['moduleEvenement']){
			$model = $this->loadModel($id);

			$model->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}else{
			$this->redirect(array('site/index'));
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($evenementfiltre="0", $pourfiltre="0")
	{
		if(Yii::app()->params['moduleEvenement']){
			if(!Yii::app()->request->isAjaxRequest){
				$this->render('index',array(
						'filtre' =>$evenementfiltre,
				));
			} else {
				$criteria = new CDbCriteria;
				$criteria->condition = (($evenementfiltre!="0")?'tbl_formation_id <> 0 ':'');
				if(!Yii::app()->user->checkAccess('Evenement:create'))
				{
					$criteria->join='LEFT JOIN tbl_evenement_usager as eu ON eu.tbl_evenement_id=t.Id ';
					if($criteria->condition != "")
						$criteria->condition .= "AND ";
					$criteria->condition .='tbl_usager_id ='.Yii::app()->user->id;
				}
				else if($pourfiltre)
				{
					$criteria->join='LEFT JOIN tbl_evenement_usager as eu ON eu.tbl_evenement_id=t.Id ';
					if($criteria->condition != "")
						$criteria->condition .= "AND ";
					$criteria->condition .='tbl_usager_id ='.Yii::app()->user->id;
				}
				$evenements = Evenement::model()->findAll($criteria);
				$items = array();
				foreach ($evenements as $key => $evenement)
				{
					if($evenement->tbl_formation_id == 1)
					{
						$styleBackGroundColor = '#0099FF';
					}
					else
					{
						$styleBackGroundColor = '#00CC33';
					}
					$items[]=array(
							'id'=>$evenement->id,
							'title'=>$evenement->nom,
							'start'=>$evenement->dateDebut,
							'end'=>$evenement->dateFin,
							'color'=>$styleBackGroundColor,
							'className' =>'over',
					);
				}
				echo CJSON::encode($items);
			}
		}else{
			$this->redirect(array('site/index'));
		}			
	}

	/*
	 * Va chercher les usagers d'un groupe 
	*/
	public function actionUsagerGroupe(){
		if(isset($_POST['idGroupe'])){
			if($_POST['type']=='F'){
				$usagers = GroupeFormationUsager::model()->findAllByAttributes(array('tbl_groupe_formation_id'=>$_POST['idGroupe']));
			}else{
				$usagers = EquipeUsager::model()->findAllByAttributes(array('tbl_equipe_id'=>$_POST['idGroupe']));
			}
			$usagerID = array();
			foreach($usagers as $usager){
				$usagerID[] = $usager->tbl_usager_id;
			}
			
			$retour = json_encode($usagerID);
			
			echo $retour;
		}
	}
	
	/*
	 * 	 
	 * Valider si les usagers non-dispos ou qui n'ont pas les pré-requis sont correct.
	 * 
	 */
	public function actionValidation($id){
		$model = $this->loadModel($id);
		
		$parametres = Parametres::model()->findByPk(1);		
		
		$lstUsagerNDispo = array();
		$lstUsagerPreRequis = array();
		$lstUsagerNDispoID = array();
		$lstUsagerPreRequisID = array();
		
		$dateDebutTS = date('Y-m-d H:i:s',strtotime($model->dateDebut));
		$dateFinTS = date('Y-m-d H:i:s',strtotime($model->dateFin));
		
		$date = date('Y-m-d',strtotime($model->dateDebut));
		$heureDebut = date('H:i:s',strtotime($model->dateDebut));
		$heureFin = date('H:i:s',strtotime($model->dateFin));
		
		$dateDebut = new DateTime($dateDebutTS,new DateTimeZone('America/Montreal'));
		$dateFin = new DateTime($dateFinTS,new DateTimeZone('America/Montreal'));
		
		$usagerEvenement = '';
		foreach($model->tblUsagers as $usager){
			$usagerEvenement .= $usager->id.', ';
		}
		$usagerEvenement = substr($usagerEvenement, 0, strlen($usagerEvenement)-2);
		
		/*Ce SQL sort les usagers qui sont de garde pendant l'évènement*/	
		$sql = "
SELECT * 
FROM tbl_usager t 
WHERE(
		t.id IN (
		SELECT h.tbl_usager_id 
		FROM `tbl_horaire` h 
		LEFT JOIN tbl_poste_horaire ph ON ph.id = h.tbl_poste_horaire_id 
		LEFT JOIN tbl_quart q ON q.id = ph.tbl_quart_id 
		WHERE 
			h.date = '".$dateDebut->format('Y-m-d')."'
			AND(
				(
					CONCAT(
						IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',
							IF(ph.heureDebut<ph.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
								IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',
									IF(ph.heureDebut<ph.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
										IF(q.heureDebut<q.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."')
								)
						)
						,' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut))
					)
					<=
					CONCAT('".$dateDebut->format('Y-m-d')."',' ', '".$heureDebut."')
					AND
					CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin)))
					>
					CONCAT('".$dateDebut->format('Y-m-d')."',' ', '".$heureDebut."')		
				)
				OR
				(
					CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut)))
					<
					CONCAT('".$dateDebut->format('Y-m-d')."',' ', '".$heureFin."')
					AND
					CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin)))
					>=
					CONCAT(					
						IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',
							IF(ph.heureDebut<ph.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
								IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',
									IF(ph.heureDebut<ph.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
										IF(q.heureDebut<q.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."')
								)
						),' ', '".$heureFin."')
				)
				OR(
					CONCAT('".$dateDebut->format('Y-m-d')."',' ','".$heureDebut."')<=CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureDebut,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureDebut,q.heureDebut)))
					AND
					CONCAT(IF('".$heureFin."'<'".$heureDebut."',DATE_ADD('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY),'".$dateDebut->format('Y-m-d')."'),' ','".$heureFin."')
					>=
					CONCAT(IF(q.heureDebut<q.heureFin, DATE_ADD('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),' ',IF(h.heureDebut<>'00:00:00' AND h.heureFin <> '00:00:00',h.heureFin,IF(ph.heureDebut<>'00:00:00' AND ph.heureFin <> '00:00:00',ph.heureFin,q.heureFin)))
				)
			)
			AND h.id NOT IN(
			SELECT h2.parent_id
			FROM tbl_horaire h2
			LEFT JOIN tbl_poste_horaire ph2 ON ph2.id = h2.tbl_poste_horaire_id 
			LEFT JOIN tbl_quart q2 ON q2.id = ph2.tbl_quart_id
			WHERE
				h2.type <> 0
				AND h2.date = '".$dateDebut->format('Y-m-d')."'
				AND(
					(
						CONCAT(
							IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',
								IF(ph2.heureDebut<ph2.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
									IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',
										IF(ph2.heureDebut<ph2.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
											IF(q2.heureDebut<q2.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."')
									)
							)
							,' ',IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',h2.heureDebut,IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',ph2.heureDebut,q2.heureDebut))
						)
						<=
						CONCAT('".$dateDebut->format('Y-m-d')."',' ', '".$heureDebut."')
						AND
						CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',h2.heureFin,IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',ph2.heureFin,q2.heureFin)))
						>
						CONCAT('".$dateDebut->format('Y-m-d')."',' ', '".$heureDebut."')		
					)
					OR
					(
						CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',h2.heureDebut,IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',ph2.heureDebut,q2.heureDebut)))
						<
						CONCAT('".$dateDebut->format('Y-m-d')."',' ', '".$heureFin."')
						AND
						CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',h2.heureFin,IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',ph2.heureFin,q2.heureFin)))
						>=
						CONCAT(					
							IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',
								IF(ph2.heureDebut<ph2.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
									IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',
										IF(ph2.heureDebut<ph2.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),
											IF(q2.heureDebut<q2.heureFin, DATE_SUB('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."')
									)
							),' ', '".$heureFin."')
					)
					OR(
						CONCAT('".$dateDebut->format('Y-m-d')."',' ','".$heureDebut."')<=CONCAT('".$dateDebut->format('Y-m-d')."',' ',IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',h2.heureDebut,IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',ph2.heureDebut,q2.heureDebut)))
						AND
						CONCAT(IF('".$heureFin."'<'".$heureDebut."',DATE_ADD('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY),'".$dateDebut->format('Y-m-d')."'),' ','".$heureFin."')
						>=
						CONCAT(IF(q2.heureDebut<q2.heureFin, DATE_ADD('".$dateDebut->format('Y-m-d')."', INTERVAL 1 DAY), '".$dateDebut->format('Y-m-d')."'),' ',IF(h2.heureDebut<>'00:00:00' AND h2.heureFin <> '00:00:00',h2.heureFin,IF(ph2.heureDebut<>'00:00:00' AND ph2.heureFin <> '00:00:00',ph2.heureFin,q2.heureFin)))
					)
				)		
			
			)
		)
		OR t.id NOT IN (
			SELECT d.tbl_usager_id
			FROM ".(($parametres->eve_dispo==0)?"tbl_dispo_evenement":"tbl_dispo_horaire")." d 
			WHERE
				('".$dateDebut->getTimestamp()."' >= UNIX_TIMESTAMP(d.tsDebut) AND '".$dateDebut->getTimestamp()."' < UNIX_TIMESTAMP(d.tsFin))
				OR
				('".$dateFin->getTimestamp()."' > UNIX_TIMESTAMP(d.tsDebut) AND '".$dateFin->getTimestamp()."' <= UNIX_TIMESTAMP(d.tsFin))
				AND d.dispo = 0		
		)
	)	
	AND t.id IN (".$usagerEvenement.")
	AND t.actif = 1 
ORDER BY t.matricule ASC";
		$usagers = Usager::model()->findAllBySql($sql);
		foreach($usagers as $usager){
			$lstUsagerNDispo[$usager->id] = $usager->getMatPrenomNom();
			$lstUsagerNDispoID[$usager->id] = $usager->id;
		}
		
		if($model->tbl_formation_id !=0){
			$criteria = new CDbCriteria;
			$criteria->alias = 't';
			$criteria->join = 'LEFT JOIN tbl_evenement_usager ue ON ue.tbl_usager_id = t.id';
			$criteria->condition = 'ue.tbl_evenement_id = '.$model->id;
			$criteria->addNotInCondition('t.id', $lstUsagerNDispoID);
				
			$usagers = Usager::model()->findAll($criteria);
				
			$prerequis = FormationPreRequis::model()->findAllByAttributes(array('tbl_formation_id'=>$model->tbl_formation_id));
			$idprerequis = array();
			foreach($prerequis as $pre){
				$idprerequis[]=$pre->tbl_formation_pre;
			}
			if(count($idprerequis)>0){
				foreach($usagers as $usager){
					$criteria = new CDbCriteria;
					$criteria->alias = 'eu';
					$criteria->condition = 'eu.tbl_usager_id = '.$usager->id.' AND eu.resultat = 1';
					$criteria->join = 'LEFT JOIN tbl_evenement e ON eu.tbl_evenement_id = e.id LEFT JOIN tbl_formation f ON f.id = e.tbl_formation_id';
					$criteria->addInCondition('f.id', $idprerequis);
					$retour = EvenementUsager::model()->findAll($criteria);
					if(empty($retour)){
						$lstUsagerPreRequis[$usager->id]=$usager->getMatPrenomNom();
						$lstUsagerPreRequisID[$usager->id]=$usager->id;
					}
				}
			}
		}
		
		if(isset($_POST['validation'])){
			$lstUsager = array();
			foreach($lstUsagerNDispoID as $usager){
				if(isset($_POST['Dispo'])){
					if(!in_array($usager, $_POST['Dispo'])){
						$lstUsager [] = $usager;
					}
				}else{
					$lstUsager [] = $usager;
				}
			}
			foreach($lstUsagerPreRequisID as $usager){
				if(isset($_POST['Prerequis'])){
					if(!in_array($usager, $_POST['Prerequis']) && !in_array($usager, $lstUsager)){
						$lstUsager [] = $usager;
					}
				}else{
					if(!in_array($usager, $lstUsager)){
						$lstUsager [] = $usager;
					}
				}
			}
			foreach($lstUsager as $usager){
				$usagerEve = EvenementUsager::model()->find(array('condition'=>'tbl_usager_id = '.$usager.' AND tbl_evenement_id = '.$model->id));
				$usagerEve->delete();
			}
			
			$this->redirect(array('view','id'=>$model->id));
		}
		
		$this->render('validation',array(
				'model'=>$model,
				'lstUsagerNDispo'=>$lstUsagerNDispo,
				'lstUsagerPreRequis'=>$lstUsagerPreRequis,
		));		
	}

	public function actionGetEvenementInfo($id)
	{
		if(Yii::app()->user->checkAccess('Evenement:index'))
		{
			$evenement = Evenement::model()->findByPk($id);
			$this->renderPartial('_evenementInfo', array('evenement' => $evenement));
		}
	}
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Evenement::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404, Yii::t('erreur','erreur404'));
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='evenement-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
