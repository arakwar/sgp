<?php

/**
 * This is the model class for table "tbl_minimum2".
 *
 * The followings are the available columns in table 'tbl_minimum2':
 * @property integer $id
 * @property integer $jourSemaine
 * @property integer $minimum
 * @property string $dateHeure
 */
class Minimum extends CActiveRecord
{
	public $dimancheMin;
	public $dimancheNiv;
	public $lundiMin;
	public $lundiNiv;
	public $mardiMin;
	public $mardiNiv;
	public $mercrediMin;
	public $mercrediNiv;
	public $jeudiMin;
	public $jeudiNiv;
	public $vendrediMin;
	public $vendrediNiv;
	public $samediMin;
	public $samediNiv;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Minimum2 the static model class
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
		return 'tbl_minimum';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('jourSemaine, minimum, niveauAlerte', 'required'),
			array('jourSemaine, minimum, niveauAlerte, dimancheMin, dimancheNiv, lundiMin, lundiNiv, mardiMin, 
				mardiNiv, mercrediMin, mercrediNiv, jeudiMin, jeudiNiv, vendrediMin, vendrediNiv, samediMin, samediNiv, tbl_caserne_id', 'numerical', 'integerOnly'=>true),
			array('dateHeure', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, jourSemaine, minimum, dateHeure', 'safe', 'on'=>'search'),
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
			'tblCaserne' => array(self::BELONGS_TO, 'Caserne', 'tbl_caserne_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'dimanche' => Yii::t('generale','dimanche'),
			'lundi' => Yii::t('generale','lundi'),
			'mardi' => Yii::t('generale','mardi'),
			'mercredi' => Yii::t('generale','mercredi'),
			'jeudi' => Yii::t('generale','jeudi'),
			'vendredi' => Yii::t('generale','vendredi'),
			'samedi' => Yii::t('generale','samedi'),
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
		$criteria->compare('jourSemaine',$this->jourSemaine);
		$criteria->compare('minimum',$this->minimum);
		$criteria->compare('dateHeure',$this->dateHeure,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}