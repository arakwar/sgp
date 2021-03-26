<?php

class NotificationController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	public $pageTitle = "Notification";

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
				'actions'=>array('index','delete', 'count'),
				'users'=>array('@'),
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
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();
		$this->redirect(Yii::app()->createUrl("notification/index"));
	}
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$notifications = Notification::model()->findByAttributes(array('tbl_usager_id'=>Yii::app()->user->id,'dateVisionnement'=>null));
		if($notifications)
		{
			$dataProvider=new CActiveDataProvider('Notification', array(
				'criteria'=>array(
					'condition'=>'tbl_usager_id = '.Yii::app()->user->id.' AND dateVisionnement IS NULL',
				),
				'pagination'=> false,
			));
			$criteria = new CDbCriteria;
			$criteria->condition = 'tbl_usager_id = '.Yii::app()->user->id.' AND dateVisionnement IS NULL';
			$this->render('index',array(
				'dataProvider'=>$dataProvider,
			));
			$notifications = Notification::model()->findAll($criteria);
			foreach ($notifications as $key => $value) 
			{

				$model=$this->loadModel($value['id']);
				$model->attributes= $value;	
				$model->dateVisionnement = date('Y-m-d');
				$model->save();
			}
		}
		else
		{
			$dataProvider=new CActiveDataProvider('Notification', array(
				'criteria'=>array(
					'condition'=>'tbl_usager_id = '.Yii::app()->user->id
				),
			));
			$this->render('index',array(
				'dataProvider'=>$dataProvider,
			));
		}
	}

	public function actionCount()
	{
		$nbrNotification = Notification::model()->count('tbl_usager_id ='.Yii::app()->user->id.' AND dateVisionnement IS NULL');
		if($nbrNotification > 5)
		{
			echo "5+";
		}
		else if($nbrNotification != 0)
		{
			echo $nbrNotification;
		}
	}
	/**
	 * Manages all models.
	 */

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Notification the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Notification::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404, Yii::t('erreur','erreur404'));
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Notification $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='notification-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
