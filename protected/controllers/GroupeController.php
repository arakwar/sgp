<?php

class GroupeController extends Controller
{
	public $pageTitle = 'Équipes spécialisées';
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
				'roles'=>array('Groupe:index'),
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
		
		$model=new Groupe;
		$casernes = Caserne::model()->findAll(array('condition'=>'siActif = 1 AND id IN ('.$casernesUsager.')'));
		$lstCaserne = CHtml::listData($casernes, 'id', 'nom');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Groupe']))
		{
			$model->attributes=$_POST['Groupe'];
			if($model->save()){
				$histo = new HistoGroupe;
				$histo->nom=$model->nom;
				$histo->typeAction=2;
				$histo->tbl_groupe_id=$model->id;
				$histo->tbl_caserne_id=$model->tbl_caserne_id;
				if($histo->save())			
					$this->redirect(array('view','id'=>$model->id));
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

		if(isset($_POST['Groupe']))
		{
			$model->attributes=$_POST['Groupe'];
			if($model->save()){
				$histo = new HistoGroupe;
				$histo->nom=$model->nom;
				$histo->typeAction=1;
				$histo->tbl_groupe_id=$model->id;
				$histo->tbl_caserne_id=$model->tbl_caserne_id;
				if($histo->save())	
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
			$model = $this->loadModel($id);
			
			$model->siActif = 0;
			if($model->save()){
				$histo = new HistoGroupe;
				$histo->nom=$model->nom;
				$histo->typeAction=0;
				$histo->tbl_groupe_id=$model->id;
				
				$histo->save();
			}
			
			$GroupeUsager = GroupeUsager::model()->findAll('tbl_groupe_id = '.$model->id);
			
			foreach($GroupeUsager as $value){
				$GUmodel = GroupeUsager::model()->find('tbl_groupe_id = '.$value['tbl_groupe_id'].' AND tbl_usager_id = '.$value['tbl_usager_id']);
				$GUmodel->delete();
			}
			
			$model->save();

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
		
		$criteria = new CDbCriteria();
		$criteria->condition = 'siActif=1';
		
		if($caserne!='0'){
			$criteria->condition .= ' AND tbl_caserne_id = '.$caserne;
		}
		
		$casernes = Caserne::model()->findAll(array('condition'=>'siActif = 1 AND id IN ('.$casernesUsager.')', 'order'=>'id ASC'));
		$dataCaserne = array();
		$dataCaserne['0'] = '- Tous -';
		foreach($casernes as $cas){
			$dataCaserne[$cas->id]=$cas->nom;
		}
		
		$dataProvider=new CActiveDataProvider('Groupe',array('criteria'=>$criteria));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'dataCaserne'=>$dataCaserne,
			'caserne'=>$caserne,
		));
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Groupe::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='groupe-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
