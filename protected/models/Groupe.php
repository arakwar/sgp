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
class Groupe extends CActiveRecord
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
		return 'tbl_groupe';
	}
	
	public function defaultScope(){
		$ta = $this->getTableAlias(false,false);
		return $ta == 't' ? array('condition'=>$ta.'.siActif=1') : array();
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('nom', 'length', 'max'=>8),
			array('nomL', 'length', 'max'=>45),
			array('garde_sur_total_groupe','boolean'),
			array('tbl_caserne_id', 'numerical', 'integerOnly'=>true),
			array('id, nom, nomL', 'safe', 'on'=>'search'),
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
			'tblUsagers' => array(self::MANY_MANY, 'Usager', 'tbl_groupe_usager(tbl_groupe_id, tbl_usager_id)'),		
			'tblCaserne' => array(self::BELONGS_TO, 'Caserne', 'tbl_caserne_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','groupe.id'),
			'nom' => Yii::t('model','groupe.nom'),
			'nomL' => Yii::t('model','groupe.nomL'),
			'tbl_caserne_id'=> Yii::t('model','groupe.tbl_caserne_id'),
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