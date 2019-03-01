<?php
require_once('../Doku.php');

Doku_Initiate::$sharedKey = 'eaM6i1JjS19J';
//Doku_Initiate::$sharedKey = 'k8UhY5t4RF4e';
Doku_Initiate::$mallId = $_POST['doku_mall_id'];

$params = array(
	'amount' => $_POST['doku_amount'],
	'invoice' => $_POST['doku_invoice_no'],
	'currency' => $_POST['doku_currency'],
	'pairing_code' => $_POST['doku_pairing_code'],
	'token' => $_POST['doku_token']
	);

$words = Doku_Library::doCreateWords($params);

$basket[] = array(
	'name' => 'sayur',
	'amount' => '10000.00',
	'quantity' => '1',
	'subtotal' => '10000.00'
);

$basket[] = array(
	'name' => 'buah',
	'amount' => '10000.00',
	'quantity' => '1',
	'subtotal' => '10000.00'
);

$customer = array(
	'name' => 'TEST NAME',
	'data_phone' => '08121111111',
	'data_email' => 'test@test.com',
	'data_address' => 'bojong gede #1 08/01'
	);

$dataPayment = array(
	'req_mall_id' => $_POST['doku_mall_id'],
	'req_chain_merchant' => $_POST['doku_chain_merchant'],
	'req_amount' => $_POST['doku_amount'],
	'req_words' => $words,
	'req_words_raw' => Doku_Library::doCreateWordsRaw($params),
	'req_purchase_amount' => $_POST['doku_amount'],
	'req_trans_id_merchant' => $_POST['doku_invoice_no'],
	'req_request_date_time' => date('YmdHis'),
	'req_currency' => $_POST['doku_currency'],
	'req_purchase_currency' => $_POST['doku_currency'],
	'req_session_id' => sha1(date('YmdHis')),
	'req_basket' => $basket,
	'req_name' => $customer['name'],
	'req_payment_channel' => "04",
	'req_email' => $customer['data_email'],
	'req_token_id' => $_POST['doku_token'],
	'req_mobile_phone' => $customer['data_phone'],
	'req_address' => $customer['data_address']
);

$responsePayment = Doku_Api::doPayment($dataPayment);

if($responsePayment->res_response_code == '0000'){

	//merchant process
	//do what you want to do

	//redirect process to doku
	$responsePayment->res_redirect_url = '../example-payment/merchant-redirect-example.php';
	$responsePayment->res_show_doku_page = true; //true if you want to show doku page first before redirecting to redirect url

	echo json_encode($responsePayment);

}else{

	echo json_encode($responsePayment);

}

?>
