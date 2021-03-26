<?php

/**
 * This is the model class for table "tbl_soustype_evenement".
 *
 * The followings are the available columns in table 'tbl_soustype_evenement':
 * @property integer $id
 * @property string $nom
 * @property string $numero
 * @property integer $tbl_type_evenement_id
 *
 * The followings are the available model relations:
 * @property Evenement[] $tblEvenements
 * @property TypeEvenement $tblTypeEvenement
 */
class SoustypeEvenement extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return SoustypeEvenement the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function getTypeEvenement()
	{
		return CHtml::listData(TypeEvenement::model()->findAll('',array()),'id','nom');
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_soustype_evenement';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tbl_type_evenement_id', 'required'),
			array('tbl_type_evenement_id, siAfficher', 'numerical', 'integerOnly'=>true),
			array('nom, numero', 'length', 'max'=>45),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, nom, numero, tbl_type_evenement_id', 'safe', 'on'=>'search'),
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
			'tblEvenements' => array(self::MANY_MANY, 'Evenement', 'tbl_evenement_soustype_evenement(tbl_soustype_evenement_id, tbl_evenement_id)'),
			'tblTypeEvenement' => array(self::BELONGS_TO, 'TypeEvenement', 'tbl_type_evenement_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','soustypeEvenement.id'),
			'nom' => Yii::t('model','soustypeEvenement.nom'),
			'numero' => Yii::t('model','soustypeEvenement.numero'),
			'tbl_type_evenement_id' => Yii::t('model','soustypeEvenement.tbl_type_evenement_id'),
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
		$criteria->compare('numero',$this->numero,true);
		$criteria->compare('tbl_type_evenement_id',$this->tbl_type_evenement_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}