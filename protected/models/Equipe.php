<?php

/**
 * This is the model class for table "tbl_equipe".
 *
 * The followings are the available columns in table 'tbl_equipe':
 * @property integer $id
 * @property string $nom
 * @property string $couleur
 * @property integer $siHoraire
 * @property integer $siAlerte
 *
 * The followings are the available model relations:
 * @property EquipeGarde[] $equipeGardes
 * @property Usager[] $tblUsagers
 * @property Periode[] $periodes
 */
class Equipe extends CActiveRecord
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
		return 'tbl_equipe';
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
			array('couleur', 'required'),
			array('siHoraire, siFDF, ordre, tbl_caserne_id, id', 'numerical', 'integerOnly'=>true),
			array('nom', 'length', 'max'=>45),
			array('couleur', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, nom, couleur', 'safe', 'on'=>'search'),
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
			'equipeGardes' => array(self::HAS_MANY, 'EquipeGarde', 'tbl_equipe_id'),
			'tblUsagers' => array(self::MANY_MANY, 'Usager', 'tbl_equipe_usager(tbl_equipe_id, tbl_usager_id)'),
			'periodes' => array(self::HAS_MANY, 'Periode', 'equipeTour'),
			'tblCaserne' => array(self::BELONGS_TO, 'Caserne', 'tbl_caserne_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','equipe.id'),
			'nom' => Yii::t('model','equipe.nom'),
			'couleur' => Yii::t('model','equipe.couleur'),
			'siHoraire' => Yii::t('model','equipe.siHoraire'),
			'siFDF' => Yii::t('model','equipe.siFDF'),
			'siAlerte' => Yii::t('model','equipe.siAlerte'),
			'tbl_caserne_id' => Yii::t('model','equipe.tbl_caserne_id'),
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
		$criteria->compare('tbl_caserne_id',$this->tbl_caserne_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}