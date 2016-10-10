<?php
use yii\components\Migration;

class m161010_051227_payment_init extends Migration {

	public $messageCategory ='payment';

	public function init() {
		$this->messagesPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'messages';

		parent::init();
	}

	public function safeUp() {
		$tableOptions = null;
		if($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%payment}}', [
			'id' => $this->string(68)->comment(\Yii::t($this->messageCategory, 'id')),
			'order_id' => $this->text()->notNull()->comment(\Yii::t($this->messageCategory, 'order id')),
			'type' => $this->smallInteger()->notNull()->defaultValue(1)->comment(\Yii::t($this->messageCategory, 'type')),
			'title' => $this->string(68)->notNull()->comment(\Yii::t($this->messageCategory, 'title')),
			'description' => $this->string()->comment(\Yii::t($this->messageCategory, 'description')),
			'url' => $this->text()->comment(\Yii::t($this->messageCategory, 'url')),
			'amount' => $this->bigInteger()->unsigned()->notNull()->defaultValue(0)->comment(\Yii::t($this->messageCategory, 'amount')),
			'mode' => $this->string(68)->notNull()->comment(\Yii::t($this->messageCategory, 'mode')),
			'trade_id' => $this->string(68)->comment(\Yii::t($this->messageCategory, 'trade id')),
			'expired_at' => $this->integer()->notNull()->defaultValue(0)->comment(\Yii::t($this->messageCategory, 'expired time')),
			'completed_at' => $this->integer()->notNull()->defaultValue(0)->comment(\Yii::t($this->messageCategory, 'completed time')),
			'created_at' => $this->integer()->notNull()->comment(\Yii::t($this->messageCategory, 'created time')),
			'updated_at' => $this->integer()->notNull()->comment(\Yii::t($this->messageCategory, 'updated time')),
		], $tableOptions);
		$this->addPrimaryKey('id', '{{%payment}}', 'id');
		$this->createIndex('type', '{{%payment}}', 'type');
		$this->createIndex('amount', '{{%payment}}', 'amount');
		$this->createIndex('mode', '{{%payment}}', 'mode');
		$this->createIndex('completed_at', '{{%payment}}', 'completed_at');
		$this->addCommentOnTable('{{%payment}}', \Yii::t($this->messageCategory, 'payment'));

		$this->createTable('{{%payment_notify}}', [
			'id' => $this->primaryKey()->comment(\Yii::t($this->messageCategory, 'id')),
			'payment_id' => $this->string(68)->notNull()->comment(\Yii::t($this->messageCategory, 'payment id')),
			'mode' => $this->string(68)->notNull()->comment(\Yii::t($this->messageCategory, 'mode')),
			'trade_id' => $this->string(68)->notNull()->comment(\Yii::t($this->messageCategory, 'trade id')),
			'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment(\Yii::t($this->messageCategory, 'status')),
			'verified' => $this->smallInteger()->notNull()->defaultValue(0)->comment(\Yii::t($this->messageCategory, 'verified')),
			'data' => $this->text()->notNull()->comment(\Yii::t($this->messageCategory, 'data')),
			'created_at' => $this->integer()->notNull()->comment(\Yii::t($this->messageCategory, 'created time')),
			'updated_at' => $this->integer()->notNull()->comment(\Yii::t($this->messageCategory, 'updated time')),
		], $tableOptions);
		$this->createIndex('payment_id', '{{%payment_notify}}', 'payment_id');
		$this->createIndex('mode', '{{%payment_notify}}', 'mode');
		$this->createIndex('trade_id', '{{%payment_notify}}', 'trade_id');
		$this->createIndex('verified', '{{%payment_notify}}', 'verified');
		$this->addCommentOnTable('{{%payment_notify}}', \Yii::t($this->messageCategory, 'payment notify'));
	}

	public function safeDown() {
		$this->dropTable('{{%payment_notify}}');
		$this->dropTable('{{%payment}}');
	}

}

