<?php
require_once('../Doku.php');

Doku_Initiate::$sharedKey = 'eaM6i1JjS19J';
//Doku_Initiate::$sharedKey = 'aKh4dSX72d6C';
//Doku_Initiate::$sharedKey = 'k8UhY5t4RF4e';
Doku_Initiate::$mallId = $_POST['doku-mall-id'];

$params = array(
    'amount' => $_POST['doku-amount'],
    'invoice' => $_POST['doku-invoice-no'],
    'currency' => $_POST['doku-currency'],
    'pairing_code' => $_POST['doku-pairing-code'],
    'token' => $_POST['doku-token']
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

$data = array(
    'req_token_id' => $_POST['doku-token'],
    'req_pairing_code' => $_POST['doku-pairing-code'],
    'req_bin_filter' => array("411111", "548117", "433???6", "41*3"),
    'req_customer' => $customer,
    'req_basket' => $basket,
    'req_words' => $words
);

$responsePrePayment = Doku_Api::doPrePayment($data);

if($responsePrePayment->res_response_code == '0000'){ //prepayment success

    echo "PREPAYMENT SUCCESS -- ";

    $dataPayment = array(
        'req_mall_id' => $_POST['doku-mall-id'],
        'req_chain_merchant' => $_POST['doku-chain-merchant'],
        'req_amount' => $_POST['doku-amount'],
        'req_words' => $words,
        'req_purchase_amount' => $_POST['doku-amount'],
        'req_trans_id_merchant' => $_POST['doku-invoice-no'],
        'req_request_date_time' => date('YmdHis'),
        'req_currency' => $_POST['doku-currency'],
        'req_purchase_currency' => $_POST['doku-currency'],
        'req_session_id' => sha1(date('YmdHis')),
        'req_name' => $customer['name'],
        'req_payment_channel' => 15,
        'req_basket' => $basket,
        'req_email' => $customer['data_email'],
        'req_token_id' => $_POST['doku-token'],
        'req_mobile_phone' => $customer['data_phone'],
        'req_address' => $customer['data_address'],
        'req_payment_type' => 'A'

    );

    $result = Doku_Api::doPayment($dataPayment);

    if($result->res_response_code == '0000'){
        echo 'PAYMENT SUCCESS -- ';
        //success

        //process tokenization
        if(isset($result->res_bundle_token)) {
            $tokenPayment = json_decode($result->res_bundle_token);

            if ($tokenPayment->res_token_code == '0000') {
                //save token
                $getTokenPayment = $tokenPayment->res_token_payment;
                echo PHP_EOL . 'SUCCESS SAVE TOKEN PAYMENT --';
            }
        }

    }else{
        echo 'PAYMENT FAILED';
        //failed
    }

    var_dump($result);

}else{
    echo 'PREPAYMENT FAILED --';
    var_dump($responsePrePayment);

    //prepayment fail
}
?>