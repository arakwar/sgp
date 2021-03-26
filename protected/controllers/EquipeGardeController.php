<?php

class EquipeGardeController extends Controller
{	
	public $pageTitle = 'Équipe de garde';
	
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
						'actions'=>array('create','update','index','view', 'delete', 'garde'),
						'roles'=>array('SuperAdmin'),
				),
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}
	
	public function actionIndex($id)
	{	
		$message = "";
		
		$casernes = Caserne::model()->findAll(array('condition'=>'siActif = 1'));
		if(isset($_POST)){
			foreach($_POST as $key => $value){
				$strpos = strpos($key,'-');
				$caserne_id = substr($key,0,$strpos);
				$modquart = substr($key,$strpos+1);
				if(is_numeric($modquart)){

					$modulo = floor($modquart/10);
					$quart_id = $modquart%10;
					
					$garde = EquipeGarde::model()->find('modulo=:modulo AND tbl_quart_id=:quart AND tbl_caserne_id = :caserne AND tbl_garde_id = :id',array(':modulo'=>$modulo,':quart'=>$quart_id, ':caserne'=>$caserne_id, ':id'=>$id));
					if($garde===NULL){
						$garde = new EquipeGarde;
						$garde->modulo = $modulo;
						$garde->tbl_quart_id = $quart_id;	
						$garde->tbl_caserne_id = $caserne_id;
						$garde->tbl_garde_id = $id;
					}
					if($garde->tbl_equipe_id != $value){
						$histo = new HistoEquipeGarde;
						
						$histo->modulo = $modulo;
						$histo->tbl_quart_id = $quart_id;
						$histo->tbl_equipe_id = $value;
						$garde->tbl_caserne_id = $caserne_id;
						$garde->tbl_garde_id = $id;
						
						$histo->save();
						
						$garde->tbl_equipe_id = $value;
					}
					
					if($garde->save())
						$message = Yii::t('controller','equipeGarde.index.enregistrementReussi')."<br/>";
				}
			}
		}
		$parametres = Parametres::model()->findByPk(1);
		$garde = Garde::model()->findByPk($id);
		foreach($casernes as $caserne){
			$equipes = Equipe::model()->findAll('tbl_caserne_id = :caserne AND siHoraire = 1',array(':caserne'=>$caserne->id));
			$criteria = new CDbCriteria;		
			$criteria->alias = 'q';
			$criteria->join = 'LEFT JOIN tbl_poste_horaire ph ON ph.tbl_quart_id = q.id '.
							'LEFT JOIN tbl_poste_horaire_caserne phc ON phc.tbl_poste_horaire_id = ph.id AND phc.tbl_caserne_id = :caserne';		
			$criteria->params = array(':caserne'=>$caserne->id);
			$criteria->group = 'q.id';		
			$quarts = Quart::model()->findAll($criteria);		
			
			$idEquipes = array();
			foreach($equipes as $equipe){
				$idEquipes[] = $equipe->id;
			}
			
			$idQuarts = array();
			foreach($quarts as $quart){
				$idQuarts[] = $quart->id;
			}
			$criteria = new CDbCriteria;
			$criteria->condition = 'tbl_garde_id = :id';
			$criteria->params = array(':id'=>$id);
			$criteria->addInCondition('tbl_quart_id', $idQuarts);
			$criteria->addInCondition('tbl_equipe_id', $idEquipes);
			
			
			$EG = EquipeGarde::model()->count($criteria);
			if($EG==0){
				$idEquipe = $equipes['0']->id;
				
				foreach($quarts as $quart){
					$i = 0;
					while($i < $garde->nbr_jour_periode){
						$equipeGarde = new EquipeGarde;
						
						$equipeGarde->modulo = $i;
						$equipeGarde->tbl_quart_id = $quart->id;
						$equipeGarde->tbl_equipe_id = $idEquipe;
						$equipeGarde->tbl_caserne_id = $caserne->id;
						$equipeGarde->tbl_garde_id = $id;
						
						$equipeGarde->save();
						
						$i++;
					}
				}
				
			}
		}
		$this->render('index',array(
			'message'=>$message,
			'casernes'=>$casernes,
			'idGarde'=>$id,
			'parametres' => $parametres,
		));
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
		$model=new Garde;
	
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
	
		if(isset($_POST['Garde']))
		{
			$model->attributes=$_POST['Garde'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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
	
		if(isset($_POST['Garde']))
		{
			$model->attributes=$_POST['Garde'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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
		$model=$this->loadModel($id);
			
		$criteriaEquipe = new CDbCriteria;
		$criteriaEquipe->condition = 'tbl_garde_id = :id';
		$criteriaEquipe->params = array(':id'=>$id);
		
		$equipes = EquipeGarde::model()->findAll($criteriaEquipe);
		
		foreach($equipes as $equipe){
			$equipe->delete();
		}
		
		$model->delete();
		
		$this->redirect(array('garde'));
	}
	
	/**
	 * Lists all models.
	 */
	public function actionGarde()
	{
		$layout='//layouts/column2';
		
		$criteria = new CDbCriteria();
	
		$dataProvider=new CActiveDataProvider('Garde',array('criteria'=>$criteria));
		$this->render('garde',array(
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
		$model=Garde::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404, Yii::t('erreur','erreur404'));
		return $model;
	}

}