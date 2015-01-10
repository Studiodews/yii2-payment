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

use Yii;
use yii\base\ErrorException;
use yii\payment\models\Payment;
use yii\payment\apis\Alipay;

class Manager{

	//配置支付方式
	public $modes = [];

	//交易记录
	private $payment = false;

	/**
	 * 进行支付
	 * @method getPayUrl
	 * @since 0.0.1
	 * @param {number} $id 支付记录id
	 * @param {string} $async 异步通知地址
	 * @param {string} $sync 同步通知地址
	 * @return {none}
	 * @example Yii::$app->payment->getPayUrl($id, $async, $sync);
	 */
	public function getPayUrl($id, $async, $sync){
		$payment = $this->getPayment($id);
		switch($payment->mode){
			case 'alipay':
				return $this->alipayPayUrl($async, $sync);
				break;
		}

		return false;
	}

	/**
	 * 使用支付宝进行支付
	 * @method alipayPayUrl
	 * @since 0.0.1
	 * @param {string} $async 异步通知地址
	 * @param {string} $sync 同步通知地址
	 * @return {none}
	 */
	private function alipayPayUrl($async, $sync){
		$alipay = new Alipay($this->modes['alipay']);
		return $alipay->getPayUrl($async, $sync, $this->payment->id, $this->payment->title, $this->getYuans($this->payment->amount), $this->payment->description, $this->payment->url);
	}

	/**
	 * 创建交易记录
	 * @method create
	 * @since 0.0.1
	 * @param {number} $oid 订单id
	 * @param {number} $amount 交易总额(分)
	 * @param {string} $mode 支付方式
	 * @param {string} [$title] 订单名称
	 * @param {string} [$description] 描述信息
	 * @param {string} [$url] 商品展示url
	 * @return {number}
	 * @example Yii::$app->payment->create($oid, $amount, $mode, $title, $description, $url);
	 */
	public function create($oid, $amount, $mode, $title = null, $description = null, $url = null){
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
		$this->payment->title = $title ? $title : Yii::$app->name;
		$this->payment->amount = $amount;
		$this->payment->description = $description;
		$this->payment->url = $url;
		$this->payment->mode = $mode;
		$this->payment->save();

		return $this->payment->save() ? $this->payment->id : 0;
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

	/**
	 * 获取支付方式
	 * @method getMode
	 * @since 0.0.1
	 * @param {number} $id 支付记录id
	 * @return {string}
	 * @example Yii::$app->payment->getMode($id);
	 */
	public function getMode($id){
		return $this->getPayment($id)->mode;
	}

	/**
	 * 获取支付记录
	 * @method getPayment
	 * @since 0.0.1
	 * @param {number} $id 支付记录id
	 * @return {object}
	 */
	private function getPayment($id){
		if($this->payment === false || $this->payment->id != $id){
			$this->payment = Payment::findOne($id);
			if(!$this->payment){
				throw new ErrorException('No record of the transaction');
			}
		}

		return $this->payment;
	}

	/**
	 * 把金额转换成以元为单位
	 * @method getYuans
	 * @since 0.0.1
	 * @param {number} $cents 以分为单位的金额
	 * @return {number}
	 */
	private function getYuans($cents){
		return $cents / 100;
	}

}
