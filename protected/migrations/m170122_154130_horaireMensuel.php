<?php

class m170122_154130_horaireMensuel extends CDbMigration
{
	public function up()
	{
		$this->addColumn('tbl_parametres','horaire_mensuel','INT(1) DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('tbl_parametres','horaire_mensuel');
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