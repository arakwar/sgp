<?php

/**
 * This is the model class for table "tbl_parametres".
 *
 * The followings are the available columns in table 'tbl_parametres':
 * @property integer $heureMaximum
 * @property string $nbJourPeriode
 * @property integer $id
 * @property integer $moduloDebut
 * @property string $dateDebutPeriode
 */
class Parametres extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Parametres the static model class
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
		return 'tbl_parametres';
	}
	
	public function primaryKey()
	{
	  return 'id';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('heureMaximum, moduloDebut, moduloDepotDispo, moisFDF, heureMinimum, listeDispo_Equipe, garde_horaire, garde_fdf, 
					caserne_horaire, nbJourHoraireFixe,	defaut_fdf, affichage_fdf, grandEcran_fdf, grandEcran_horaire, 
					grandEcran_horaire_dateDebut, grandEcran_nbr_periode_horaire, eve_dispo, grandEcran_style, dispoParHeure,
					colonneGauche, droitVoirDispoHoraire, horaireCalculHeure, congeHeureMax, dispo_fdf_type, siCalculHeureHoraire,
					fdf_minimum_type, fdf_equipe_spe, caserne_defaut_horaire, caserne_defaut_fdf, horaire_mensuel',
					'numerical', 'integerOnly'=>true),
			array('nbJourPeriode', 'length', 'max'=>45),
			array('documentValidation', 'length', 'max'=>10),
			array('colonne', 'length', 'max'=>15),
			array('ordre', 'length', 'max'=>4),
			array('timezone', 'length', 'max'=>100),
			array('dateDebutPeriode, dernierAlerteFDF, timezone, dateDebutCalculTemps, maxDateReculRapport','safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('heureMaximum, nbJourPeriode', 'safe', 'on'=>'search'),
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
			'heureMaximum' => Yii::t('model','parametres.heureMaximum'),
			'nbJourPeriode' => Yii::t('model','parametres.nbJourPeriode'),
			'moduloDebut' => Yii::t('model','parametres.moduloDebut'),
			'dateDebutPeriode' => Yii::t('model','parametres.dateDebutPeriode'),
			'moisFDF' => Yii::t('model','parametres.moisFDF'),
			'moduloDepotDispo' => Yii::t('model','parametres.moduloDepotDispo'),
			'colonne' => Yii::t('model','parametres.colonne'),
			'heureMinimum' => Yii::t('model','parametres.heureMinimum'),
			'listeDispo_Equipe' => Yii::t('model','parametres.listeDispo_Equipe'),
			'garde_horaire' => Yii::t('model','parametres.garde_horaire'),
			'garde_fdf' => Yii::t('model','parametres.garde_fdf'),
			'timezone' => Yii::t('model','parametres.timezone'),
			'caserne_horaire' => Yii::t('model','parametres.caserne_horaire'),
			'nbJourHoraireFixe' => Yii::t('model','parametres.nbJourHoraireFixe'),
			'defaut_fdf' => Yii::t('model','parametres.defaut_fdf'),
			'affichage_fdf' => Yii::t('model','parametres.affichage_fdf'),
			'grandEcran_fdf' => Yii::t('model','parametres.grandEcran_fdf'),
			'grandEcran_horaire' => Yii::t('model','parametres.grandEcran_horaire'),
			'grandEcran_horaire_dateDebut' => Yii::t('model','parametres.grandEcran_horaire_dateDebut'),
			'grandEcran_nbr_periode_horaire' => Yii::t('model','parametres.grandEcran_nbr_periode_horaire'),
			'dateDebutCalculTemps'	=>	Yii::t('model','parametres.dateDebutCalculTemps'),
			'eve_dispo'	=>	Yii::t('model','parametres.eve_dispo'),
			'grandEcran_style'	=>	Yii::t('model','parametres.grandEcran_style'),
			'dispoParHeure'	=>	Yii::t('model','parametres.dispoParHeure'),
			'colonneGauche'	=>	Yii::t('model','parametres.colonneGauche'),
			'droitVoirDispoHoraire'	=>	Yii::t('model','parametres.drmoitVoirDispoHoraire'),
			'maxDateReculRapport'	=>	Yii::t('model','parametres.maxDateReculRapport'),
			'horaireCalculHeure'	=>	Yii::t('model','parametres.horaireCalculHeure'),
			'congeHeureMax'	=>	Yii::t('model','parametres.congeHeureMax'),
			'congeHeureDate'	=>	Yii::t('model','parametres.congeHeureDate'),
			'dispo_fdf_type'	=>	Yii::t('model','parametres.dispo_fdf_type'),
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

		$criteria->compare('heureMaximum',$this->heureMaximum);
		$criteria->compare('nbJourPeriode',$this->nbJourPeriode,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}