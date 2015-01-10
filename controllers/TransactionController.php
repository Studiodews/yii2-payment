<?php

namespace yii\payment\controllers;

use Yii;
use yii\web\Controller;

class TransactionController extends Controller{

	public $defaultAction = 'pay';

	public function actionPay($id){
		return $this->run($this->module->manager->getPayment($id)->mode, [
			'id' => $id,
		]);
	}

	public function actionAlipay($id){
		if($this->module->manager->disabledMode('alipay')){
			echo 'disabled';
			return false;
		}

		$payment = $this->module->manager->getPayment($id);
		
	}

}
