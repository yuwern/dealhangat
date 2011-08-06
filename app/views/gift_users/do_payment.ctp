<?php /* SVN: $Id: do_payment.ctp 2877 2010-04-23 12:44:46Z senthilkumar_017ac09 $ */ ?>
<h2><?php echo __l('Purchase Gift Card');?></h2>
    <div class="wallet-amount-block">
        <?php echo __l('Amount: '); ?><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($amount)); ?>
    </div>
<?php 
	if($action == 'pagseguro'):
		$this->PagSeguro->form($gateway_options);
		$this->PagSeguro->data(); ?>
     <div class="submit-block clearfix">
        <?php
		$this->PagSeguro->submit($gateway_options);
		?>
		</div>
		<?php
	else:
		$this->Gateway->$action($gateway_options);
	endif;
?>
