<?php

/**
 * This is the model class for table "tbl_document".
 *
 * The followings are the available columns in table 'tbl_document':
 * @property integer $id
 * @property string $nom
 * @property string $date
 * @property string $description
 * @property integer $tbl_usager_id
 * @property string $nom_fichier
 *
 * The followings are the available model relations:
 * @property Usager $tblUsager
 */
class Absence extends CActiveRecord
{
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Document the static model class
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
		return 'tbl_absence';
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
			array('tbl_usager_id, dateEmis, dateConge, heureDebut, heureFin, tbl_type_id, tbl_quart_id', 'required'),
			array('tbl_usager_id, tbl_type_id, tbl_quart_id, statut, chef_id, archive', 'numerical', 'integerOnly'=>true),
			array('tbl_usager_id, dateEmis, dateConge, heureDebut, heureFin, tbl_type_id, tbl_quart_id, statut, chef_id, dateRecu, heureRecu, note, raison', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('tbl_usager_id, dateEmis, dateConge, heureDebut, heureFin, tbl_type_id, tbl_quart_id, statut, chef_id, dateRecu, heureRecu', 'safe', 'on'=>'search'),
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
			'tblUsager' => array(self::BELONGS_TO, 'Usager', 'tbl_usager_id'),
			'tblChefs' => array(self::BELONGS_TO, 'Usager', 'chef_id'),
			'tblType' => array(self::BELONGS_TO, 'TypeConge', 'tbl_type_id'),
			'tblQuarts' => array(self::BELONGS_TO, 'Quart', 'tbl_quart_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'tbl_usager_id'=>Yii::t('model','absence.tbl_usager_id'),
			'dateConge' =>Yii::t('model','absence.dateConge'),
			'dateEmis' =>Yii::t('model','absence.dateEmis'),
			'heureDebut' =>Yii::t('model','absence.heureDebut'),
			'heureFin' =>Yii::t('model','absence.heureFin'),
			'tbl_type_id' =>Yii::t('model','absence.tbl_type_id'),
			'tbl_quart_id' =>Yii::t('model','absence.tbl_quart_id'),
			'note' =>Yii::t('model','absence.note'),
			'statut' =>Yii::t('model','absence.statut'),
			'chef_id' =>Yii::t('model','absence.chef_id'),
			'dateRecu' =>Yii::t('model','absence.dateRecu'),
			'statut' =>Yii::t('model','absence.statut'),
			'raison' =>Yii::t('model','absence.raison'),
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getHoraireQuartOptions($usagerID){
		$usager = Usager::model()->findByPk($usagerID);
		
		$casernesUsager = $usager->getCaserne();
		
		$quarts = Quart::model()->findAll();
		
		$criteria = new CDbCriteria;
		$criteria->condition = 'siActif = 1 AND id IN ('.$casernesUsager.')';
		$criteria->order = 'nom ASC';
		$casernes = Caserne::model()->findAll($criteria);		
		
		$listQuarts = array();

		foreach($casernes as $caserne){
			foreach($quarts as $quart){
				$PH = PosteHoraire::model()->find(array('alias'=>'ph', 'join'=>'LEFT JOIN tbl_poste_horaire_caserne phc ON phc.tbl_poste_horaire_id = ph.id', 'condition'=>'ph.tbl_quart_id = '.$quart->id.' AND phc.tbl_caserne_id = '.$caserne->id)); 
				if($PH !== NULL){
					$listQuarts[$caserne->nom][$quart->id]=$quart->nom;
				}
			}
		}
		return $listQuarts;
	}
}