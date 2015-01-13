<?php

namespace yii\payment\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

class AlipayController extends Controller{

	public $enableCsrfValidation = false;

	public function behaviors(){
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'async' => ['post'],
					'sync' => ['get'],
				],
			],
		];
	}

	public function actionAsync(){
		if(!$this->module->manager->verifySign(true)){
			return 'fail';
		}

		if($this->checkTradeStatus(Yii::$app->request->post('trade_status'))){
			$id = Yii::$app->request->post('out_trade_no');
			$this->module->manager->complete($id);
			if($this->module->asyncRoute){
				$this->run($this->module->asyncRoute, ['id' => $id]);
			}
		}

		return 'success';
	}

	public function actionSync(){
		if(!$this->module->manager->verifySign()){
			return '验证失败';
		}

		$request = Yii::$app->request;
		if($this->checkTradeStatus($request->get('trade_status'))){
			//return $this->module->syncRoute ? $this->redirect($this->module->syncRoute, ['id' => $request->get('out_trade_no')]) : '付款成功';
			return '付款成功';
		}

		return '验证成功';
	}

	private function checkTradeStatus($trade_status){
		return $trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS';
	}

}
