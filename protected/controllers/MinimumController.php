<?php

class MinimumController extends Controller
{
	public $pageTitle = "Minimums - Force de Frappe";
	
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
						'actions'=>array('index','getException','save'),
						'roles'=>array('Minimum:index'),
				),
				array('deny',  // deny all users
						'users'=>array('*'),
				),
		);
	}
	
	public function actionIndex($caserne="0")
	{
		$usager = Usager::model()->findByPk(Yii::app()->user->id);

		$casernesUsager = $usager->getCaserne();
		
		$casernes = Caserne::model()->findAll(array('condition'=>'siActif = 1 AND id IN ('.$casernesUsager.')'));
		$lstCaserne = CHtml::listData($casernes, 'id', 'nom');
		
		if($caserne=="0"){
			foreach($casernes as $cas){
				$caserne = $cas->id;
				break;
			}			
		}
		
		//on charge les informations des paramètres.
		$model=Minimum::model()->findBySql(
					'SELECT (SELECT minimum FROM tbl_minimum WHERE jourSemaine = 0 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as dimancheMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 0 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as dimancheNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 1 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as lundiMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 1 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as lundiNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 2 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as mardiMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 2 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as mardiNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 3 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as mercrediMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 3 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as mercrediNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 4 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as jeudiMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 4 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as jeudiNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 5 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as vendrediMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 5 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as vendrediNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 6 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as samediMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 6 AND tbl_caserne_id = '.$caserne.' ORDER BY dateHeure DESC LIMIT 0,1) as samediNiv 
					FROM `tbl_minimum` LIMIT 0,1');
		if($model===null)
			$model = new Minimum();
		if(isset($_POST['MinimumException']))
		{			
			//UMinimumException contient le data des exceptions déjà enregistrer. U pour Update
			if(isset($_POST['UMinimumException'])){
				$i = 0;
				foreach($_POST['UMinimumException']['id'] as $value){
					//Si dateDebut, dateFin et minimum sont setter, sa veut dire que le champ n'est pas encore passé
					if(isset($_POST['UMinimumException']['dateDebut'][$i]) && isset($_POST['UMinimumException']['dateFin'][$i]) && isset($_POST['UMinimumException']['minimum'][$i]) && isset($_POST['UMinimumException']['niveauAlerte'][$i])){
						//On va chercher lexception dans la bd, on la supprime et on entre les nouvelles données
						$exception = new MinimumException;
						$exception->findByPk($_POST['UMinimumException']['id'][$i]);
						$exception->deleteAll('id = "'.$_POST['UMinimumException']['id'][$i].'"');
						//On update si le check box n'est pas cocher, sinon on supprime
						if(!isset($_POST['UMinimumException']['check'][$i])){
							$exception->id = $_POST['UMinimumException']['id'][$i];
							$exception->tbl_usager_id = $_POST['UMinimumException']['tbl_usager_id'][$i];
							$exception->dateDebut = $_POST['UMinimumException']['dateDebut'][$i];
							$exception->dateFin = $_POST['UMinimumException']['dateFin'][$i];
							$exception->minimum = $_POST['UMinimumException']['minimum'][$i];
							$exception->niveauAlerte = $_POST['UMinimumException']['niveauAlerte'][$i];
							$exception->tbl_caserne_id = $_POST['UMinimumException']['tbl_caserne_id'][$i];
							$exception->save();
						}
					//Si les champs ne sont pas setter, on vérifie si la case a cocher est cocher pour supprimer le vieux MinimumException
					}elseif(isset($_POST['UMinimumException']['check'][$i])){
						$exception = new MinimumException;
						$exception->findByPk($_POST['UMinimumException']['id'][$i]);
						$exception->deleteAll('id = "'.$_POST['UMinimumException']['id'][$i].'"');
					}
					$i++;
				}
			}
			//On vérifie si il y du data dans les champs d'ajout afin de ne pas ajouter une exception vide
			if($_POST['MinimumException']['dateDebut'] != NULL && $_POST['MinimumException']['dateFin'] != NULL && $_POST['MinimumException']['minimum'] != NULL && $_POST['MinimumException']['niveauAlerte'] != NULL){
				$exception = new MinimumException;
				$exception->attributes=$_POST['MinimumException'];			
				$exception->tbl_usager_id = Yii::app()->user->id;;
				$exception->save();
			}			
			$this->redirect(array('index'));
		}
				
		$this->render('index',array(
			'model'=>$model,
			'lstCaserne'=>$lstCaserne,
			'caserne'=>$caserne,
		));
	}
	
	//sauvegarde les minimums
	public function actionSave(){
		$parametres = Parametres::model()->findByPk(1);
		//on charge les informations des paramètres.
		$model=Minimum::model()->findBySql(
					'SELECT (SELECT minimum FROM tbl_minimum WHERE jourSemaine = 0 AND tbl_caserne_id = '.$_POST['lstCaserne'].' ORDER BY dateHeure DESC LIMIT 0,1) as dimancheMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 0 AND tbl_caserne_id = '.$_POST['lstCaserne'].' ORDER BY dateHeure DESC LIMIT 0,1) as dimancheNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 1 AND tbl_caserne_id = '.$_POST['lstCaserne'].' ORDER BY dateHeure DESC LIMIT 0,1) as lundiMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 1 AND tbl_caserne_id = '.$_POST['lstCaserne'].' ORDER BY dateHeure DESC LIMIT 0,1) as lundiNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 2 AND tbl_caserne_id = '.$_POST['lstCaserne'].' ORDER BY dateHeure DESC LIMIT 0,1) as mardiMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 2 AND tbl_caserne_id = '.$_POST['lstCaserne'].' ORDER BY dateHeure DESC LIMIT 0,1) as mardiNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 3 AND tbl_caserne_id = '.$_POST['lstCaserne'].' ORDER BY dateHeure DESC LIMIT 0,1) as mercrediMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 3 AND tbl_caserne_id = '.$_POST['lstCaserne'].' ORDER BY dateHeure DESC LIMIT 0,1) as mercrediNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 4 AND tbl_caserne_id = '.$_POST['lstCaserne'].' ORDER BY dateHeure DESC LIMIT 0,1) as jeudiMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 4 AND tbl_caserne_id = '.$_POST['lstCaserne'].' ORDER BY dateHeure DESC LIMIT 0,1) as jeudiNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 5 AND tbl_caserne_id = '.$_POST['lstCaserne'].' ORDER BY dateHeure DESC LIMIT 0,1) as vendrediMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 5 AND tbl_caserne_id = '.$_POST['lstCaserne'].' ORDER BY dateHeure DESC LIMIT 0,1) as vendrediNiv, 
					(SELECT minimum FROM tbl_minimum WHERE jourSemaine = 6 AND tbl_caserne_id = '.$_POST['lstCaserne'].' ORDER BY dateHeure DESC LIMIT 0,1) as samediMin, 
					(SELECT niveauAlerte FROM tbl_minimum WHERE jourSemaine = 6 AND tbl_caserne_id = '.$_POST['lstCaserne'].' ORDER BY dateHeure DESC LIMIT 0,1) as samediNiv 
					FROM `tbl_minimum` LIMIT 0,1');
		if($model===null)
			$model = new Minimum();
		
		$pass=0;
		$date = new DateTime(NULL,new DateTimeZone($parametres->timezone));
		if($model->dimancheMin != $_POST['Minimum']['dimancheMin'] || $model->dimancheNiv != $_POST['Minimum']['dimancheNiv']){
			$modelS = new Minimum;
			$modelS->jourSemaine = 0;
			$modelS->minimum = $_POST['Minimum']['dimancheMin'];
			$modelS->niveauAlerte = (($_POST['Minimum']['dimancheNiv']==0)?1:$_POST['Minimum']['dimancheNiv']);
			$modelS->tbl_caserne_id = $_POST['lstCaserne'];
			if(!$modelS->save()){
				$pass=1;
				Yii::log(json_encode($modelS->getErrors()),'error','Minimum.index');
			}
		}
		if($model->lundiMin != $_POST['Minimum']['lundiMin'] || $model->lundiNiv != $_POST['Minimum']['lundiNiv']){
			$modelS = new Minimum;
			$modelS->jourSemaine = 1;
			$modelS->minimum = $_POST['Minimum']['lundiMin'];
			$modelS->niveauAlerte = (($_POST['Minimum']['lundiNiv']==0)?1:$_POST['Minimum']['lundiNiv']);
			$modelS->tbl_caserne_id = $_POST['lstCaserne'];
			if(!$modelS->save()){
				$pass=1;
				Yii::log(json_encode($modelS->getErrors()),'error','Minimum.index');
			}
		}
		if($model->mardiMin != $_POST['Minimum']['mardiMin'] || $model->mardiNiv != $_POST['Minimum']['mardiNiv']){
			$modelS = new Minimum;
			$modelS->jourSemaine = 2;
			$modelS->minimum = $_POST['Minimum']['mardiMin'];
			$modelS->niveauAlerte = (($_POST['Minimum']['mardiNiv']==0)?1:$_POST['Minimum']['mardiNiv']);
			$modelS->tbl_caserne_id = $_POST['lstCaserne'];
			if(!$modelS->save()){
				$pass=1;
				Yii::log(json_encode($modelS->getErrors()),'error','Minimum.index');
			}
		}
		if($model->mercrediMin != $_POST['Minimum']['mercrediMin'] || $model->mercrediNiv != $_POST['Minimum']['mercrediNiv']){
			$modelS = new Minimum;
			$modelS->jourSemaine = 3;
			$modelS->minimum = $_POST['Minimum']['mercrediMin'];
			$modelS->niveauAlerte = (($_POST['Minimum']['mercrediNiv']==0)?1:$_POST['Minimum']['mercrediNiv']);
			$modelS->tbl_caserne_id = $_POST['lstCaserne'];
			if(!$modelS->save()){
				$pass=1;
				Yii::log(json_encode($modelS->getErrors()),'error','Minimum.index');
			}
		}
		if($model->jeudiMin != $_POST['Minimum']['jeudiMin'] || $model->jeudiNiv != $_POST['Minimum']['jeudiNiv']){
			$modelS = new Minimum;
			$modelS->jourSemaine = 4;
			$modelS->minimum = $_POST['Minimum']['jeudiMin'];
			$modelS->niveauAlerte = (($_POST['Minimum']['jeudiNiv']==0)?1:$_POST['Minimum']['jeudiNiv']);
			$modelS->tbl_caserne_id = $_POST['lstCaserne'];
			if(!$modelS->save()){
				$pass=1;
				Yii::log(json_encode($modelS->getErrors()),'error','Minimum.index');
			}
		}
		if($model->vendrediMin != $_POST['Minimum']['vendrediMin'] || $model->vendrediNiv != $_POST['Minimum']['vendrediNiv']){
			$modelS = new Minimum;
			$modelS->jourSemaine = 5;
			$modelS->minimum = $_POST['Minimum']['vendrediMin'];
			$modelS->niveauAlerte = (($_POST['Minimum']['vendrediNiv']==0)?1:$_POST['Minimum']['vendrediNiv']);
			$modelS->tbl_caserne_id = $_POST['lstCaserne'];
			if(!$modelS->save()){
				$pass=1;
				Yii::log(json_encode($modelS->getErrors()),'error','Minimum.index');
			}
		}
		if($model->samediMin != $_POST['Minimum']['samediMin'] || $model->samediNiv != $_POST['Minimum']['samediNiv']){
			$modelS = new Minimum;
			$modelS->jourSemaine = 6;
			$modelS->minimum = $_POST['Minimum']['samediMin'];
			$modelS->niveauAlerte = (($_POST['Minimum']['samediNiv']==0)?1:$_POST['Minimum']['samediNiv']);
			$modelS->tbl_caserne_id = $_POST['lstCaserne'];
			if(!$modelS->save()){
				$pass=1;
				Yii::log(json_encode($modelS->getErrors()),'error','Minimum.index');
			}
		}
		if($pass==0){
			echo 'Enregistrement réussi';	
		}else{
			echo 'Enregistrement échoué';	
		}
		Yii::app()->end();	
	}
	
	public function actionGetException(){
		$date = date('Y-m-d');
		$criteria = new CDbCriteria;
		$criteria->condition = 'dateFin >= "'.$date.'"';
		$criteria->order = 'tbl_caserne_id ASC, dateDebut ASC';
		$table = MinimumException::model()->findAll($criteria);
		$nbr = count($table);
		
		$casernes = Caserne::model()->findAll(array('condition'=>'siActif = 1'));
		
		if($nbr == 0){
			$return = '';
		}else{
			$i = 0;$caserne=0;
			$return = '<table><tr><th>Date de début</th><th>Date de fin</th><th>Minimum</th><th>Niveau d\'alerte</th><th>Supprimer</th></tr>';
			foreach($table as $value){
				$return .= '<tr>';				
				if($caserne != $value['tbl_caserne_id']){
					$return .= '<th colspan="5">';
					$caserne = $value['tbl_caserne_id'];
					foreach($casernes as $cas){
						if($caserne == $cas->id){
							$return .= $cas->nom.' :';
							break;
						}
					}
					
					$return.= '</th></tr><tr>';
				}				
				$return .= '<td>'.$this->widget('zii.widgets.jui.CJuiDatePicker',array('name'=>'UMinimumException[dateDebut]['.$i.']', 'value'=>$value['dateDebut'],'options'=>array('dateFormat'=>'yy-mm-dd'),), true).'</td>';				
				$return .= '<td>'.$this->widget('zii.widgets.jui.CJuiDatePicker',array('name'=>'UMinimumException[dateFin]['.$i.']', 'value'=>$value['dateFin'],'options'=>array('dateFormat'=>'yy-mm-dd'),), true).'</td>';
				$return .= '<td>'.CHtml::textField('UMinimumException[minimum]['.$i.']',$value['minimum'],array('size'=>5)).'</td>';
				$return .= '<td>'.CHtml::textField('UMinimumException[niveauAlerte]['.$i.']',$value['niveauAlerte'],array('size'=>5)).'</td>';
				$return .= '<td>'.CHtml::checkbox('UMinimumException[check]['.$i.']', false);
				$return .= CHtml::hiddenField('UMinimumException[id]['.$i.']', $value['id']);
				$return .= CHtml::hiddenField('UMinimumException[tbl_caserne_id]['.$i.']', $value['tbl_caserne_id']);
				$return .= CHtml::hiddenField('UMinimumException[tbl_usager_id]['.$i.']', $value['tbl_usager_id']).'</td>';
				$return .= '</tr>';
				$i++;
			}
			$return .= '</table>';
		}
		
		return $return;
	}
	

}