<?php 
require_once('../Doku.php');
date_default_timezone_set('Asia/Jakarta');
Doku_Initiate::$sharedKey = 'd9E2c5Fc6LSw';
Doku_Initiate::$mallId = '5019';

// Doku_Initiate::$sharedKey = <Put Your Shared Key Here>;
// Doku_Initiate::$mallId = <Put Your Merchant Code Here>

$params = array(
	'amount' => '10000.00', 
	'invoice' => 'tr001',  //$_POST['trans_id'], 
	'currency' =>  '360' //$_POST['currency']
);
$words = Doku_Library::doCreateWords($params);
$customer = array(
	'name' => 'TEST NAME',
	'data_phone' => '08121111111', 
	'data_email' => 'test@test.com', 
	'data_address' => 'bojong gede #1 08/01'
);
$dataPayment = array(
	'req_mall_id' =>  '5019', // $_POST['mall_id'], 
	'req_chain_merchant' =>  'NA',  //$_POST['chain_merchant'], 
	'req_amount' => $params['amount'], 
	'req_words' => $words, 
	'req_trans_id_merchant' => 'tr001', // $_POST['trans_id'], 
	'req_purchase_amount' => $params['amount'], 
	'req_request_date_time' => date('YmdHis'), 
	'req_session_id' => sha1(date('YmdHis')), 
	'req_email' => $customer['data_email'], 
	'req_name' => $customer['name'], 
	// 'req_basket' => 'sayur,10000.00,1,10000.00;', 
	'req_address' => 'Jl Kembang 1 No 5 Cilandak Jakarta', 
	'req_mobile_phone' => '081311529594', 
	'req_expiry_time' => '360'
);

$response = Doku_Api::doGeneratePaycode($dataPayment);
if($response->res_response_code == '0000'){
        echo 'GENERATE SUCCESS -- ';
}else{
        echo 'GENERATE FAILED -- ';
} 
var_dump($response);
?>
