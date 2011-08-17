<?php
class ipay88Component extends Component
{
    
    const merchantKey = '2fOUVQq4Fm';
    const merchantCode = 'M03648';
    
    function iPay88_signature($source) {
        return base64_encode($this->hex2bin(sha1($source)));
    }

    function hex2bin($hexSource) {

        for ($i=0;$i<strlen($hexSource);$i=$i+2) {
            $bin .= chr(hexdec(substr($hexSource,$i,2))); 
        }
        return $bin;
    }

    function iPay88hash($gateway_options){
        $string = self::merchantKey.self::merchantCode.$gateway_options['refno'].$gateway_options['amount'].'00'.$gateway_options['currency_code'];
        return iPay88_signature($string);
    }
    
    function validateResponseSignature($request)
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
		$strHash = $this->iPay88_signature($strToHash);
		
		if($eStatus == '1' && $strHash ==  $ipaySignature)
		{
		    return 1;
		}
		else{
		    return 0;   
		}
    }
    
}
?>