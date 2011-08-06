<?php /* SVN: $Id: $ */ ?>
<div class="currencies form">
	<h2><?php echo __l('Edit Currency');?></h2>
<?php echo $this->Form->create('Currency', array('class' => 'normal'));?>
	<fieldset>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name', array('label' => __l('Name')));
		echo $this->Form->input('code', array('label' => __l('Code')));
		echo $this->Form->input('symbol', array('label' => __l('Symbol')));
		echo $this->Form->input('prefix', array('label' => __l('Prefix')));
		echo $this->Form->input('suffix', array('label' => __l('Suffix')));
		echo $this->Form->input('decimals', array('label' => __l('Decimals')));
		echo $this->Form->input('dec_point', array('label' => __l('Decimal Point')));
		echo $this->Form->input('thousands_sep', array('label' => __l('Thousand Separate')));
		echo $this->Form->input('locale', array('label' => __l('Local')));
		echo $this->Form->input('format_string', array('label' => __l('Format String')));
		echo $this->Form->input('grouping_algorithm_callback', array('label' => __l('Grouping Algorithm Callback')));
		echo $this->Form->input('is_use_graphic_symbol',array('label' =>__l('User graphic symbol?')));
		$_currencies = Cache::read('site_currencies');
		$selected_currency = $_currencies[Configure::read('site.currency_id')];
		$c_selected_currency = $_currencies[Configure::read('site.paypal_currency_converted_id')];
		if(($selected_currency['Currency']['id'] != $this->request->data['Currency']['id']) && ($c_selected_currency['Currency']['id'] != $this->request->data['Currency']['id'])):
			echo $this->Form->input('is_enabled',array('label' =>__l('Enabled?')));
			echo $this->Form->input('is_paypal_supported');		
		endif;
	?>
	<?php if(empty($this->request->data['Currency']['is_paypal_supported'])): ?>
		<div class="overflow-block">
         <table class="list">
			<tr>     	
				<th><?php echo __l('Conversion');?></th>
				<th><?php echo __l('Rate');?></th>
			</tr>
			
			<?php $i=0; foreach($currencies as $currency) : ?>
				<tr>
					<td><?php echo $this->Form->input('CurrencyConversion.'.$i.'.converted_currency_id', array('type' => 'hidden', 'value' => $currency['converted_currency_id']));
					echo $this->Form->input('CurrencyConversion.'.$i.'.id', array('type' => 'hidden', 'value' => $currency['id'])); ?>
					<?php echo $this->request->data['Currency']['code'],' -> ', $currency['code']; ?></td>
					<td><?php echo $this->Form->input('CurrencyConversion.'.$i.'.rate', array('label' => false, 'value' => $currency['rate'])); ?></td>
				</tr>
			<?php $i++; endforeach; ?>						
		</table>
		</div>
	<?php endif; ?>
	</fieldset>
    <div class="submit-block clearfix">
<?php echo $this->Form->end(__l('Update'));?>
	</div>
</div>
