<?php

/**
 * This is the model class for table "tbl_dispo_horaire".
 *
 * The followings are the available columns in table 'tbl_dispo_horaire':
 * @property integer $id
 * @property integer $tbl_quart_id
 * @property string $date
 * @property integer $tbl_usager_id
 * @property integer $modulo
 *
 * The followings are the available model relations:
 * @property Quart $tblQuart
 * @property Usager $tblUsager
 */
class DispoEvenement extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return DispoHoraire the static model class
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
		return 'tbl_dispo_evenement';
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
			array('id, tbl_usager_id, modulo', 'numerical', 'integerOnly'=>true),
			array('dispo', 'boolean'),
			array('date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, date, tbl_usager_id, modulo', 'safe', 'on'=>'search'),
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
			'tblCaserne' => array(self::BELONGS_TO, 'Caserne', 'tbl_caserne_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','dispoEvenement.id'),
			'date' => Yii::t('model','dispoEvenement.date'),
			'tbl_usager_id' => Yii::t('model','dispoEvenement.tbl_usager_id'),
			'modulo' => Yii::t('model','dispoEvenement.modulo'),
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
		$criteria->compare('date',$this->date,true);
		$criteria->compare('tbl_usager_id',$this->tbl_usager_id);
		$criteria->compare('modulo',$this->modulo);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}