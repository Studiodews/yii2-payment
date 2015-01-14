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
			'id' => Schema::TYPE_BIGINT . ' not null primary key comment "支付记录id"',
			'oid' => Schema::TYPE_INTEGER . ' not null comment "订单id"',
			'title' => Schema::TYPE_STRING . '(50) not null comment "订单名称"',
			'amount' => Schema::TYPE_BIGINT . ' unsigned not null default 0 comment "支付总额(分)"',
			'description' => Schema::TYPE_STRING . ' comment "描述信息"',
			'url' => Schema::TYPE_TEXT . ' comment "商品展示url"',
			'mode' => Schema::TYPE_STRING . '(50) not null comment "支付方式"',
			'tid' => Schema::TYPE_BIGINT . ' comment "第三方支付端流水号"',
			'completed_at' => Schema::TYPE_INTEGER . ' not null default 0 comment "支付完成时间"',
			'created_at' => Schema::TYPE_INTEGER . ' not null comment "创建时间"',
			'updated_at' => Schema::TYPE_INTEGER . ' not null comment "更新时间"',
		], $tableOptions . ' comment="支付记录"');

		$this->createTable('{{%payment_notify}}', [
			'mode' => Schema::TYPE_STRING . '(50) not null comment "第三方支付类型"',
			'tid' => Schema::TYPE_BIGINT . ' not null comment "第三方支付端流水号"',
			'pid' => Schema::TYPE_BIGINT . ' not null comment "支付记录id"',
			'status' => Schema::TYPE_BOOLEAN . ' not null default 0 comment "支付结果状态"',
			'data' => Schema::TYPE_TEXT . ' not null comment "消息通知数据(json)"',
			'created_at' => Schema::TYPE_INTEGER . ' not null comment "接收时间"',
		], $tableOptions . ' comment="第三方支付消息通知记录"');
	}

	public function down(){
		$this->dropTable('{{%payment_notify}}');
		$this->dropTable('{{%payment}}');
	}

}
