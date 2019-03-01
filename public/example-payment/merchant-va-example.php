<?php
require_once('../Doku.php');

date_default_timezone_set('Asia/Jakarta');

//Doku_Initiate::$sharedKey = 'k8UhY5t4RF4e';
Doku_Initiate::$sharedKey = 'd9E2c5Fc6LSw';
Doku_Initiate::$mallId = '5019';

$params = array(
    'amount' => '100000.00', //$_POST['amount'],
    'invoice' => 'akupadamujuga1', // $_POST['trans_id'],
    'currency' => '360' // $_POST['currency']
);

$words = Doku_Library::doCreateWords($params);

$customer = array(
    'name' => 'TEST NAME',
    'data_phone' => '08121111111',
    'data_email' => 'test@test.com',
    'data_address' => 'bojong gede #1 08/01'
);

$dataPayment = array(
    'req_mall_id' => '5019', //$_POST['mall_id'],
    'req_chain_merchant' => 'NA', //$_POST['chain_merchant'],
    'req_amount' => $params['amount'],
    'req_words' => $words,
    'req_trans_id_merchant' => 'akupadamujuga1', // $_POST['trans_id'],
    'req_purchase_amount' => $params['amount'],
    'req_request_date_time' => date('YmdHis'),
    'req_session_id' => sha1(date('YmdHis')),
    'req_email' => $customer['data_email'],
    'req_name' => $customer['name']
);

$response = Doku_Api::doGeneratePaycode($dataPayment);

if($response->res_response_code == '0000'){
    echo 'GENERATE SUCCESS -- ';
}else{
    echo 'GENERATE FAILED -- ';
}

var_dump($response);

?>
