<?php

/**
 * This is the model class for table "tbl_message".
 *
 * The followings are the available columns in table 'tbl_message':
 * @property integer $id
 * @property string $dateEnvoi
 * @property string $objet
 * @property string $message
 * @property integer $auteur
 *
 * The followings are the available model relations:
 * @property Usager $auteur0
 * @property Usager[] $tblUsagers
 */
class Message extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Message the static model class
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
		return 'tbl_message';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('auteur', 'required'),
			array('auteur', 'numerical', 'integerOnly'=>true),
			array('objet', 'length', 'max'=>45),
			array('dateEnvoi, message', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, dateEnvoi, objet, message, auteur', 'safe', 'on'=>'search'),
		);
	}
	
	public function behaviors(){
    	return array( 'CAdvancedArBehavior' => array(
        	'class' => 'system.ext.CAdvancedArBehavior'));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'auteur0' => array(self::BELONGS_TO, 'Usager', 'auteur'),
			'tblUsagers' => array(self::MANY_MANY, 'Usager', 'tbl_message_usager(tbl_message_id, tbl_usager_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','message.id'),
			'date d\'envoi' => Yii::t('model','message.dateEnvoi'),
			'objet' => Yii::t('model','message.objet'),
			'message' => Yii::t('model','message.message'),
			'auteur' => Yii::t('model','message.auteur'),
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
		$criteria->compare('dateEnvoi',$this->dateEnvoi,true);
		$criteria->compare('objet',$this->objet,true);
		$criteria->compare('message',$this->message,true);
		$criteria->compare('auteur',$this->auteur);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}