<?php

class NoticeController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	
	public $pageTitle = "Notices";

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
				'actions'=>array('create','update','delete','index','view'),
				'roles'=>array('Notice:index'),
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
		
		$model=new Notice;
		
		$model->tbl_usager_id = Yii::app()->user->id;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Notice']))
		{
			$model->attributes=$_POST['Notice'];
			if($model->save()){				
				if(isset($_POST['tblCasernes'])) $model->tblCasernes = $_POST['tblCasernes'];
				if($model->save()){	
					$this->redirect(array('view','id'=>$model->id));
				}
			}
		}

		$this->render('create',array(
			'model'=>$model,
			'casernesUsager'=>$casernesUsager,
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

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Notice']))
		{
			$model->attributes=$_POST['Notice'];
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
			'casernesUsager'=>$casernesUsager,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
			$notices = NoticeCaserne::model()->findAll(array('condition'=>'tbl_notice_id = '.$id));
			foreach($notices as $notice){
				$notice->delete();
			}
		
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($caserne="0")
	{
		$usager = Usager::model()->findByPk(Yii::app()->user->id);

		$casernesUsager = $usager->getCaserne();
		
		$criteria = new CDbCriteria;

		if($caserne!='0'){
			$notices = NoticeCaserne::model()->findAll(array('condition'=>'tbl_caserne_id = '.$caserne));
			$ids = '(';
			foreach($notices as $notice){
				$ids .= '"'.$notice->tbl_notice_id.'", ';
			}
			if($ids != '('){
				$ids = substr($ids, 0, strlen($ids)-2);
				$criteria->condition = 'id IN '.$ids.')';
			}else{
				$criteria->condition = 'id = 0';
			}
		}
		
		$casernes = Caserne::model()->findAll(array('condition'=>'siActif = 1 AND id IN ('.$casernesUsager.')', 'order'=>'id ASC'));
		$dataCaserne = array();
		$dataCaserne['0'] = '- Tous -';
		foreach($casernes as $cas){
			$dataCaserne[$cas->id]=$cas->nom;
		}
		$dataProvider=new CActiveDataProvider('Notice',array('criteria'=>$criteria));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
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
		$model=Notice::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='notice-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
