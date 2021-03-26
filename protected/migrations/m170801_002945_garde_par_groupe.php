<?php

class m170801_002945_garde_par_groupe extends CDbMigration
{
	public function up()
	{
		$this->addColumn('tbl_groupe','garde_sur_total_groupe','INT(1) DEFAULT 1');
	}

	public function down()
	{
		$this->dropColumn('tbl_groupe','garde_sur_total_groupe');
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