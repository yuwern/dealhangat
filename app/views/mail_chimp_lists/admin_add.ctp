<?php /* SVN: $Id: $ */ ?>
<div class="mailChimpLists form">
<?php echo $this->Form->create('MailChimpList', array('class' => 'normal'));?>
	<fieldset>
 		<h2><?php echo __l('Add Mail Chimp List');?></h2>
	<?php
		echo $this->Form->input('city_id');
		echo $this->Form->input('list_id', array('type' => 'text'));
	?>
	</fieldset>
		<div class="submit-block clearfix">
<?php echo $this->Form->end(__l('Add'));?>
</div>
</div>
