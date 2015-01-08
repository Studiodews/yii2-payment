<?php

namespace yii\payment\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Payment extends ActiveRecord{

	public static function tableName(){
		return '{{%payment}}';
	}

	public function behaviors(){
		return [
			TimestampBehavior::className(),
		];
	}

}
