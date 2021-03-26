<?php

class m160511_194943_client_id extends CDbMigration
{
	public function up()
	{
		/*
1 = montjoli 
2 = rocher-percé
3 = rdl
4 = demo
5 = tadoussac
6 = edmunston
7 = matane (faire la migration a multi-caserne avant)
8 = scjc
9 = SADM
		*/
		$client_id = 5;
		$this->addColumn('tbl_absence','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_absence_appel','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_caserne','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_dispo_evenement','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_dispo_horaire','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_dispo_jour','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_document','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_document_caserne','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_document_suivi','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_equipe','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_equipe_garde','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_equipe_usager','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_evenement','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_evenement_usager','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_formation','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_formation_pre','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_garde_info','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_grade','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_groupe','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_groupe_formation','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_groupe_formation_usager','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_groupe_usager','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_histo_fdf','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_histoEquipe','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_histoEquipe_Garde','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_histoGroupe','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_histoUsager','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_histoUsagerEquipe','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_histoUsagerGroupe','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_horaire','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_message','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_message_usager','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_minimum','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_minimum_exception','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_notice','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_notice_caserne','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_notification','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_parametres','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_poste','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_poste_horaire','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_poste_horaire_caserne','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_quart','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_remplacement_info','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_type_conge','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_type_document','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_usager','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
		$this->addColumn('tbl_usager_poste','client_id','INT(1) NOT NULL DEFAULT '.$client_id);
	}

	public function down()
	{
		echo "m160511_194943_client_id does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
