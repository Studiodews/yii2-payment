<?php

use yii\db\Schema;
use yii\db\Migration;

class m150108_115246_payment extends Migration{

	public function up(){
		$tableOptions = 'engine=innodb character set utf8';
		if($this->db->driverName === 'mysql') {
			$tableOptions .= ' collate utf8_unicode_ci';
		}

		$this->createTable('{{%payment}}', [
			'id' => Schema::TYPE_PK . ' comment "支付id"',
			'oid' => Schema::TYPE_INTEGER . ' not null comment "订单id"',
			'amount' => Schema::TYPE_BIGINT . ' unsigned not null default 0 comment "支付总额(分)"',
			'method' => Schema::TYPE_STRING . '(50) not null comment "支付方式"',
			'flow' => Schema::TYPE_STRING . ' comment "支付端流水号"',
			'completed_at' => Schema::TYPE_INTEGER . ' not null default 0 comment "完成时间"',
			'created_at' => Schema::TYPE_INTEGER . ' not null comment "创建时间"',
			'updated_at' => Schema::TYPE_INTEGER . ' not null comment "更新时间"',
		], $tableOptions . ' comment="支付记录"');
	}

	public function down(){
		$this->dropTable('{{%payment}}');
	}

}
