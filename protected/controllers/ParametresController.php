<?php

class ParametresController extends Controller
{
	public $pageTitle = "Paramètres";
	private $debut = '#Les lignes suivantes sont gerees automatiquement via un script PHP. - Merci de ne pas editer manuellement';
	private $fin = '#Les lignes suivantes ne sont plus gerees automatiquement';
	
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
						'actions'=>array('index','save','cron','genereUsager'),
						'roles'=>array('Parametres:index'),
				),
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}
	
	public function actionIndex()
	{
		//on charge les informations des paramètres.
		$model=Parametres::model()->find('',array('limit'=>1));
		
		if($model===null) 
			$model = new Parametres;
			
		$listeCol = array();
		$listeCol[0]['label'] = 'Nom';$listeCol[0]['value'] = 'nom';
		$listeCol[1]['label'] = 'Matricule';$listeCol[1]['value'] = 'matricule';
		$listeCol[2]['label'] = 'Date d\'embauche';$listeCol[2]['value'] = 'dateEmbauche';
		
		$listeOrd = array();
		$listeOrd[0]['label'] = 'Croissant';$listeOrd[0]['value'] = 'ASC';
		$listeOrd[1]['label'] = 'Décroissant';$listeOrd[1]['value'] = 'DESC';
		
		$listeDef = array();
		$listeDef[0]['label'] = 'Disponible';$listeDef[0]['value'] = '0';
		$listeDef[1]['label'] = 'Non-disponible';$listeDef[1]['value'] = '1';	

		$listeAff = array();
		$listeAff[0]['label'] = 'Par caserne';$listeAff[0]['value'] = '0';
		$listeAff[1]['label'] = 'Tous';$listeAff[1]['value'] = '1';
		
		$listeGEHdate = array();
		$listeGEHdate[0]['label'] = 'Date du jour';$listeGEHdate[0]['value'] = '0';
		$listeGEHdate[1]['label'] = 'Début de la période';$listeGEHdate[1]['value'] = '1';
		
		$listeGEHnbrP = array();
		$listeGEHnbrP[0]['label'] = '1';$listeGEHnbrP[0]['value'] = '1';
		$listeGEHnbrP[1]['label'] = '2';$listeGEHnbrP[1]['value'] = '2';
		
		$listeEveDispo = array();
		$listeEveDispo[0]['label'] = 'Dispo évènement';$listeEveDispo[0]['value'] = '0';
		$listeEveDispo[1]['label'] = 'Dispo horaire';$listeEveDispo[1]['value'] = '1';
		
		$listeGEStyle = array();
		$listeGEStyle[0]['label'] = 'Normal';$listeGEStyle[0]['value'] = '0';
		$listeGEStyle[1]['label'] = 'Grand';$listeGEStyle[1]['value'] = '1';
		
		$listeOuiNon = array();
		$listeOuiNon[0]['label'] = 'Oui';$listeOuiNon[0]['value'] = '0';
		$listeOuiNon[1]['label'] = 'Non';$listeOuiNon[1]['value'] = '1';
		
		$listeDroitDispoHoraire = array();
		$listeDroitDispoHoraire[0]['label'] = 'Gestionnaire';$listeDroitDispoHoraire[0]['value'] = '0';
		$listeDroitDispoHoraire[1]['label'] = 'Tous';$listeDroitDispoHoraire[1]['value'] = '1';
		
		$listeHoraireCalcH = array();
		$listeHoraireCaclH[0]['label'] = 'Semaine/Période';$listeHoraireCalcH[0]['value'] = '0';
		$listeHoraireCalcH[1]['label'] = 'Période/Année';$listeHoraireCalcH[1]['value'] = '1';

		$criteriaCaserne = new CDbCriteria;
		$criteriaCaserne->condition = 'siActif = 1';
		$casernes = Caserne::model()->findAll($criteriaCaserne);

		$tblCaserne = CHtml::listData($casernes,'id','nom');	
		$listeColonne = CHtml::listData($listeCol, 'value', 'label');
		$listeOrdre = CHtml::listData($listeOrd, 'value', 'label');
		$listeDefaut = CHtml::listData($listeDef, 'value', 'label');
		$listeAffichage = CHtml::listData($listeAff, 'value', 'label');
		$listeGrandEcranHoraireDate = CHtml::listData($listeGEHdate, 'value', 'label');
		$listeGrandEcranHoraireNbrPeriode = CHtml::listData($listeGEHnbrP, 'value', 'label');
		$listeEvenementDispo = CHtml::listData($listeEveDispo, 'value', 'label');
		$listeGrandEcranStyle = CHtml::listData($listeGEStyle, 'value', 'label');
		$listeDispoParHeure = CHtml::listData($listeOuiNon, 'value', 'label');
		$listeColonneGauche = CHtml::listData($listeOuiNon, 'value', 'label');
		$listeDroitVoirDispoHoraire = CHtml::listData($listeDroitDispoHoraire, 'value', 'label');
		$listeHoraireCalculHeure = CHtml::listData($listeHoraireCalcH, 'value', 'label');
		
		
		$gardes = Garde::model()->findAll();
		
		$listeGarde = array();
		
		$listeGarde[0] = 'Aucun';
		foreach($gardes as $garde){
			$listeGarde[$garde->id] = $garde->nom;
		}
		
		$listeTZ['America/Moncton'] = 'America/Moncton';
		$listeTZ['America/Montreal'] = 'America/Montreal';
		
		$this->render('index',array(
			'model'=>$model,
			'listeColonne'=>$listeColonne,
			'listeOrdre'=>$listeOrdre,
			'listeDefaut'=>$listeDefaut,
			'listeGarde'=>$listeGarde,
			'listeAffichage'=>$listeAffichage,
			'listeGrandEcranHoraireDate'=>$listeGrandEcranHoraireDate,
			'listeTZ'=>$listeTZ,
			'listeGrandEcranHoraireNbrPeriode'=>$listeGrandEcranHoraireNbrPeriode,
			'listeEvenementDispo'=>$listeEvenementDispo,
			'listeGrandEcranStyle'=>$listeGrandEcranStyle,
			'listeDispoParHeure'=>$listeDispoParHeure,
			'listeColonneGauche'=>$listeColonneGauche,
			'listeDroitVoirDispoHoraire'=>$listeDroitVoirDispoHoraire,
			'listeHoraireCalculHeure'=>$listeHoraireCalculHeure,
			'tblCaserne'=>$tblCaserne
		));
	}
	
	
	public function actionSave()
	{
		$model=Parametres::model()->find('',array('limit'=>1));
		if($model===null){
			$model = new Parametres;
		} 
		
		$model->attributes=$_POST['Parametres'];
		$model->dateDebutPeriode = $_POST['dateDebutPeriode'];
		$model->dateDebutCalculTemps = $_POST['dateDebutCalculTemps'];
		if($model->dateDebutCalculTemps=="" || $model->dateDebutCalculTemps=="0000-00-00"){
			$model->dateDebutCalculTemps = null;
		}
		$model->maxDateReculRapport = $_POST['maxDateReculRapport'];
		$model->id = 1;
		
		/*
		 * Calcul de la différence du modulo
		 * Ce calcul permet de faire "l'ajustement modulo" dans le système, pour ensuite pouvoir utiliser le % pour déterminer à quel jour d'une période on est.
		 */
		$dateDebut = new DateTime($_POST['dateDebutPeriode'],new DateTimeZone('UTC'));
		$tsDebut = $dateDebut->getTimestamp();
		$model->moduloDebut = ($tsDebut/86400)%$model->nbJourPeriode;		
		
		
		if($model->save())
			echo "Enregistrement réussi";
		else
			echo "Enregistrement échoué";
		Yii::app()->end();
	}
	
	public function actionGenereUsager(){
		
		if(isset($_POST['ajouteUsager'])){
			$tblPrenom =array('Alain','Alexandre','Carl','Christian','Claude','Dany','David','Denis','Dominic','Fernand',
					'Francis','Gaston','Gilles','Guillaume','Guy','Guylain','Janne','Jean','Jean-Claude','Joan','Jonathan',
					'Keven','Louis','Luc','Marc-Antoine','Marco','Martin','Maxime','Michael','Michel','Patrick','Raynald',
					'Roger','Samuel','Serge','Simon','Steeve','Sylvain','Thierry','Tommy','Vincent','Yann');
			$tblNom = array('Lemieux','Morissette','Côté','Blanchet','Martin','Bélanger','Roussel');
			
			$usager = new Usager;
			$usager->nom = $tblNom[array_rand($tblNom)];
			$usager->prenom = $tblPrenom[array_rand($tblPrenom)];
			$usager->matricule = "000";
			$usager->pseudo = substr($usager->nom,0,3).substr($usager->prenom,0,3).rand(100,999);
			$usager->motdepasse = md5("swordtest");
			$usager->courriel = "info@swordware.com";
			$usager->adresseCivique = "123, rue des Chênes";
			$usager->ville = "Rivière-du-Loup";
			$usager->telephone1 = "418-555-".rand(1000,9999);
			$usager->telephone2 = "581-555-".rand(1000,9999);
			$grade = Grade::model()->find();
			$usager->tbl_grade_id = $grade->id;
			if($usager->save()){
				echo "1";
			}else{
				echo "0";
			}
			Yii::app()->end();
		}
		
		$this->render('genereUsager');
	}
	
	public function actionCron()
	{
		try{
			$this->ajouteScript(1, 0, "*", "*", "*", "mysqldump ---user ".Yii::app()->params['username']." ---password=".Yii::app()->params['password'].
				" ".Yii::app()->params['db']." > $(date +%F)".Yii::app()->params['db'], "Backup BD");
			echo "Installation réussie";
		}catch(Exception $e){
			echo "Erreur : ".$e->getMessage();
		}
	}
	
	// Source : http://matthieu.developpez.com/execution_periodique/#L1

	private function ajouteScript($chpHeure, $chpMinute, $chpJourMois, $chpJourSemaine, $chpMois, $chpCommande, $chpCommentaire)
	{
		$debut = '#Les lignes suivantes sont gerees automatiquement via un script PHP. - Merci de ne pas editer manuellement';
		$fin = '#Les lignes suivantes ne sont plus gerees automatiquement';
		
		try{
		$oldCrontab = Array();				/* pour chaque cellule une ligne du crontab actuel */
		$newCrontab = Array();				/* pour chaque cellule une ligne du nouveau crontab */
		$isSection = false;
		$maxNb = 0;					/* le plus grand numéro de script trouvé */
		exec('crontab -l', $oldCrontab);		/* on récupère l'ancienne crontab dans $oldCrontab */
		
		foreach($oldCrontab as $index => $ligne)	/* copie $oldCrontab dans $newCrontab et ajoute le nouveau script */
		{
			if ($isSection == true)			/* on est dans la section gérée automatiquement */
			{
				$motsLigne = explode(' ', $ligne);
				if ($motsLigne[0] == '#' && $motsLigne[1] > $maxNb)	/* si on trouve un numéro plus grand */
	
				{
						$maxNb = $motsLigne[1];
				}
			}
			
			if ($ligne == $debut) { $isSection = true;}
			
			if ($ligne == $fin)			/* on est arrivé à la fin, on rajoute le nouveau script */
			{
				$id = $maxNb + 1;
				$newCrontab[] = '# '.$id.' : '.$chpCommentaire;
	
				$newCrontab[] = $chpMinute.' '.$chpHeure.' '.$chpJourMois.' '.
					$chpMois.' '.$chpJourSemaine.' '.$chpCommande;
			}
			
			$newCrontab[] = $ligne;			/* copie $oldCrontab, ligne après ligne */
		}
		
		if ($isSection == false) 			/* s'il n'y a pas de section gérée par le script */
		{						/*  on l'ajoute maintenant */
			$id = 1;
			$newCrontab[] = $debut;
			$newCrontab[] = '# 1 : '.$chpCommentaire;
	
			$newCrontab[] = $chpMinute.' '.$chpHeure.' '.$chpJourMois.' '.$chpMois.' '.$chpJourSemaine.' '.$chpCommande;
			$newCrontab[] = $fin;
		}
	
		$f = fopen('./tmp', 'wb');			/* on crée le fichier temporaire */
		fwrite($f, implode(PHP_EOL, $newCrontab));
		fclose($f);
	
		exec('crontab ./tmp',$retourExec);				/* on le soumet comme crontab */
	
		return 	$id;
		}catch(Exception $e){
			throw $e;
		}
	}	

	private function retireScript($id)
	{
		$oldCrontab = Array();				/* pour chaque cellule une ligne du crontab actuel */
		$newCrontab = Array();				/* pour chaque cellule une ligne du nouveau crontab */
		$isSection = false;
		
		exec('crontab -l', $oldCrontab);		/* on récupère l'ancienne crontab dans $oldCrontab */
		
		foreach($oldCrontab as $ligne)			/* copie $oldCrontab dans $newCrontab sans le script à effacer */
		{
			if ($isSection == true)			/* on est dans la section gérée automatiquement */
			{
				$motsLigne = explode(' ', $ligne);
				if ($motsLigne[0] != '#' || $motsLigne[1] != $id)	/* ce n est pas le script à effacer */
	
				{
						$newCrontab[] = $ligne;			/* copie $oldCrontab, ligne après ligne */
				}
			}else{
				$newCrontab[] = $ligne;		/* copie $oldCrontab, ligne après ligne */
			}
			
			if ($ligne == $debut) { $isSection = true; }
		}
		
		$f = fopen('/tmpCronTab', 'w');			/* on crée le fichier temporaire */
		fwrite($f, implode('\n', $newCrontab)); 
		fclose($f);
		
		exec('crontab /tmpCronTab');			/* on le soumet comme crontab */
		
		return 	$id;
	}
	
}