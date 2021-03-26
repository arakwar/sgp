<?php

/**
 * This is the model class for table "tbl_histoEquipe".
 *
 * The followings are the available columns in table 'tbl_histoEquipe':
 * @property integer $id
 * @property string $nom
 * @property string $couleur
 * @property integer $siHoraire
 * @property integer $siAlerte
 * @property integer $typeAction
 * @property integer $tbl_equipe_id
 * @property string $dateHeure
 *
 * The followings are the available model relations:
 * @property Equipe $tblEquipe
 */
class HistoEquipeGarde extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HistoEquipe the static model class
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
		return 'tbl_histoEquipe_Garde';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('modulo, tbl_equipe_id, tbl_quart_id', 'required'),
			array('id, modulo, tbl_equipe_id, tbl_quart_id', 'numerical', 'integerOnly'=>true),
			array('dateHeure', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, modulo, tbl_equipe_id, tbl_quart_id, dateHeure', 'safe', 'on'=>'search'),
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
			'tblQuart' => array(self::BELONGS_TO, 'Quart', 'tbl_quart_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','histoEquipeGarde.id'),
			'modulo' => Yii::t('model','histoEquipeGarde.modulo'),
			'tbl_equipe_id' => Yii::t('model','histoEquipeGarde.tbl_equipe_id'),
			'tbl_quart_id' => Yii::t('model','histoEquipeGarde.tbl_quart_id'),
			'dateHeure' => Yii::t('model','histoEquipeGarde.dateHeure'),
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
		$criteria->compare('modulo',$this->modulo);
		$criteria->compare('tbl_equipe_id',$this->tbl_equipe_id);	
		$criteria->compare('tbl_quart_id',$this->tbl_quart_id);
		$criteria->compare('dateHeure',$this->dateHeure,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}