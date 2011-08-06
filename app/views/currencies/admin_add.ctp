<?php /* SVN: $Id: $ */ ?>
<div class="currencies form">
	<h2><?php echo __l('Add Currency');?></h2>
<?php echo $this->Form->create('Currency', array('class' => 'normal'));?>
	<fieldset>
	<?php
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
		echo $this->Form->input('is_enabled',array('label' =>__l('Enabled?')));
		echo $this->Form->input('is_paypal_supported');
	?>
	</fieldset>
    <div class="submit-block clearfix">
<?php echo $this->Form->end(__l('Add'));?>
	</div>
</div>
