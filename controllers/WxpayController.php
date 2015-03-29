<?php

namespace yii\payment\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

class WxpayController extends Controller{

	public $enableCsrfValidation = false;

	private $mode = 'wxpay';

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

	public function actionPackage(){
		return $this->module->manager->getPackage(\Yii::$app->urlManager->createAbsoluteUrl($this->module->id . DIRECTORY_SEPARATOR . $this->id . DIRECTORY_SEPARATOR . 'async'));
	}

	public function actionAsync(){

	}

}
