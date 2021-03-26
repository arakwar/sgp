<?php

class PosteUsager extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Notice the static model class
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
		return 'tbl_usager_poste';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tbl_poste_id, tbl_usager_id', 'required'),
			array('tbl_poste_id, tbl_usager_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('tbl_poste_id, tbl_usager_id', 'safe', 'on'=>'search'),
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
			'tbl_poste_id'=> Yii::t('model','posteUsager.tbl_poste_id'),
			'tbl_usager_id'=> Yii::t('model','posteUsager.tbl_usager_id'),
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

		$criteria->compare('tbl_poste_id',$this->tbl_poste_id);
		$criteria->compare('tbl_usager_id',$this->tbl_usager_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}