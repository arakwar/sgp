<?php

/**
 * This is the model class for table "tbl_periode".
 *
 * The followings are the available columns in table 'tbl_periode':
 * @property integer $id
 * @property integer $statut
 * @property string $equipeTermine
 * @property integer $equipeTour
 * @property string $dateDebut
 *
 * The followings are the available model relations:
 * @property DispoHoraire[] $dispoHoraires
 * @property Horaire[] $horaires
 * @property Equipe $equipeTour0
 */
class Periode extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Periode the static model class
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
		return 'tbl_periode';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dateDebut', 'required'),
			array('id, statut, equipeTour', 'numerical', 'integerOnly'=>true),
			array('equipeTermine', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, statut, equipeTermine, equipeTour, dateDebut', 'safe', 'on'=>'search'),
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
			'dispoHoraires' => array(self::HAS_MANY, 'DispoHoraire', 'tbl_periode_id'),
			'horaires' => array(self::HAS_MANY, 'Horaire', 'tbl_periode_id'),
			'equipeTour0' => array(self::BELONGS_TO, 'Equipe', 'equipeTour'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','periode.id'),
			'statut' => Yii::t('model','periode.statut'),
			'equipeTermine' => Yii::t('model','periode.equipeTermine'),
			'equipeTour' => Yii::t('model','periode.equipeTour'),
			'dateDebut' => Yii::t('model','periode.dateDebut'),
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
		$criteria->compare('statut',$this->statut);
		$criteria->compare('equipeTermine',$this->equipeTermine,true);
		$criteria->compare('equipeTour',$this->equipeTour);
		$criteria->compare('dateDebut',$this->dateDebut,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function debutPeriode($nbJour,$modDebut,$date="")
	{
		$parametres = Parametres::model()->findByPk(1);
		if($date=="") $date = date("Y-m-d");
		$dateActuel = new DateTime($date."T00:00:00",new DateTimeZone($parametres->timezone));
		$modActuel = (floor($dateActuel->getTimestamp())/86400)%$nbJour;
		$modDiff = $modDebut-$modActuel;
		if($modDiff>0) $modDiff-=$nbJour;
		$modDiff = abs($modDiff);
		$dateActuel->sub(new DateInterval("P".$modDiff."D"));
		return $dateActuel;
	}
}