<?php /* SVN: $Id: $ */ ?>
<div class="dealCategories form">
<?php echo $this->Form->create('DealCategory', array('class' => 'normal'));?>
	<h2><?php echo __l('Edit Deal Subscription Category');?></h2>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
	?>
<?php echo $this->Form->end(__l('Update'));?>
</div>
