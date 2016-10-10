<?php

namespace yii\payment\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

class BaifubaoController extends Controller{

	public $enableCsrfValidation = false;

	private $mode = 'baifubao';

	public function behaviors(){
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'async' => ['get'],
					'sync' => ['get'],
				],
			],
		];
	}

	public function actionAsync(){
		if(empty($_GET) || !isset($_GET['order_no']) || !isset($_GET['bfb_order_no']) || !isset($_GET['pay_result'])){
			return false;
		}

		$id = $_GET['order_no'];
		$trade_id = $_GET['bfb_order_no'];
		$status = $this->checkTradeStatus($_GET['pay_result']) ? 1 : 0;
		$manager = $this->module->manager;
		$verified = $manager->verifySign($this->mode, true);
		$manager->saveNotify($this->mode, $id, $trade_id, $status, $verified, $_GET);

		if(!$verified){
			return false;
		}

		if($status && $manager->complete($id, $trade_id) && $asyncClass = $this->module->asyncClass){
			$asyncClass::paied($id);
		}

		$this->layout = $this->mode;
		return $this->render($this->action->id);
	}

	public function actionSync(){
		if(!$this->module->manager->verifySign($this->mode)){
			return false;
		}

		$request = \Yii::$app->request;
		if($this->checkTradeStatus($request->get('pay_result'))){
			return $this->module->syncRoute ? $this->redirect([$this->module->syncRoute, 'id' => $request->get('order_no')]) : '付款成功';
		}

		return true;
	}

	private function checkTradeStatus($trade_status){
		return $trade_status == 1;
	}

}
