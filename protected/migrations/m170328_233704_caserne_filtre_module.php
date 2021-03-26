<?php

class m170328_233704_caserne_filtre_module extends CDbMigration
{
	public function up()
	{
		$this->addColumn('tbl_caserne','si_fdf','INT(1) DEFAULT 1');
		$this->addColumn('tbl_caserne','si_horaire','INT(1) DEFAULT 1');
	}

	public function down()
	{
		$this->dropColumn('tbl_caserne','si_fdf');
		$this->dropColumn('tbl_caserne','si_horaire');
	}
}