<?php

class DocumentController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	
	public $pageTitle = 'Document';

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
				'actions'=>array('index', 'view', 'visionner', 'telecharger'),
				'roles'=>array('Document:index'),
			),
			array('allow', 
				'actions'=>array('create','update', 'delete', 'suivi'),
				'roles'=>array('Document:create'),
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
		
		$model=new Document;
	
		$model->date = date('Y-m-d');
		$types = TypeDocument::model()->findAll(array('condition'=>'siActif = 1'));
		$lstType = CHtml::listData($types, 'id', 'nom');		

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Document']))
		{

			$model->attributes=$_POST['Document'];
			if(CUploadedFile::getInstance($model,'fichier') !== null)
			{
				unset($model->url);
				$model->fichier=CUploadedFile::getInstance($model,'fichier');			
				$model->nom_fichier = $model->nom.strrchr($model->fichier->name,'.');			
				$model->nom_fichier = $model->stripAccents($model->nom_fichier);
				$model->fichier->__construct($model->nom_fichier, $model->fichier->tempName, $model->fichier->type, $model->fichier->size, $model->fichier->error);
				if($model->save()){
					if(isset($_POST['tblCasernes'])){ $model->tblCasernes = $_POST['tblCasernes'];}
					else{
						$_POST['tblCasernes'] = explode(', ',$casernesUsager);
						$model->tblCasernes = $_POST['tblCasernes'];
					}					
					$model->nom_fichier = $model->nom.'-'.$model->id.strrchr($model->fichier->name,'.');			
					$model->nom_fichier = $model->stripAccents($model->nom_fichier);
					if($model->fichier->saveAs(Yii::app()->basePath.DIRECTORY_SEPARATOR.'document'.DIRECTORY_SEPARATOR.DOMAINE.DIRECTORY_SEPARATOR.$model->nom_fichier)){
						if($model->save()){
							$this->redirect(array('view','id'=>$model->id));
						}
					}
				}
			}
			else if(isset($model->url))
			{
				$infoVideo = $this->parseVideos($model->url);
				$model->url = $infoVideo[0]['url'];
				if($model->save())
				{
					if(isset($_POST['tblCasernes'])){ $model->tblCasernes = $_POST['tblCasernes'];}
					else{
						$_POST['tblCasernes'] = explode(', ',$casernesUsager);
						$model->tblCasernes = $_POST['tblCasernes'];
					}
					if($model->save())
					{		
						$this->redirect(array('view','id'=>$model->id));
					}
				}
			}
		}

		$this->render('create',array(
			'model'=>$model,
			'lstType'=>$lstType,
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
		$types = TypeDocument::model()->findAll(array('condition'=>'siActif = 1'));
		$lstType = CHtml::listData($types, 'id', 'nom');

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Document']))
		{
			if(CUploadedFile::getInstance($model,'fichier') !== null){
				if(file_exists(Yii::app()->basePath.DIRECTORY_SEPARATOR.'document'.DIRECTORY_SEPARATOR.DOMAINE.DIRECTORY_SEPARATOR.$model->nom_fichier)){
					unlink(Yii::app()->basePath.DIRECTORY_SEPARATOR.'document'.DIRECTORY_SEPARATOR.DOMAINE.DIRECTORY_SEPARATOR.$model->nom_fichier);
				}				
			}
			
			$model->attributes=$_POST['Document'];
			if(isset($_POST['tblCasernes'])){
				$model->tblCasernes = $_POST['tblCasernes'];
			}else{
				$model->tblCasernes = NULL;
			}
			
			if(CUploadedFile::getInstance($model,'fichier') !== null){
				unset($model->url);
				$model->fichier=CUploadedFile::getInstance($model,'fichier');
				$model->nom_fichier = $model->nom.strrchr($model->fichier->name,'.');
				$model->nom_fichier = $model->stripAccents($model->nom_fichier);
				$model->fichier->__construct($model->nom_fichier, $model->fichier->tempName, $model->fichier->type, $model->fichier->size, $model->fichier->error);
				if($model->save()){
					if(isset($_POST['tblCasernes'])){ $model->tblCasernes = $_POST['tblCasernes'];}
					else{
						$_POST['tblCasernes'] = explode(', ',$casernesUsager);
						$model->tblCasernes = $_POST['tblCasernes'];
					}
					$model->nom_fichier = $model->nom.'-'.$model->id.strrchr($model->fichier->name,'.');
					$model->nom_fichier = $model->stripAccents($model->nom_fichier);
					if($model->fichier->saveAs(Yii::app()->basePath.DIRECTORY_SEPARATOR.'document'.DIRECTORY_SEPARATOR.DOMAINE.DIRECTORY_SEPARATOR.$model->nom_fichier)){
						if($model->save()){
							$this->redirect(array('view','id'=>$model->id));
						}
					}
				}				
			}
			
			if($model->save()){				
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update',array(
			'model'=>$model,
			'lstType'=>$lstType,
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
		$documents = DocumentCaserne::model()->findAll(array('condition'=>'tbl_document_id = '.$id));
		foreach($documents as $document){
			$document->delete();
		}
	
		// we only allow deletion via POST request
		$model = $this->loadModel($id);
		if($model->url === null)
		{
			if(file_exists(Yii::app()->basePath.DIRECTORY_SEPARATOR.'document'.DIRECTORY_SEPARATOR.DOMAINE.DIRECTORY_SEPARATOR.$model->nom_fichier)){
				unlink(Yii::app()->basePath.DIRECTORY_SEPARATOR.'document'.DIRECTORY_SEPARATOR.DOMAINE.DIRECTORY_SEPARATOR.$model->nom_fichier);
			}
			$model->delete();
		}

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex($caserne="0",$type="0")
	{
		$usager = Usager::model()->findByPk(Yii::app()->user->id);

		$casernesUsager = $usager->getCaserne();
		
		$conditionCaserne = 'tbl_caserne_id IN ('.$casernesUsager.')';
		
		if($caserne=='0'){
				$condition = $conditionCaserne;
		}else{
			$condition = 'tbl_caserne_id = '.$caserne;
		}
		$documents = DocumentCaserne::model()->findAll(array('condition'=>$condition));
		$ids = '(';
		foreach($documents as $document){
			$ids .= '"'.$document->tbl_document_id.'", ';
		}
		if($ids != '('){
			$ids = substr($ids, 0, strlen($ids)-2);
			$condition = 'id IN '.$ids.')';
		}else{
			$condition = 'id = 0';
		}
		
		$casernes = Caserne::model()->findAll(array('condition'=>'siActif = 1 AND id IN ('.$casernesUsager.')', 'order'=>'id ASC'));
		$dataCaserne = array();
		$dataCaserne['0'] = '- Tous -';
		foreach($casernes as $cas){
			$dataCaserne[$cas->id]=$cas->nom;
		}
		
		if($type!='0'){
			$condition .= ' AND tbl_type_id = '.$type;
		}		
		
		$types = TypeDocument::model()->findAll(array('condition'=>'siActif = 1', 'order'=>'nom ASC'));
		$dataType = array();
		$dataType['0'] = '- Tous -';
		foreach($types as $ty){
			$dataType[$ty->id]=$ty->nom;
		}
		$dataProvider=new CActiveDataProvider('Document', array(
			'criteria'=>array(
					'condition'=>$condition,
					'order'=>'date DESC',
				),					
		));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'caserne'=>$caserne,
			'dataCaserne'=>$dataCaserne,
			'type'=>$type,
			'dataType'=>$dataType,
		));
	}
	public function actionVisionner($id)
	{
		$document = Document::model()->findByPk($id);
		
		//On vérifie si l'usager a le droit de visionner ce document. Permet d'empêcher le monde d'y aller via l'URL en mettant des ID random
		$droit = false;
		if(Yii::app()->user->checkAccess('SuperAdmin') || Yii::app()->user->checkAccess('Admin') || Yii::app()->user->checkAccess('GesService')){
			$droit = true;
		}else{
			$usager = Usager::model()->findByPk(Yii::app()->user->id);
			
			$casernesUsager = explode(', ',$usager->getCaserne());
			
			$casernesDocument = DocumentCaserne::model()->findAll('tbl_document_id = :document',array(':document'=>$document->id));
			
			foreach($casernesDocument as $casDoc){
				foreach($casernesUsager as $casUsa){
					if($casDoc->tbl_caserne_id == $casUsa){
						$droit = true;
					}
				}
			}
		}
		
		if($droit){			
			//On fait le suivi si la case a été cochée
			if($document->suivi==1){			
				$documentSuivi = DocumentSuivi::model()->find('tbl_document_id = :document AND tbl_usager_id = :usager', array(':document'=>$document->id,':usager'=>Yii::app()->user->id));
				
				if($documentSuivi === NULL){
					$documentSuivi = new DocumentSuivi;
					
					$documentSuivi->tbl_document_id = $document->id;
					$documentSuivi->tbl_usager_id = Yii::app()->user->id;
					
					$documentSuivi->save();
				}
			}
			$this->render('visionner',array(
				'document'=>$document,
			));
		}else{
			$this->redirect(array('index'));
		}
	}
	
	public function actionTelecharger($id)
	{
		$document = Document::model()->findByPk($id);
		
		//On vérifie si l'usager a le droit de visionner ce document. Permet d'empêcher le monde d'y aller via l'URL en mettant des ID random
		$droit = false;
		if(Yii::app()->user->checkAccess('SuperAdmin') || Yii::app()->user->checkAccess('Admin') || Yii::app()->user->checkAccess('GesService')){
			$droit = true;
		}else{
			$usager = Usager::model()->findByPk(Yii::app()->user->id);
			
			$casernesUsager = explode(', ',$usager->getCaserne());
			
			$casernesDocument = DocumentCaserne::model()->findAll('tbl_document_id = :document',array(':document'=>$document->id));
			
			foreach($casernesDocument as $casDoc){
				foreach($casernesUsager as $casUsa){
					if($casDoc->tbl_caserne_id == $casUsa){
						$droit = true;
					}
				}
			}
		}
		
		if($droit){	
			//On fait le suivi si la case a été cochée
			if($document->suivi==1){			
				$documentSuivi = DocumentSuivi::model()->find('tbl_document_id = :document AND tbl_usager_id = :usager', array(':document'=>$document->id,':usager'=>Yii::app()->user->id));
				
				if($documentSuivi === NULL){
					$documentSuivi = new DocumentSuivi;
					
					$documentSuivi->tbl_document_id = $document->id;
					$documentSuivi->tbl_usager_id = Yii::app()->user->id;
					
					$documentSuivi->save();
				}
			}
			
			$file_url = 'protected/document/'.DOMAINE.'/'.$document->nom_fichier;
			header('Content-Type: application/octet-stream');
			header("Content-Transfer-Encoding: Binary"); 
			header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
			readfile($file_url); // do the double-download-dance (dirty but worky)
		}
	}
	
	public function actionSuivi(){
		$criteria = new CDbCriteria;
		$criteria->alias = 'ds';
		$criteria->join = 'LEFT JOIN tbl_document d ON d.id = ds.tbl_document_id';
		$criteria->join .= ' LEFT JOIN tbl_document_caserne dc ON dc.tbl_document_id = d.id';
		$criteria->join .= ' LEFT JOIN tbl_caserne c ON c.id = dc.tbl_caserne_id AND c.siActif = 1';
		
		
		if(!(Yii::app()->user->checkAccess('SuperAdmin') || Yii::app()->user->checkAccess('Admin') || Yii::app()->user->checkAccess('GesService'))){			
			$usager = Usager::model()->findByPk(Yii::app()->user->id);
			
			$casernesUsager = $usager->getCaserne();

			$criteria->join .= ' AND c.id IN ('.$casernesUsager.')';			
		}
		
		$criteria->order = 'tbl_document_id ASC, tbl_usager_id ASC, date ASC';
		
		$documentsSuivis = DocumentSuivi::model()->findAll($criteria);
		
		$dataProvider=new CActiveDataProvider('DocumentSuivi', array(
			'criteria'=>$criteria,				
		));		
		
		$this->render('suivi',array(
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
		$model=Document::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='document-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	// https://gist.github.com/jlamim/47152ba2dfc463bf05f4
	public function parseVideos($videoString = null)
	{
	    // return data
	    $videos = array();

	    if (!empty($videoString)) {

	        // split on line breaks
	        $videoString = stripslashes(trim($videoString));
	        $videoString = explode("\n", $videoString);
	        $videoString = array_filter($videoString, 'trim');
	        // check each video for proper formatting
	        foreach ($videoString as $video) 
	        {
	            // check for iframe to get the video url
	            if (strpos($video, 'iframe') !== FALSE) {
	                // retrieve the video url
	                $anchorRegex = '/src="(.*)?"/isU';
	                $results = array();
	                if (preg_match($anchorRegex, $video, $results)) {
	                    $link = trim($results[1]);
	                }
	            } else {
	                // we already have a url
	                $link = $video;
	            }

	            // if we have a URL, parse it down
	            if (!empty($link)) {

	                // initial values
	                $video_id = NULL;
	                $videoIdRegex = NULL;
	                $results = array();

	                // check for type of youtube link
	                if (strpos($link, 'youtu') !== FALSE) {
	                    // works on:
	                    // http://www.youtube.com/watch?v=VIDEOID
						if(strpos($link, 'youtube.com/watch') !== FALSE){
							$videoIdRegex = '/youtube.*watch\?v=([a-zA-Z0-9\-_]+)/';
						}
						else if (strpos($link, 'youtube.com') !== FALSE) {
	                        // works on:
	                        // http://www.youtube.com/embed/VIDEOID
	                        // http://www.youtube.com/embed/VIDEOID?modestbranding=1&amp;rel=0
	                        // http://www.youtube.com/v/VIDEO-ID?fs=1&amp;hl=en_US
	                        $videoIdRegex = '/youtube.com\/(?:embed|v){1}\/([a-zA-Z0-9_]+)\??/i';
	                    } else if (strpos($link, 'youtu.be') !== FALSE) {
	                        // works on:
	                        // http://youtu.be/daro6K6mym8
	                        $videoIdRegex = '/youtu.be\/([a-zA-Z0-9_]+)\??/i';
	                    }

	                    if ($videoIdRegex !== NULL) {
	                        if (preg_match($videoIdRegex, $link, $results)) {
	                            $video_str = 'https://www.youtube.com/embed/%s';
	                            $video_id = $results[1];
	                        }
	                    }
	                }

	                // handle vimeo videos
	                else if (strpos($video, 'vimeo') !== FALSE) {
	                    if (strpos($video, 'player.vimeo.com') !== FALSE) {
	                        // works on:
	                        // http://player.vimeo.com/video/37985580?title=0&amp;byline=0&amp;portrait=0
	                        $videoIdRegex = '/player.vimeo.com\/video\/([0-9]+)\??/i';
	                    } else {
	                        // works on:
	                        // http://vimeo.com/37985580
	                        //$videoIdRegex = '/vimeo.com\/([0-9]+)\??/i';
	                        $videoIdRegex = '/\/[^\/]+$/';
	                    }

	                    if ($videoIdRegex !== NULL) {
	                        if (preg_match($videoIdRegex, $link, $results)) {
	                        	if (strpos($video, 'player.vimeo.com') !== FALSE) 
	                        	{
	                           		$video_id = $results[1];
	                           	}
	                           	else
	                           	{
	                           		$video_id = $results[0];
	                           	}
	                            // get the thumbnail
	                            $video_str = 'https://player.vimeo.com/video%s';
	                        }
	                    }
	                }
	                // check if we have a video id, if so, add the video metadata
	                if (!empty($video_id)) {
	                    // add to return
	                    $videos[] = array(
	                        'url' => sprintf($video_str, $video_id),
	                    );
	                }
	            }
	        }
	    }
    // return array of parsed videos
    return $videos;
	}
}
