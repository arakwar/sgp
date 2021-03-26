<?php

/**
 * This is the model class for table "tbl_grandecran_module".
 *
 * The followings are the available columns in table 'tbl_grandecran_module':
 * @property integer $id
 * @property integer $tbl_grandecran_id
 * @property string $module
 * @property string $parametres
 */
class GrandEcranModule extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return GrandEcranModule the static model class
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
		return 'tbl_grandecran_module';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tbl_grandecran_id', 'numerical', 'integerOnly'=>true),
			array('module', 'length', 'max'=>255),
			array('parametres', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, tbl_grandecran_id, module, parametres', 'safe', 'on'=>'search'),
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
			'grandecran' => array(self::BELONGS_TO, 'GrandEcran', 'tbl_grandecran_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'tbl_grandecran_id' => 'Tbl Grandecran',
			'module' => 'Module',
			'parametres' => 'Parametres',
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
		$criteria->compare('tbl_grandecran_id',$this->tbl_grandecran_id);
		$criteria->compare('module',$this->module,true);
		$criteria->compare('parametres',$this->parametres,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}