<?php

namespace yii\payment;

use Yii;
use yii\base\ErrorException;
use yii\payment\models\Payment;

class Module extends \yii\base\Module{

	public $defaultRoute = 'method';

	public $methods = [];


	
}
