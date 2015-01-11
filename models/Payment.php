<?php

namespace yii\payment\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Payment extends ActiveRecord{

	private $mac_hach = 'sha256';

	public static function tableName(){
		return '{{%payment}}';
	}

	public function behaviors(){
		return [
			TimestampBehavior::className(),
		];
	}

	public function generateDataHash($key, $id = false){
		return hash_hmac('sha256', $this->getDataString($id === false ? $this->id : $id), $key, false);
	}

	public function validateData($id, $hash, $key){
		return Yii::$app->security->compareString($hash, $this->generateDataHash($key, $id));
	}

	private function getDataString($id){
		return $this->id . $this->oid . $this->amount . $this->mode;
	}

}
