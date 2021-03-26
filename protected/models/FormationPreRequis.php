<?php

/**
 * This is the model class for table "tbl_groupe_usager".
 *
 * The followings are the available columns in table 'tbl_groupe_usager':
 * @property integer $tbl_groupe_id
 * @property integer $tbl_usager_id
 */
class FormationPreRequis extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return GroupeUsager the static model class
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
		return 'tbl_formation_pre';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tbl_formation_id, tbl_formation_pre', 'required'),
			array('tbl_formation_id, tbl_formation_pre', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('tbl_formation_id, tbl_formation_pre', 'safe', 'on'=>'search'),
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
			'tblFormation' => array(self::BELONGS_TO, 'Formation', 'tbl_formation_id'),
			'tblPreRequis' => array(self::BELONGS_TO, 'Formation', 'tbl_formation_pre'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'tbl_formation_id' => Yii::t('model','formationPreRequis.tbl_formation_id'),
			'tbl_formation_pre' => Yii::t('model','formationPreRequis.tbl_formation_pre'),
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

		$criteria->compare('tbl_groupe_formation_id',$this->tbl_groupe_formation_id);
		$criteria->compare('tbl_usager_id',$this->tbl_usager_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}