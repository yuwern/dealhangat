<?php
class ipay88Component extends Component
{
    
    const merchantKey = '2fOUVQq4Fm';
    const merchantCode = 'M03648';
    
    public $components = array(
        'RequestHandler'
    );
    
    function iPay88_signature($source) {
        return base64_encode($this->hex2bin(sha1($source)));
    }

    function hex2bin($hexSource) {

        for ($i=0;$i<strlen($hexSource);$i=$i+2) {
            $bin .= chr(hexdec(substr($hexSource,$i,2))); 
        }
        return $bin;
    }

    function generateRequestSignature($gateway_options){
        
        //must add stupid handling for amount because stupid code sometimes returns amounts without any decimals
        $amount = number_format($gateway_options['amount'],2);
        $conv_amount = str_replace(".","",str_replace(",","",$amount));
        $string = self::merchantKey.self::merchantCode.$gateway_options['refno'].$conv_amount.$gateway_options['currency_code'];
        return $this->iPay88_signature($string);
    }
    
    function generateResponseSignature($request)
    {
        $MerchantKey = self::merchantKey;	// Change MerchantKey here
        $MerchantCode = $request["MerchantCode"];
        $PaymentId = $request["PaymentId"];
        $RefNo = $request["RefNo"];
        $Amount = $request["Amount"];
        $eCurrency = $request["Currency"];
        $Remark = $request["Remark"];
        $TransId = $request["TransId"];
        $AuthCode = $request["AuthCode"];
        $eStatus = $request["Status"];
        $ErrDesc = $request["ErrDesc"];
        $ipaySignature = $request["Signature"];
        
        
        $conv_amount = str_replace(".","",str_replace(",","",$Amount));
		
		// Concatenate the variable and assign it to $strToHash
		$strToHash = $MerchantKey . $MerchantCode . $PaymentId . $RefNo . $conv_amount . $eCurrency . $eStatus;
	
		// hash $strToHash with iPay88_signature function and assign it to $strHash
		return $this->iPay88_signature($strToHash);   
        
    }
    
    function validateResponseSignature($request)
    {
        /*$MerchantKey = self::merchantKey;	// Change MerchantKey here
        $MerchantCode = $request["MerchantCode"];
        $PaymentId = $request["PaymentId"];
        $RefNo = $request["RefNo"];
        $Amount = $request["Amount"];
        $eCurrency = $request["Currency"];
        $Remark = $request["Remark"];
        $TransId = $request["TransId"];
        $AuthCode = $request["AuthCode"];
        $eStatus = $request["Status"];
        $ErrDesc = $request["ErrDesc"];
        $ipaySignature = $request["Signature"];
        
        
        $conv_amount = str_replace(".","",str_replace(",","",$Amount));
		
		// Concatenate the variable and assign it to $strToHash
		$strToHash = $MerchantKey . $MerchantCode . $PaymentId . $RefNo . $conv_amount . $eCurrency . $eStatus;
	
		// hash $strToHash with iPay88_signature function and assign it to $strHash
		$strHash = $this->iPay88_signature($strToHash);*/
		
		if($request["Status"] == '1' && $this->generateResponseSignature($request) ==  $request["Signature"])
		{
		    return 1;
		}
		else{
		    return 0;   
		}
    }
    
    public function logTransaction($request, $transaction_data)
    {
        //MOHAN
        //We are going to reuse the paypaltransaction logs cause the 
        //stupid pagseguro log tables dont seem to exist in the db
        //I really don't know how the fuck the guys wrote this shit app
        //Stuff we need to log are as follows - 
        /*
        $request["MerchantCode"];
        $request["PaymentId"];
        -$request["RefNo"];
        -$request["Amount"];
        -$request["Currency"];
        $request["Remark"];
        -$request["TransId"];
        -$request["AuthCode"];
        -$request["Status"];
        $request["ErrDesc"];
        $request["Signature"];*/
        //TODO - Add logging for the signature verification status
        
        $status = $this->validateResponseSignature($request);
        $generatedSignature = $this->generateResponseSignature($request);
        if($generatedSignature !=  $request["Signature"])
        {
            $request["ErrDesc"] .= " Signature passed in '".$request["Signature"]."' does not match generated signature '$generatedSignature' ";
        }
        
        $ori_request_str = '';
        $ori_request = (!empty($_REQUEST)) ? $_REQUEST : array();
        foreach($ori_request as $key => $value) {
            $value = urlencode(stripslashes($value));
             $ori_request_str.= "&$key=$value";
        }
        
        $paypalTransactionModel = ClassRegistry::init('PaypalTransactionLog');
        $data['PaypalTransactionLog']['paypal_response'] = $request['AuthCode'];
        $data['PaypalTransactionLog']['payment_status'] = $status;
        
        //we do the following so the the paypal transaction log page lists CC payments that are successful as complete
        if($status == 1)
        {
            $data['PaypalTransactionLog']['capture_ack'] = "1";    
        }
        
        $data['PaypalTransactionLog']['user_id'] = $transaction_data['user_id'];
        //$data['PaypalTransactionLog']['error_no'] = $this->errno;
        $data['PaypalTransactionLog']['txn_id'] = $request["TransId"];
        
        //our ref when sending to ipay88
        $data['PaypalTransactionLog']['authorization_auth_id'] = $request["RefNo"];

        $data['PaypalTransactionLog']['error_message'] = $request["ErrDesc"];
        $data['PaypalTransactionLog']['paypal_post_vars'] = $ori_request_str;
        $data['PaypalTransactionLog']['ip'] = $this->RequestHandler->getClientIP();
        $data['PaypalTransactionLog']['mc_gross'] = $request["Amount"];
        $data['PaypalTransactionLog']['mc_currency'] = $request["Currency"];
        $paypalTransactionModel->save($data);
        return $paypalTransactionModel->getLastInsertId();
    }
    
}
?>