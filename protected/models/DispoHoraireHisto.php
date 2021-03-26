<?php

/**
 * This is the model class for table "tbl_dispo_horaire_histo".
 *
 * The followings are the available columns in table 'tbl_dispo_horaire_histo':
 * @property integer $id
 * @property integer $usager_id
 * @property integer $created_by
 * @property integer $created_at
 * @property string $date
 * @property integer $quart_id
 * @property integer $dispo
 * @property string $heureDebut
 * @property string $heureFin
 * @property integer $client_id
 *
 * The followings are the available model relations:
 * @property Usager $createdBy
 * @property Usager $usager
 * @property Quart $quart
 */
class DispoHoraireHisto extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DispoHoraireHisto the static model class
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
		return 'tbl_dispo_horaire_histo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('usager_id, created_by, quart_id, dispo', 'numerical', 'integerOnly'=>true),
			array('date, heureDebut, heureFin, created_at', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, usager_id, created_by, created_at, date, quart_id, dispo, heureDebut, heureFin, client_id', 'safe', 'on'=>'search'),
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
			'createdBy' => array(self::BELONGS_TO, 'Usager', 'created_by'),
			'usager' => array(self::BELONGS_TO, 'Usager', 'usager_id'),
			'quart' => array(self::BELONGS_TO, 'Quart', 'quart_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'usager_id' => 'Usager',
			'created_by' => 'Created By',
			'created_at' => 'Created At',
			'date' => 'Date',
			'quart_id' => 'Quart',
			'dispo' => 'Dispo',
			'heureDebut' => 'Heure Debut',
			'heureFin' => 'Heure Fin',
			'client_id' => 'Client',
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
		$criteria->compare('usager_id',$this->usager_id);
		$criteria->compare('created_by',$this->created_by);
		$criteria->compare('created_at',$this->created_at);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('quart_id',$this->quart_id);
		$criteria->compare('dispo',$this->dispo);
		$criteria->compare('heureDebut',$this->heureDebut,true);
		$criteria->compare('heureFin',$this->heureFin,true);
		$criteria->compare('client_id',$this->client_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}