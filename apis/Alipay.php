<?php
/*!
 * yii2 extension - 支付系统 - 支付宝sdk
 * xiewulong <xiewulong@vip.qq.com>
 * https://github.com/xiewulong/yii2-payment
 * https://raw.githubusercontent.com/xiewulong/yii2-payment/master/LICENSE
 * create: 2015/1/10
 * update: 2015/1/10
 * version: 0.0.1
 */

namespace yii\payment\apis;

use Yii;

class Alipay{

	//支付宝网关
	private $api = 'https://mapi.alipay.com/gateway.do?';

	//即时到账交易接口参数
	private $params = [
		'service' => 'create_direct_pay_by_user',	//服务名称
		'payment_type' => 1,	//支付类型
		'anti_phishing_key' => null,	//防钓鱼时间戳
		'exter_invoke_ip' => null,	//客户端的IP地址
		'_input_charset' => 'utf-8',	//字符编码格式
	];

	private $sign_type = 'MD5';

	//配置参数
	private $config;

	/**
	 * 构造器
	 * @method __construct
	 * @since 0.0.1
	 * @param {array} $config 参数数组
	 * @return {none}
	 */
	public function __construct($config){
		$this->config = $config;
	}

	/**
	 * 获取类对象
	 * @method sdk
	 * @since 0.0.1
	 * @param {array} $config 参数数组
	 * @return {none}
	 * @example Alipay::sdk($config);
	 */
	public static function sdk($config){
		return new static($config);
	}

	/**
	 * 验证签名
	 * @method verifySign
	 * @since 0.0.1
	 * @param {boolean} [$async=false] 是否为异步通知
	 * @return {boolean}
	 * @example Alipay::sdk($config)->verifySign($async);
	 */
	public function verifySign($async = false){
		$data = $async ? Yii::$app->request->post() : Yii::$app->request->get();

		if(empty($data)){
			return false;
		}

		$sign = $data['sign'];

		$data['sign'] = null;
		$data['sign_type'] = null;

		return Yii::$app->security->compareString($sign, $this->sign($this->getQeuryString($this->arrSort($data))));
	}

	/**
	 * 获取支付链接
	 * @method getPayUrl
	 * @since 0.0.1
	 * @param {string} $out_trade_no 商户订单号
	 * @param {string} $subject 订单名称
	 * @param {number} $total_fee 付款金额
	 * @param {string} $body 订单描述
	 * @param {string} $show_url 商品展示地址
	 * @return {string}
	 * @example Alipay::sdk($config)->getPayUrl($notify_url, $return_url, $out_trade_no, $subject, $total_fee, $body, $show_url);
	 */
	public function getPayUrl($notify_url, $return_url, $out_trade_no, $subject, $total_fee, $body = null, $show_url = null){
		return $this->buildRequest(array_merge([
			'seller_email'	=> $this->config['seller_email'],
			'partner' => $this->config['partner'],
			'notify_url'	=> $notify_url,
			'return_url'	=> $return_url,
			'out_trade_no'	=> $out_trade_no,
			'subject'	=> $subject,
			'total_fee'	=> $total_fee,
			'body'	=> $body,
			'show_url'	=> $show_url,
		], $this->params));
	}

	/**
	 * 创建支付链接
	 * @method buildRequest
	 * @since 0.0.1
	 * @param {array} $params 参数数组
	 * @return {string}
	 */
	private function buildRequest($params){
		$querystring = $this->getQeuryString($this->arrSort($params));

		return $this->api . $querystring . '&sign=' . $this->sign($querystring) . '&sign_type=' . $this->sign_type;
	}

	/**
	 * 对querystring进行签名并返回相应的string
	 * @method sign
	 * @since 0.0.1
	 * @param {string} $string uerystring 
	 * @return {string}
	 */
	private function sign($string){
		$sign = '';
		switch($this->sign_type){
			case 'MD5':
				$sign = md5($string . $this->config['key']);
				break;
		}

		return $sign;
	}

	/**
	 * 获取querystring
	 * @method getQeuryString
	 * @since 0.0.1
	 * @param {array} $arr 需转换数组
	 * @return {string}
	 */
	private function getQeuryString($arr){
		return urldecode(http_build_query($arr));
	}

	/**
	 * 对签名参数进行数组排序
	 * @method arrSort
	 * @since 0.0.1
	 * @param {array} $arr 需排序数组
	 * @return {array}
	 */
	private function arrSort($arr){
		ksort($arr);
		reset($arr);

		return $arr;
	}

}
