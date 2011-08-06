<?php /* SVN: $Id: admin_add.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<div class="users form">
	<h2> <?php echo __l('Add User');?></h2>
<?php echo $this->Form->create('User', array('class' => 'normal'));?>
	<fieldset>
 	
	<?php
        echo $this->Form->input('user_type_id',array('label' => __l('User Type')));
		echo $this->Form->input('email',array('label' => __l('Email')));
		echo $this->Form->input('username',array('label' => __l('Username')));
		echo $this->Form->input('passwd', array('label' => __l('Password')));
		//echo $this->Form->input('UserProfile.first_name');
		//echo $this->Form->input('UserProfile.last_name');
		//echo $this->Form->input('UserProfile.dob', array('empty' => __l('Please Select'), 'maxYear' => date('Y') - 18, 'minYear' => date('Y') - 100,'label' => 'Date of birth'));
		//echo $this->Form->input('UserProfile.city_id');
		//echo $this->Form->input('UserProfile.state_id');
	?>
	</fieldset>
<div class="submit-block clearfix">
    <?php echo $this->Form->submit(__l('Add'));?>
    </div>
    <?php echo $this->Form->end();?>
</div>