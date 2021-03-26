<?php

/**
 * This is the model class for table "tbl_minimum_exception".
 *
 * The followings are the available columns in table 'tbl_minimum_exception':
 * @property integer $id
 * @property string $dateDebut
 * @property string $dateFin
 * @property integer $minimum
 * @property integer $tbl_usager_id
 *
 * The followings are the available model relations:
 * @property Usager $tblUsager
 */
class MinimumException extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return MinimumException the static model class
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
		return 'tbl_minimum_exception';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tbl_usager_id, minimum, niveauAlerte, dateDebut, dateFin', 'required'),
			array('minimum, niveauAlerte, tbl_usager_id, tbl_caserne_id', 'numerical', 'integerOnly'=>true),
			array('dateDebut, dateFin', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, dateDebut, dateFin, minimum, tbl_usager_id', 'safe', 'on'=>'search'),
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
			'tblCaserne' => array(self::BELONGS_TO, 'Caserne', 'tbl_caserne_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','minimumException.id'),
			'dateDebut' => Yii::t('model','minimumException.dateDebut'),
			'dateFin' => Yii::t('model','minimumException.dateFin'),
			'minimum' => Yii::t('model','minimumException.minimum'),
			'niveauAlerte' => Yii::t('model','minimumException.niveauAlerte'),
			'tbl_usager_id' => Yii::t('model','minimumException.tbl_usager_id'),
			'titre'=> Yii::t('model','minimumException.titre'),
			'tbl_caserne_id'=> Yii::t('model','minimumException.tbl_caserne_id'),
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
		$criteria->compare('dateDebut',$this->dateDebut,true);
		$criteria->compare('dateFin',$this->dateFin,true);
		$criteria->compare('minimum',$this->minimum);
		$criteria->compare('tbl_usager_id',$this->tbl_usager_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}