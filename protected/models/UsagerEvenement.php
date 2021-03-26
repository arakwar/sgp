<?php

/**
 * This is the model class for table "tbl_usager_evenement".
 *
 * The followings are the available columns in table 'tbl_usager_evenement':
 * @property integer $tbl_usager_id
 * @property integer $tbl_evenement_id
 * @property string $heureDebut
 * @property string $heureFin
 * @property string $deplacement
 * @property string $repas
 */
class UsagerEvenement extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return UsagerEvenement the static model class
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
		return 'tbl_usager_evenement';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tbl_usager_id, tbl_evenement_id', 'required'),
			array('tbl_usager_id, tbl_evenement_id', 'numerical', 'integerOnly'=>true),
			array('deplacement, repas', 'length', 'max'=>45),
			array('heureDebut, heureFin', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('tbl_usager_id, tbl_evenement_id, heureDebut, heureFin, deplacement, repas', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'tbl_usager_id' => Yii::t('model','usagerEvenement.tbl_usager_id'),
			'tbl_evenement_id' => Yii::t('model','usagerEvenement.tbl_evenement_id'),
			'heureDebut' => Yii::t('model','usagerEvenement.heureDebut'),
			'heureFin' => Yii::t('model','usagerEvenement.heureFin'),
			'deplacement' => Yii::t('model','usagerEvenement.deplacement'),
			'repas' => Yii::t('model','usagerEvenement.repas'),
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

		$criteria->compare('tbl_usager_id',$this->tbl_usager_id);
		$criteria->compare('tbl_evenement_id',$this->tbl_evenement_id);
		$criteria->compare('heureDebut',$this->heureDebut,true);
		$criteria->compare('heureFin',$this->heureFin,true);
		$criteria->compare('deplacement',$this->deplacement,true);
		$criteria->compare('repas',$this->repas,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}