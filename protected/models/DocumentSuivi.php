<?php

/**
 * This is the model class for table "tbl_document_suivi".
 *
 * The followings are the available columns in table 'tbl_document':
 * @property integer $id
 * @property integer $tbl_document_id
 * @property integer $tbl_usager_id
 * @property timestamp $date
 *
 * The followings are the available model relations:
 * @property Usager $tblUsager
 */
class DocumentSuivi extends CActiveRecord
{
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Document the static model class
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
		return 'tbl_document_suivi';
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
			array('tbl_document_id, tbl_usager_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, date', 'safe', 'on'=>'search'),
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
			'tblUsagers' => array(self::BELONGS_TO, 'Usager', 'tbl_usager_id'),
			'tblDocuments' => array(self::BELONGS_TO, 'Document', 'tbl_document_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','documentSuivi.id'),
			'tbl_document_id' => Yii::t('model','documentSuivi.tbl_document_id'),
			'tbl_usager_id' => Yii::t('model','documentSuivi.tbl_usager_id'),
			'date' => Yii::t('model','documentSuivi.date'),
			
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}