<?php

/**
 * This class provides a utility sign and encrypt function of a string using PKCS7
 */
class PPCrypto
{
	/**
	 * Sign and Envelope the passed data string, returning a PKCS7 blob that can be posted to PayPal.
	 * Make sure the passed data string is seperated by UNIX linefeeds (ASCII 10, '\n').
	 *
	 * @param	string	The candidate for signature and encryption
	 * @param	string	The file path to the EWP(merchant) certificate
	 * @param	string	The file path to the EWP(merchant) private key
	 * @param	string	The EWP(merchant) private key password
	 * @param	string	The file path to the PayPal Certificate
	 * @return	array	Contains a bool status, error_msg, error_no, and an encrypted string: encryptedData if successfull
	 *
	 * @access	public
	 * @static
	 */
	function signAndEncrypt($dataStr_, $ewpCertPath_, $ewpPrivateKeyPath_, $ewpPrivateKeyPwd_, $paypalCertPath_)
	{
		$dataStrFile  = realpath(tempnam('/tmp', 'pp_'));
        $fd = fopen($dataStrFile, 'w');
		if(!$fd) {
			$error = "Could not open temporary file $dataStrFile.";
			return array("status" => false, "error_msg" => $error, "error_no" => 0);
		}
		fwrite($fd, $dataStr_);
		fclose($fd);

		$signedDataFile = realpath(tempnam('/tmp', 'pp_'));
		if(!@openssl_pkcs7_sign(	$dataStrFile,
									$signedDataFile,
									"file://$ewpCertPath_",
									array("file://$ewpPrivateKeyPath_", $ewpPrivateKeyPwd_),
									array(),
									PKCS7_BINARY)) {
			unlink($dataStrFile);
			unlink($signedDataFile);
			$error = "Could not sign data: ".openssl_error_string();
			return array("status" => false, "error_msg" => $error, "error_no" => 0);
		}

		unlink($dataStrFile);

		$signedData = file_get_contents($signedDataFile);
		$signedDataArray = explode("\n\n", $signedData);
		$signedData = $signedDataArray[1];
		$signedData = base64_decode($signedData);

		unlink($signedDataFile);

		$decodedSignedDataFile = realpath(tempnam('/tmp', 'pp_'));
		$fd = fopen($decodedSignedDataFile, 'w');
		if(!$fd) {
			$error = "Could not open temporary file $decodedSignedDataFile.";
			return array("status" => false, "error_msg" => $error, "error_no" => 0);
		}
		fwrite($fd, $signedData);
		fclose($fd);

		$encryptedDataFile = realpath(tempnam('/tmp', 'pp_'));
		if(!@openssl_pkcs7_encrypt(	$decodedSignedDataFile,
									$encryptedDataFile,
									file_get_contents($paypalCertPath_),
									array(),
									PKCS7_BINARY)) {
			unlink($decodedSignedDataFile);
			unlink($encryptedDataFile);
			$error = "Could not encrypt data: ".openssl_error_string();
			return array("status" => false, "error_msg" => $error, "error_no" => 0);
		}

		unlink($decodedSignedDataFile);

		$encryptedData = file_get_contents($encryptedDataFile);
		if(!$encryptedData) {
			$error = "Encryption and signature of data failed.";
			return array("status" => false, "error_msg" => $error, "error_no" => 0);
		}

		unlink($encryptedDataFile);

		$encryptedDataArray = explode("\n\n", $encryptedData);
		$encryptedData = trim(str_replace("\n", '', $encryptedDataArray[1]));

        return array("status" => true, "encryptedData" => $encryptedData);
	} // signAndEncrypt
} // PPCrypto

class paypal extends paybase 
{
    function __construct()
    {
        $this->pay_name = _l("PayPal");
        $this->pay_id = "paypal";
        $this->img_url = "img/paypal.png";

        $cert_path = SITE_ROOT . "resource/cert/paypal/";
        if (DEBUG_PAY) {
	        $this->ewpCertPath = $cert_path . "sandbox_pubcert.pem";
	        $this->ewpPrivateKeyPath = $cert_path . "sandbox_prvkey.pem";
	        $this->paypalCertPath = $cert_path . "sandbox_paypal_cert_pem.txt";
					$this->envURL = "https://www.sandbox.paypal.com";
        }
        else {
	        $this->ewpCertPath = $cert_path . "pubcert.pem";
	        $this->ewpPrivateKeyPath = $cert_path . "prvkey.pem";
	        $this->paypalCertPath = $cert_path . "paypal_cert_pem.txt";
					$this->envURL = "https://www.paypal.com";
        }
		$this->caCert = $cert_path . "cacert.pem";
  }
	
	public function getApiContext() {
		if (DEBUG_PAY) {
			$clientId = PAYPAL_SANDBOX_CLIENT_ID;
			$clientSecret = PAYPAL_SANDBOX_CLIENT_SECRET;
		} else {
			$clientId = PAYPAL_CLIENT_ID;
			$clientSecret = PAYPAL_CLIENT_SECRET;
		}
		
		$apiContext = new \PayPal\Rest\ApiContext(
			new \PayPal\Auth\OAuthTokenCredential(
				 $clientId,
				 $clientSecret
		 ));
		if (DEBUG_PAY) {
			
		} else {
			$apiContext->setConfig(array('mode' => 'live'));
		}
		return $apiContext;
	}

	function get_button($order_id, $amount, $front_url, $back_url, $pay_time)
    {
		$img_url = $this->img_url;
		$item = new \PayPal\Api\Item(); 
		$item->setName('Interview')
			->setCurrency('USD')
			->setMarkcustity(1)
			->setSku(htmlspecialchars($order_id)) 
			->setPrice($amount);
		$itemList = new \PayPal\Api\ItemList();
		$itemList->setItems(array($item));
		
 		$payer = new \PayPal\Api\Payer();
 		$payer->setPaymentMethod('paypal');
 		$amountObj = new \PayPal\Api\Amount();
 		$amountObj->setTotal($amount);
 		$amountObj->setCurrency('USD');
		

 		$transaction = new \PayPal\Api\Transaction();
 		$transaction->setAmount($amountObj)
			->setItemList($itemList);
		
		$successBackUrl = urlencode($front_url);
		$cancelBackUrl = urlencode(_abs_url("interview"));
		$redirectBaseUrl = "$back_url?successBackUrl=$successBackUrl&cancelBackUrl=$cancelBackUrl";//?backurl=".urlencode($front_url));
		$returnUrl = "$front_url?success=true";
		$cancelUrl = _abs_url("interview")."?success=false";
		$redirectUrls = new \PayPal\Api\RedirectUrls();
		$redirectUrls->setReturnUrl($returnUrl)
				->setCancelUrl($cancelUrl);

		$payment = new \PayPal\Api\Payment();
		$payment->setIntent('sale')
				->setPayer($payer)
				->setTransactions(array($transaction))
				->setRedirectUrls($redirectUrls);
		
		try {
			$payment->create($this->getApiContext());
			$approvalLink = $payment->getApprovalLink();
		} catch (\PayPal\Exception\PayPalConnectionException $ex) {
			echo $ex->getData();
		}
		
		/**
		 * Build and return HTML string
		 */
		$encrypteddata = "-----BEGIN PKCS7-----". ["encryptedData"]."-----END PKCS7-----";
//		$button = <<<PPHTML
//<button type="submit" class="btn-pay" onclick="window.location.href='$approvalLink'"><img src="${img_url}"></button>
//PPHTML;
		$button = <<<PPHTML
<button type="submit" class="btn-pay" onclick="onPaypalSubmit('$approvalLink')"><img src="${img_url}"></button>
PPHTML;
        return $button;
    }

	function get_queryid($order_id)
	{
		$data = array('error_code'=>'0', 'message'=>_l(""), 'query_id'=>$order_id);
		return $data;
	}
	function respond($interview_id)
	{
		$successBackUrl = $_GET['successBackUrl'];
		$cancelBackUrl = $_GET['cancelBackUrl'];
		$backUrl = $cancelBackUrl;
		$success = false;
		
		if (isset($_GET['success']) && $_GET['success'] == 'true') {
			$apiContext = $this->getApiContext();
			$paymentId = $_GET['paymentId'];
			$payment = \PayPal\Api\Payment::get($paymentId, $apiContext);

			$execution = new \PayPal\Api\PaymentExecution();
			$execution->setPayerId($_GET['PayerID']);
			try {
				$result = $payment->execute($execution, $apiContext);
				
				$transactions = $payment->getTransactions();
				$relatedResources = $transactions[0]->getRelatedResources();
				$sale = $relatedResources[0]->getSale();
				$saleId = $sale->getId();
				
				$backUrl = "$successBackUrl?paymentId=$saleId";
				$success = true;
			} catch (Exception $ex) {
				ResultPrinter::printError("Executed Payment", "Payment", null, null, $ex);
			}
		}
		
		return $saleId;
	}

	function refund($order_id, $query_id, $amount, $back_url, $payment_id) {
		$amt = new \PayPal\Api\Amount();
		$amt->setCurrency('USD')
			->setTotal($amount);
		$refundRequest = new \PayPal\Api\RefundRequest(); 
		$refundRequest->setAmount($amt);
		$sale = new \PayPal\Api\Sale();
		$sale->setId($payment_id);
		
		try {
			$refundedSale = $sale->refundSale($refundRequest, $this->getApiContext());
			return true;
		} catch (Exception $ex) {
			ResultPrinter::printError("Executed Payment", "Payment", null, null, $ex);
		}
		
		return false;
	}
    /**
	 * Send HTTP POST Request
	 *
	 * @param	string	The request URL
	 * @param	string	The POST Message fields in &name=value pair format
	 * @param	bool		determines whether to return a parsed array (true) or a raw array (false)
	 * @return	array		Contains a bool status, error_msg, error_no,
	 *				and the HTTP Response body(parsed=httpParsedResponseAr  or non-parsed=httpResponse) if successful
	 *
	 * @access	public
	 * @static
	 */
	private function PPHttpPost($url_, $postFields_, $parsed_)
	{
		//setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url_);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);

		//setting the nvpreq as POST FIELD to curl
		curl_setopt($ch,CURLOPT_POSTFIELDS,$postFields_);

		//getting response from server
		$httpResponse = curl_exec($ch);

	    if (curl_errno($ch) == 60) {
	        curl_setopt($ch, CURLOPT_CAINFO, $this->caCert);
	        $httpResponse = curl_exec($ch);
		}

		if(!$httpResponse) {
			return array("status" => false, "error_msg" => curl_error($ch), "error_no" => curl_errno($ch));
		}

		if(!$parsed_) {
			return array("status" => true, "httpResponse" => $httpResponse);
		}

		$httpResponseAr = explode("\n", $httpResponse);

		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if(sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}

		if(0 == sizeof($httpParsedResponseAr)) {
			$error = "Invalid HTTP Response for POST request($postFields_) to $url_.";
			return array("status" => false, "error_msg" => $error, "error_no" => 0);
		}
		return array("status" => true, "httpParsedResponseAr" => $httpParsedResponseAr);

	} // PPHttpPost
}