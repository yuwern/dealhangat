<?php /* SVN: $Id: commission_calculator.ctp 47780 2011-03-23 07:04:34Z lakshmi_150act10 $ */ ?>
<h2><?php echo __l('Budget Calculator'); ?></h2>
<div class="clearfix">
	<?php
		if(Configure::read('site.currency_symbol_place') == 'left'):
			$currecncy_place = 'between';
		else:
			$currecncy_place = 'after';
		endif;	
	?>
	<?php
	    echo $this->Form->input('budget_amt',array('label'=>__l('Discount Budget Amout'),'div' =>'input text budget-amount', $currecncy_place => Configure::read('site.currency')));
	    echo $this->Form->input('original_amt',array('class' => 'js-deal-original-price','label'=>__l('Original Price'),'div' =>'input text original-price', $currecncy_place => Configure::read('site.currency') ));
		echo $this->Form->input('discount_amt',array('class' => 'js-deal-discount','label'=>__l('Discount Price'),'div' =>'input text discount-price',$currecncy_place => Configure::read('site.currency')));
	?>
</div>
<dl class="result-list clearfix">
	<dt><?php echo __l('No of Max Coupons').':  '; ?></dt>
	<dd><span class="js-budget-calculator"><?php echo (!empty($this->request->data['Deal']['calculator_qty'])) ? $this->Html->cInt($this->request->data['Deal']['calculator_qty']) : 0; ?></span></dd>
</dl>