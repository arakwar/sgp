<?php

class m210406_193704_add_poste_horaire_order_color extends CDbMigration
{
	public function up()
	{
		$this->addColumn('tbl_poste_horaire','couleur','VARCHAR(6) DEFAULT NULL');
		$this->addColumn('tbl_poste_horaire','order','INT DEFAULT 0');
	}

	public function down()
	{
		echo "m210406_193704_add_poste_horaire_order_color does not support migration down.\n";
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