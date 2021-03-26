<?php

/**
 * This is the model class for table "tbl_grade".
 *
 * The followings are the available columns in table 'tbl_grade':
 * @property integer $id
 * @property string $nom
 *
 * The followings are the available model relations:
 * @property Usager[] $usagers
 */
class Grade extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Grade the static model class
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
		return 'tbl_grade';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			//array('roleName', 'required'),
			array('nom', 'length', 'max'=>45),
			//array('length', 'max'=>64),
			//array('roleName','safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, nom', 'safe', 'on'=>'search'),
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
			//'roleName0' => array(self::BELONGS_TO, 'AuthItem', 'roleName'),
			'usagers' => array(self::HAS_MANY, 'Usager', 'tbl_grade_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','grade.id'),
			'nom' => Yii::t('model','grade.nom'),
			//'roleName' => 'Role Name',
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
		$criteria->compare('nom',$this->nom,true);
		//$criteria->compare('roleName',$this->roleName,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}