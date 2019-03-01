<?php
/**
 * Initiate Doku data's
 */
class Doku_Initiate {
	 
	//local
	const prePaymentUrl = 'https://staging.doku.com/api/payment/PrePayment';
	const paymentUrl = 'https://staging.doku.com/api/payment/paymentMip';
	const directPaymentUrl = 'https://staging.doku.com/api/payment/PaymentMIPDirect';
	const generateCodeUrl = 'https://staging.doku.com/api/payment/DoGeneratePaycodeVA';
	const redirectPaymentUrl = 'https://staging.doku.com/api/payment/doInitiatePayment';
	const captureUrl = 'https://staging.doku.com/api/payment/DoCapture';

	public static $sharedKey; //doku's merchant unique key
	public static $mallId; //doku's merchant id

}
