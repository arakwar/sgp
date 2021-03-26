<?php

/**
 * This is the model class for table "tbl_histo_fdf".
 *
 * The followings are the available columns in table 'tbl_histo_jour':
 * @property integer $id
 * @property integer $tbl_quart_id
 * @property string $date
 * @property integer $tbl_usager_id
 * @property integer $action
 * @property integer $dateAction
 * @property integer $usager_action
 *
 * The followings are the available model relations:
 * @property Quart $tblQuart
 * @property Usager $tblUsager
 */
class HistoFDF extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DispoFDF the static model class
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
		return 'tbl_histo_fdf';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tbl_quart_id, tbl_usager_id, usager_action', 'required'),
			array('id, tbl_quart_id, tbl_usager_id, usager_action', 'numerical', 'integerOnly'=>true),
			array('action', 'boolean'),
			array('heureDebut, heureFin, date, dateAction', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, tbl_quart_id, date, tbl_usager_id', 'safe', 'on'=>'search'),
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
			'tblQuart' => array(self::BELONGS_TO, 'Quart', 'tbl_quart_id'),
			'tblUsager' => array(self::BELONGS_TO, 'Usager', 'tbl_usager_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','histoFDF.id'),
			'tbl_quart_id' => Yii::t('model','histoFDF.tbl_quart_id'),
			'date' => Yii::t('model','histoFDF.date'),
			'tbl_usager_id' => Yii::t('model','histoFDF.tbl_usager_id'),
			'dateAction' => Yii::t('model','histoFDF.dateAction'),
			'action' => Yii::t('model','histoFDF.action'),
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
		$criteria->compare('tbl_quart_id',$this->tbl_quart_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('tbl_usager_id',$this->tbl_usager_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}