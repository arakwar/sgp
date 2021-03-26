<?php

/**
 * This is the model class for table "tbl_histoGroupe".
 *
 * The followings are the available columns in table 'tbl_histoGroupe':
 * @property integer $id
 * @property string $nom
 * @property string $dateHeure
 * @property integer $typeAction
 * @property integer $tbl_groupe_id
 *
 * The followings are the available model relations:
 * @property Groupe $tblGroupe
 */
class HistoGroupe extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HistoGroupe the static model class
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
		return 'tbl_histoGroupe';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tbl_groupe_id', 'required'),
			array('id, typeAction, tbl_groupe_id, tbl_caserne_id', 'numerical', 'integerOnly'=>true),
			array('nom', 'length', 'max'=>45),
			array('dateHeure', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, nom, dateHeure, typeAction, tbl_groupe_id', 'safe', 'on'=>'search'),
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
			'tblGroupe' => array(self::BELONGS_TO, 'Groupe', 'tbl_groupe_id'),
			'tblCaserne' => array(self::BELONGS_TO, 'Caserne', 'tbl_caserne_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','histoGroupe.id'),
			'nom' => Yii::t('model','histoGroupe.nom'),
			'dateHeure' => Yii::t('model','histoGroupe.dateHeure'),
			'typeAction' => Yii::t('model','histoGroupe.typeAction'),
			'tbl_groupe_id' => Yii::t('model','histoGroupe.tbl_groupe_id'),
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
		$criteria->compare('dateHeure',$this->dateHeure,true);
		$criteria->compare('typeAction',$this->typeAction);
		$criteria->compare('tbl_groupe_id',$this->tbl_groupe_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}