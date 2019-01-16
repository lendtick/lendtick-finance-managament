<?php

namespace App\Helpers;

/*
| ==============================================================================================================
| Doku Helper for Merchant Hosted
| 
| ==============================================================================================================
*/

Class Doku {
	const stagingPrePaymentUrl = 'https://staging.doku.com/api/payment/PrePayment';
	const stagingPaymentUrl = 'https://staging.doku.com/api/payment/paymentMip';
	const stagingDirectPaymentUrl = 'https://staging.doku.com/api/payment/PaymentMIPDirect';
	const stagingGenerateCodeUrl = 'https://staging.doku.com/api/payment/DoGeneratePaycodeVA';
	const stagingRedirectPaymentUrl = 'https://staging.doku.com/api/payment/doInitiatePayment';
	const stagingCaptureUrl = 'https://staging.doku.com/api/payment/DoCapture';

	const livePrePaymentUrl = 'https://pay.doku.com/api/payment/PrePayment';
	const livePaymentUrl = 'https://pay.doku.com/api/payment/paymentMip';
	const liveDirectPaymentUrl = 'https://pay.doku.com/api/payment/PaymentMIPDirect';
	const liveGenerateCodeUrl = 'https://pay.doku.com/api/payment/DoGeneratePaycodeVA';
	const liveRedirectPaymentUrl = 'https://pay.doku.com/api/payment/doInitiatePayment';
	const liveCaptureUrl = 'https://pay.doku.com/api/payment/DoCapture';

	private static $url, $ci, $error, $res, $stage, $config, $sharedkey, $device_id, $pairing_code, $method, $basket, $raw_words;
	public static $json = false;

	public function __construct($var=null){
    
		if(is_array($var)){
			self::debug($var);
		} else if(!is_array($var) && !is_null($var))
      self::_error('Variable must array format');
		self::$stage = false;
    self::$config = array();
    self::_url();
    
    self::$config['req_mall_id'] = env("DOKU_MALL_ID", "");
    self::$sharedkey = env("DOKU_SHARED_KEY", "");
	}

	public static function setID($mall=null,$sharedkey=null){
		if(!is_null($mall))
			self::$config['req_mall_id'] = $mall;
		else
			self::_error('MallID must not empty setID(MallID,SharedKey)');

		if(!is_null($sharedkey))
			self::$sharedkey = $sharedkey;
		else
			self::_error('SharedKey must not empty setID(MallID,SharedKey)');
	}

	public static function staging($v=false){
		if(in_array(strtolower($v),array("true","false","0","1")))
			self::$stage = $v;
		else
			self::_error('Staging must be (true/false/1/0)');
		self::_url();
	}

	public static function data($data=null){
		if(is_null($data))
			self::_error('Data can\'t empty or null');
		else if(!is_array($data))
			self::_error('Data must be array');
		else{
			self::$config = array_merge(self::$config, $data);
			// if(!isset(self::$config['req_currency'])){
			// 	self::$config['req_purchase_currency'] = 360;
			// 	self::$config['req_currency'] = 360;
			// }
			
			// set item data
			if(!empty(self::$basket)) self::$config['req_basket'] = self::$basket;

			if(!empty(self::$device_id) && !empty(self::$pairing_code)){
				if(!isset(self::$config['req_token_id']))
					self::_error('req_token_id must input in data');
				else
					self::_words();
			} else
				self::_words();
		}
	}

	public static function setDeviceId($d=null){
		if(is_null($d))
			self::_error('Device ID can\'t be null');
		else{
			self::$device_id = $d;
			if(count(self::$config) > 2)
				self::_words();
		}
	}

	public static function setPairingCode($p=null){
		if(is_null($p))
			self::_error('Pairing Code can\'t be null');
		else{
			self::$pairing_code = $p;
			if(count(self::$config) > 2)
				self::_words();
		}
	}

	public static function setMethod($m=null){
		if(!is_null($m)){
			if(!isset(self::$url->{$m}))
				self::_error('Method not found');
			else
				self::$method = $m;
		} else
			self::_error('Method must define');
	}

	public static function setItems($it=null){
		if(!is_null($it)){
			if(is_array($it)){
				if(isset($it[0]['name']))
					foreach($it as $basket){
						self::$basket = self::$basket . $basket['name'] .','. $basket['amount'] .','. $basket['quantity'] .','. $basket['subtotal'] .';';
					}
			} else if(is_object($data)){
				if(isset($it[0]->name))
					foreach($it as $basket){
						self::$basket = self::$basket . $basket->name .','. $basket->amount .','. $basket->quantity .','. $basket->subtotal .';';
					}
			} else
				$parseBasket = $it;
		}
	}

	public static function getWords(){
		return isset(self::$config['req_words'])?self::$config['req_words']:NULL;
	}

	public static function setRawWords(){
		// execution get raw words
		// notes : execution after set data
		self::_words(false);
		self::$config['req_words_raw'] = !empty(self::$raw_words)?self::$raw_words:NULL;
	}

	public static function getMallId(){
		return isset(self::$config['req_mall_id'])?self::$config['req_mall_id']:NULL;
	}

	public static function getConfig(){
		return self::$config;
	}

	public static function send(){
		if(empty(self::$method))
			self::_error('Method must define');

		if(!empty(self::$method) && empty(self::$error) && count(self::$config) > 2){
			$ch = curl_init( self::$url->{self::$method} );
			curl_setopt( $ch, CURLOPT_POST, 1);
			curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode(self::$config));
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt( $ch, CURLOPT_HEADER, 0);
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

			try {
				$resp = curl_exec( $ch );
			} catch (Exception $e) {
				$error = true;	
			}

			curl_close($ch);

			if(isset($resp)){
				if(is_string($resp)){
					$tmp = json_decode($resp);
				}else{
					$tmp = $resp;
				}
			}

			self::_resp(isset($error)?false:true, isset($error)?'Failed send data':'Success send data', isset($error)?NULL:$tmp);
		}
	}

	public static function response(){
		// check error before send response
		if(is_object(self::$error))
			self::_resp(false, self::$error->error_message);
		// check response is null
		if(is_null(self::$res))
			self::_resp(false, 'No response');
		return self::$json?json_encode(self::$res):self::$res;
	}

	public function getResponse(){
		self::response();
	}

	private static function _url(){
		self::$url = (object) array(
			'prePaymentUrl' => self::$stage?self::stagingPrePaymentUrl:self::livePrePaymentUrl,
			'paymentUrl' => self::$stage?self::stagingPaymentUrl:self::livePaymentUrl,
			'directPaymentUrl' => self::$stage?self::stagingDirectPaymentUrl:self::liveDirectPaymentUrl,
			'generateCodeUrl' => self::$stage?self::stagingGenerateCodeUrl:self::liveGenerateCodeUrl,
			'redirectPaymentUrl' => self::$stage?self::stagingRedirectPaymentUrl:self::liveRedirectPaymentUrl,
			'captureUrl' => self::$stage?self::stagingCaptureUrl:self::liveCaptureUrl
		);
	}

	private static function _words($sha=true){
		if($sha){
			if(!empty(self::$device_id)){
				if(!empty(self::$pairing_code))
					self::$config['req_words'] = sha1(self::$config['req_amount'] . self::$config['req_mall_id'] . self::$sharedkey . self::$config['req_trans_id_merchant'] . self::$config['req_currency'] . self::$config['req_token_id'] . self::$pairing_code . self::$device_id);
				else
					self::$config['req_words'] = sha1(self::$config['req_amount'] . self::$config['req_mall_id'] . self::$sharedkey . self::$config['req_trans_id_merchant'] . self::$config['req_currency'] . self::$device_id);
			}
			else if(!empty(self::$pairing_code))
				self::$config['req_words'] = sha1(self::$config['req_amount'] . self::$config['req_mall_id'] . self::$sharedkey . self::$config['req_trans_id_merchant'] . self::$config['req_currency'] . self::$config['req_token_id'] . self::$pairing_code);
			else if(!empty(self::$config['req_currency']))
				self::$config['req_words'] = sha1(self::$config['req_amount'] . self::$config['req_mall_id'] . self::$sharedkey . self::$config['req_trans_id_merchant'] . self::$config['req_currency']);
			else
				self::$config['req_words'] = sha1(self::$config['req_amount'] . self::$config['req_mall_id'] . self::$sharedkey . self::$config['req_trans_id_merchant']);
		} else {
			if(!empty(self::$device_id)){
				if(!empty(self::$pairing_code))
					self::$raw_words = self::$config['req_amount'] . self::$config['req_mall_id'] . self::$sharedkey . self::$config['req_trans_id_merchant'] . self::$config['req_currency'] . self::$config['req_token_id'] . self::$pairing_code . self::$device_id;
				else
					self::$raw_words = self::$config['req_amount'] . self::$config['req_mall_id'] . self::$sharedkey . self::$config['req_trans_id_merchant'] . self::$config['req_currency'] . self::$device_id;
			}
			else if(!empty(self::$pairing_code))
				self::$raw_words = self::$config['req_amount'] . self::$config['req_mall_id'] . self::$sharedkey . self::$config['req_trans_id_merchant'] . self::$config['req_currency'] . self::$config['req_token_id'] . self::$pairing_code;
			else if(!empty(self::$config['req_currency']))
				self::$raw_words = self::$config['req_amount'] . self::$config['req_mall_id'] . self::$sharedkey . self::$config['req_trans_id_merchant'] . self::$config['req_currency'];
			else
				self::$raw_words = self::$config['req_amount'] . self::$config['req_mall_id'] . self::$sharedkey . self::$config['req_trans_id_merchant'];
		}
	}

	private static function _error($m=null, $c=400){
		if(!is_null($m))
			self::$error = (object) array('error_code' => $c, 'error_message' => $m);
	}

	private static function _resp($s=true,$m=null,$d=null){
		if(!is_null($m)){
			self::$res = (object) array('status' => $s, 'message' => $m);
			if(!is_null($d))
				self::$res->{'data'} = $d;
		} else
			self::$res = (object) array('status' => false, 'message' => 'No response');
	}

	public static function debug($var){
		dd($var);
	}
}