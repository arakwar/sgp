<?php

class Caserne extends CActiveRecord
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
		return 'tbl_caserne';
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
			array('nom', 'required'),
			array('nom', 'length', 'max'=>45),
			array('adresse', 'length', 'max'=>255),
			array('numero', 'length', 'max'=>20),
			array('ville', 'length', 'max'=>100),
			array('codePostal', 'length', 'max'=>6),
			array('siActif, siGrandEcran, si_fdf, si_horaire', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, nom, adresse, numero, ville, codePostal', 'safe', 'on'=>'search'),
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
			'tblNotices' => array(self::MANY_MANY, 'Notice', 'tbl_notice_caserne(tbl_caserne_id, tbl_notice_id)'),
			'tblDocuments' => array(self::MANY_MANY, 'Document', 'tbl_document_caserne(tbl_caserne_id, tbl_document_id)'),
			'tblQuarts' => array(self::MANY_MANY, 'Quart', 'tbl_quart_caserne(tbl_caserne_id, tbl_quart_id)'),
			'tblPostesHoraire' => array(self::MANY_MANY, 'PosteHoraire', 'tbl_poste_horaire_caserne(tbl_caserne_id, tbl_poste_horaire_id)'),
			'tblEquipes' => array(self::HAS_MANY, 'Equipe', 'tbl_caserne_id'),
			'tblGroupes' => array(self::HAS_MANY, 'Groupe', 'tbl_caserne_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','caserne.id'),
			'nom' => Yii::t('model','caserne.nom'),
			'adresse' => Yii::t('model','caserne.adresse'),
			'numero' => Yii::t('model','caserne.numero'),
			'ville' => Yii::t('model','caserne.ville'),
			'codePostal' => Yii::t('model','caserne.codePostal'),
			'siGrandEcran' => Yii::t('model','caserne.siGrandEcran'),
			'si_fdf' => Yii::t('model','caserne.si_fdf'),
			'si_horaire' => Yii::t('model','caserne.si_horaire'),
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
		$criteria->compare('adresse',$this->adresse,true);
		$criteria->compare('numero',$this->numero,true);
		$criteria->compare('ville',$this->ville,true);
		$criteria->compare('codePostal',$this->codePostal,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}