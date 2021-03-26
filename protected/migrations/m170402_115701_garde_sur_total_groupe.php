<?php

class m170402_115701_garde_sur_total_groupe extends CDbMigration
{
	public function up()
	{
		$this->addColumn('tbl_parametres','garde_sur_total_groupe','INT(1) DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('tbl_parametres','garde_sur_total_groupe');
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