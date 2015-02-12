<?php
/*!
 * yii2 extension - 支付系统
 * xiewulong <xiewulong@vip.qq.com>
 * https://github.com/xiewulong/yii2-payment
 * https://raw.githubusercontent.com/xiewulong/yii2-payment/master/LICENSE
 * create: 2015/1/10
 * update: 2015/1/29
 * version: 0.0.1
 */

namespace yii\payment;

use Yii;
use yii\base\ErrorException;
use yii\helpers\Json;
use yii\payment\models\Payment;
use yii\payment\models\PaymentNotify;
use yii\payment\apis\Alipay;

class Manager{

	//支付单id前缀, 最高4位纯数字, 默认1000
	public $idpre = 1000;

	//认证密钥
	public $hashkey = false;

	//配置支付方式
	public $modes = [];

	//交易记录
	private $payment = false;

	/**
	 * 第三方消息通知记录
	 * @method complete
	 * @since 0.0.1
	 * @param {string} $mode 第三方支付端流水号
	 * @param {number} $pid 支付单id
	 * @param {number} $tid 第三方支付端流水号
	 * @param {boolean} $status 支付结果状态
	 * @param {boolean} $verified 消息验证结果
	 * @param {string} $data 消息通知数据
	 * @return {none}
	 * @example Yii::$app->payment->saveNotify($mode, $pid, $tid, $status, $verified, $data);
	 */
	public function saveNotify($mode, $pid, $tid, $status, $verified, $data){
		$notify = new PaymentNotify;
		$notify->mode = $mode;
		$notify->pid = $pid;
		$notify->tid = $tid;
		$notify->status = $status;
		$notify->verified = $verified ? 1 : 0;
		$notify->data = Json::encode($data);
		$notify->created_at = time();
		$notify->save();
	}

	/**
	 * 完成支付
	 * @method complete
	 * @since 0.0.1
	 * @param {number} $id 支付单id
	 * @param {string} $tid 第三方支付端流水号
	 * @return {none}
	 * @example Yii::$app->payment->complete($id);
	 */
	public function complete($id, $tid){
		$payment = $this->getPayment($id);
		if($payment->completed_at > 0){
			return false;
		}
		$payment->tid = $tid;
		$payment->completed_at = time();
		$payment->save();
		
		return true;
	}

	/**
	 * 进行支付
	 * @method verifySign
	 * @since 0.0.1
	 * @param {boolean} [$async=false] 是否为异步通知
	 * @return {boolean}
	 * @example Yii::$app->payment->verifySign($async);
	 */
	public function verifySign($async = false){
		return Alipay::sdk($this->modes['alipay'])->verifySign($async);
	}

	/**
	 * 进行支付
	 * @method getPayUrl
	 * @since 0.0.1
	 * @param {number} $id 支付单id
	 * @param {string} $async 异步通知地址
	 * @param {string} $sync 同步通知地址
	 * @param {string} $hash hash加密串
	 * @return {none}
	 * @example Yii::$app->payment->getPayUrl($id, $async, $sync, $hash);
	 */
	public function getPayUrl($id, $async, $sync, $hash = null){
		$payment = $this->getPayment($id);
		
		if($this->hashkey === false || $payment->validateData($id, $hash, $this->hashkey)){
			switch($payment->mode){
				case 'alipay':
					return $this->alipayPayUrl($async, $sync);
					break;
			}
		}

		return false;
	}

	/**
	 * 获取hash加密串
	 * @method getPaymentHash
	 * @since 0.0.1
	 * @param {number} [$id] 支付单id
	 * @return {string}
	 */
	public function getPaymentHash($id = null){
		if($id){
			$this->getPayment($id);
		}

		return $this->payment === false ? '' : $this->payment->generateDataHash($this->hashkey);
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
		return Alipay::sdk($this->modes['alipay'])->getPayUrl($async, $sync, $this->payment->id, $this->payment->title, $this->getYuans($this->payment->amount), $this->payment->description, $this->payment->url);
	}

	/**
	 * 获取当前支付单id
	 * @method getId
	 * @since 0.0.1
	 * @return {number}
	 */
	public function getId(){
		return $this->payment->id;
	}

	/**
	 * 创建交易记录
	 * @method create
	 * @since 0.0.1
	 * @param {number} $oid 订单id
	 * @param {number} $amount 交易总额(分)
	 * @param {string} $mode 支付方式
	 * @param {string} [$title=null] 订单名称
	 * @param {string} [$description=null] 描述信息
	 * @param {string} [$url=null] 商品展示url
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

		if(!array_key_exists($mode, $this->modes)){
			throw new ErrorException('Unsupported payment mode');
		}

		if($this->disabledMode($mode)){
			throw new ErrorException('Payment mode has been disabled');
		}

		$this->payment = new Payment;
		$this->payment->id = $this->createId();
		$this->payment->oid = $oid;
		$this->payment->title = $title ? $title : Yii::$app->name;
		$this->payment->amount = $amount;
		$this->payment->description = $description;
		$this->payment->url = $url;
		$this->payment->mode = $mode;
		$this->payment->save();

		return $this;
	}

	/**
	 * 创建支付单id
	 * @method createId
	 * @since 0.0.1
	 * @return {number}
	 */
	private function createId(){
		return $this->idpre . floor(microtime(true) * 1000) . mt_rand(100, 999);
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
	 * @param {number} $id 支付单id
	 * @return {string}
	 * @example Yii::$app->payment->getMode($id);
	 */
	public function getMode($id){
		return $this->getPayment($id)->mode;
	}

	/**
	 * 获取支付单
	 * @method getPayment
	 * @since 0.0.1
	 * @param {number} $id 支付单id
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
	 * @param {boolean} [$float=false] 是否强制以浮点输出
	 * @param {number} [$decimals=2] 规定多少个小数
	 * @param {string} [$separator=''] 规定用作千位分隔符的字符串
	 * @param {string} [$decimalpoint='.'] 规定用作小数点的字符串, 默认'.'
	 * @return {number|float}
	 * @example Yii::$app->payment->getYuans($cents, $float, $decimals, $separator, $decimalpoint);
	 */
	public function getYuans($cents, $float = false, $decimals = 2, $separator = '', $decimalpoint = '.'){
		$yuans = $cents / 100;
		return $float ? number_format($yuans, $decimals, $decimalpoint, $separator) : $yuans;
	}

}
