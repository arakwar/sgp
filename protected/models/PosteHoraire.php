<?php

/**
 * This is the model class for table "tbl_poste_horaire".
 *
 * The followings are the available columns in table 'tbl_poste_horaire':
 * @property integer $id
 * @property string $heureDebut
 * @property string $heureFin
 * @property integer $tbl_quart_id
 * @property integer $tbl_poste_id
 *
 * The followings are the available model relations:
 * @property Horaire[] $horaires
 * @property poste $tblPoste
 * @property Quart $Quart
 */
class PosteHoraire extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PosteHoraire the static model class
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
		return 'tbl_poste_horaire';
	}
	
	public function behaviors(){
		return array( 'CAdvancedArBehavior' => array(
				'class' => 'system.ext.CAdvancedArBehavior'));
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tbl_quart_id, tbl_poste_id', 'required'),
			array('tbl_quart_id, tbl_poste_id', 'numerical', 'integerOnly'=>true),
			array('heureDebut, heureFin, dateDebut, dateFin', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, heureDebut, heureFin, tbl_quart_id, tbl_poste_id', 'safe', 'on'=>'search'),
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
			'horaires' => array(self::HAS_MANY, 'Horaire', 'tbl_poste_horaire_id'),
			'poste' => array(self::BELONGS_TO, 'Poste', 'tbl_poste_id'),
			'Quart' => array(self::BELONGS_TO, 'Quart', 'tbl_quart_id'),
			'tblCasernes' => array(self::MANY_MANY, 'Caserne', 'tbl_poste_horaire_caserne(tbl_poste_horaire_id, tbl_caserne_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','posteHoraire.id'),
			'heureDebut' => Yii::t('model','posteHoraire.heureDebut'),
			'heureFin' => Yii::t('model','posteHoraire.heureFin'),
			'tbl_quart_id' => Yii::t('model','posteHoraire.tbl_quart_id'),
			'tbl_poste_id' => Yii::t('model','posteHoraire.tbl_poste_id'),
			'tblCasernes' => Yii::t('model','posteHoraire.tblCasernes'),
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
		$criteria->compare('heureDebut',$this->heureDebut,true);
		$criteria->compare('heureFin',$this->heureFin,true);
		$criteria->compare('tbl_quart_id',$this->tbl_quart_id);
		$criteria->compare('tbl_poste_id',$this->tbl_poste_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getHoraireQuartOptions(){
		$quarts = Quart::model()->findAll();	
		
		$listQuarts = array();

		foreach($quarts as $quart){
			$listQuarts[$quart->id]=$quart->nom;
		}
		
		return $listQuarts;
	}
	
	public function getHorairePosteOptions(){
		$postes = Poste::model()->findAll();
		
		$listeData = array();
		foreach($postes as $poste){
			$listeData[$poste->id] = $poste->nom;
		}
		
		return $listeData;
	}
	
	public function getNbHeures(){
		if($this->heureDebut=="00:00:00"){
			$heureDebut = $this->Quart->heureDebut;
		}else{
			$heureDebut = $this->heureDebut;
		}
		
		if($this->heureFin=="00:00:00"){
			$heureFin = $this->Quart->heureFin;
		}else{
			$heureFin = $this->heureFin;
		}
		return $this->heureTotal($heureDebut,$heureFin);
	}
	
	/**
	 * Retourne la différence entre les heures, 1h30 donnera 1,5
	 * @param string heureDebut : sous le format HH:MM:SS
	 * @param string heureFin   : sous le format HH:MM:SS
	 */
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
}