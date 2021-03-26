<?php

class m180127_140337_histo_dispo_horaire extends CDbMigration
{
	public function up()
	{
		$this->createTable('tbl_dispo_horaire_histo',array(
			'id'=>'pk',
			'usager_id'=>'integer',
			'created_by'=>'integer',
			'created_at'=>'datetime',
			'date'=>'date',
			'quart_id'=>'int',
			'dispo'=>'boolean',
			'heureDebut'=>'time',
			'heureFin'=>'time'
		), 'ENGINE InnoDB');
		$this->addForeignKey(
			'fk_dispo_jour_histo_pompier',
			'tbl_dispo_horaire_histo', 'usager_id', 
			'tbl_usager', 'id', 
			'CASCADE', 'CASCADE');
		$this->addForeignKey(
			'fk_dispo_jour_histo_admin',
			'tbl_dispo_horaire_histo', 'created_by', 
			'tbl_usager', 'id', 
			'CASCADE', 'CASCADE');
		$this->addForeignKey(
			'fk_dispo_jour_histo_quart',
			'tbl_dispo_horaire_histo', 'quart_id', 
			'tbl_quart', 'id', 
			'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropTable('tbl_dispo_horaire_histo');
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