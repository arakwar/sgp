<?php

/**
 * This is the model class for table "tbl_histoUsagerEquipe".
 *
 * The followings are the available columns in table 'tbl_histoUsagerEquipe':
 * @property integer $id
 * @property string $dateHeure
 * @property integer $tbl_usager_id
 * @property integer $tbl_equipe_id
 *
 * The followings are the available model relations:
 * @property Equipe $tblEquipe
 * @property Usager $tblUsager
 */
class HistoUsagerEquipe extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HistoUsagerEquipe the static model class
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
		return 'tbl_histoUsagerEquipe';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tbl_usager_id', 'required'),
			array('id, tbl_usager_id, tbl_equipe_id', 'numerical', 'integerOnly'=>true),
			array('dateHeure', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, dateHeure, tbl_usager_id, tbl_equipe_id', 'safe', 'on'=>'search'),
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
			'tblUsager' => array(self::BELONGS_TO, 'Usager', 'tbl_usager_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','histoUsagerEquipe.id'),
			'dateHeure' => Yii::t('model','histoUsagerEquipe.dateHeure'),
			'tbl_usager_id' => Yii::t('model','histoUsagerEquipe.tbl_usager_id'),
			'tbl_equipe_id' => Yii::t('model','histoUsagerEquipe.tbl_equipe_id'),
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
		$criteria->compare('dateHeure',$this->dateHeure,true);
		$criteria->compare('tbl_usager_id',$this->tbl_usager_id);
		$criteria->compare('tbl_equipe_id',$this->tbl_equipe_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}