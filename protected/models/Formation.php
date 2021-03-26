<?php

/**
 * This is the model class for table "tbl_groupe".
 *
 * The followings are the available columns in table 'tbl_groupe':
 * @property integer $id
 * @property string $nom
 *
 * The followings are the available model relations:
 * @property Usager[] $tblUsagers
 */
class Formation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Groupe the static model class
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
		return 'tbl_formation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nom', 'length', 'max'=>255),
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
			'tblPreRequis' => array(self::MANY_MANY, 'FormationPreRequis',  'tbl_formation_pre(tbl_formation_id, tbl_formation_pre)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','formation.id'),
			'nom' => Yii::t('model','formation.nom'),
			'tblPreRequis' => Yii::t('model','formation.tblPreRequis'),
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}