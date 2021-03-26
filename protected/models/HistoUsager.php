<?php

/**
 * This is the model class for table "tbl_histoUsager".
 *
 * The followings are the available columns in table 'tbl_histoUsager':
 * @property integer $id
 * @property string $matricule
 * @property integer $tbl_usager_id
 * @property string $dateHeure
 */
class HistoUsager extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return HistoUsager the static model class
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
		return 'tbl_histoUsager';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('matricule, tbl_usager_id', 'required'),
			array('tbl_usager_id', 'numerical', 'integerOnly'=>true),
			array('matricule', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, matricule, tbl_usager_id, dateHeure', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','histoUsager.id'),
			'matricule' => Yii::t('model','histoUsager.matricule'),
			'tbl_usager_id' => Yii::t('model','histoUsager.tbl_usager_id'),
			'dateHeure' => Yii::t('model','histoUsager.dateHeure'),
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
		$criteria->compare('matricule',$this->matricule,true);
		$criteria->compare('tbl_usager_id',$this->tbl_usager_id);
		$criteria->compare('dateHeure',$this->dateHeure,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}