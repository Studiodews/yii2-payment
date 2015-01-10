<?php

namespace yii\payment;

use Yii;
use yii\payment\models\Payment;

class Module extends \yii\base\Module{

	public $defaultRoute = 'transaction';

	public $defaultComponent = 'payment';

	public $manager;

	//结果显示页route路径
	public $resultRoute;

	public function init(){
		parent::init();

		$this->manager = Yii::createObject(Yii::$app->components[$this->defaultComponent]);
	}

}
