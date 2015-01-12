<?php

namespace yii\payment\controllers;

use Yii;
use yii\base\ErrorException;
use yii\web\Controller;

class AlipayController extends Controller{

	public function actionSync(){
		echo $this->module->manager->verifySign();
	}

	public function actionAsync(){
		echo $this->module->manager->verifySign(true);
	}

}
