<?php /* SVN: $Id: $ */ ?>
<div class="currencies index">
<h2><?php echo $this->pageTitle;?></h2>

 <div class="add-block">
	<?php echo $this->Html->link(__l('Add'),array('controller'=>'currencies','action'=>'add'),array('title' => __l('Add New Currency'), 'class' => 'admin-add'));?>
    <?php echo $this->Html->link(__l('Manual Currency Rate Update'), array('controller' => 'currencies', 'action' => 'update_status'), array('class' => 'update-status', 'title' => __l('You can use this to update currency rate.This will be used in the scenario where cron is not working')));?>
	<?php echo $this->element('paging_counter');?>
</div>
<div class="overflow-block">
<table class="list">
    <tr>     	
        <th><?php echo __l('Action');?></th>
		<th><?php echo $this->Paginator->sort(__l('Name'), 'name');?></th>
        <th><?php echo $this->Paginator->sort(__l('Code'), 'code');?></th>
        <th><?php echo $this->Paginator->sort(__l('Symbol'), 'symbol');?></th>
		<th><?php echo $this->Paginator->sort(__l('Added On'),'created');?></th>
		<th><?php echo $this->Paginator->sort(__l('Prefix'), 'prefix');?></th>
		<th><?php echo $this->Paginator->sort(__l('Suffix'), 'suffix');?></th>
		<th><?php echo $this->Paginator->sort(__l('Decimals'), 'decimals');?></th>
		<th><?php echo $this->Paginator->sort(__l('Dec Point'), 'dec_point');?></th>
		<th><?php echo $this->Paginator->sort(__l('Thousands Sep'), 'thousands_sep');?></th>
		<th><?php echo $this->Paginator->sort(__l('Local'), 'locale');?></th>
		<th><?php echo $this->Paginator->sort(__l('Format String'), 'format_string');?></th>
		<th><?php echo $this->Paginator->sort(__l('Grouping Algorithm Callback'), 'grouping_algorithm_callback');?></th>
		<th><?php echo $this->Paginator->sort(__l('Enabled?'),'is_enabled');?></th>
		<th><?php echo $this->Paginator->sort(__l('Use graphic symbol?'),'is_use_graphic_symbol');?></th>
		<th><?php echo $this->Paginator->sort(__l('PayPal Support?'),'is_paypal_supported');?></th>

    </tr>
<?php
if (!empty($currencies)):

$i = 0;
$_currencies = Cache::read('site_currencies');
$selected_currency = $_currencies[Configure::read('site.currency_id')];
$c_selected_currency = $_currencies[Configure::read('site.paypal_currency_converted_id')];
foreach ($currencies as $currency):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<span><?php echo $this->Html->link(__l('Edit'), array('action' => 'edit', $currency['Currency']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span> 
			<?php if(($selected_currency['Currency']['id'] != $currency['Currency']['id']) && ($c_selected_currency['Currency']['id'] != $currency['Currency']['id'])):?>
				<span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $currency['Currency']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span>
			<?php endif;?>
		</td>
		<td>
			<?php echo $this->Html->cText($currency['Currency']['name']);?>
		</td>
		<td><?php echo $this->Html->cText($currency['Currency']['code']);?></td>
		<td><?php echo $this->Html->cText($currency['Currency']['symbol']);?></td>
		<td><?php echo $this->Html->cDateTimeHighlight($currency['Currency']['created']);?></td>
		<td><?php echo $this->Html->cText($currency['Currency']['prefix']);?></td>
		<td><?php echo $this->Html->cText($currency['Currency']['suffix']);?></td>
		<td><?php echo $this->Html->cText($currency['Currency']['decimals']);?></td>
		<td><?php echo $this->Html->cText($currency['Currency']['dec_point']);?></td>
		<td><?php echo $this->Html->cText($currency['Currency']['thousands_sep']);?></td>
		<td><?php echo $this->Html->cText($currency['Currency']['locale']);?></td>
		<td><?php echo $this->Html->cText($currency['Currency']['format_string']);?></td>
		<td><?php echo $this->Html->cText($currency['Currency']['grouping_algorithm_callback']);?></td>
		<td><span class="round-5 bool-<?php echo $currency['Currency']['is_enabled']; ?>"><?php echo $this->Html->cBool($currency['Currency']['is_enabled']);?></span></td>
		<td><span class="round-5 bool-<?php echo $currency['Currency']['is_use_graphic_symbol']; ?>"><?php echo $this->Html->cBool($currency['Currency']['is_use_graphic_symbol']);?></span></td>
		<td><span class="round-5 bool-<?php echo $currency['Currency']['is_paypal_supported']; ?>"><?php echo $this->Html->cBool($currency['Currency']['is_paypal_supported']);?></span></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="7" class="notice"><?php echo __l('No Currencies available');?></td>
	</tr>
<?php
endif;
?>
</table>
</div>
<?php if (!empty($currencies)) {?>	
	<div>
		<?php echo $this->element('paging_links');?>
	</div>	
	<?php echo $this->Form->end(); ?>
	<?php }?>
</div>
