<div class="js-login-response">
<div class="pop">
  <div class="pop-bor">
	<div class="rip"><?php echo __l('Welcome to');?></div>
	  <p class="a-center"><?php echo $this->Html->image('login/pop_logo.png', array('width'=>180, 'height'=>87));?></p>
	<p class="pop-text"><?php echo __l("Every day, DealHangat will offer you an exclusive stunning in Kuala Lumpur at an unbeatable price. Daily Deals we are for: Halal Restaurant, Spa, Concert, Sport Events, Classes, Salon, and many more ...");?></p>
	  <ul class="icon-list">
		<li><?php echo $this->Html->image('login/icon1.png');?></li>
		<li><?php echo $this->Html->image('login/icon2.png');?></li>
		<li><?php echo $this->Html->image('login/icon3.png');?></li>
		<li><?php echo $this->Html->image('login/icon4.png');?></li>
		<li><?php echo $this->Html->image('login/icon5.png');?></li>
		<li><?php echo $this->Html->image('login/icon6.png');?></li>
		<li><?php echo $this->Html->image('login/icon7.png');?></li>
		<li><?php echo $this->Html->image('login/icon8.png');?></li>
		<li><?php echo $this->Html->image('login/icon9.png');?></li>
	  </ul>
	  
	  <div class="pop_login">
		<?php 
			$formClass = !empty($this->request->data['User']['is_requested']) ? 'js-ajax-login' : '';
			echo $this->Form->create('User', array('action' => 'login', 'class' => 'js-ajax-login'.$formClass));				
		?>
			<ul>
			<li><strong><?php echo __l('Username'); ?></strong></li>
			<li><?php echo $this->Form->input(Configure::read('user.using_to_login'), array( 'div'=>false, 'label'=>false));?></li>
			<li><strong><?php echo __l('Password'); ?></strong></li>
			<li><?php echo $this->Form->input('passwd', array('label' => false,'div'=>false));?></li>	
			<li>
				<?php echo $this->Form->input('User.is_remember', array('type' => 'checkbox', 'div'=>false, 'label' => __l('Remember me on this computer.'), 'class'=>'checkbox')); ?>
				<?php echo $this->Html->link(__l('Forgot your password?') , array('controller' => 'users', 'action' => 'forgot_password', 'admin' => false),array('title' => __l('Forgot your password?'))); ?>
			</li>
			<li><div class="float:left"><?php echo $this->Form->submit(__l('Login'), array('div'=>false)); ?>
			<?php echo $this->Html->link(__l('Register?') , array('controller' => 'users', 'action' => 'register', 'admin' => false),array('title' => __l('User Registration?'), 'class'=>'reg')); ?></div>
			</li>
			
		</ul><?php echo $this->Form->end(); ?>
		<div class="f-connect">
			<div class="f-btn">	<?php echo $this->Html->link($this->Html->image('f-connect_2.png'), array('controller' => 'users', 'action' => 'login','type'=>'facebook'), array('target' => '_blank','title' => __l('Sign up today, to RM').' '. Configure::read('user.register_e_wallet_amount').'!', 'escape' => false)); ?></div>
			<div class="rm"><?php echo __l('Sign up today, to RM').' '. Configure::read('user.register_e_wallet_amount').'!';?></div>
		</div>
		<div class="clearfix"></div>		
	  </div>
	</div>
</div>
</div>