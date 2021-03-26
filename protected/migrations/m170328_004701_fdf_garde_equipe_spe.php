<?php

class m170328_004701_fdf_garde_equipe_spe extends CDbMigration
{
	public function up()
	{
		$this->addColumn('tbl_parametres','fdf_garde_specialise','INT(1) DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('tbl_parametres','fdf_garde_specialise');
	}
}