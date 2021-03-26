<?php

/**
 * This is the model class for table "tbl_poste".
 *
 * The followings are the available columns in table 'tbl_poste':
 * @property integer $id
 * @property string $nom
 * @property string $diminutif
 *
 * The followings are the available model relations:
 * @property PosteHoraire[] $posteHoraires
 */
class Poste extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Poste the static model class
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
		return 'tbl_poste';
	}
	
	public function defaultScope(){
		return array(
				'condition'=>"siActif=1",
		);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nom, diminutif', 'length', 'max'=>45),
			array('siActif, formationObli', 'numerical', 'integerOnly'=>true),
				array('nom, diminutif', 'required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, nom, diminutif', 'safe', 'on'=>'search'),
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
			'posteHoraires' => array(self::HAS_MANY, 'PosteHoraire', 'tbl_poste_id'),
			'tblUsagers' => array(self::MANY_MANY, 'Usager', 'tbl_groupe_usager(tbl_groupe_id, tbl_usager_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','poste.id'),
			'nom' => Yii::t('model','poste.nom'),
			'diminutif' => Yii::t('model','poste.diminutif'),
			'formationObli' => Yii::t('model','poste.formationObli'),
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
		$criteria->compare('diminutif',$this->diminutif,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}