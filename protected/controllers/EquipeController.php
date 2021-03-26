<?php

class EquipeController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	
	public $pageTitle = 'Équipe';

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
				'actions'=>array('index','view','create','update','delete','ordre','ordreEquipe','saveO'),
				'roles'=>array('Equipe:index'),
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
		
		$model=new Equipe;
		$casernes = Caserne::model()->findAll();
		$lstCaserne = CHtml::listData($casernes, 'id', 'nom');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Equipe']))
		{
			$model->attributes=$_POST['Equipe'];
			if($model->siFDF==1){
				$criteria = new CDbCriteria;
				$criteria->condition = 'tbl_caserne_id = '.$model->tbl_caserne_id;
				$criteria->order = 'ordre DESC';
				$res = Equipe::model()->find($criteria);
				if($res===null){
					$model->ordre = 1;
				}else{
					$model->ordre = ($res->ordre+1);
				}
				
			}
			if($model->save()){
				$histo = new HistoEquipe;
				$histo->nom=$model->nom;
				$histo->couleur=$model->couleur;
				$histo->siHoraire=$model->siHoraire;
				$histo->siAlerte=$model->siAlerte;
				$histo->tbl_caserne_id=$model->tbl_caserne_id;
				$histo->typeAction=2;
				$histo->tbl_equipe_id=$model->id;
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
		
		$model=$this->loadModel($id);
		$casernes = Caserne::model()->findAll();
		$lstCaserne = CHtml::listData($casernes, 'id', 'nom');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Equipe']))
		{
			$siFDFOLD = $model->siFDF;
			$oldCaserneId = $model->tbl_caserne_id;
			$model->attributes=$_POST['Equipe'];
			$pass = true;
			if($model->siHoraire == 0){
				$equipeGarde = EquipeGarde::model()->findAll('tbl_equipe_id = '.$model->id);
				if(count($equipeGarde)>=1){
					$equipe = Equipe::model()->findAll('siHoraire = 1 AND id <> '.$model->id.' AND siActif = 1 AND tbl_caserne_id = '.$model->tbl_caserne_id, array('limit'=>1));
					if(count($equipe)==1){
						foreach($equipeGarde as $value){
							if($value['tbl_equipe_id']==$model->id){
								$garde = EquipeGarde::model()->find('modulo=:modulo AND tbl_quart_id=:quart',array(':modulo'=>$value['modulo'],':quart'=>$value['tbl_quart_id']));
								//$garde->modulo = $value['modulo'];
								//$garde->tbl_quart_id = $value['tbl_quart_id'];
								$garde->tbl_equipe_id = $equipe[0]['id'];
								$garde->save();
							}
						}
					}else{
						$pass = false;
						$model->siHoraire = 1;
					}
				}
			}
			//On vérifie s'il y a un changement à siFDF afin de donner le bon ordre
			if($siFDFOLD!=$model->siFDF){
				if($model->siFDF==0){
					$model->ordre = 0;
				}else{
					$criteria = new CDbCriteria;
					$criteria->condition = 'tbl_caserne_id = '.$model->tbl_caserne_id;
					$criteria->order = 'ordre DESC';
					$criteria->limit = 1;
					$res = Equipe::model()->findAll($criteria);
					$model->ordre = ($res[0]['ordre']+1);
				}
			}
			if($oldCaserneId != $model->tbl_caserne_id && $model->siFDF == 1){
				$criteria = new CDbCriteria;
				$criteria->condition = 'tbl_caserne_id = '.$model->tbl_caserne_id;
				$criteria->order = 'ordre DESC';
				$criteria->limit = 1;
				$res = Equipe::model()->findAll($criteria);
				$model->ordre = ($res[0]['ordre']+1);				
			}
			if($model->save()){
				$histo = new HistoEquipe;
				$histo->nom=$model->nom;
				$histo->couleur=$model->couleur;
				$histo->siHoraire=$model->siHoraire;
				$histo->siAlerte=$model->siAlerte;
				$histo->tbl_caserne_id=$model->tbl_caserne_id;
				$histo->typeAction=1;
				$histo->tbl_equipe_id=$model->id;
				
				if($histo->save()){
					if($pass){
						$this->redirect(array('view','id'=>$model->id));
					}else{
						$this->redirect(array('view','id'=>$model->id,'m'=>''));
					}
				}				
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'lstCaserne'=>$lstCaserne,
		));
	}
	
	/*
	 *Gère l'ordre des équipe de la FDF
	*/
	public function actionOrdre($caserne="0"){		
		$casernes = Caserne::model()->findAll(array('order'=>'id ASC'));
		$lstCaserne = CHtml::listData($casernes, 'id', 'nom');
		
		if($caserne=="0"){
			$caserne = $casernes[0]->id;
		}
		
		
		$this->render('ordre', array(
			'dataCaserne'=>$lstCaserne,
			'caserne'=>$caserne,
		));		
	}
	
	/*
	 Sauvegarde l'ordre 
	 */
	public function actionSaveO(){
		//TODO : Sécurisé les requête sur la BD
		$i=1;
		while(isset($_POST['Equipe']['ordre'][$i])){
			$i++;
		}$i--;
		//Si l'utilisateur savegarde alors qu'il n'y a pas d'équipe
		if($i==0){				
			$this->redirect(array('ordre'));	
		}
		//On vérifie si l'usager n'a pas choisi la meme équipe 2 fois
		$pass=0;
		for($j=1;$j<$i;$j++){
			$k=1;
			while($k<=$i){
				if($k!=$j && $_POST['Equipe']['ordre'][$k]==$_POST['Equipe']['ordre'][$j]){
					$pass=1;
				}
				$k++;
			}
		}
		//Si pass = 1 c'est que l'usager a choisi 2 fois la même équipe
		if($pass==1){
			echo Yii::t('controller','equipe.actionSaveO.selectionMemeEquipe');
			Yii::app()->end();	
		}
		//Finalement, on sauvegarde...
		$pass=0;
		for($j=1;$j<=$i;$j++){
			$model = Equipe::model()->findByPk($_POST['Equipe']['ordre'][$j]);
			$model->ordre = $j;
			if(!$model->save()){
				$pass=1;
			}
		}
		//Si la sauvegarde a fonctionner, on affiche le carré vert, sinon ??
		if($pass==0){
			//Fonctionne
			echo Yii::t('controller','equipe.actionSaveO.enregistrementReussi');	
		}else{
			//Fonctionne pas
			echo Yii::t('controller','equipe.actionSaveO.enregistrementEchoue');
		}
		Yii::app()->end();
	}
	
	/*
	 *Retourne les équipe de la FDF en ordre 
	*/
	public function actionOrdreEquipe($caserne)
	{
		$criteria = new CDbCriteria;
		$criteria->condition = 'siFDF = 1 AND tbl_caserne_id = '.$caserne;
		$criteria->order = 'ordre ASC';
		$models = Equipe::model()->findAll($criteria);
		
		$return = '';$i = 1;
		if(count($models)==0){
			$return = Yii::t('controller','equipe.ordreEquipe.aucunEquipe');
		}else{
			$list = CHtml::listData($models, 'id', 'nom');
			foreach($models as $model){
				$return .= CHtml::label('Équipe '.$i,'Equipe[ordre]['.$i.']');
				$return .= CHtml::dropDownList('Equipe[ordre]['.$i.']',$model->id,$list);				
				$i++;
			}
		}
		
		return $return;
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
			$model = $this->loadModel($id);
			
			$pass = 0;
			//On vérifie si il reste au moins 2 équipes liés à l'horaire à la suite de la suppression.
			if($model->siHoraire == 1){
				$equipeGarde = EquipeGarde::model()->findAll('tbl_equipe_id = '.$model->id);
				$equipe = Equipe::model()->findAll('siHoraire = 1 AND id <> '.$model->id.' AND siActif = 1');
				if(count($equipe)<>0){
					foreach($equipeGarde as $value){
						if($value['tbl_equipe_id']==$model->id){
							$garde = EquipeGarde::model()->find('modulo=:modulo AND tbl_quart_id=:quart',array(':modulo'=>$value['modulo'],':quart'=>$value['tbl_quart_id']));
							//$garde->modulo = $value['modulo'];
							//$garde->tbl_quart_id = $value['tbl_quart_id'];
							$garde->tbl_equipe_id = $equipe[0]['id'];
							
							$garde->save();
						}
					}
				}else{
					$pass = 1;
				}
			}
			//On vérifie qu'il ne reste pas d'usagers dans l'équipe
			$criteria = new CDbCriteria;
			$criteria->condition = 'tbl_equipe_id = :equipe';
			$criteria->params = array(':equipe'=>$id);
			$usagerEquipe = EquipeUsager::model()->findAll($criteria);
			
			foreach($usagerEquipe as $UE){
				$usager = Usager::model()->findByPk($UE->tbl_usager_id);
				$histoEquipe = new HistoUsagerEquipe;
				$histoEquipe->tbl_usager_id = $usager->id;
				$histoEquipe->tbl_equipe_id = $id;
				$histoEquipe->save();			
			}
			//On vérifie les ordre des équipes
			if($model->ordre!=0){
				$criteria = new CDbCriteria;
				$criteria->condition = 'ordre > '.$model->ordre;
				$equipes = Equipe::model()->findAll($criteria);
				foreach($equipes as $equipe){
					$modelE = Equipe::model()->findByPk($equipe->id);
					$modelE->ordre -= 1;
					
					$modelE->save();
				}
			}
			if($pass==0){
				$model->siHoraire=0;
				$model->siFDF=0;
				$model->siActif=0;
				$model->ordre=0;
				if($model->save()){
					$histo = new HistoEquipe;
					$histo->nom=$model->nom;
					$histo->couleur=$model->couleur;
					$histo->siHoraire=$model->siHoraire;
					$histo->siAlerte=$model->siAlerte;
					$histo->typeAction=0;
					$histo->tbl_equipe_id=$model->id;
					
					$histo->save();
				}					

				// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
				if(!isset($_GET['ajax']))
					$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
			}else{
				$this->redirect(array('view','id'=>$model->id,'m'=>$pass));
			}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($caserne="0")
	{
		$criteria = new CDbCriteria();
		
		$casernes = Caserne::model()->findAll();		
		$dataCaserne = CHtml::listData($casernes,'id','nom');
		
		if($caserne!="0"){
			$criteria->condition = "tbl_caserne_id = :cas";
			$criteria->params = array(':cas'=>$caserne);
		}
		
		$dataProvider=new CActiveDataProvider('Equipe',array('criteria'=>$criteria));
		$dataProvider->pagination->pageSize=12;
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
		$model=Equipe::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='equipe-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
