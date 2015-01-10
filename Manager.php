<?php
/*!
 * yii2 extension - 支付系统
 * xiewulong <xiewulong@vip.qq.com>
 * https://github.com/xiewulong/yii2-payment
 * https://raw.githubusercontent.com/xiewulong/yii2-payment/master/LICENSE
 * create: 2015/1/10
 * update: 2015/1/10
 * version: 0.0.1
 */

namespace yii\payment;

use yii\base\ErrorException;
use yii\payment\models\Payment;

class Manager{

	//配置支付方式
	public $modes = [];

	//交易记录
	private $payment = false;

	/**
	 * 创建交易记录
	 * @method createTransaction
	 * @since 0.0.1
	 * @param {number} $oid 订单id
	 * @param {number} $amount 交易总额(分)
	 * @param {string} $mode 支付方式
	 * @return {number|boolean}
	 * @example Yii::$app->payment->createTransaction($oid, $amount, $mode);
	 */
	public function createTransaction($oid, $amount, $mode){
		if(empty($oid)){
			throw new ErrorException('Order id must be requied');
		}

		if($amount <= 0){
			throw new ErrorException('Payment amount must be a positive integer');
		}

		if(!isset($this->modes[$mode])){
			throw new ErrorException('Unsupported payment mode');
		}

		if($this->disabledMode($mode)){
			throw new ErrorException('Payment mode has been disabled');
		}

		$this->payment = new Payment;
		$this->payment->oid = $oid;
		$this->payment->amount = $amount;
		$this->payment->mode = $mode;

		return $this->payment->save() ? $this->payment->id : false;
	}

	/**
	 * 获取支付记录
	 * @method getPayment
	 * @since 0.0.1
	 * @param {number} $id 支付记录id
	 * @return {object}
	 * @example Yii::$app->payment->getPayment($id);
	 */
	public function getPayment($id){
		if($this->payment === false || $this->payment->id != $id){
			$this->payment = Payment::findOne($id);
			if(!$this->payment){
				throw new ErrorException('No record of the transaction');
			}
		}

		return $this->payment;
	}

	/**
	 * 检查支付方式是否被禁用
	 * @method disabledMode
	 * @since 0.0.1
	 * @param {string} $mode mode
	 * @return {boolean}
	 * @example Yii::$app->payment->disabledMode($mode);
	 */
	public function disabledMode($mode){
		$mode = $this->modes[$mode];
		return isset($mode['disabled']) && $mode['disabled'];
	}

}