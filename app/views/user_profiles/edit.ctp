<div class="userProfiles form js-responses">
	<div class="main-content-block js-corner round-5">
		  <?php if($this->request->params['action'] == 'my_account') { ?>
	    <div class="js-tabs">
			<ul class="clearfix">
				<li><?php echo $this->Html->link(__l('My Profile'), '#my-profile'); ?></li>
				<?php if(!$this->Auth->user('fb_user_id') && !$this->Auth->user('is_openid_register')){?>
				    <li><?php  echo $this->Html->link(__l('Change Password'),array('controller'=> 'users', 'action'=>'change_password'),array('title' => __l('Change Password'))); ?></li>
				<?php } ?>
					  <li><?php echo $this->Html->link(__l('Privacy Settings'), array('controller' => 'user_permission_preferences', 'action' => 'edit', $this->request->data['UserProfile']['user_id'], 'admin' => false), array('title' => __l('Privacy Settings')));?></li>
			</ul>
		</div>
		<?php } ?>
		<div id='my-profile'>
			<h2><?php echo sprintf(__l('Edit Profile - %s'), $this->request->data['User']['username']); ?></h2>
			<div class="form-blocks  js-corner round-5">
				<?php echo $this->Form->create('UserProfile', array('action' => 'edit', 'class' => 'normal js-ajax-form', 'enctype' => 'multipart/form-data'));?>
					<fieldset class="form-block round-5">
						<legend class="round-5"><?php echo __l('Personal'); ?></legend> 
						<div class="profile-image">
							<?php 
								$user_details = array(
									'username' => $this->request->data['User']['username'],
									'user_type_id' =>  $this->request->data['User']['user_type_id'],
									'id' =>  $this->request->data['User']['id'],
									'fb_user_id' =>  $this->request->data['User']['fb_user_id'],
									'UserAvatar' => $this->request->data['UserAvatar']
								);
								echo $this->Html->getUserAvatarLink($user_details, 'normal_thumb').' ';
							?>
							<p>
								<?php  echo $this->Html->link(__l('Change Image'),array('controller'=> 'users', 'action'=>'profile_image', $this->request->data['User']['id'], 'admin' => false),array('title' => __l('Change Image'))); ?>	
							</p>
						</div>
						<?php
							if($this->Auth->user('user_type_id') == ConstUserTypes::Admin):
								echo $this->Form->input('User.id',array('label' => __l('User')));
							endif;
							if($this->Auth->user('user_type_id') == ConstUserTypes::Admin):
								echo $this->Form->input('User.username');
							endif;
							echo $this->Form->input('first_name',array('label' => __l('First Name')));
							echo $this->Form->input('last_name',array('label' => __l('Last Name')));
							echo $this->Form->input('middle_name',array('label' => __l('Middle Name')));
							echo $this->Form->input('gender_id', array('empty' => __l('Please Select'),'label' => __l('Gender')));
							if($this->Auth->user('user_type_id') == ConstUserTypes::Admin):
								echo $this->Form->input('User.email',array('label' => __l('Email')));
							endif;
						?>
						<div class="date-time-block clearfix">
						<div class="input date-time clearfix required">
							<div class="js-datetime">
								<?php echo $this->Form->input('dob', array('label' => __l('DOB'),'empty' => __l('Please Select'), 'div' => false, 'minYear' => date('Y') - 100, 'maxYear' => date('Y'), 'orderYear' => 'asc')); ?>
							</div>
						</div>
                        </div>
							<?php
								if(Configure::read('site.currency_symbol_place') == 'left'):
									$currecncy_place = 'between';
								else:
									$currecncy_place = 'after';
								endif;	
							?>		
						<?php echo $this->Form->input('about_me', array('label' => __l('About Me'))); ?>
						<?php echo $this->Form->input('user_education_id', array('empty' => __l('Please Select'),'label' => __l('Education'))); ?>
						<?php echo $this->Form->input('user_employment_id', array('empty' =>__l('Please Select'),'label' => __l('Employment Status'))); ?>
						<?php $currecncy_place = '<span class="currency">'.Configure::read('site.currency'). '</span>' ; ?>
						<?php echo $this->Form->input('user_incomerange_id', array('empty' => __l('Please Select'),'label' => __l('Income range'),'after' => $currecncy_place)); ?>
						<?php
                         $options = array('1' => 'Yes', '0' => 'No');
                         echo $this->Form->input('own_home', array('options' => $options, 'type' => 'radio', 'legend' => false, 'before' => '<span class="label-content label-content-radio">'.__l('Own home?').'</span>'));
                        ?>
						<?php echo $this->Form->input('user_relationship_id', array('empty' => __l('Please Select'),'label' => __l('Relationship status'))); ?>
						<?php
                           $options=array('1'=>'Yes','0'=>'No');
                           echo $this->Form->input('have_children', array('options' => $options, 'type' => 'radio', 'legend' => false, 'before' => '<span class="label-content label-content-radio">'.__l('Have Children?').'</span>'));
                        ?>
					</fieldset>
					<fieldset class="form-block round-5">
						<legend class="round-5"><?php echo __l('Address'); ?></legend> 
						<?php
							echo $this->Form->input('address',array('label' => __l('Address')));
							echo $this->Form->input('country_id', array('empty' => __l('Please Select'),'label' => __l('Country')));
							echo $this->Form->autocomplete('State.name', array('label' => __l('State'), 'acFieldKey' => 'State.id', 'acFields' => array('State.name'), 'acSearchFieldNames' => array('State.name'), 'maxlength' => '255'));
							echo $this->Form->autocomplete('City.name', array('label' => __l('City'), 'acFieldKey' => 'City.id', 'acFields' => array('City.name'), 'acSearchFieldNames' => array('City.name'), 'maxlength' => '255'));
                            echo $this->Form->input('zip_code',array('label' => __l('Zip Code')));
						?>
					</fieldset>
					<?php if($this->Auth->user('user_type_id') == ConstUserTypes::Admin || Configure::read('user.is_user_can_with_draw_amount')): ?>
						<fieldset class="form-block round-5">
							<legend class="round-5"><?php echo __l('Paypal'); ?></legend>
							<?php echo $this->Form->input('paypal_account', array('label' => __l('PayPal').' '.__l('Account'))); ?>
						</fieldset>
					<?php endif; ?>
					<fieldset class="form-block round-5">
						<legend class="round-5"><?php echo __l('Other'); ?></legend>
						<?php
							if($this->Auth->user('user_type_id') == ConstUserTypes::Admin):
								if($this->request->data['User']['id'] != ConstUserIds::Admin):
									echo $this->Form->input('User.is_active', array('label' => __l('Active')));
								endif;
								echo $this->Form->input('User.is_email_confirmed', array('label' => __l('Email confirmed')));
							endif;
							echo $this->Form->input('UserAvatar.filename', array('type' => 'file','size' => '33', 'label' => __l('Upload Photo'),'class' =>'browse-field'));
							echo $this->Form->input('language_id', array('empty' => __l('Please Select'),'label' => __l('Profile Language'), 'info'=>__l('This will be the default site languge after logged in')));
						?>
					</fieldset>
				  <div class="submit-block clearfix">
                    <?php
                    	echo $this->Form->submit(__l('Update'));
                    ?>
                    </div>
                <?php
                	echo $this->Form->end();
                ?>
			
			</div>
		</div>
	</div>
</div>