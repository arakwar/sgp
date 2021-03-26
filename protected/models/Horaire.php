<?php

/**
 * This is the model class for table "tbl_horaire".
 *
 * The followings are the available columns in table 'tbl_horaire':
 * @property integer $id
 * @property string $date
 * @property integer $tbl_poste_horaire_id
 * @property integer $tbl_usager_id
 * @property integer $tbl_periode_id
 *
 * The followings are the available model relations:
 * @property Periode $tblPeriode
 * @property PosteHoraire $tblPosteHoraire
 * @property Usager $tblUsager
 * @property Modification[] $modifications
 */
class Horaire extends CActiveRecord
{
	public $id_usager;
	/**
	 * Returns the static model of the specified AR class.
	 * @return Horaire the static model class
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
		return 'tbl_horaire';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('date, tbl_poste_horaire_id, tbl_usager_id, tbl_caserne_id', 'required'),
			array('heureDebut, heureFin, dateDecoche', 'safe'),
			array('tbl_poste_horaire_id, tbl_usager_id, modif_usager_id, tbl_periode_id, tbl_caserne_id, modifLu, type, parent_id, statut', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, date, tbl_poste_horaire_id, tbl_usager_id, tbl_periode_id', 'safe', 'on'=>'search'),
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
			'PosteHoraire' => array(self::BELONGS_TO, 'PosteHoraire', 'tbl_poste_horaire_id'),
			'Usager' => array(self::BELONGS_TO, 'Usager', 'tbl_usager_id'),
			'ModifUsager' => array(self::BELONGS_TO, 'Usager', 'modif_usager_id'),
			'modifications' => array(self::HAS_MANY, 'Modification', 'tbl_horaire_id'),
			'Caserne' => array(self::BELONGS_TO, 'Caserne', 'tbl_caserne_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','horaire.id'),
			'date' => Yii::t('model','horaire.date'),
			'tbl_poste_horaire_id' => Yii::t('model','horaire.tbl_poste_horaire_id'),
			'tbl_usager_id' => Yii::t('model','horaire.tbl_usager_id'),
			'tbl_periode_id' => Yii::t('model','horaire.tbl_periode_id'),
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
		$criteria->compare('tbl_poste_horaire_id',$this->tbl_poste_horaire_id);
		$criteria->compare('tbl_usager_id',$this->tbl_usager_id);
		$criteria->compare('tbl_periode_id',$this->tbl_periode_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function debutPeriode($nbJour,$modDebut,$date="")
	{
		$parametres = Parametres::model()->findByPk(1);
		if($date=="") $date = date("Y-m-d");
		$dateActuel = new DateTime($date."T00:00:00",new DateTimeZone($parametres->timezone));
		if(!empty($parametres->horaire_mensuel)){
			$dateActuel->modify('first day of this month');
		}else{
			$modActuel = (floor($dateActuel->getTimestamp())/86400)%$nbJour;
			$modDiff = $modDebut-$modActuel;
			if($modDiff>0) $modDiff-=$nbJour;
			$modDiff = abs($modDiff);
			$dateActuel->sub(new DateInterval("P".$modDiff."D"));
		}
		return $dateActuel;
	}
	//$usager : string d'id usager 1;2;3 pour faire un array
	public static function heureTempsPartiel($usager)
	{
		$criteria = new CDbCriteria;		
		
		$criteria->select = 'h.tbl_usager_id AS id_usager, '. 
							'SUM(
								IF(h1.id IS NULL,
									IF(subtime(h.heureFin,h.heureDebut)=0,
										IF(subtime(ph.heureFin,ph.heureDebut)=0,
											IF(subtime(q.heureFin,q.heureDebut)<0,
												subtime(addtime(q.heureFin,"24:00:00"),q.heureDebut),
												subtime(q.heureFin,q.heureDebut)		
											),
											IF(subtime(ph.heureFin,ph.heureDebut)<0,
												subtime(addtime(ph.heureFin,"24:00:00"),ph.heureDebut),
												subtime(ph.heureFin,ph.heureDebut)
											)	
										),
										IF(subtime(h.heureFin,h.heureDebut)<0,
											subtime(addtime(h.heureFin,"24:00:00"),h.heureDebut),
											subtime(h.heureFin,h.heureDebut)
										)
									),
									0
								)
							) AS nbr_heure, p.heureMinimum AS heure_minimum';
		$criteria->alias = 'h';
		$criteria->join = 'LEFT JOIN tbl_poste_horaire ph ON h.tbl_postE_horaire_id = ph.id '.
							'LEFT JOIN tbl_quart q ON ph.tbl_quart_id = q.id '.
							'INNER JOIN tbl_parametres p ON p.id = 1 '.
							'LEFT JOIN tbl_usager u ON h.tbl_usager_id = u.id '.
							'INNER JOIN tbl_horaire h1 ON h1.parent_id = h.id AND h1.type = 1';
		//$criteria->condition = 'h.date BETWEEN :dateDebut AND :dateFin AND u.tempsPlein = 0 AND u.heureTravaillee = 0';
		$criteria->condition = 'h.date <= :date AND u.tempsPlein = 0 AND u.heureTravaillee = 0';
		if($usager!=''){
			$usagers = explode(';',$usager);
			$criteria->addInCondition('h.tbl_usager_id', $usagers);
		}
		//$criteria->params = array(':dateDebut'=>$dateDebut, ':dateFin'=>$dateFin);
		$criteria->params = array(':date'=>date('Y-m-d'));
		$criteria->group = 'h.tbl_usager_id';
		$criteria->having = 'nbr_heure >= heure_minimum';
		
		$horaires = Horaire::model()->findAll($criteria);
		$retour = 0;
		foreach($horaires as $horaire){
			$usager = Usager::model()->findByPK($horaire->id_usager);
			
			$usager->heureTravaillee = 1;
			
			if(!$usager->save())
				$retour = 1;
		}
		
		return $retour;
	}

}