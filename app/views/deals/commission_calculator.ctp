<?php /* SVN: $Id: commission_calculator.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<h2><?php echo __l('Commission Calculator'); ?></h2>
<?php
	if(empty($this->request->data['Deal']['user_id'])):
		//echo $this->Form->create('Deal', array('action'=> 'commission_calculator', 'class' => 'normal'));
	endif;
?>
<div class="clearfix">
	<?php
		if(Configure::read('site.currency_symbol_place') == 'left'):
			$currecncy_place = 'between';
		else:
			$currecncy_place = 'after';
		endif;	
	?>
	<?php
		echo $this->Form->input('calculator_discounted_price',array('label'=>__l('Discounted Price'), $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>'));
		echo $this->Form->input('calculator_bonus_amount', array('label'=> __l('Bonus Amount'), 'value' => '0.00'));
	?>
</div>
<div class="clearfix">
	<?php
		echo $this->Form->input('calculator_commission_percentage', array('label'=>__l('Commission (%)')));
		echo $this->Form->input('calculator_min_limit', array('label'=>__l('No of Buyers')));
	?>
</div>
<?php
	if(empty($this->request->data['Deal']['user_id'])):
		//echo $this->Form->end(__l('Calculate'));
	endif;
?>
<dl class="result-list clearfix">
	<dt><?php echo __l('Total Purchased Amount: '); ?></dt>
	<dd><span class="js-calculator-purchased"><?php echo $this->Html->siteCurrencyFormat((!empty($this->request->data['Deal']['calculator_total_purchased_amount'])) ? $this->request->data['Deal']['calculator_total_purchased_amount'] : 0); ?></span></dd>
	<dt><?php echo __l('Total Commission Amount: '); ?></dt>
	<dd><span class="js-calculator-commission"><?php echo $this->Html->siteCurrencyFormat((!empty($this->request->data['Deal']['calculator_total_commission_amount'])) ? $this->request->data['Deal']['calculator_total_commission_amount'] : 0); ?></span></dd>
	<?php if($this->Auth->user('user_type_id') == ConstUserTypes::Admin):?>
		<dt><?php echo __l('Net Profit: '); ?></dt>
		<dd><span class="js-calculator-net-profit"><?php echo $this->Html->siteCurrencyFormat((!empty($this->request->data['Deal']['calculator_net_profit'])) ? $this->request->data['Deal']['calculator_net_profit'] : 0); ?></span></dd>
	<?php endif; ?>
</dl>