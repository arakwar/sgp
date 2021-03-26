<?php
class Validation extends CApplicationComponent {
	
	public $dateDebut;
	public $dateFin;
	
	public function init()
	{
		return parent::init();
	}
	
	public function validerHoraire($date, $caserne){
		$parametres = Parametres::model()->findByPk(1);
		if(file_exists('protected'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'validation'.
		   DIRECTORY_SEPARATOR.$parametres->documentValidation.DIRECTORY_SEPARATOR.'ValidationService.php')){
			Yii::import('application.components.validation.'.$parametres->documentValidation.'.ValidationService');
			$this->attachBehavior('validationService', new ValidationService);

			/**
			 *  La fonction valider est contenu dans le ValidationService de chaque service.
			 *  Comme norme, la fonction valider() doit retourner un array structuré de la façon suivante :
			 *  [0][0] : 0 si horaire ne peut être fermé, 1 si elle peut, 2 si elle est vide
			 *  [0][]  : Liste des messages à afficher
			 *  [1][]  : Liste des cases à identifier avec une pastille de couleur. La ligne contiendra : 
			 *  		 [date][posteHoraire_id][usager_id]:typeErreur
			 *     		 date : date de la journée
			 *           posteHoraire_id et usager_id : j'ai tu besoin d'expliquer ?
			 *           typeErreur : w pour warning, e pour erreur
			 */

			return $this->valider($date, $caserne);
		}else{
			return [0=>[0=>1]];
		}
	}	
}