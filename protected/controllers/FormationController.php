<?php

class FormationController extends Controller
{
	public $pageTitle = 'Formation';
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
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','delete','index','view','plan','evaluation'),
				'roles'=>array('Formation:index'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$prerequis = FormationPreRequis::model()->findAllByAttributes(array('tbl_formation_id'=>$id));
		
		$lstPrerequis = array();
		foreach($prerequis as $pre){
			$formation = Formation::model()->findByPk($pre->tbl_formation_pre);
			$lstPrerequis[] = $formation->nom;
		}
		
		
		$this->render('view',array(
			'model'=>$this->loadModel($id),
			'lstPrerequis'=>$lstPrerequis,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		if(Yii::app()->params['moduleFormation']){
			$model=new Formation;
				
			$formations = Formation::model()->findAll(array('order'=>'nom ASC'));
				
			$lstFormations = CHtml::listData($formations, 'id', 'nom');
				
			//Liste des formations qui peuvent devenir pré-requis (toutes sauf la formation elle-même)
			$formationsPre = Formation::model()->findAll(array('order'=>'nom ASC'));
				
			$lstFormationsPre = array();
			foreach($formationsPre as $prerequis){
				$lstFormationsPre[$prerequis->id] = $prerequis->nom;
			}
			
			// Uncomment the following line if AJAX validation is needed
			// $this->performAjaxValidation($model);
			
			if(isset($_POST['Formation']))
			{
				$model->attributes=$_POST['Formation'];
				if($model->save()){
					$model->refresh();
					if(isset($_POST['tblPreRequis'])){
						foreach($_POST['tblPreRequis'] as $preRequis){
							$form_pre = new FormationPreRequis;
								
							$form_pre->tbl_formation_id = $model->id;
							$form_pre->tbl_formation_pre = $preRequis;
								
							$form_pre->save();
						}
					}
					$this->redirect(array('view','id'=>$model->id));
				}
			}
			
			$this->render('create',array(
					'model'=>$model,
					'lstFormations'=>$lstFormations,
					'lstFormationsPre' => $lstFormationsPre,
					'lstFormationsPreC' => array(),
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
	public function actionUpdate($id)
	{
		if(Yii::app()->params['moduleFormation']){
			$model=$this->loadModel($id);
			
			$formations = Formation::model()->findAll(array('order'=>'nom ASC'));
			
			$lstFormations = CHtml::listData($formations, 'id', 'nom');
			
			//Liste des formations qui peuvent devenir pré-requis (toutes sauf la formation elle-même)
			$formationsPre = Formation::model()->findAll(array('condition'=>'id <> '.$model->id, 'order'=>'nom ASC'));
			
			$lstFormationsPre = array();
			foreach($formationsPre as $prerequis){
				$lstFormationsPre[$prerequis->id] = $prerequis->nom;	
			}
			//Liste des pré-requis déjà coché
			$formationsPreC = FormationPreRequis::model()->findAll(array('condition'=>'tbl_formation_id = '.$id));
			
			$lstFormationsPreC = array();
			foreach($formationsPreC as $prerequis){
				$lstFormationsPreC[] = $prerequis->tbl_formation_pre;	
			}
			// Uncomment the following line if AJAX validation is needed
			// $this->performAjaxValidation($model);
	
			if(isset($_POST['Formation']))
			{
				$model->attributes=$_POST['Formation'];
				if($model->save()){
					FormationPreRequis::model()->deleteAll(array('condition'=>'tbl_formation_id = '.$id));
					if(isset($_POST['tblPreRequis'])){
						foreach($_POST['tblPreRequis'] as $preRequis){
							$form_pre = new FormationPreRequis;
							
							$form_pre->tbl_formation_id = $model->id;
							$form_pre->tbl_formation_pre = $preRequis;
							
							$form_pre->save();
						}
					}
					$this->redirect(array('view','id'=>$model->id));
				}
			}
	
			$this->render('update',array(
				'model'=>$model,
				'lstFormations'=>$lstFormations,
				'lstFormationsPre' => $lstFormationsPre,
				'lstFormationsPreC' => $lstFormationsPreC,
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
		if(Yii::app()->params['moduleFormation']){
			$model = $this->loadModel($id);
			
			$model->tblPreRequis = NULL;
			$model->save();
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
	public function actionIndex()
	{
		if(Yii::app()->params['moduleFormation']){
			$criteria = new CDbCriteria;
			
			$dataProvider=new CActiveDataProvider('Formation',array('criteria'=>$criteria));
			$this->render('index',array(
				'dataProvider'=>$dataProvider,
			));
		}else{
			$this->redirect(array('site/index'));
		}
	}
	
	/**
	 * Crée un évènement à partir de la formation
	 */
	public function actionPlan()
	{
		if(Yii::app()->params['moduleFormation']){
			$parametres = Parametres::model()->findByPk(1);
			
			$criteria = new CDbCriteria;
			$criteria->order = 'nom ASC';
			
			$formations = Formation::model()->findAll($criteria);
			$lstFormations = CHtml::listData($formations, 'id', 'nom');
	
			$criteria = new CDbCriteria;
			$criteria->order = $parametres->colonne.' '.$parametres->ordre;
			
			$usagers = Usager::model()->findAll(array('order'=>'matricule ASC'));
			$lstUsagers = array();
			foreach($usagers as $usager){
				$lstUsagers[$usager->id] = $usager->getMatPrenomNom();
			}
			
			if(isset($_POST['formation'])){
				$evenement = new Evenement;
				
				$formation = Formation::model()->findByPk($_POST['formation']);
				
				$evenement->nom = $formation->nom;
				$evenement->dateDebut = $_POST['dateDebut'];
				$evenement->dateFin = $_POST['dateFin'];
				$evenement->lieu = $_POST['lieu'];
				$evenement->instituteur = $_POST['instituteur'];
				$evenement->moniteur = $_POST['moniteur'];
				$evenement->tbl_formation_id = $_POST['formation'];
				
				if($evenement->save()){	
					$evenement->refresh();		
					$this->redirect(array('evenement/update','id'=>$evenement->id));
				}
			}
		
			$this->render('plan',array(
				'lstFormations' => $lstFormations,
				'lstUsagers' => $lstUsagers,
			));
		}else{
			$this->redirect(array('site/index'));
		}
	}
	
	public function actionEvaluation($id){
		if(Yii::app()->params['moduleFormation']){
			if(isset($_POST['tblResultat'])){
				foreach($_POST['tblResultat'] as $resultat){
					$usagerEve = EvenementUsager::model()->find(array('condition'=>'tbl_usager_id = :usager AND tbl_evenement_id = :evenement','params'=>array(':usager'=>$resultat, ':evenement'=>$id)));
					$usagerEve->resultat = 1;
					
					$usagerEve->save();
				}
				$this->redirect(array('evenement/view','id'=>$id));
			}
			
			$formation = Evenement::model()->findByPk($id);
			
			$usagers = EvenementUsager::model()->findAllByAttributes(array('tbl_evenement_id'=>$id));
			$lstUsagers = array();
			foreach($usagers as $usager){
				$usa = Usager::model()->findByPk($usager->tbl_usager_id);
				$lstUsagers[$usager->tbl_usager_id] = $usa->getMatPrenomNom();
			}
			$lstResultats = array();
			foreach($usagers as $usager){
				if($usager->resultat==1)
					$lstResultats[] = $usager->tbl_usager_id;
			}
			
			$this->render('evaluation',array(
					'formation' => $formation,
					'lstUsagers' => $lstUsagers,
					'lstResultats' => $lstResultats,
			));	
		}else{
			$this->redirect(array('site/index'));
		}	
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Formation::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='formation-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
