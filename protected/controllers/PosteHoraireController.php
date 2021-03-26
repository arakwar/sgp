<?php

class PosteHoraireController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	
	public $pageTitle = "Poste - Horaire";

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
				'actions'=>array('create','update','index','view', 'delete'),
				'roles'=>array('PosteHoraire:index'),
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
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$usager = Usager::model()->findByPk(Yii::app()->user->id);

		$casernesUsager = $usager->getCaserne();
		
		$model=new PosteHoraire;		
		$casernes = Caserne::model()->findAll(array('condition'=>'siActif = 1 AND id IN ('.$casernesUsager.')'));
		$lstCaserne = CHtml::listData($casernes, 'id', 'nom');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['PosteHoraire']))
		{
			$model->attributes=$_POST['PosteHoraire'];
			$model->dateDebut = date('Y-m-d');
			if($model->save()){
				if(isset($_POST['tblCasernes'])) $model->tblCasernes = $_POST['tblCasernes'];
				if($model->save()){				
					$this->redirect(array('view','id'=>$model->id));
				}
			}
		}

		$this->render('create',array(
			'model'=>$model,
			'lstCaserne'=>$lstCaserne,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{		
		$usager = Usager::model()->findByPk(Yii::app()->user->id);

		$casernesUsager = $usager->getCaserne();		
		
		$model=$this->loadModel($id);
		$casernes = Caserne::model()->findAll(array('condition'=>'siActif = 1 AND id IN ('.$casernesUsager.')'));
		$lstCaserne = CHtml::listData($casernes, 'id', 'nom');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['PosteHoraire']))
		{
			$model->attributes=$_POST['PosteHoraire'];			
			if(isset($_POST['tblCasernes'])){
				$model->tblCasernes = $_POST['tblCasernes'];
			}else{
				$model->tblCasernes = NULL;
			}
			if($model->save()){
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'lstCaserne'=>$lstCaserne,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
			$model=$this->loadModel($id);	
			
			$model->dateFin = date('Y-m-d');
			
			$model->save();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($caserne="")
	{
		$usager = Usager::model()->findByPk(Yii::app()->user->id);

		$casernesUsager = $usager->getCaserne();
		
		$casernes = Caserne::model()->findAll(array('condition'=>'id IN ('.$casernesUsager.')', 'order'=>'id ASC'));
		
		if($caserne==''){
			foreach($casernes as $cas){
				$caserne = $cas->id;
				break;
			}			
		}
		
		$dataCaserne = CHtml::listData($casernes, 'id', 'nom');
		
		$criteria = new CDbCriteria;	
		$criteria->alias = 'ph';
		$criteria->join = 'INNER JOIN tbl_poste_horaire_caserne phc ON ph.id = phc.tbl_poste_horaire_id '.
							'INNER JOIN tbl_quart q ON q.id = ph.tbl_quart_id';
		$criteria->condition = 'phc.tbl_caserne_id = :caserne AND dateFin IS NULL';
		$criteria->params = array(':caserne'=>$caserne);
		$criteria->order = 'q.heureDebut ASC, tbl_poste_id ASC, ph.heureDebut ASC';
		
		$posteHoraires = PosteHoraire::model()->findAll($criteria);
		$quarts = Quart::model()->findAll();
		$postes = Poste::model()->findAll('siActif = 1');
		$this->render('index',array(
			'posteHoraires'=>$posteHoraires,
			'quarts'=>$quarts,
			'postes'=>$postes,
			'caserne'=>$caserne,
			'dataCaserne'=>$dataCaserne,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=PosteHoraire::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='poste-horaire-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
