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
class DispoHoraire extends CActiveRecord
{

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'tbl_dispo_horaire';
	}

	public function rules()
	{
		// NOTE: you should only define rules for those attributes that will receive user inputs.
		return array(
			array('tbl_usager_id', 'required'),
			array('id, tbl_usager_id, modulo', 'numerical', 'integerOnly'=>true),
			array('modulo, tbl_periode_id','default','value'=>0),
			array('dispo', 'boolean'),
			array('date, heureDebut, heureFin, dateDecoche, tsTebut, tsFin', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, date, tbl_usager_id, modulo', 'safe', 'on'=>'search'),
		);
	}


	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related class name for the relations automatically generated below.
		return array(
			'tblUsager' => array(self::BELONGS_TO, 'Usager', 'tbl_usager_id'),
			'tblCaserne' => array(self::BELONGS_TO, 'Caserne', 'tbl_caserne_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','dispoHoraire.id'),
			'date' => Yii::t('model','dispoHoraire.date'),
			'tbl_usager_id' => Yii::t('model','dispoHoraire.tbl_usager_id'),
			'modulo' => Yii::t('model','dispoHoraire.modulo'),
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

	public function beforeSave()
	{
		$this->updated_at = date('Y-m-d H:i:s');
		return parent::beforeSave();
	}

	protected function afterSave()
	{
		Yii::log('test','warning');
		if(Yii::app()->db->schema->getTable('tbl_dispo_horaire_histo')){
			try{
				$histo = new DispoHoraireHisto();
				$histo->usager_id = $this->tbl_usager_id;
				$histo->created_by = Yii::app()->user->id;
				$histo->created_at = date('Y-m-d H:i:s');
				$histo->date = $this->date;
				$histo->quart_id = $this->tbl_quart_id;
				$histo->dispo = $this->dispo;
				$histo->heureDebut = $this->heureDebut;
				$histo->heureFin = $this->heureFin;
				if(!$histo->save()){
					Yii::log('Error while saving dispo_horaire_histo','warning');
					die();
				}
			}catch(Exception $e){
				Yii::log('Error while trying to save dispo_horaire_histo','warning');
			}
		}
		parent::afterSave();
	}
}