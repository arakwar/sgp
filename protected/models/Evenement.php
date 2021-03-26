<?php

class Evenement extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Equipe the static model class
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
		return 'tbl_evenement';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nom, dateDebut, dateFin', 'required'),
			array('nom, lieu', 'length', 'max'=>255),
			array('dateDebut, dateFin', 'safe'),
			array('instituteur, moniteur, tbl_formation_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, nom, dateDebut, dateFin, lieu, instituteur, moniteur, tbl_formation_id', 'safe', 'on'=>'search'),
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
				'Instituteur' => array(self::BELONGS_TO, 'Usager', 'instituteur'),
				'Moniteur' => array(self::BELONGS_TO, 'Usager', 'moniteur'),
				'Formation' => array(self::BELONGS_TO, 'Formation', 'tbl_formation_id'),
				'tblUsagers' => array(self::MANY_MANY, 'Usager', 'tbl_evenement_usager(tbl_evenement_id, tbl_usager_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','evenement.id'),
			'nom' => Yii::t('model','evenement.nom'),
			'dateDebut' => Yii::t('model','evenement.dateDebut'),
			'dateFin' => Yii::t('model','evenement.dateFin'),
			'lieu' => Yii::t('model','evenement.lieu'),
			'instituteur' => Yii::t('model','evenement.instituteur'),
			'moniteur' => Yii::t('model','evenement.moniteur'),
			'tbl_formation_id' => Yii::t('model','evenement.tbl_formation_id'),
			'tblUsagers' => Yii::t('model','evenement.tblUsagers'),
			'tblInvites' => Yii::t('model','evenement.tblInvites'),
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
		$criteria->compare('dateDebut',$this->dateDebut,true);
		$criteria->compare('dateFin',$this->dateFin,true);
		$criteria->compare('lieu',$this->lieu,true);
		$criteria->compare('instituteur',$this->instituteur,true);
		$criteria->compare('moniteur',$this->moniteur,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}