<?php

class m170122_145450_caserneParDefaut extends CDbMigration
{
	public function up()
	{
		$this->addColumn('tbl_parametres','caserne_defaut_horaire','INT(11) NULL DEFAULT NULL');
		$this->addColumn('tbl_parametres','caserne_defaut_fdf','INT(11) NULL DEFAULT NULL');
		$this->alterColumn('tbl_parametres','dateDebutCalculTemps','DATE NULL');
	}

	public function down()
	{
		$this->dropColumn('tbl_parametres','caserne_defaut_horaire');
		$this->dropColumn('tbl_parametres','caserne_defaut_fdf');
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