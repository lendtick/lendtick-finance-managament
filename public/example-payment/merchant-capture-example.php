<?php
require_once('../Doku.php');

date_default_timezone_set('Asia/Jakarta');

//Doku_Initiate::$sharedKey = 'k8UhY5t4RF4e';
Doku_Initiate::$sharedKey = 'eaM6i1JjS19J';
Doku_Initiate::$mallId = $_POST['mall_id'];

$params = array('invoice' => $_POST['trans_id'], 'session_id'=>$_POST['session_id']);

$words = Doku_Library::doCreateWordsCapture($params);

$dataPayment = array(
    'req_mall_id' => $_POST['mall_id'],
    'req_chain_merchant' => $_POST['chain_merchant'],
    'req_trans_id_merchant' => $_POST['trans_id'],
    'req_payment_channel' => 15,
    'req_approval_code' => $_POST['approval_code'],
    'req_session_id' => $_POST['session_id'],
    'req_words' => $words
);

$response = Doku_Api::doCapture($dataPayment);

if($response->res_response_code == '0000'){
    echo 'CAPTURE SUCCESS -- ';
}else{
    echo 'CAPTURE FAILED -- ';
}

var_dump($response);

?>
