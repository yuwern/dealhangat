<?php /* SVN: $Id: admin_edit.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<div class="userComments form">
<?php echo $this->Form->create('UserComment', array('class' => 'normal'));?>
	<fieldset>
 		<h2><?php echo __l('Edit User Comment');?></h2>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('user_id',array('label' => __l('User')));
		echo $this->Form->input('posted_user_id',array('label' => __l('Posted user')));
		echo $this->Form->input('comment',array('label' => __l('Comment')));
	?>
	</fieldset>
 <div class="submit-block clearfix">
    <?php echo $this->Form->submit(__l('Update'));?>
    </div>
    <?php echo $this->Form->end();?>
</div>
