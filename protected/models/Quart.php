<?php

/**
 * This is the model class for table "tbl_quart".
 *
 * The followings are the available columns in table 'tbl_quart':
 * @property integer $id
 * @property string $nom
 * @property string $heureDebut
 * @property string $heureFin
 *
 * The followings are the available model relations:
 * @property DispoHoraire[] $dispoHoraires
 * @property DispoJour[] $dispoJours
 * @property PosteHoraire[] $posteHoraires
 */
class Quart extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Quart the static model class
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
		return 'tbl_quart';
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
			array('nom', 'length', 'max'=>45),
			array('heureDebut, heureFin', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, nom, heureDebut, heureFin', 'safe', 'on'=>'search'),
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
			'dispoJours' => array(self::HAS_MANY, 'DispoJour', 'tbl_quart_id'),
			'posteHoraires' => array(self::HAS_MANY, 'PosteHoraire', 'tbl_quart_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','quart.id'),
			'nom' => Yii::t('model','quart.nom'),
			'heureDebut' => Yii::t('model','quart.heureDebut'),
			'heureFin' => Yii::t('model','quart.heureFin'),
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
		$criteria->compare('heureDebut',$this->heureDebut,true);
		$criteria->compare('heureFin',$this->heureFin,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getHeuresTotales(){
		$diff = strtotime($this->heureFin) - strtotime($this->heureDebut);
		$diff = $diff/(60*60);
		if($diff<0) $diff = $diff+24;
		return $diff;
	}
	
	static function diffHeures($heureDebut,$heureFin){
		$diff = strtotime($heureFin) - strtotime($heureDebut);
		$diff = $diff/(60*60);
		if($diff<0) $diff = $diff+24;
		return $diff;
	}
}