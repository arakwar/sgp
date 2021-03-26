<?php

class GroupeFormationController extends Controller
{
	public $pageTitle = 'Groupe de formation';
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
				'actions'=>array('create','update','index','view','delete'),
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
		
		$model=new GroupeFormation;
		
		$usagers = Usager::model()->findAll(array('order'=>'matricule ASC'));
		$lstUsagers = array();
		foreach($usagers as $usager){
			$lstUsagers[$usager->id] = $usager->getMatPrenomNom();
		}

		if(isset($_POST['GroupeFormation']))
		{
			$model->attributes=$_POST['GroupeFormation'];
			if($model->save()){	
				$model->refresh();		
				foreach($_POST['usagers'] as $usager){
					$groupeUser = new GroupeFormationUsager;
				
					$groupeUser->tbl_groupe_formation_id=$model->id;
					$groupeUser->tbl_usager_id=$usager;
						
					$groupeUser->save();
				}
				
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('create',array(
			'model'=>$model,
			'lstUsagers' => $lstUsagers,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		
		$usagers = Usager::model()->findAll();
		$lstUsagers = array();
		foreach($usagers as $usager){
			$lstUsagers[$usager->id] = $usager->getMatPrenomNom();
		}
		
		$GFUsagers = GroupeFormationUsager::model()->findAll(array('condition'=>'tbl_groupe_formation_id = '.$id));
		$lstGFUsagers = array();
		foreach($GFUsagers as $usager){
			$lstGFUsagers[$usager->tbl_usager_id] = $usager->tbl_usager_id;
		}

		if(isset($_POST['GroupeFormation']))
		{
			$model->attributes=$_POST['GroupeFormation'];
			if($model->save()){
				GroupeFormationUsager::model()->deleteAll(array('condition'=>'tbl_groupe_formation_id = '.$id));
				foreach($_POST['usagers'] as $usager){
					$groupeUser = new GroupeFormationUsager;
				
					$groupeUser->tbl_groupe_formation_id= $model->id;
					$groupeUser->tbl_usager_id= $usager;
						
					$groupeUser->save();
				}
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'lstUsagers' => $lstUsagers,
			'lstGFUsagers' => $lstGFUsagers,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
			$model = $this->loadModel($id);
			
			GroupeFormationUsager::model()->deleteAll(array('condition'=>'tbl_groupe_formation_id'));
			
			$model->delete();
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('GroupeFormation');
		
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=GroupeFormation::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='groupe-formation-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
