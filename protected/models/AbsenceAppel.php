<?php

/**
 * This is the model class for table "tbl_document".
 *
 * The followings are the available columns in table 'tbl_document':
 * @property integer $id
 * @property string $nom
 * @property string $date
 * @property string $description
 * @property integer $tbl_usager_id
 * @property string $nom_fichier
 *
 * The followings are the available model relations:
 * @property Usager $tblUsager
 */
class AbsenceAppel extends CActiveRecord
{
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Document the static model class
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
		return 'tbl_absence_appel';
	}
	
	public function behaviors(){
    	return array( 'CAdvancedArBehavior' => array(
        	'class' => 'system.ext.CAdvancedArBehavior'));
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tbl_absence_id, tbl_usager_id, dateAppel, heure, reponse', 'required'),
			array('tbl_absence_id, tbl_usager_id, reponse', 'numerical', 'integerOnly'=>true),
			array('tbl_absence_id, tbl_usager_id, dateAppel, heure, reponse', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('tbl_absence_id, tbl_usager_id, dateAppel, heure, reponse', 'safe', 'on'=>'search'),
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
			'tblUsager' => array(self::BELONGS_TO, 'Usager', 'tbl_usager_id'),
			'tblAbsence' => array(self::BELONGS_TO, 'Absnce', 'tbl_absence_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'tbl_usager_id'=>Yii::t('model','absenceAppel.tbl_usager_id'),
			'dateAppel' => Yii::t('model','absenceAppel.dateAppel'),
			'heure' => Yii::t('model','absenceAppel.heure'),
			'reponse' => Yii::t('model','absenceAppel.reponse'),
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}