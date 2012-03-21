<?php /* SVN: $Id: add.ctp 59262 2011-07-07 05:51:00Z aravindan_111act10 $ */ ?>
<?php if(Configure::read('site.is_in_prelaunch_mode') && (!empty($this->request->params['named']['is_from_pages']) || $this->request->data['Subscription']['is_from_pages'])):?>
		<div class="form js-responses">
			<?php echo $this->Form->create('Subscription', array('id' => 'homeSubscriptionFrom', 'class' => 'normal js-ajax-form'));?>
				<?php echo $this->Form->input('email',array('id' => 'homeEmail', 'label' => __l('Enter your Email address:'))); ?>
				<span class="subcription-info-second">(<?php echo __l('We\'ll never share your e-mail address)');?></span>
				<?php echo $this->Form->input('city_id',array('id' => 'homeCityId', 'label' => __l('Choose your city:'), 'options' => $cities)); ?>
				<?php echo $this->Form->input('is_from_pages',array('type' => 'hidden', 'value' => (!empty($this->request->params['named']['is_from_pages']) ? $this->request->params['named']['is_from_pages'] : $this->request->data['Subscription']['is_from_pages']))); ?>
				<div class="submit-block clearfix">
					<?php echo $this->Form->submit(__l('Subscribe'));?>
				</div>
				<?php echo $this->Form->end(); ?>
		</div>
	<?php else:?>
		<?php if(preg_match('/subscribe/s',$this->request->url) && Configure::read('site.enable_three_step_subscription') && !$this->Auth->sessionValid() && empty($this->layoutPath)): ?>
			<div class="form">
			
			<?php echo $this->Form->create('Subscription', array('id' => 'homeSubscriptionFrom', 'class' => 'normal js-grouponpro_sub_form {Currentstep:'.$Currentstep.'}'));?>
				
					<div class="clearfix">
						<div class="js-step_one step-one js-form_step">
							<div class="step-content-info round-10">
							<div class="step-input-block">
							<?php echo $this->Form->input('city_id',array('id' => 'homeCityId', 'label' => __l('Choose your city:'), 'options' => $cities)); ?>				
							</div>
							<div class="js-buttons">
							<div class="clearfix">
								<?php echo $this->Form->submit(__l('Continue'), array('type'=>'button','id' => 'step_one', 'class' => 'js-button js-continue'));?>							
									<ul class="sign-in">
										<?php if(!$this->Auth->sessionValid()): ?>
										<li>
										   <?php echo $this->Html->link(__l('Sign in'), array('controller' => 'users', 'action' => 'login'), array('title' => __l('Sign in'),'class'=>'login-link'));?>
                                        </li>
                                            <?php elseif($this->Auth->user('user_type_id') == ConstUserTypes::User):?>
                                            <li>
                                            <?php echo $this->Html->link(__l('My Stuff'), array('controller' => 'users', 'action' => 'my_stuff'), array('title' => __l('My Stuff')));?>
                                            </li>
                                        <?php elseif($this->Auth->user('user_type_id') == ConstUserTypes::Company):?>
                                            <li>
											<?php echo $this->Html->link(__l('My Deals'), array('controller' => 'deals', 'action' => 'index', 'company' => $company['Company']['slug'] ), array('title' => __l('My Deals')));?>
                                            </li>
                                        <?php elseif($this->Auth->user('user_type_id') == ConstUserTypes::Admin):?>
                                            <li>
                                            <?php echo $this->Html->link(__l('Admin'), array('controller' => 'users' , 'action' => 'stats' , 'admin' => true), array('title' => __l('Admin'))); ?>
                                            </li>
										<?php endif;?>
                                        <?php if(configure::read('site.subscription_skip_option_enabled')):?>
                                            <li>
											<?php echo $this->Html->link(__l('Already Registered').'?', array('controller' => 'subscriptions', 'action' => 'skip'), array('title' => __l('Already Registered').'?', 'class'=>'login-link'));?>
                                            </li>
                                        <?php endif; ?>    
									</ul>
										</div>
							</div>
						</div>
						</div>
						<div class="js-step_two step-one js-form_step">
						<div class="step-content-info round-10">
						<div class="step-input-block">
							<?php echo $this->Form->input('email',array('id' => 'homeEmail', 'label' => __l('Enter your email address:'))); ?>	
							<p class="email_never_share"><?php echo __l("Don't worry, your email is safe and secure with us."); ?></p>
							</div>
								 <div class="clearfix">
									<?php echo $this->Form->submit(__l('Subscribe'));?>
							
									<ul class="sign-in">
										<?php if(!$this->Auth->sessionValid()): ?>
										<li>
											<?php echo $this->Html->link(__l('Sign in'), array('controller' => 'users', 'action' => 'login'), array('title' => __l('Sign in'),'class'=>'login-link')) ;?>
                                            </li>
                                            <?php elseif($this->Auth->user('user_type_id') == ConstUserTypes::User):?>
                                            <li>
                                            <?php echo $this->Html->link(__l('My Stuff'), array('controller' => 'users', 'action' => 'my_stuff'), array('title' => __l('My Stuff')));?>
                                            </li>
                                        <?php elseif($this->Auth->user('user_type_id') == ConstUserTypes::Company):?>
                                            <li>
                                        	<?php echo $this->Html->link(__l('My Deals'), array('controller' => 'deals', 'action' => 'index', 'company' => $company['Company']['slug'] ), array('title' => __l('My Deals')));?>
                                            </li>
                                        <?php elseif($this->Auth->user('user_type_id') == ConstUserTypes::Admin):?>
                                            <li>
                                            <?php echo $this->Html->link(__l('Admin'), array('controller' => 'users' , 'action' => 'stats' , 'admin' => true), array('title' => __l('Admin'))); ?>
                                            </li>
                                    	<?php endif;?>
                                         <?php if(configure::read('site.subscription_skip_option_enabled')):?>
                                        <li>
											<?php echo $this->Html->link(__l('Already Registered').'?', array('controller' => 'subscriptions', 'action' => 'skip'), array('title' => __l('Already Registered').'?', 'class'=>'login-link'));?>
                                         </li>
                                          <?php endif; ?>    
									</ul>
										</div>
						</div>
						</div>
					</div>
				<?php echo $this->Form->end(); ?>
			</div>
		<?php elseif(preg_match('/subscribe/s',$this->request->url) ): ?>
	
			<div class="pop js-responses">
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
						<?php echo $this->Form->create('Subscription', array('id' => 'homeSubscriptionFrom', 'class' => 'normal'));?>
						<ul>
							<li><?php echo $this->Form->input('email',array('id' => 'homeEmail', 'class'=>'js-home-email','div'=>false,'label' => __l('Enter your Email address:'))); ?></li>
							<li><?php echo $this->Form->input('city_id',array('id' => 'homeCityId', 'div'=>false, 'label' => __l('Choose your city:'), 'options' => $cities)); ?></li>
							<li><?php echo $this->Form->submit(__l('Subscribe'));?></li>
						</ul>
					 <?php echo $this->Form->end(); ?>
				  </div>
				</div>
			</div>							
			</div>
		<?php else: ?> 
			<div class="subscriptions form clearfix">			
				<?php echo $this->Form->create('Subscription', array('class' => 'subscription round-10 clearfix', 'id' => 'SubscriptionAddForm'.$step ));?>
					<?php
						if(!empty($city_id)):
							$this->request->data['Subscription']['city_id'] = $city_id;
						endif;
					?>
					<?php echo $this->Form->input('email',array('label' => __l('Email'), 'class' => 'emailsubscription', 'id' => 'SubscriptionEmail'.$step)); ?>
					<?php 
					if(!empty($this->request->data['Subscription']['city_id'])):
						echo $this->Form->input('city_id',array('type' => 'hidden', 'value' => $this->request->data['Subscription']['city_id'], 'id' => 'SubscriptionCityId'.$step)); 
					else:
						echo $this->Form->input('city_id',array('type' => 'hidden', 'id' => 'SubscriptionCityId'.$step)); 
					endif;			
					?>
					<div class="clearfix">
						<?php echo $this->Form->submit(__l('Subscribe'));?>
					</div>
				<?php echo $this->Form->end();?>
			</div>
		<?php endif; ?>
<?php endif; ?>