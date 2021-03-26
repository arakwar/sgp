<?php

/**
 * This is the model class for table "tbl_notice".
 *
 * The followings are the available columns in table 'tbl_notice':
 * @property integer $id
 * @property string $dateDebut
 * @property string $dateFin
 * @property string $message
 * @property integer $tbl_usager_id
 *
 * The followings are the available model relations:
 * @property Usager $tblUsager
 */
class Notice extends CActiveRecord
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
		return 'tbl_notice';
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
			array('tbl_usager_id', 'required'),
			array('tbl_usager_id', 'numerical', 'integerOnly'=>true),
			array('message', 'length', 'max'=>100),
			array('dateDebut, dateFin, tblCasernes', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, dateDebut, dateFin, message, tbl_usager_id', 'safe', 'on'=>'search'),
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
			'tblCasernes' => array(self::MANY_MANY, 'Caserne', 'tbl_notice_caserne(tbl_notice_id, tbl_caserne_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','notice.id'),
			'dateDebut' => Yii::t('model','notice.dateDebut'),
			'dateFin' => Yii::t('model','notice.dateFin'),
			'message' => Yii::t('model','notice.message'),
			'tbl_usager_id'=> Yii::t('model','notice.tbl_usager_id'),
			'tblCasernes'=> Yii::t('model','notice.tblCasernes'),
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
		$criteria->compare('message',$this->message,true);
		$criteria->compare('tbl_usager_id',$this->tbl_usager_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}