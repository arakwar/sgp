<?php

/**
 * This is the model class for table "tbl_usager".
 *
 * The followings are the available columns in table 'tbl_usager':
 * @property integer $id
 * @property string $nom
 * @property string $prenom
 * @property string $matricule
 * @property string $pseudo
 * @property string $motdepasse
 * @property string $courriel
 * @property string $adresseCivique
 * @property string $ville
 * @property string $telephone1
 * @property string $telephone2
 * @property integer $tbl_grade_id
 * @property integer $tbl_equipe_id
 * @property integer $tempsPlein
 *
 * The followings are the available model relations:
 * @property DispoHoraire[] $dispoHoraires
 * @property DispoJour[] $dispoJours
 * @property Document[] $documents
 * @property Horaire[] $horaires
 * @property Message[] $messages
 * @property Message[] $tblMessages
 * @property MinimumException[] $minimumExceptions
 * @property Modification[] $modifications
 * @property Modification[] $modifications1
 * @property Notice[] $notices
 * @property Equipe $tblEquipe
 * @property Grade $tblGrade
 * @property Evenement[] $tblEvenements
 */
class Usager extends CActiveRecord
{
	public $nmotdepasse;
	public $nmotdepasse_repeat;
	public $imageUpload;
	public $imageFinal;
	public $dHeureDebut;
	public $dHeureFin;
	public $id_quart;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Usager the static model class
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
		return 'tbl_usager';
	}

	public function behaviors(){
    	return array( 'CAdvancedArBehavior' => array(
        	'class' => 'system.ext.CAdvancedArBehavior'));
	}
	
	public function defaultScope(){
		$ta = $this->getTableAlias(false,false);
		return $ta == 't' ? array('condition'=>$ta.'.actif=1') : array();
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nmotdepasse','compare'),
			array('nmotdepasse, nmotdepasse_repeat, tblEquipes, tblGroupes, tblPostes, imageUpload, image, imageFinal, dateEmbauche, enService, alerteFDF, heureTravaillee, afficher_tooltip','safe'),
			array('nom, prenom, courriel, pseudo', 'required'),
			array('tbl_grade_id, tempsPlein, alerteFDF', 'numerical', 'integerOnly'=>true),
			array('nom, prenom, pseudo', 'length', 'max'=>45),
			array('matricule', 'length', 'max'=>10),
			array('motdepasse, courriel, adresseCivique', 'length', 'max'=>255),
			array('ville', 'length', 'max'=>100),
			array('telephone1, telephone2, telephone3', 'length', 'max'=>20),
			array('imageUpload','file','types'=>'jpg, png','allowEmpty'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('nom, prenom, matricule, pseudo, courriel, adresseCivique, ville, telephone1, telephone2, telephone3, tbl_grade_id', 'safe', 'on'=>'search'),
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
			'dispoHoraires' => array(self::HAS_MANY, 'DispoHoraire', 'tbl_usager_id'),
			'dispoJours' => array(self::HAS_MANY, 'DispoFDF', 'tbl_usager_id'),
			'documents' => array(self::HAS_MANY, 'Document', 'tbl_usager_id'),
			'horaires' => array(self::HAS_MANY, 'Horaire', 'tbl_usager_id'),
			'messages' => array(self::HAS_MANY, 'Message', 'auteur'),
			'tblMessages' => array(self::MANY_MANY, 'Message', 'tbl_message_usager(tbl_usager_id, tbl_message_id)'),
			'minimumExceptions' => array(self::HAS_MANY, 'MinimumException', 'tbl_usager_id'),
			'modifications' => array(self::HAS_MANY, 'Modification', 'nouveau_usager_id'),
			'modifications1' => array(self::HAS_MANY, 'Modification', 'modif_usager_id'),
			'notices' => array(self::HAS_MANY, 'Notice', 'tbl_usager_id'),
			'grade' => array(self::BELONGS_TO, 'Grade', 'tbl_grade_id'),
			'tblEquipes' => array(self::MANY_MANY, 'Equipe', 'tbl_equipe_usager(tbl_usager_id, tbl_equipe_id)'),
			'tblGroupes' => array(self::MANY_MANY, 'Groupe', 'tbl_groupe_usager(tbl_usager_id, tbl_groupe_id)'),
			'tblPostes'  => array(self::MANY_MANY, 'Poste',  'tbl_usager_poste(tbl_usager_id, tbl_poste_id)'),
			'tblGroupesFormation' => array(self::MANY_MANY, 'GroupeFormation', 'tbl_groupe_formation_usager(tbl_usager_id, tbl_groupe_formation_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','usager.id'),
			'nom' => Yii::t('model','usager.nom'),
			'prenom' => Yii::t('model','usager.prenom'),
			'matricule' => Yii::t('model','usager.matricule'),
			'pseudo' => Yii::t('model','usager.pseudo'),
			'motdepasse' => Yii::t('model','usager.motdepasse'),
			'courriel' => Yii::t('model','usager.courriel'),
			'adresseCivique' => Yii::t('model','usager.adresseCivique'),
			'ville' => Yii::t('model','usager.ville'),
			'telephone1' => Yii::t('model','usager.telephone1'),
			'telephone2' => Yii::t('model','usager.telephone2'),
			'telephone3' => Yii::t('model','usager.telephone3'),
			'tbl_grade_id' => Yii::t('model','usager.tbl_grade_id'),
			'nmotdepasse' => Yii::t('model','usager.nmotdepasse'),
			'nmotdepasse_repeat' => Yii::t('model','usager.nmotdepasse_repeat'),
			'tblGroupes' => Yii::t('model','usager.tblGroupes'),
			'tblPostes' => Yii::t('model','usager.tblPostes'),
			'enService' => Yii::t('model','usager.enService'),
			'tblEquipes' => Yii::t('model','usager.tblEquipes'),
			'tempsPlein' => Yii::t('model','usager.tempsPlein'),
			'alerteFDF' => Yii::t('model','usager.alerteFDF'),
			'heureTravaillee' => Yii::t('model','usager.heureTravaillee'),
			'gstDroit' => Yii::t('model','usager.gstDroit'),
			'gstHoraire' => Yii::t('model','usager.gstHoraire'),
			'afficher_tooltip' => Yii::t('model','usager.afficher_tooltip'),
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
		$criteria->compare('nom',$this->nom,true);
		$criteria->compare('prenom',$this->prenom,true);
		$criteria->compare('matricule',$this->matricule,true);
		$criteria->compare('pseudo',$this->pseudo,true);
		$criteria->compare('motdepasse',$this->motdepasse,true);
		$criteria->compare('courriel',$this->courriel,true);
		$criteria->compare('adresseCivique',$this->adresseCivique,true);
		$criteria->compare('ville',$this->ville,true);
		$criteria->compare('telephone1',$this->telephone1,true);
		$criteria->compare('telephone2',$this->telephone2,true);
		$criteria->compare('telephone3',$this->telephone3,true);
		$criteria->compare('tbl_grade_id',$this->tbl_grade_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getUsagerGradeOptions(){
		return CHtml::listData(Grade::model()->findAll(array('order'=>'nom')),'id','nom');
	}
	
	public function getUsagerEquipeOptions(){
		return CHtml::listData(Equipe::model()->findAll('siActif=1',array()),'id','nom');
	}
	
	public function getHeuresSemaine($date){
		$dateDebut = new DateTime($date."T00:00:00");
		$retourDimanche = date("w",$dateDebut->getTimestamp());
		$dateDebut->sub(new DateInterval("P".$retourDimanche."D"));
		$dateFin = new DateTime(date("Y-m-d",$dateDebut->getTimestamp())."T00:00:00");
		$dateFin->add(new DateInterval("P6D"));
		$critere = new CDbCriteria();
		$critere->addCondition('tbl_usager_id='.$this->id);
		$critere->addBetweenCondition('date',date("Y-m-d",$dateDebut->getTimestamp()),date("Y-m-d",$dateFin->getTimestamp()));
		$listeHoraire = Horaire::model()->findAll($critere);
		$totalHeure = 0;
		foreach($listeHoraire as $horaire){
			if($horaire->PosteHoraire->heureDebut=="00:00:00"){
				//on prends les heures de début du quart
				$heureDebut = $horaire->PosteHoraire->Quart->heureDebut;
				//$totalHeure++;
			} else {
				$heureDebut = $horaire->PosteHoraire->heureDebut;
			}
			if($horaire->PosteHoraire->heureFin=="00:00:00"){
				//on prends les heures de début du quart
				$heureFin = $horaire->PosteHoraire->Quart->heureFin;
				//$totalHeure++;
			} else {
				$heureFin = $horaire->PosteHoraire->heureFin;
			}
			$totalHeure = $totalHeure + $this->heureTotal($heureDebut,$heureFin);
		}
		return $totalHeure;
		//return date("Y-m-d",$dateFin->getTimestamp()); 
	}
	
	/*Retourne le total des heures, 1h30 donnera 1,5*/
	private function heureTotal($heureDebut, $heureFin)
	{
			$secondeDebut = substr($heureDebut,6,2);
			$secondeFin = substr($heureFin,6,2);
			$minuteDebut = substr($heureDebut,3,2);
			$minuteFin = substr($heureFin,3,2);
			$heureDebut = substr($heureDebut,0,2);
			$heureFin = substr($heureFin,0,2);
			
			$tempsDebut = $heureDebut*3600+$minuteDebut*60+$secondeDebut;
			$tempsFin = $heureFin*3600+$minuteFin*60+$secondeFin;
			
			$tempsTotal = $tempsFin-$tempsDebut;
			if($tempsTotal<0){
				$tempsTotal += 86400;
			}
			
			return $tempsTotal/3600;	
	}
	
	/**
	* perform one-way encryption on the password before we store it in the database
	*/
	protected function afterValidate()
	{
		if($this->nmotdepasse!="")
		{
			$this->motdepasse = $this->encrypt($this->nmotdepasse);
		}
		parent::afterValidate();
	}
	
	public function encrypt($value)
	{
		return md5($value);
	}
	
	public function getPrenomNom()
	{
		return $this->prenom.' '.$this->nom;
	}
	
	public function getMatPrenomNom()
	{
		return $this->matricule.' '.$this->prenom.' '.$this->nom;
	}
	
	public function getEnServiceOptions()
	{
		return array(0=>'En congé',1=>'En service');
	}

	public function getTempsPleinOptions()
	{
		return array(0=>'Temps partiel',1=>'Temps plein');
	}
	
	public function getImageFinal(){
		if(file_exists(Yii::getPathOfAlias('webroot').'/imagesProfil/'.DOMAINE.DIRECTORY_SEPARATOR.'final/'.$this->id.'.jpg')){
			return $this->id.'.jpg';
		}elseif(file_exists(Yii::getPathOfAlias('webroot').'/imagesProfil/'.DOMAINE.DIRECTORY_SEPARATOR.'final/'.$this->id.'.JPG')){
			return $this->id.'.JPG';
		}elseif(file_exists(Yii::getPathOfAlias('webroot').'/imagesProfil/'.DOMAINE.DIRECTORY_SEPARATOR.'final/'.$this->id.'.jpeg')){
			return $this->id.'.jpeg';
		}elseif(file_exists(Yii::getPathOfAlias('webroot').'/imagesProfil/'.DOMAINE.DIRECTORY_SEPARATOR.'final/'.$this->id.'.JPEG')){
			return $this->id.'.JPEG';
		}elseif(file_exists(Yii::getPathOfAlias('webroot').'/imagesProfil/'.DOMAINE.DIRECTORY_SEPARATOR.'final/'.$this->id.'.png')){
			return $this->id.'.png';
		}elseif(file_exists(Yii::getPathOfAlias('webroot').'/imagesProfil/'.DOMAINE.DIRECTORY_SEPARATOR.'final/'.$this->id.'.PNG')){
			return $this->id.'.PNG';
		}else{
			Yii::log('Image is none : '.Yii::getPathOfAlias('webroot').'/imagesProfil/'.DOMAINE.DIRECTORY_SEPARATOR.'final/','info','Usager');
			return false;
		}
	}
	public function setImageFinal($source){
		if(file_exists(Yii::getPathOfAlias('webroot').'/imagesProfil/'.DOMAINE.DIRECTORY_SEPARATOR.$source)){
			$extension = explode(".",$source);
			return copy(Yii::getPathOfAlias('webroot').'/imagesProfil/'.DOMAINE.DIRECTORY_SEPARATOR.$source,Yii::getPathOfAlias('webroot').'/imagesProfil/'.DOMAINE.DIRECTORY_SEPARATOR.'final/'.$this->id.'.'.$extension[1]);
		}else{
			$this->image = NULL;
			return true;
		}
	}
	
	public function getCaserne(){
		if(!Yii::app()->user->checkAccess('GesService')){
			$ids = $this->getEquipe();
			if($ids!='0'){
				$criteria = new CDbCriteria;
				$criteria->condition = 'id IN ('.$ids.')';
				$criteria->group = 'tbl_caserne_id';
				$equipes = Equipe::model()->findAll($criteria);
				$ids = '';
				foreach($equipes as $equipe){
					$ids .= $equipe->tbl_caserne_id.', ';
				}
				if($ids != ''){
					$ids = substr($ids,0,strlen($ids)-2);
				}else{
					$ids = '0';
				}
			}
		}else{
			$casernes = Caserne::model()->findAll(array('condition'=>'siActif = 1'));
			$ids = '';
			foreach($casernes as $caserne){
				$ids .= $caserne->id.', ';
			}
			$ids = substr($ids,0,strlen($ids)-2);
		}
		//retourne les ID en string pour permettre de préparer une requête utilisant une condition IN
		return $ids;	
	}
	
	public function getEquipe(){
		$equipes = EquipeUsager::model()->findAll(array('condition'=>'tbl_usager_id = '.$this->id));
		
		$ids = '';
		foreach($equipes as $equipe){
			$ids .= $equipe->tbl_equipe_id.', ';
		}
		if($ids != ''){
			$ids = substr($ids,0,strlen($ids)-2);
		}else{
			$ids = '0'; //= 0 car aucun usager n'A 0 comme ID. On aurait pu mettre une lettre aussi
		}	
		return $ids;	
	}
	
	public static function peutGerer($id){
		$usager = Usager::model()->findByPk($id);
		$usagerConnecter = Usager::model()->findByPk(Yii::app()->user->id);
		if($id!=Yii::app()->user->id){
			if(!Yii::app()->user->checkAccess('GesService')) {
				if(!(Yii::app()->user->checkAccess('GesCaserne') && count(array_intersect(explode($usagerConnecter->getCaserne()), explode($usager->getCaserne())))>0)){
					if(!(Yii::app()->user->checkAccess('GesEquipe') && count(array_intersect(explode($usagerConnecter->getEquipe()), explode($usager->getEquipe())))>0)){
						unset($usager);unset($usagerConnecter);
						return false;
					}
				}
			}
		}
		unset($usager);unset($usagerConnecter);
		return true;
	}
	
	public function getPostes()
	{
		$postes = array();
		foreach($this->tblPostes as $poste) {
			$postes[] = $poste->nom.",";
		}
		
		$retour = implode(' ', $postes);
		return substr($retour, 0, strlen($retour)-1);
	}
}