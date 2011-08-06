<?php /* SVN: $Id: $ */ ?>
<div class="dealCategories form">
<?php echo $this->Form->create('DealCategory', array('class' => 'normal'));?>
	<fieldset>
 		<legend><?php echo $this->Html->link(__l('Deal Subscription Categories'), array('action' => 'index'));?> &raquo; <?php echo __l('Add Deal Subscription Category');?></legend>
	<?php
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end(__l('Add'));?>
</div>
