<?php

/**
 * This is the model class for table "tbl_histoEquipe".
 *
 * The followings are the available columns in table 'tbl_histoEquipe':
 * @property integer $id
 * @property string $nom
 * @property string $couleur
 * @property integer $siHoraire
 * @property integer $siAlerte
 * @property integer $typeAction
 * @property integer $tbl_equipe_id
 * @property string $dateHeure
 *
 * The followings are the available model relations:
 * @property Equipe $tblEquipe
 */
class HistoEquipe extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HistoEquipe the static model class
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
		return 'tbl_histoEquipe';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tbl_equipe_id', 'required'),
			array('id, siHoraire, siAlerte, typeAction, tbl_equipe_id, tbl_caserne_id', 'numerical', 'integerOnly'=>true),
			array('nom', 'length', 'max'=>45),
			array('couleur', 'length', 'max'=>10),
			array('dateHeure', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, nom, couleur, siHoraire, siAlerte, typeAction, tbl_equipe_id, dateHeure', 'safe', 'on'=>'search'),
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
			'tblEquipe' => array(self::BELONGS_TO, 'Equipe', 'tbl_equipe_id'),
			'tblCaserne' => array(self::BELONGS_TO, 'Caserne', 'tbl_caserne_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','histoEquipe.id'),
			'nom' => Yii::t('model','histoEquipe.nom'),
			'couleur' => Yii::t('model','histoEquipe.couleur'),
			'siHoraire' => Yii::t('model','histoEquipe.siHoraire'),
			'siAlerte' => Yii::t('model','histoEquipe.siAlerte'),
			'typeAction' => Yii::t('model','histoEquipe.typeAction'),
			'tbl_equipe_id' => Yii::t('model','histoEquipe.tbl_equipe_id'),
			'dateHeure' => Yii::t('model','histoEquipe.dateHeure'),
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
		$criteria->compare('couleur',$this->couleur,true);
		$criteria->compare('siHoraire',$this->siHoraire);
		$criteria->compare('siAlerte',$this->siAlerte);
		$criteria->compare('typeAction',$this->typeAction);
		$criteria->compare('tbl_equipe_id',$this->tbl_equipe_id);
		$criteria->compare('dateHeure',$this->dateHeure,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}