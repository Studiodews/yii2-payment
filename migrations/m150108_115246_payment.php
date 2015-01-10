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
			'title' => Schema::TYPE_STRING . '(50) not null comment "订单名称"',
			'amount' => Schema::TYPE_BIGINT . ' unsigned not null default 0 comment "支付总额(分)"',
			'description' => Schema::TYPE_STRING . ' comment "描述信息"',
			'url' => Schema::TYPE_TEXT . ' comment "商品展示url"',
			'mode' => Schema::TYPE_STRING . '(50) not null comment "支付方式"',
			'flowid' => Schema::TYPE_STRING . ' comment "第三方支付端流水号"',
			'completed_at' => Schema::TYPE_INTEGER . ' not null default 0 comment "支付完成时间"',
			'created_at' => Schema::TYPE_INTEGER . ' not null comment "创建时间"',
			'updated_at' => Schema::TYPE_INTEGER . ' not null comment "更新时间"',
		], $tableOptions . ' comment="支付记录"');
	}

	public function down(){
		$this->dropTable('{{%payment}}');
	}

}
