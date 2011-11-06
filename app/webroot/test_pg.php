<?PHP 

if($_GET['done'])
{
    print "In done!!!";
    print_r($_REQUEST);    
}


function iPay88_signature($source) {
    return base64_encode(hex2bin(sha1($source)));
}

function hex2bin($hexSource) {

    for ($i=0;$i<strlen($hexSource);$i=$i+2) {
        $bin .= chr(hexdec(substr($hexSource,$i,2))); 
    }
    return $bin;
}
?>

<HTML> 
<BODY> 
<FORM method="post" name="ePayment" action="https://www.mobile88.com/ePayment/entry.asp">
<INPUT type="hidden"name="MerchantCode" value="M03648">  
<INPUT type="hidden" name="PaymentId" value="2"> 
<INPUT type="hidden" name="RefNo" value="A00000001">  
<INPUT type="hidden" name="Amount" value="1.00"> 
<INPUT type="hidden" name="Currency" value="MYR"> 
<INPUT type="hidden" name="ProdDesc" value="Photo Print">  
<INPUT type="hidden" name="UserName" value="John Tan">
<INPUT type="hidden" name="UserEmail" value="john@hotmail.com"> 
<INPUT type="hidden" name="UserContact" value="0126500100">
<INPUT type="hidden" name="Remark"  value="">
<INPUT type="hidden" name="Lang" value="UTF-8">
<INPUT type="hidden" name="Signature"  value="EAYzYJGcrsuBbfpKSKWVyPRCnyw=">
<INPUT type="hidden" name="ResponseURL" value="http://www.dealhangat.com/test_pg.php?done=1"> 
<INPUT type="submit" value="Proceed with Payment" name="Submit">
</FORM>
</BODY> 
</HTML>

