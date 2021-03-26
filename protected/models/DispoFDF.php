<?php

/**
 * This is the model class for table "tbl_dispo_jour".
 *
 * The followings are the available columns in table 'tbl_dispo_jour':
 * @property integer $id
 * @property integer $tbl_quart_id
 * @property string $date
 * @property integer $tbl_usager_id
 *
 * The followings are the available model relations:
 * @property Quart $tblQuart
 * @property Usager $tblUsager
 */
class DispoFDF extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DispoFDF the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_dispo_jour';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tbl_quart_id, tbl_usager_id', 'required'),
			array('id, tbl_quart_id, tbl_usager_id, tbl_caserne_id, tbl_usager_action', 'numerical', 'integerOnly'=>true),
			array('dispo', 'boolean'),
			array('heureDebut, heureFin,date, dateDecoche', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, tbl_quart_id, date, tbl_usager_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'tblQuart' => array(self::BELONGS_TO, 'Quart', 'tbl_quart_id'),
			'tblUsager' => array(self::BELONGS_TO, 'Usager', 'tbl_usager_id'),
			'tblCaserne' => array(self::BELONGS_TO, 'Caserne', 'tbl_caserne_id'),
			'tblUsagerAction' => array(self::BELONGS_TO, 'Usager', 'tbl_usager_action'),		
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','dispoFDF.id'),
			'tbl_quart_id' => Yii::t('model','dispoFDF.tbl_quart_id'),
			'date' => Yii::t('model','dispoFDF.date'),
			'tbl_usager_id' => Yii::t('model','dispoFDF.tbl_usager_id'),
			'dateDecoche' => Yii::t('model','dispoFDF.dateDecoche'),
			'dispo' => Yii::t('model','dispoFDF.dispo'),
			'heureDebut' => Yii::t('model','dispoFDF.heureDebut'),
			'heureFin' => Yii::t('model','dispoFDF.heureFin'),
			'tbl_usager_action' => Yii::t('model','dispoFDF.tbl_usager_action'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('tbl_quart_id',$this->tbl_quart_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('tbl_usager_id',$this->tbl_usager_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Va chercher une liste des pompiers disponibles
	 * @param mixed $date : vide pour la date du jour, un DateTime pour une date précise, un array avec les paramètres suivant :
	 * 		OBLIGATOIRE : dateDebut : date de début de la période souhaitée
	 * 		OBLIGATOIRE : nombreJour : nombre de jour souhaité
	 * @param mixed $quart : vide pour tout les quarts, un objet Quart ou l'id de un quart pour un seul quart spécifique, un array de Quart ou de id de quart pour plusieurs...
	 * @param mixed $equipe : voir quart
	 * @param bool $siGarde : défaut à true, si est passé à false, ce sera les groupes qui seront utilisé pour la requête
	 */
	public static function getDispoJour($date="",$quart="",$lstEquipe="",$siGarde=true, $caserne, $ajax, $siGrandEcran=false){
		$parametres = Parametres::model()->findByPk(1);
		/**
		 * Définition de la période de disponibilité à aller chercher
		 */
		if($parametres->affichage_fdf == 0){
			if(is_string($caserne))
				$casernes[] = $caserne;
			else if (is_array($caserne))
			{
				foreach ($caserne as $key => $value) {
					if(is_object($value))
					{
						$casernes[] = $value->id;
					}
					else if(is_string($value))
					{
						$casernes[] = $value;
					}
					else 
						throw new CException("Le contenu de $caserne n'est pas du bon type");
				}
			}
			else 
				throw new CException("$caserne n'est pas du bon type");
		}elseif($parametres->affichage_fdf == 1){
			$critereCaserne = new CDbCriteria;
			$critereCaserne->condition = 'siActif = 1'.(($siGrandEcran)?' AND siGrandEcran = 1':'');
			$critereCaserne->order = 'nom ASC';
			$Caserne = Caserne::model()->findAll($critereCaserne);
			foreach($Caserne as $cas){
				$casernes[] = $cas->id;
			}
		}
		
		/**
		 * Boucle pour les informations
		 * POUR chaque caserne
		 * POUR chaque jour
		 * POUR chaque quart : liste des gars non dispo
		 * POUR chaque équipe : liste des gars non présent dans la liste des non dispo
		 */
		$tblListe = array();
		foreach($casernes as $cas){
			$dateDebut = "";
			$nombreJour = 1;
			if($date==""){
				$dateDebut = new DateTime('now',new DateTimeZone('America/Montreal'));
			}elseif(is_array($date)){
				if(isset($date['dateDebut'])){
					if($date['dateDebut']==""){
						$dateDebut = new DateTime('now',new DateTimeZone('America/Montreal'));
					}elseif(get_class($date['dateDebut'])=="DateTime"){
						$dateDebut = $date['dateDebut'];
					}else{
						throw new CException('$date["dateDebut"] est non défini.');
					}
				}else{$dateDebut = new DateTime('now',new DateTimeZone($parametres->timezone));}
				if(isset($date['nombreJour'])){$nombreJour = $date['nombreJour'];}else{throw new CException('$date["nombreJour"] est non défini.');}
			}
			
			/**
			 * Définition du ou des quarts à aller chercher
			 */
			$tblQuart = array();
			if($quart==""){
				$criteria = new CDbCriteria;
				$criteria->condition = 'tbl_caserne_id = :caserne';
				$criteria->params = array(':caserne'=>$cas);
				$posteHC = PosteHoraireCaserne::model()->findAll($criteria);
				$posteId = array();
				foreach($posteHC as $postehc){
					if(!in_array($postehc->tbl_poste_horaire_id,$posteId)){
						$posteId[]=$postehc->tbl_poste_horaire_id;
					}
				}
				$criteria = new CDbCriteria;
				$criteria->addInCondition('id',$posteId);
				$postesH = PosteHoraire::model()->findAll($criteria);
				$quartId = array();
				foreach($postesH as $postes){
					if(!in_array('$postes->tbl_quart_id', $quartId)){
						$quartId[] = $postes->tbl_quart_id;
					}
				}
				$criteria = new CDbCriteria;
				$criteria->addInCondition('id',$quartId);
				$tblQuart = Quart::model()->findAll($criteria);
			}
			elseif(is_array($quart)){
				foreach($quart as $value){
					if(get_class($value)=="Quart"){
						$tblQuart[] = $value;
					}elseif(is_numeric($value)){
						$tblQuart[] = Quart::model()->findByPk($value);
					}
					else{
						throw new CException('$quart comporte des valeurs illégales.');
					}
				}
			}
			
			/**
			 * Définition des équipes à aller chercher
			 */
			
			$tblEquipe = array();
			if($lstEquipe==""){
				if($siGarde){
					$criteriaEquipe = new CDbCriteria();
					$criteriaEquipe->condition = 'siFDF=1 AND tbl_caserne_id = '.$cas;
					$criteriaEquipe->order = 'ordre ASC';
					$tblEquipe = Equipe::model()->findAll($criteriaEquipe);
				}else{
					if($parametres->fdf_equipe_spe == 0){
						$criteriaGroupe = new CDbCriteria;
						$criteriaGroupe->alias = 't';
						$criteriaGroupe->join = 'LEFT JOIN tbl_caserne c ON c.id = t.tbl_caserne_id';
						$criteriaGroupe->condition = 'c.siActif = 1';
						$criteriaGroupe->order = 'c.nom ASC';
						$tblEquipe = Groupe::model()->findAll($criteriaGroupe);
					}else{
						$criteriaGroupe = new CDbCriteria;
						$criteriaGroupe->alias = 't';
						$criteriaGroupe->join = 'LEFT JOIN tbl_caserne c ON c.id = t.tbl_caserne_id';
						$criteriaGroupe->condition = 'c.siActif = 1 AND c.id = '.$cas;
						$criteriaGroupe->order = 'c.nom ASC';
						$tblEquipe = Groupe::model()->findAll($criteriaGroupe);
					}
				}
			}
			elseif(is_array($lstEquipe)){
				foreach($lstEquipe as $value){
					if(get_class($value)=="Equipe"){
						$tblEquipe[] = $value;
					}elseif(get_class($value)=="Groupe"){
						$tblEquipe[] = $value;
					}
					else{
						throw new CException('$equipe comporte des valeurs illégales.');
					}
				}
			}
			$dateJour = clone $dateDebut;
			for($i=0;$i<$nombreJour;$i++){
				foreach($tblQuart as $Quart){
					$criteriaQuart = new CDbCriteria();
					$criteriaQuart->condition = 'heureDebut <= "'.date('H:i:s').'" AND IF(heureFin <= heureDebut,ADDTIME(heureFin,"24:00:00") >= "'.date('H:i:s').'",heureFin >= "'.date('H:i:s').'")';
					
					$quartActuel = Quart::model()->find($criteriaQuart);
					
					$memeQuart = false;
					/*if(date("Y-m-d",$dateJour->getTimestamp())==date("Y-m-d") && $quartActuel->id==$Quart->id){
						$memeQuart=true;
					}*/
					
					$Cas = Caserne::model()->findByPk($cas); 
					$tblListe[$Cas->nom][$dateJour->getTimestamp()][$Quart->id]['nom'] = $Quart->nom;
					$criteriaListeDispo = new CDbCriteria;
					$criteriaListeDispo->condition = 'date = "'.$dateJour->format("Y-m-d").'" AND tbl_quart_id = '.$Quart->id.' AND (dispo =';
					if($parametres->defaut_fdf==0){
						$criteriaListeDispo->condition .= '0'.(($memeQuart)?' OR (dispo = 1 AND NOT (heureDebut <= "'.$dateDebut->format('H:i:s').'" AND IF(heureFin <= heureDebut, ADDTIME(heureFin,"24:00:00") >= "'.$dateDebut->format('H:i:s').'",heureFin >= "'.$dateDebut->format('H:i:s').'"))))':')').(($siGarde)?' AND tbl_caserne_id = '.$cas:'');
					}else{
						$criteriaListeDispo->condition .= '1'.(($memeQuart)?' AND heureDebut <= "'.$dateDebut->format('H:i:s').'" AND IF(heureFin <= heureDebut,ADDTIME(heureFin,"24:00:00") >= "'.$dateDebut->format('H:i:s').'",heureFin >= "'.$dateDebut->format('H:i:s').'"))':')').(($siGarde)?' AND tbl_caserne_id = '.$cas:'');
					}
					$listeDispo = DispoFDF::model()->findAll($criteriaListeDispo);
					$tblUsager = array();
					foreach($listeDispo as $value){
						$tblUsager[] = $value->tbl_usager_id;
					}
					$listeDispo = NULL;
					unset($listeDispo);
					//On replace les équipes dans l'ordre suivant :
					//1iere équipe : équipe de garde
					//équipes suivante : en ordre
					$equipeGarde = EquipeGarde::model()->findByAttributes(array('modulo'=>(($dateJour->getTimestamp()/86400)%$parametres->nbJourPeriode),'tbl_quart_id'=>$Quart->id,'tbl_caserne_id'=>$cas, 'tbl_garde_id'=>$parametres->garde_fdf));
					if($siGarde && $lstEquipe==""){
						while($tblEquipe[0]->id!=$equipeGarde->tbl_equipe_id){
							array_push($tblEquipe,array_shift($tblEquipe));
						}
					}
					foreach($tblEquipe as $equipe){
						/*Nouvelle méthode*/
						$sql = "SELECT CONCAT_WS(' ', u.prenom, u.nom) AS nom, u.id FROM tbl_usager u";
						if($siGarde){						
							$sql .= " INNER JOIN tbl_equipe_usager e ON e.tbl_usager_id=u.id ";
							$sql .= " WHERE u.actif=1 AND u.enService=1 AND e.tbl_equipe_id=".$equipe->id;
						}else{
							$sql .= " INNER JOIN tbl_groupe_usager g ON g.tbl_usager_id=u.id ";
							$sql .= " WHERE u.actif=1 AND u.enService=1 AND g.tbl_groupe_id=".$equipe->id;
						}
						if($parametres->defaut_fdf==0){
							if(count($tblUsager)>0)	$sql .= " AND u.id NOT IN (".implode(', ',$tblUsager).")";
						}else{
							if(count($tblUsager)>0){
								$sql .= " AND u.id IN (".implode(', ',$tblUsager).")";
							}else{
								$sql .= ' AND u.id = 0';
							}
						}
						$cn = Yii::app()->db;		
						$cm = $cn->createCommand($sql);
								
						$dataReader = $cm->queryAll();
						$tblListe[$Cas->nom][$dateJour->getTimestamp()][$Quart->id][$equipe->id]['nombre'] = count($dataReader);
						$tblListe[$Cas->nom][$dateJour->getTimestamp()][$Quart->id][$equipe->id]['nomEquipe'] = $equipe->nom;
						/*si garde/total*/

						if(!$siGarde && isset($parametres->garde_sur_total_groupe) && $parametres->garde_sur_total_groupe){
							$tblListe[$Cas->nom][$dateJour->getTimestamp()][$Quart->id][$equipe->id]['garde_sur_total_groupe'] = $equipe->garde_sur_total_groupe;
							$usager_garde = array_column($dataReader,'id');
							$sql = "SELECT u.id FROM tbl_usager u 
							INNER JOIN tbl_equipe_usager eu ON eu.tbl_usager_id=u.id
							INNER JOIN tbl_equipe e on e.id=eu.tbl_equipe_id
							WHERE e.id = ".$equipeGarde->tbl_equipe_id;
							if(count($usager_garde))
								$sql .= " AND u.id IN (".implode(', ',$usager_garde).")";
							$sql .= " ORDER BY u.id";
							$cn = Yii::app()->db;		
							$cm = $cn->createCommand($sql);
									
							$dataReader2 = $cm->queryAll();
							$tblListe[$Cas->nom][$dateJour->getTimestamp()][$Quart->id][$equipe->id]['nombre_garde'] = count($dataReader2);
							$dataReader2 = null;
							unset($dataReader2);
						}
						$dataReader = null;
						unset($dataReader);
					}
				}
				$dateJour->add(new DateInterval("P1D"));
			}
		}
		return $tblListe;
	}
	
	protected function beforeSave()
	{
		$parametres = Parametres::model()->findByPk(1);
		if($this->dispo==0){
			$date = new DateTime(date('Y-m-d H:i:s'),new DateTimeZone($parametres->timezone));
			$this->dateDecoche = $date->format('Y-m-d H:i:s');
		}
		return parent::beforeSave();
	}
}