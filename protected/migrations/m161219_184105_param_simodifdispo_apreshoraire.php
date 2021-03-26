<?php

class m161219_184105_param_simodifdispo_apreshoraire extends CDbMigration
{
	public function up()
	{
		$this->addColumn('tbl_parametres','dispo_horaire_debarre','INT(1) NOT NULL DEFAULT 0');
		$this->addColumn('tbl_horaire','updated_at','TIMESTAMP DEFAULT "1990-01-01"');
		$this->addColumn('tbl_dispo_horaire','updated_at','TIMESTAMP DEFAULT "1990-01-01"');
	}

	public function down()
	{
		$this->dropColumn('tbl_parametres','dispo_horaire_debarre');
		$this->dropColumn('tbl_horaire','updated_at');
		$this->dropColumn('tbl_dispo_horaire','updated_at');
	}
}
