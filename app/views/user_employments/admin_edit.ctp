<?php /* SVN: $Id: $ */ ?>
<div class="userEmployments form">
<?php echo $this->Form->create('UserEmployment', array('class' => 'normal'));?>
	<fieldset>
 	<h2><?php echo __l('Edit Employment');?></h2>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('employment');
		echo $this->Form->input('is_active');
	?>
	</fieldset>
 <div class="submit-block clearfix">
    <?php echo $this->Form->submit(__l('Update'));?>
    </div>
    <?php echo $this->Form->end();?>
</div>
