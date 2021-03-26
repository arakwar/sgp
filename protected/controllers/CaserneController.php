<?php

class CaserneController extends Controller
{
	public $pageTitle = 'Caserne';
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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','create','update','delete'),
				'roles'=>array('Caserne:index'),
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
		$model=new Caserne;
		
		$model->siGrandEcran = 1;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Caserne']))
		{
			$model->attributes=$_POST['Caserne'];
			if($model->save()){
				$i=0;
				while($i<=6){
					$minimum = new Minimum;
					
					$minimum->jourSemaine = $i;
					$minimum->minimum = '9';
					$minimum->niveauAlerte = 1;
					$minimum->tbl_caserne_id = $model->id;
					
					$minimum->save();
					$i++;
				}
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('create',array(
			'model'=>$model,
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

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Caserne']))
		{
			$model->attributes=$_POST['Caserne'];
			if($model->save()){
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update',array(
			'model'=>$model,
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
		
		$Quart = Quart::model()->findAll(array('condition'=>'tbl_caserne_id = '.$id));
		$Equipe = Equipe::model()->findAll(array('condition'=>'tbl_caserne_id = '.$id.' AND siActif = 1'));
		$Groupe = Groupe::model()->findAll(array('condition'=>'tbl_caserne_id = '.$id.' AND siActif = 1'));
		
		$nbr = count($Quart)+count($Equipe)+count($Groupe);		
		
		if($nbr == 0){
			$Documents = DocumentCaserne::model()->findAll('tbl_caserne_id = '.$id);
			
			foreach($Documents as $document){
				$DCmodel = DocumentCaserne::model()->find('tbl_caserne_id = '.$document['tbl_caserne_id'].' AND tbl_document_id = '.$document['tbl_document_id']);
				$DCmodel->delete();
			}
			
			$Notices = NoticeCaserne::model()->findAll('tbl_caserne_id = '.$id);
			
			foreach($Notices as $notice){
				$NCmodel = NoticeCaserne::model()->find('tbl_caserne_id = '.$notice['tbl_caserne_id'].' AND tbl_notice_id = '.$notice['tbl_notice_id']);
				$NCmodel->delete();
			}
			
			$Minimums = Minimum::model()->findAll('tbl_caserne_id = '.$id);
			foreach($Minimums as $minimum){
				$minimum->delete();
			}
			
			$model->siActif = 0;
			$model->save();
			
			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}else{
			$this->redirect(array('view','id'=>$model->id,'m'=>1));
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$criteria = new CDbCriteria();
		$criteria->condition = 'siActif=1';
		
		$dataProvider=new CActiveDataProvider('Caserne',array('criteria'=>$criteria));
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
		$model=Caserne::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,Yii::t('erreur','erreur404'));
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='caserne-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
