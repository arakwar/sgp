<?php

/**
 * This is the model class for table "tbl_notification".
 *
 * The followings are the available columns in table 'tbl_notification':
 * @property integer $id
 */
class Notification extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Notification the static model class
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
		return 'tbl_notification';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tbl_usager_id, message','required'),
			array('id, tbl_usager_id', 'numerical', 'integerOnly'=>true),
			array('dateCreation, categorie, message, details, dateVisionnement', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, tbl_usager_id, dateCreation, dateVisionnement' ,'safe', 'on'=>'search'),
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
			'Usagers' => array(self::BELONGS_TO, 'Usager', 'tbl_usager_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','notification.id'),
			'tbl_usager_id' => Yii::t('model','notification.tbl_usager_id'),
			'dateCreation' => Yii::t('model','notification.dateCreation'),
			'categorie' => Yii::t('model','notification.categorie'),
			'message' => Yii::t('model','notification.message'),
			'details' => Yii::t('model','notification.details'),
			'dateVisionnement' => Yii::t('model','notification.dateVisionnement'),
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
		$criteria->compare('tbl_usager_id',$this->tbl_usager_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function getMessage(){
		if($this->categorie==NULL){
			return $this->message;
		}else{
			return Yii::t($this->categorie, $this->message, json_decode($this->details,true));
		}
	}
}