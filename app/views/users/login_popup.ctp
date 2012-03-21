<div class="pop">
  <div class="pop-bor">
	<div class="rip">Selamat datang ke</div>
	  <p class="a-center"><?php echo $this->Html->image('login/pop_logo.png', array('width'=>180, 'height'=>87));?></p>
	<p class="pop-text">Setiap hari, DealHangat akan e-mel anda satu tawaran eksklusif yang menakjubkan di Kuala Lumpur pada harga yang tak terkalahkan. Deals harian kita adalah untuk:	Restoran Halal, Spa, Konsert, Acara Sukan, Kelas, Salon, dan banyak lagi ...</p>
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
			echo $this->Form->create('User', array('action' => 'login', 'class' => 'normal '.$formClass));				
		?>
			<ul>
			<li><?php echo $this->Form->input(Configure::read('user.using_to_login'), array( 'div'=>false));?></li>
			<li><?php echo $this->Form->input('passwd', array('label' => __l('Password'),'div'=>false));?></li>	
			<li>
				<?php echo $this->Form->input('User.is_remember', array('type' => 'checkbox', 'label' => __l('Remember me on this computer.'), 'class'=>'checkbox')); ?>
				<?php echo $this->Html->link(__l('Forgot your password?') , array('controller' => 'users', 'action' => 'forgot_password', 'admin' => false),array('title' => __l('Forgot your password?'))); ?>
			</li>
			<li><?php echo $this->Form->submit(__l('Login')); ?>
			<?php echo $this->Html->link(__l('Register?') , array('controller' => 'users', 'action' => 'register', 'admin' => false),array('title' => __l('User Registration?'))); ?>
			</li>
		</ul>
	  </div>
	</div>
</div>