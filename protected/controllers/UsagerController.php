<?php

class UsagerController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';	
	
	public $pageTitle = "Usagers";
	
	function init(){
	  if(isset($_POST['SESSION_ID'])){
	    $session=Yii::app()->getSession();
	    $session->close();
	    $session->sessionID = $_POST['SESSION_ID'];
	    $session->open();
	  }
	  parent::init();
	}

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
	 * @return array access control rulesk
	 */
	public function accessRules()
	{
		return array(
			array('allow',
					'actions'=>array('droit','view','update','getEquipe'),
					'roles'=>array('Admin')
			),			
			array('allow',
				'actions'=>array('index','view','update','getEquipe'),
				'roles'=>array('Usager:index')	
			),
			array('allow',
				'actions'=>array('viewInvite', 'delete','create','createInvite','updateInvite','invite'),
				'roles'=>array('Usager:create')	
			),
			array('allow',
				'actions'=>array('uploadImageBrut','ajaxCrop','updateImageBrut','supprimerPhoto'),
				'roles'=>array('Usager:image')	
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
		if(!Usager::peutGerer($id)){
			throw new CHttpException(403,'Vous n\'avez pas les droits requis pour accéder à cette page.');
			Yii::app()->end();
		} else {
			$model = $this->loadModel($id);
			
			$criteria = new CDbCriteria;
			$criteria->alias = 'e';
			$criteria->join = 'INNER JOIN tbl_equipe_usager eu ON eu.tbl_equipe_id = e.id';
			$criteria->condition = 'eu.tbl_usager_id = :usager';
			$criteria->params = array(':usager'=>$id);
			
			$equipes = Equipe::model()->findAll($criteria);
			
			$criteria = new CDbCriteria;
			$criteria->alias = 't';
			$criteria->join = 'INNER JOIN tbl_groupe_usager gu ON gu.tbl_groupe_id = t.id';
			$criteria->condition = 'gu.tbl_usager_id = :usager';
			$criteria->params = array(':usager'=>$id);
			
			$groupes = Groupe::model()->findAll($criteria);
			
			$casernesA = array();
			foreach($equipes as $equipe){
				if(!in_array($equipe->tbl_caserne_id, $casernesA)){
					$casernesA[] = $equipe->tbl_caserne_id;
				}
			}
			
			$criteria = new CdbCriteria;
			$criteria->addInCondition('id', $casernesA, '');
			
			$casernes = Caserne::model()->findAll($criteria);
			
			$Arr_caserne = array();
			foreach($casernes as $caserne){
				$Arr_caserne[$caserne->id]['nom']=$caserne->nom;
				foreach($equipes as $equipe){
					if($equipe->tbl_caserne_id == $caserne->id){
						$Arr_caserne[$caserne->id]['Equipe'][]=$equipe->nom;
					}
				}
				foreach($groupes as $groupe){
					if($groupe->tbl_caserne_id == $caserne->id){
						$Arr_caserne[$caserne->id]['Groupe'][]=$groupe->nom;					
					}
				}
			}
			
			$this->render('view',array(
				'model'=>$model,
				'Casernes'=>$Arr_caserne,
			));
		}
	}
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionViewInvite($id)
	{
		$model = $this->loadModel($id);
			
		$this->render('viewInvite',array(
				'model'=>$model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Usager;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Usager']))
		{
			$model->attributes=$_POST['Usager'];
			
			$saveE = true;
			if(!isset($_POST['tblEquipes'])){
				$saveE = false;
			}
			
			if($saveE){
				if($model->save()){
					$model->refresh();
					if($_POST['fichierImageBrut']!=""){
						$model->setImageFinal($_POST['fichierImageBrut']);
					}
					$save = true;
					$model->tblEquipes = $_POST['tblEquipes'];
					if(isset($_POST['tblPostes'])) $model->tblPostes = $_POST['tblPostes'];
					if(isset($_POST['tblGroupes'])){
						$model->tblGroupes = $_POST['tblGroupes'];
						foreach($_POST['tblGroupes'] as $value){
							$histoGroupe = new HistoUsagerGroupe;
							$histoGroupe->siAjoute = 1;
							$histoGroupe->tbl_usager_id = $model->id;
							$histoGroupe->tbl_groupe_id = $value;
							
							if(!$histoGroupe->save())
								$save = false;
						}
					}
					if(isset($_POST['tblEquipes'])){
						foreach($_POST['tblEquipes'] as $value){
							$histoEquipe = new HistoUsagerEquipe;
							$histoEquipe->tbl_usager_id = $model->id;
							$histoEquipe->tbl_equipe_id = $value;
							
							if(!$histoEquipe->save())
								$save = false;
						}
					}
					if($model->save()){				
						//Sauvegarde des rôles					
						if(Yii::app()->user->checkAccess('SuperAdmin') || Yii::app()->user->checkAccess('Admin')){
							Yii::app()->authManager->assign($_POST['lstDroits'], $model->id);
							if(isset($_POST['droitHoraire'])){
								Yii::app()->authManager->assign('GesHoraire', $model->id);
							}
						}
						if($save)
							$this->redirect(array('view','id'=>$model->id));
					}
				}
			}else{
				$model->addError('tblEquipes', 'Une Équipe doit être sélectionnée');
			}
		}
	
		$roles = Yii::app()->authManager->getRoles();
		
		$listRole = array();
		foreach($roles as $role){
			if($role->name != 'GesHoraire' && $role->name != 'SuperAdmin'){
				$listRole[$role->name] = $role->name;
			}
		}
		
		$this->render('create',array(
			'model'=>$model,
			'listRole'=>$listRole,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);
		if(!Usager::peutGerer($id)){
			throw new CHttpException(403,'Vous n\'avez pas les droits requis pour accéder à cette page.');
			Yii::app()->end();
		} else {
		
			// Uncomment the following line if AJAX validation is needed
			// $this->performAjaxValidation($model);
	
			if(isset($_POST['Usager'])){
				$model->attributes=$_POST['Usager'];
				if($model->dateEmbauche=="" || $model->dateEmbauche=="0000-00-00"){
					$model->dateEmbauche = null;
				}
				$save = true;
								
				$oldModel = $this->loadModel($id);
				
				if(!isset($_POST['fichierImageBrut'])) $_POST['fichierImageBrut'] = "";
				
				if($_POST['fichierImageBrut']!=""){
					$model->setImageFinal($_POST['fichierImageBrut']);
				}

				//$ancienRole = $model->grade->roleName;
				if(Yii::app()->user->checkAccess('Admin')){
					$tblGroupe = GroupeUsager::model()->findAll('tbl_usager_id = '.$model->id);
					if(isset($_POST['tblGroupes'])){ 
						$model->tblGroupes = $_POST['tblGroupes'];
						foreach($tblGroupe as $old){
							//On boucle pour savoir de quels groupes l'usager a été supprimés
							$present = false;
							foreach($_POST['tblGroupes'] as $new){
								if($old['tbl_groupe_id']==$new['0']){
									$present = true;
								}
							}
							if(!$present){
								$histoGroupe = new HistoUsagerGroupe;
								
								$histoGroupe->siAjoute = 0;
								$histoGroupe->tbl_usager_id = $model->id;
								$histoGroupe->tbl_groupe_id = $old['tbl_groupe_id'];
								
								$histoGroupe->save();
							}
						}
						foreach($_POST['tblGroupes'] as $new){
							//On boucle pour savoir dans quels groupes l'usager a été ajouter.
							$present = false;
							foreach($tblGroupe as $old){
								if($old['tbl_groupe_id']==$new){
									$present = true;
								}							
							}
							if(!$present){
								$histoGroupe = new HistoUsagerGroupe;
								$histoGroupe->siAjoute = 1;
								$histoGroupe->tbl_usager_id = $model->id;
								$histoGroupe->tbl_groupe_id = $new;
								$histoGroupe->save();
							}					
						}
					}else{
						if(count($tblGroupe)>=1){
							foreach($tblGroupe as $old){
									$histoGroupe = new HistoUsagerGroupe;
									
									$histoGroupe->siAjoute = 0;
									$histoGroupe->tbl_usager_id = $model->id;
									$histoGroupe->tbl_groupe_id = $old['tbl_groupe_id'];
									
									$histoGroupe->save();						
							}
							$model->tblGroupes = NULL;
						}
					}
					$tblEquipe = EquipeUsager::model()->findAll('tbl_usager_id = '.$model->id);
					if(isset($_POST['tblEquipes'])){
						$model->tblEquipes = $_POST['tblEquipes'];
						foreach($tblEquipe as $old){
							//On boucle pour savoir de quels equipes l'usager a été supprimés
							$present = false;
							foreach($_POST['tblEquipes'] as $new){
								if($old['tbl_equipe_id']==$new['0']){
									$present = true;
								}
							}
							if(!$present){
								$histoEquipe = new HistoUsagerEquipe;
						
								$histoEquipe->tbl_usager_id = $model->id;
								$histoEquipe->tbl_equipe_id = $old['tbl_equipe_id'];
								
								$histoEquipe->save();
							}
						}
						foreach($_POST['tblEquipes'] as $new){
							//On boucle pour savoir dans quels equipes l'usager a été ajouter.
							$present = false;
							foreach($tblEquipe as $old){
								if($old['tbl_equipe_id']==$new['0']){
									$present = true;
								}							
							}
							if(!$present){
								$histoEquipe = new HistoUsagerEquipe;
								
								$histoEquipe->tbl_usager_id = $model->id;
								$histoEquipe->tbl_equipe_id = $new;
								
								$histoEquipe->save();
							}					
						}
					}else{
						if(count($tblEquipe)>=1){
							foreach($tblEquipe as $old){
									$histoEquipe = new HistoUsagerEquipe;
									
									$histoEquipe->tbl_usager_id = $model->id;
									$histoEquipe->tbl_equipe_id = $old['tbl_equipe_id'];
									
									$histoEquipe->save();						
							}
						}
						$save = false;
					}
					if(isset($_POST['tblPostes'])){
						$model->tblPostes = $_POST['tblPostes'];
					}elseif(count($oldModel->tblPostes)>=1){
						$model->tblPostes = NULL;
					}
					/*if($oldModel->tbl_equipe_id != $model->tbl_equipe_id){
						$histoEquipe = new HistoUsagerEquipe;
						$histoEquipe->tbl_usager_id = $model->id;
						$histoEquipe->tbl_equipe_id = $model->tbl_equipe_id;
						
						$histoEquipe->save();
					}*/
				}
				if($oldModel->matricule!=$model->matricule){
					$histoUsager = new HistoUsager;
					$histoUsager->tbl_usager_id = $model->id;
					$histoUsager->matricule = $oldModel->matricule;
					
					$histoUsager->save();
				}

				if($model->save() && $save) {
					
					//Sauvegarde des rôles
					$roles = Yii::app()->authManager->getAuthItems(2,$model->id);
					$gesHoraire = false;
					$roleU = 'Usager';
					foreach($roles as $role){
						if($role->name=='GesHoraire'){
							$gesHoraire = true;
						}else{
							$roleU = $role->name;
						}
					}

					if(Yii::app()->user->checkAccess('SuperAdmin')){
						if($_POST['lstDroits'] != $roleU){
							Yii::app()->authManager->revoke($roleU, $model->id);
							Yii::app()->authManager->assign($_POST['lstDroits'], $model->id);
						}
						
						if($gesHoraire && !isset($_POST['droitHoraire'])){
							Yii::app()->authManager->revoke('GesHoraire',$model->id);
						}
						if(!$gesHoraire && isset($_POST['droitHoraire'])){
							Yii::app()->authManager->assign('GesHoraire', $model->id);						
						}
					}elseif(Yii::app()->user->checkAccess('Admin')){
						if(Yii::app()->user->id == $model->id || ((!($roleU=='Admin'||$roleU=='SuperAdmin')))){
							if($_POST['lstDroits'] != $roleU){
								Yii::app()->authManager->revoke($roleU, $model->id);
								Yii::app()->authManager->assign($_POST['lstDroits'], $model->id);
							}
							
							if($gesHoraire && !isset($_POST['droitHoraire'])){
								Yii::app()->authManager->revoke('GesHoraire',$model->id);
							}
							if(!$gesHoraire && isset($_POST['droitHoraire'])){
								Yii::app()->authManager->assign('GesHoraire', $model->id);
							}							
						}
					}
					
					$model->refresh();
					if(Yii::app()->user->id==$model->id){
						Yii::app()->user->equipe = $model->tblEquipes;
					}
					$this->redirect(array('view','id'=>$model->id));
				}elseif($save==false){
					$model->addError('tblEquipes', 'Une Équipe doit être sélectionnée');
				}
				
				
				/* else {
					Yii::app()->clientScript->registerScript('alerte','console.log('.json_encode($model->getErrors()).');');
				}*/
			}

			//Sauvegarde des rôles
			$roles = Yii::app()->authManager->getAuthItems(2,$model->id);
			$roleU = 'Usager';
			foreach($roles as $role){
				if($role->name=='GesHoraire'){
				}else{
					$roleU = $role->name;
				}
			}
			
			$roles = Yii::app()->authManager->getRoles();
			
			$listRole = array();
			foreach($roles as $role){
				if($role->name != 'GesHoraire' && ($role->name != 'SuperAdmin' || $roleU == 'SuperAdmin') ){
					$listRole[$role->name] = $role->name;
				}
			}
			
			$this->render('update',array(
				'model'=>$model,
				'listRole'=>$listRole,
			));
		}
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
			$model = $this->loadModel($id);
			$model->actif = "0";
			$model->pseudo = base64_encode($model->id.$model->pseudo);
			$model->courriel = "none";
			$auth = Yii::app()->authManager;
			$criteria = new CDbCriteria;
			$criteria->condition = 'tbl_usager_id = :usager';
			$criteria->params = array(':usager'=>$id);
			$equipes = EquipeUsager::model()->findAll($criteria);
			foreach($equipes as $equipe){
				$histoEquipe = new HistoUsagerEquipe;
				$histoEquipe->tbl_usager_id = $id;
				$histoEquipe->tbl_equipe_id = $equipe->tbl_equipe_id;
				
				$histoEquipe->save();
				
				$equipe->delete();
			}
			if($model->save()){		
				foreach($auth->getAuthAssignments($model->id) as $value){
					$auth->revoke($value->getItemName(),$model->id);
				}
			}else{
				throw new CHttpException(500,"Erreur lors de la suppression de l'usager : ".
					json_encode($model->getErrors()), 1);
				
			}

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}
	
	/**
	 * Creates a new model invité.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreateInvite()
	{
		$model=new Usager;
	
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
	
		if(isset($_POST['Usager']))
		{
			$model->attributes=$_POST['Usager'];
			$model->actif = '1';
			$model->enService = '1';
			$model->tempsPlein = '2';
			$model->alerteFDF = '0';
			$model->heureTravaillee = '1';
			
			if($model->save()){
				$this->redirect(array('viewInvite','id'=>$model->id));
			}
		}
		
		$this->render('createInvite',array(
				'model'=>$model,
		));
	}
	
	/**
	 * Updates a new model invité.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionUpdateInvite($id)
	{
		$model=Usager::model()->findByPK($id);
	
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
	
		if(isset($_POST['Usager']))
		{
			$model->attributes=$_POST['Usager'];
				
			if($model->save()){
				$this->redirect(array('viewInvite','id'=>$model->id));
			}
		}
	
		$this->render('updateInvite',array(
				'model'=>$model,
		));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($caserne="0", $equipe="0")
	{
		$usager = Usager::model()->findByPk(Yii::app()->user->id);
		$casernesUsager = $usager->getCaserne();
		$casernes = Caserne::model()->findAll(array('condition'=>'id IN ('.$casernesUsager.') AND siActif = 1'));
		$dataCaserne = array();
		foreach($casernes as $c){
			$dataCaserne[$c->id] = $c->nom;
		}
		
		$criteriaEquipe = new CDbCriteria;
		$criteriaEquipe->condition = 'siActif = 1';
		if($caserne == "0"){
			$criteriaEquipe->condition .= ' AND tbl_caserne_id IN ('.$casernesUsager.')';
		}else{
			$criteriaEquipe->condition .= ' AND tbl_caserne_id = :caserne';
			$criteriaEquipe->params = array(':caserne'=>$caserne);
		}
		$equipes = Equipe::model()->findAll($criteriaEquipe);
		$idEquipes = '';
		$dataEquipe = array();
		foreach($equipes as $e){
			$idEquipes .= $e->id.', ';
			$dataEquipe[$e->id]=$e->nom;
		}
		$idEquipes = substr($idEquipes,0,strlen($idEquipes)-2);
		
		//comment1
		
		$criteria = new CDbCriteria;
		$criteria->alias = "t";
		$criteria->order = "t.matricule";
		$criteria->condition ='t.tempsPlein <> 2';

		if($caserne!="0" || $equipe!="0" || ($caserne=="0" && !Yii::app()->user->checkAccess('GesService'))){
			$criteria->join = "INNER JOIN tbl_equipe_usager eu ON eu.tbl_usager_id=t.id";
			if($equipe=="0"){
				$criteria->condition .= ' AND eu.tbl_equipe_id IN ('.$idEquipes.')';
			}else{
				$criteria->condition .= ' AND eu.tbl_equipe_id = :equipe';
				$criteria->params = array(':equipe'=>$equipe);
			}
		}
		
		$dataProvider=new CActiveDataProvider('Usager',array('criteria'=>$criteria,'pagination'=>array('pageSize'=>6)));
		
		if(isset($_GET['ajax'])){
			$this->renderPartial('index',array(
					'dataProvider'=>$dataProvider,
					'dataEquipe'=>$dataEquipe,
					'dataCaserne'=>$dataCaserne,
					'caserne'=>$caserne,
					'equipe'=>$equipe,
			));
		}else{
			$this->render('index',array(
					'dataProvider'=>$dataProvider,
					'dataEquipe'=>$dataEquipe,
					'dataCaserne'=>$dataCaserne,
					'caserne'=>$caserne,
					'equipe'=>$equipe,
			));
		}

		
		/**
		 * comment1
		 * 
		$criteria = new CDbCriteria;
		if($equipe != "0"){
			$criteria->condition = 'tbl_equipe_id = '.$equipe;
		}else{
			$criteria->condition = 'tbl_equipe_id IN ('.$idEquipes.')';
		}
		$equipeUsager = EquipeUsager::model()->findAll($criteria);
		
		$idUsagers = '';
		foreach($equipeUsager as $EU){
			$idUsagers .= $EU->tbl_usager_id.', ';
		}		
		$idUsagers = substr($idUsagers,0,strlen($idUsagers)-2);
		*/
	}
	
	/**
	 * Lists all models Invités.
	 */
	public function actionInvite()
	{
		$usager = Usager::model()->findByPk(Yii::app()->user->id);
		//comment1
	
		$criteria = new CDbCriteria;
		$criteria->alias = "t";
		$criteria->order = "t.matricule";
		$criteria->condition ='t.tempsPlein = 2';
		
		$dataProvider=new CActiveDataProvider('Usager',array('criteria'=>$criteria,'pagination'=>array('pageSize'=>6)));
	
		if(isset($_GET['ajax'])){
			$this->renderPartial('index',array(
					'dataProvider'=>$dataProvider,
			));
		}else{
			$this->render('invite',array(
					'dataProvider'=>$dataProvider,
			));
		}
	}

	public function actionUploadImageBrut(){
		$model = Usager::model()->findByPk($_POST['user']);
		$image = CUploadedFile::getInstanceByName('imageUpload');
		$imagename = time().'.'.$image->getExtensionName();
		if($model->image!=NULL){
			$ancienImage = $model->image;
		}
		$model->image = $imagename;
		if(!$model->save() || !$image->saveAs('imagesProfil'.DIRECTORY_SEPARATOR.DOMAINE.DIRECTORY_SEPARATOR.$imagename)){
			Yii::log('Saving the model or image failed','error','Usager.action.uploadImageBrut');
			$model->image = $ancienImage;
			$model->save();
			throw new CHttpException(500);
		}else{
				Yii::log('Saved the model/image successfuly','info','Usager.action.uploadImageBrut');
				if(isset($ancienImage) && file_exists('imageProfil'.DIRECTORY_SEPARATOR.DOMAINE.DIRECTORY_SEPARATOR.$ancienImage)) {
					unlink('imagesProfil'.DIRECTORY_SEPARATOR.DOMAINE.DIRECTORY_SEPARATOR.$ancienImage);
				}
				echo '1<br/>';
		};
		Yii::app()->end();
	}
	
	public function actionUpdateImageBrut($uid){
		Yii::log('Enter function actionUpdateImageBrut','trace','Usager.action');
		$usager = Usager::model()->findByPk($uid);
		if(!$usager){
			Yii::log('Usager not found','error','Usager.action.updateImageBrut');
			throw new CHttpException(500);
		}
		Yii::log('Returning the image','info','Usager.action.updateImageBrut');
		return $this->renderPartial('_espaceCrop',array('model'=>$usager));
	}
	
	public function actionAjaxCrop(){
		Yii::log('Enter function actionAjaxCrop()','trace','Usager.action');
		Yii::import('system.ext.jcrop.EJCropper');
		$jcropper = new EJCropper();
		$jcropper->thumbPath = Yii::getPathOfAlias('webroot').'/imagesProfil/'.DOMAINE;
		 
		// some settings ...
		$jcropper->jpeg_quality = 95;
		$jcropper->png_compression = 8;
		 
		Yii::log('Get the crooping coordinates','info','Usager.action');
		// get the image cropping coordinates (or implement your own method)
		$coords = $jcropper->getCoordsFromPost('imageId');
		 
		// returns the path of the cropped image, source must be an absolute path.
		Yii::log('Crooping image '.$_POST['image'],'info','Usager.action');
		$result = $jcropper->crop(Yii::getPathOfAlias('webroot').'/imagesProfil/'.DOMAINE.DIRECTORY_SEPARATOR.$_POST['image'], $coords);
		if($result){
			echo 'imagesProfil/'.DOMAINE.'/'.$_POST['image'];
		}
	}
	
	public function actionSupprimerPhoto($uid){
		$usager = Usager::model()->findByPk($uid);
		
		if(file_exists(Yii::getPathOfAlias('webroot').'/imagesProfil/'.DOMAINE.DIRECTORY_SEPARATOR.$usager->image) && $usager->image!=NULL){
			unlink(Yii::getPathOfAlias('webroot').'/imagesProfil/'.DOMAINE.DIRECTORY_SEPARATOR.$usager->image);
		}
		$usager->image = NULL;
		if($usager->getImageFinal()){
			unlink(Yii::getPathOfAlias('webroot').'/imagesProfil/'.DOMAINE.DIRECTORY_SEPARATOR.'final/'.$usager->getImageFinal());
		}
		
		$usager->save();
		$this->redirect(array('update','id'=>$usager->id));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Usager::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='usager-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionGetEquipe()
	{
		echo CHtml::tag('option',array('value'=>'0'),CHtml::encode('- Tous -'),true);
		if($_POST['caserne']!=0){
			$equipes = Equipe::model()->findAll(array('condition'=>'tbl_caserne_id = '.$_POST['caserne'].' AND siActif = 1 ', 'order'=>'nom ASC'));
			foreach($equipes as $equipe){
				echo CHtml::tag('option',array('value'=>$equipe->id),CHtml::encode($equipe->nom),true);
			}
		}else{
			$equipes = Equipe::model()->findAll(array('condition'=>'siActif = 1 ', 'order'=>'nom ASC'));
			foreach($equipes as $equipe){
				echo CHtml::tag('option',array('value'=>$equipe->id),CHtml::encode($equipe->nom),true);
			}			
		}
	}
}
