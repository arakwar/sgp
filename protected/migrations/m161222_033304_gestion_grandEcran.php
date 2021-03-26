<?php

class m161222_033304_gestion_grandEcran extends CDbMigration
{
	public function up()
	{
		$this->createTable('tbl_grandecran',array(
			'id'=>'pk',
			'titre'=>'string',
			'client_id'=>'INT(1) NOT NULL DEFAULT 7'
		));
		$this->createTable('tbl_grandecran_module',array(
			'id'=>'pk',
			'tbl_grandecran_id'=>'integer',
			'module'=>'string',
			'parametres'=>'text'
		));
		$this->addForeignKey('fk_grandecran_module', 'tbl_grandecran_module', 'tbl_grandecran_id', 
			'tbl_grandecran', 'id', 'CASCADE', 'CASCADE');
		$auth=Yii::app()->authManager;
		$auth->createTask('GrandEcran:index','Voir un grand écran');
		$usager = $auth->getAuthItem('Usager');
		$usager->addChild('GrandEcran:index');
		$auth->save();
	}

	public function down()
	{
		$this->dropTable('tbl_grandecran_module');
		$this->dropTable('tbl_grandecran');
		$auth=Yii::app()->authManager;
		$auth->removeAuthItem('GrandEcran:index');
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