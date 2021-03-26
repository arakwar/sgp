<?php

class MessageController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column1';
	
	public $pageTitle = "Messagerie";

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
				'actions'=>array('index'),
				'roles'=>array('Message:index'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($caserne="0",$equipe="0")
	{
		
		$criteria = new CDbCriteria();
		$criteria->order = "matricule";
		$criteria->condition = "actif=1";
		if($equipe!="0"){
			$criteria->join = "LEFT JOIN tbl_equipe_usager eu ON eu.tbl_usager_id=t.id";
			$criteria->condition .= ' AND eu.tbl_equipe_id=:id';
			$criteria->params = array(':id'=>$equipe);
		}elseif($caserne!="0"){
			$criteria->join = "INNER JOIN tbl_equipe_usager eu ON eu.tbl_usager_id=t.id
								INNER JOIN tbl_equipe e ON e.id=eu.tbl_equipe_id";
			$criteria->condition .= ' AND e.tbl_caserne_id=:caserne';
			$criteria->params = array(':caserne'=>$caserne);
		}
		
		$dataUsager=new CActiveDataProvider('Usager',array(
			'pagination'=>array(
				'pageSize'=>10
			),
			'criteria'=>$criteria
		));
		
		
		/**
				TODO : mettre les filtres pour voir les messages de la caserne pis du service ?
		 */
		
		$critereMessage = new CDbCriteria();
		if(!Yii::app()->user->checkAccess('voirToutMessage')){
			$critereMessage->condition = 'auteur=:id';
			$critereMessage->order = 'dateEnvoi DESC';
			$critereMessage->params = array(':id'=>Yii::app()->user->id);
		}
		$dataProvider=new CActiveDataProvider('Message',array(
			'pagination'=>array(
				'pageSize'=>5
			),
			'criteria'=>$critereMessage
		));
		
		$casernes = Caserne::model()->findAll(array('condition'=>'siActif = 1', 'order'=>'id ASC'));
		$dataCaserne = array();
		foreach($casernes as $cas){
			$dataCaserne[$cas->id]=$cas->nom;
		}
		
		$equipes = Equipe::model()->findAll(array('condition'=>'siActif = 1', 'order'=>'nom ASC'));
		$dataEquipe = array();
		foreach($equipes as $equ){
			$dataEquipe[$equ->id]=$equ->nom;
		}
		
		
		if(isset($_POST['Message'])){
			$model = new Message;
			$auteur = Usager::model()->findByPk(Yii::app()->user->id);
			$model->auteur = Yii::app()->user->id;
			$model->dateEnvoi = date("Y-m-d");
			$model->message = $_POST['Message']['message'];
			$model->objet = $_POST['Message']['objet'];
			$model->tblUsagers = $_POST['listeDestinataire'];
			
			$message = new YiiMailMessage;
			$message->view = "view";
			$message->setBody(array("message"=>$model), 'text/html');
			$message->subject = $model->objet;
			if(!filter_var($auteur->courriel, FILTER_VALIDATE_EMAIL) === false){
				$message->from = $auteur->courriel;
			}else{
				$message->from = Yii::app()->params['emailSysteme'];
			}
			$crit = new CDbCriteria;
			$crit->addInCondition('id',$_POST['listeDestinataire']);
			$listeDest = Usager::model()->findAll($crit);
			foreach($listeDest as $dest){
				if(filter_var($dest->courriel, FILTER_VALIDATE_EMAIL) !== false){
					$message->addTo($dest->courriel);
				}
				else
				{
					$notification = new Notification;
					$notification->tbl_usager_id = $dest->id;
					$notification->dateCreation = $model->dateEnvoi;
					$notification->message = $model->message;
					$notification->save();
				}
			}
			Yii::app()->mail->send($message);
			$model->save();
			$newModel = new Message;
			$this->render('index',array(
				'dataProvider'=>$dataProvider,
				'dataUsager'=>$dataUsager,
				'model'=>$newModel,
				'dataEquipe'=>$dataEquipe,
				'dataCaserne'=>$dataCaserne
			));
			
		} else {
			$model = new Message;
			$model->auteur = Yii::app()->user->id;
			$model->dateEnvoi = date("Y-m-d");
			$this->render('index',array(
				'dataProvider'=>$dataProvider,
				'dataUsager'=>$dataUsager,
				'model'=>$model,
				'dataEquipe'=>$dataEquipe,
				'dataCaserne'=>$dataCaserne
			));
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Message::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='message-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
