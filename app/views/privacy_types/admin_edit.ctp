<?php /* SVN: $Id: $ */ ?>
<div class="privacyTypes form">
<?php echo $this->Form->create('PrivacyType', array('class' => 'normal'));?>
	<fieldset>
 		<h2> <?php echo __l('Edit Privacy Type');?></h2>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
	?>
	</fieldset>
    <div class="submit-block clearfix">
    <?php echo $this->Form->submit(__l('Update'));?>
    </div>
    <?php echo $this->Form->end();?>
</div>

