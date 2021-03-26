<?php

/**
 * This is the model class for table "tbl_document".
 *
 * The followings are the available columns in table 'tbl_document':
 * @property integer $id
 * @property string $nom
 * @property string $date
 * @property string $description
 * @property integer $tbl_usager_id
 * @property string $nom_fichier
 *
 * The followings are the available model relations:
 * @property Usager $tblUsager
 */
class Document extends CActiveRecord
{
	public $fichier;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Document the static model class
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
		return 'tbl_document';
	}
	
	public function behaviors(){
    	return array( 'CAdvancedArBehavior' => array(
        	'class' => 'system.ext.CAdvancedArBehavior'));
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tbl_type_id, suivi', 'numerical', 'integerOnly'=>true),
			array('nom', 'length', 'max'=>100),
			array('nom_fichier', 'length', 'max'=>110),
			array('url', 'length', 'max'=>300),
			//array('fichier', 'file', 'types'=>'png, bmp, jpeg, jpg, xlsx, xls, docx, doc, txt, pdf, zip', 'allowEmpty'=>false, 'on' => 'insert'),
			array('id, nom, date, description, nom_fichier, tblCasernes', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, nom, date, description, nom_fichier', 'safe', 'on'=>'search'),
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
			'tblType' => array(self::BELONGS_TO, 'TypeDocument', 'tbl_type_id'),
			'tblCasernes' => array(self::MANY_MANY, 'Caserne', 'tbl_document_caserne(tbl_document_id, tbl_caserne_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('model','document.id'),
			'nom' => Yii::t('model','document.nom'),
			'date' => Yii::t('model','document.date'),
			'description' => Yii::t('model','document.description'),
			'nom_fichier' => Yii::t('model','document.nom_fichier'),
			'url' => Yii::t('model','document.url'),
			'tbl_type_id' => Yii::t('model','document.tbl_type_id'),
			'tblCasernes' => Yii::t('model','document.tblCasernes'),
			'suivi' => Yii::t('model','document.suivi'),
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
		$criteria->compare('nom',$this->nom,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('nom_fichier',$this->nom_fichier,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('tbl_type_id',$this->tbl_type_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function stripAccents($string){
		$accents = array('à','á','â','ã','ä','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ',
						 'À','Á','Â','Ã','Ä','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý');
		$lettres = array('a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','u','u','u','u','y','y',
						 'A','A','A','A','A','C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','U','U','U','U','Y');
		return str_replace($accents, $lettres, $string);
	}
}