<?php /* SVN: $Id: $ */ ?>
<div class="dealCategories form">
<?php echo $this->Form->create('DealCategory', array('class' => 'normal'));?>
	<fieldset>
 		<legend><?php echo $this->Html->link(__l('Deal Categories'), array('action' => 'index'));?> &raquo; <?php echo __l('Add Deal Subscription Category');?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('name_ms');
		echo $this->Form->input('is_active');
	?>
	</fieldset>
<?php echo $this->Form->end(__l('Add'));?>
</div>
