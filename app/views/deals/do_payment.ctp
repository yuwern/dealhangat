<?php /* SVN: $Id: do_payment.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ 


?>
<h2><?php echo sprintf(__l('Buy %s Deal'),$deal['Deal']['name']);?></h2>
    <div class="wallet-amount-block">
		<?php echo __l('Amount: '); ?><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($amount)); ?>
    </div>

<?php
	if($action == 'pagseguro'):
		$this->PagSeguro->form($gateway_options);
		$this->PagSeguro->data();
?>
	<div class="submit-block clearfix">
		<?php $this->PagSeguro->submit($gateway_options); ?>
	</div>
<?php
        elseif ($action == 'creditcard'): 
?>

<HTML> 
<BODY> 
<FORM method="post" name="ePayment" action="https://www.mobile88.com/ePayment/entry.asp">
<INPUT type="hidden"name="MerchantCode" value="M03648">  
<INPUT type="hidden" name="PaymentId" value="2"> 
<INPUT type="hidden" name="RefNo" value="<?php echo $gateway_options['refno'] ?>">  
<INPUT type="hidden" name="Amount" value="<?php echo number_format($gateway_options['amount'],2) ?>"> 
<INPUT type="hidden" name="Currency" value="<?php echo $gateway_options['currency_code']?>"> 
<INPUT type="hidden" name="ProdDesc" value="<?php echo $gateway_options['item_name'] ?>">  
<INPUT type="hidden" name="UserName" value="<?php echo $gateway_options['user_defined']['username'] ?>">
<INPUT type="hidden" name="UserEmail" value="<?php echo $gateway_options['user_defined']['email'] ?>"> 
<INPUT type="hidden" name="UserContact" value="">
<INPUT type="hidden" name="Remark"  value="">
<INPUT type="hidden" name="Lang" value="UTF-8">
<INPUT type="hidden" name="Signature"  value="<?php echo $gateway_options['request_signature'] ?>">
<INPUT type="hidden" name="ResponseURL" value="http://www.dealhangat.com/deals/processpayment/creditcard"> 
<INPUT type="submit" value="Proceed with Payment" name="Submit">
</FORM>
</BODY> 
</HTML>	     
	        
<?php	        
	
	else:   

		print_r($this->Gateway->$action($gateway_options));
	endif;
?>