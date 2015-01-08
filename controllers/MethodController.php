<?php

namespace yii\payment\controllers;

use Yii;
use yii\web\Controller;

class MethodController extends Controller{

	public $defaultAction = 'choose';
	
	public function actionChoose(){

		echo 'choose method';

	}

}
