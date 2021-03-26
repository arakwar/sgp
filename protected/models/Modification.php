<?php

/**
 * This is the model class for table "tbl_modification".
 *
 * The followings are the available columns in table 'tbl_modification':
 * @property integer $id
 * @property string $dateModif
 * @property string $heureModif
 * @property integer $modifLu
 * @property integer $nouveau_usager_id
 * @property integer $modif_usager_id
 * @property integer $tbl_horaire_id
 *
 * The followings are the available model relations:
 * @property Horaire $tblHoraire
 * @property Usager $nouveauUsager
 * @property Usager $modifUsager
 */
class Modification extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Modification the static model class
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
		return 'tbl_modification';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nouveau_usager_id, modif_usager_id, tbl_horaire_id', 'required'),
			array('modifLu, nouveau_usager_id, modif_usager_id, tbl_horaire_id', 'numerical', 'integerOnly'=>true),
			array('dateModif, heureModif', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, dateModif, heureModif, modifLu, nouveau_usager_id, modif_usager_id, tbl_horaire_id', 'safe', 'on'=>'search'),
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
			'tblHoraire' => array(self::BELONGS_TO, 'Horaire', 'tbl_horaire_id'),
			'Usager' => array(self::BELONGS_TO, 'Usager', 'nouveau_usager_id'),
			'modifUsager' => array(self::BELONGS_TO, 'Usager', 'modif_usager_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','modification.id'),
			'dateModif' => Yii::t('model','modification.dateModif'),
			'heureModif' => Yii::t('model','modification.heureModif'),
			'modifLu' => Yii::t('model','modification.modifLu'),
			'nouveau_usager_id' => Yii::t('model','modification.nouveau_usager_id'),
			'modif_usager_id' => Yii::t('model','modification.modif_usager_id'),
			'tbl_horaire_id' => Yii::t('model','modification.tbl_horaire_id'),
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
		$criteria->compare('dateModif',$this->dateModif,true);
		$criteria->compare('heureModif',$this->heureModif,true);
		$criteria->compare('modifLu',$this->modifLu);
		$criteria->compare('nouveau_usager_id',$this->nouveau_usager_id);
		$criteria->compare('modif_usager_id',$this->modif_usager_id);
		$criteria->compare('tbl_horaire_id',$this->tbl_horaire_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}