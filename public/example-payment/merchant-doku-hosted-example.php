<?php
require_once('../Doku.php');

date_default_timezone_set('Asia/Jakarta');

Doku_Initiate::$sharedKey = 'eaM6i1JjS19J';
//Doku_Initiate::$sharedKey = 'k8UhY5t4RF4e';
Doku_Initiate::$mallId = $_POST['mall_id'];

$params = array(
    'amount' => $_POST['amount'],
    'invoice' => $_POST['trans_id'],
    'currency' => $_POST['currency']
);

$words = Doku_Library::doCreateWords($params);

$customer = array(
    'name' => 'TEST NAME',
    'data_phone' => '08121111111',
    'data_email' => 'test@test.com',
    'data_address' => 'bojong gede #1 08/01'
);

$dataPayment = array(
    'req_merchant_code' => $_POST['mall_id'],
    'req_chain_merchant' => $_POST['chain_merchant'],
    'req_amount' => $params['amount'],
    'req_words' => $words,
    'req_trans_id_merchant' => $_POST['trans_id'],
    'req_purchase_amount' => $params['amount'],
    'req_request_date_time' => date('YmdHis'),
    'req_session_id' => sha1(date('YmdHis')),
    'req_email' => $customer['data_email'],
    'req_name' => $customer['name'],
    'req_address'=>'TEsting',
    'req_mobile_phone'=>'081987987899',
    'req_basket'=>'productname,'.$params['amount'].',1,'.$params['amount'].';',
    'req_payment_channel' => isset($_POST['hosted_pc']) ? $_POST['hosted_pc'] : 15
);

$response = Doku_Api::doRedirectPayment($dataPayment);

if($response->res_response_code == '0000'){
    echo 'GENERATE SUCCESS -- ';
    var_dump(json_encode($response));
    echo '-- Redirecting in 5 seconds --';
?>
<body onload="doSubmit();">
<form name="formRedirect" id="formRedirect" action="<?php echo $response->res_url_redirect_payment?>"></form>
</body>
<script type="text/javascript">
    setTimeout(function () {
        window.location.href= '--><?php //echo $response->res_url_redirect_payment?>';
    },5000);
</script>
<?php
}else{
    echo 'GENERATE FAILED -- ';
    var_dump($response);
}

?>

