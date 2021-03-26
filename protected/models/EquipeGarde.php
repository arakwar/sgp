<?php

/**
 * This is the model class for table "tbl_equipe_garde".
 *
 * The followings are the available columns in table 'tbl_equipe_garde':
 * @property integer $modulo
 * @property integer $tbl_equipe_id
 *
 * The followings are the available model relations:
 * @property Equipe $tblEquipe
 */
class EquipeGarde extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return EquipeGarde the static model class
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
		return 'tbl_equipe_garde';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('modulo, tbl_equipe_id', 'required'),
			array('modulo, tbl_equipe_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('modulo, tbl_equipe_id', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'modulo' => Yii::t('model','equipeGarde.modulo'),
			'tbl_equipe_id' => Yii::t('model','equipeGarde.tbl_equipe_id'),
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

		$criteria->compare('modulo',$this->modulo);
		$criteria->compare('tbl_equipe_id',$this->tbl_equipe_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}