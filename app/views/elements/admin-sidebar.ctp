<h5 class="hidden-info"><?php echo __l('Admin side links'); ?></h5>
<ul class="admin-links">
	<?php $class = ($this->request->params['controller'] == 'users' && $this->request->params['action'] == 'admin_stats') ? ' class="active"' : null; ?>
	<li <?php echo $class;?>><?php echo $this->Html->link(__l('Site Stats'), array('controller' => 'users', 'action' => 'stats'),array('title' => __l('Site Stats'))); ?></li>
	<li class="no-bor">
		<h4><?php echo __l('Users'); ?></h4>
		<ul class="admin-sub-links">
			<?php $class = ($this->request->params['controller'] == 'user_profiles' ||  ($this->request->params['controller'] == 'users'  && ($this->request->params['action'] == 'admin_index' || $this->request->params['action'] == 'change_password' || $this->request->params['action'] == 'admin_add' )) ) ? ' class="active"' : null; ?>            
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Users'), array('controller' => 'users', 'action' => 'index'),array('title' => __l('Users'))); ?></li>
			<?php $class = ( $this->request->params['controller'] == 'user_profiles') ? ' class="active"' : null; ?>
            <?php $class = ($this->request->params['controller'] == 'user_logins') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('User Logins'), array('controller' => 'user_logins', 'action' => 'index'),array('title' => __l('User Logins'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'user_comments') ? ' class="active"' : '';?>
			<li <?php echo $class; ?>><?php echo $this->Html->link(__l('User Comments'), array('controller' => 'user_comments', 'action' => 'index'), array('title' => __l('User Comments'), 'escape' => false)); ?></li>
		</ul>
	</li>
	<?php $class = ($this->request->params['controller'] == 'companies' || $this->request->params['controller'] == 'company_addresses') ? ' class="active"' : null; ?>
	<li <?php echo $class;?>><?php echo $this->Html->link(__l('Companies'), array('controller' => 'companies', 'action' => 'index'),array('title' => __l('Companies'))); ?></li>
    <?php
    if($this->Html->isWalletEnabled('is_enable_for_add_to_wallet')){
	   if((Configure::read('company.is_user_can_withdraw_amount')) || (Configure::read('user.is_user_can_with_draw_amount'))){?>
    <?php $class = ($this->request->params['controller'] == 'user_cash_withdrawals') ? ' class="active"' : null; ?>
	<li <?php echo $class;?>><?php echo $this->Html->link(__l('Withdraw Fund Requests'), array('controller' => 'user_cash_withdrawals', 'action' => 'index'),array('title' => __l('Withdraw Fund Requests'))); ?></li>
    <?php } } ?>
	<?php $class = ($this->request->params['controller'] == 'deals' && ($this->request->params['action'] == 'admin_index' || $this->request->params['action'] == 'admin_edit')) ? ' class="active"' : null; ?>
	<li class="no-bor">
   <h4><?php echo __l('Deals'); ?></h4>
    	<ul class="admin-sub-links">
    	<li <?php echo $class;?>>
    	 <?php echo $this->Html->link(__l('Deals'), array('controller' => 'deals', 'action' => 'index'),array('title' => __l('Deals'))); ?>
        </li>
            <?php $class = ($this->request->params['controller'] == 'deals' && $this->request->params['action'] == 'add') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Add Deal'), array('controller' => 'deals', 'action' => 'add'), array('title' => __l('Add Deal'))); ?></li>
		    <?php $class = ($this->request->params['controller'] == 'deal_users') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Deal Coupons'), array('controller' => 'deal_users', 'action' => 'index'), array('title' => __l('Deal Coupons'))); ?></li>
        </ul>
    </li>
    <?php $class = ($this->request->params['controller'] == 'gift_users') ? ' class="active"' : null; ?>
	<li <?php echo $class;?>><?php echo $this->Html->link(__l('Gift Cards'), array('controller' => 'gift_users', 'action' => 'index'),array('title' => __l('Gift Cards'))); ?></li>
	<li class="no-bor">
		<h4><?php echo __l('Subscriptions'); ?></h4>
		<ul class="admin-sub-links">
        <?php $class = ($this->request->params['controller'] == 'subscriptions' && $this->request->params['action'] == 'index') ? ' class="active"' : null; ?>
		<li <?php echo $class;?>><?php echo $this->Html->link(__l('Subscriptions'), array('controller' => 'subscriptions', 'action' => 'index'),array('title' => __l('Subscriptions'))); ?></li>
		  <?php $class = ($this->request->params['controller'] == 'subscriptions' && $this->request->params['action'] == 'subscription_customise') ? ' class="active"' : null; ?>
		<li <?php echo $class;?>><?php echo $this->Html->link(__l('Customize subscription page'), array('controller' => 'subscriptions', 'action' => 'admin_subscription_customise'),array('title' => __l('Customize subscription page'))); ?></li>

        </ul>
	</li>
    <li class="no-bor">
		<h4><?php echo __l('Suggestions'); ?></h4>
		<ul class="admin-sub-links">
			<?php $class = ($this->request->params['controller'] == 'city_suggestions') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Cities'), array('controller' => 'city_suggestions', 'action' => 'index'), array('title' => __l('City Suggestions'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'business_suggestions') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Business'), array('controller' => 'business_suggestions', 'action' => 'index'), array('title' => __l('Business'))); ?></li>
		</ul>
	</li>
    <li class="no-bor">
		<h4><?php echo __l('Topics'); ?></h4>
		<ul class="admin-sub-links">
			<?php $class = ($this->request->params['controller'] == 'topics') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Topics'), array('controller' => 'topics', 'action' => 'index'),array('title' => __l('Topics'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'topic_discussions') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Topic Discussions'), array('controller' => 'topic_discussions', 'action' => 'index'),array('title' => __l('Topic Discussions'))); ?></li>
		</ul>
	</li>
	<li class="no-bor">
		<h4><?php echo __l('Payment'); ?></h4>
		<ul class="admin-sub-links">
			<?php $class = ($this->request->params['controller'] == 'payment_gateways') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Payment Gateways'), array('controller' => 'payment_gateways', 'action' => 'index'), array('title' => __l('Payment Gateways')));?></li>
			<?php $class = ($this->request->params['controller'] == 'transactions') ? ' class="active"' : null; ?>
	       <li <?php echo $class;?>><?php echo $this->Html->link(__l('Transactions'), array('controller' => 'transactions', 'action' => 'index'),array('title' => __l('Transactions'))); ?></li>	
		</ul>
	</li>
	<?php if(Configure::read('charity.is_enabled') == 1):?>
	<li class="no-bor">
		<h4><?php echo __l('Charity'); ?></h4>
		<ul class="admin-sub-links">
		    <?php $class = ($this->request->params['controller'] == 'charities') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Charities'), array('controller' => 'charities', 'action' => 'index'), array('title' => __l('Charities')));?></li>
		</ul>
	</li>
	<?php endif; ?>
	<?php if(Configure::read('affiliate.is_enabled') == 1):?>
   <li class="no-bor">
		 <?php echo $this->element('affiliate_admin_sidebar');?>
	</li>
    <?php endif; ?>
	<li class="no-bor">
		<h4><?php echo __l('Masters'); ?></h4>
		<ul class="admin-sub-links">
			<?php $class = ($this->request->params['controller'] == 'settings') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Settings'), array('controller' => 'settings', 'action' => 'index'),array('title' => __l('Settings'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'currencies') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Currencies'), array('controller' => 'currencies', 'action' => 'index'),array('title' => __l('Currencies'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'email_templates') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Email Templates'), array('controller' => 'email_templates', 'action' => 'index'),array('title' => __l('Email Templates'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'pages') ? ' class="active"' : null; ?>
            <li <?php echo $class;?>><?php echo $this->Html->link(__l(' Manage Static Pages'), array('controller' => 'pages', 'action' => 'index', 'plugin' => NULL),array('title' => __l('Manage Static Pages')));?></li>
			<?php $class = ($this->request->params['controller'] == 'blocks') ? ' class="active"' : null; ?>
            <li <?php echo $class;?>><?php echo $this->Html->link(__l(' Manage Blocks'), array('controller' => 'blocks', 'action' => 'index', 'plugin' => NULL),array('title' => __l('Manage Blocks')));?></li>
			<?php $class = ($this->request->params['controller'] == 'transaction_types') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Transaction Types'), array('controller' => 'transaction_types', 'action' => 'index'),array('title' => __l('Transaction Types'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'translations') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Translations'), array('controller' => 'translations', 'action' => 'index'),array('title' => __l('Translations'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'languages') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Languages'), array('controller' => 'languages', 'action' => 'index'),array('title' => __l('Languages'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'banned_ips') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Banned IPs'), array('controller' => 'banned_ips', 'action' => 'index'),array('title' => __l('Banned IPs'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'cities') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Cities'), array('controller' => 'cities', 'action' => 'index'),array('title' => __l('Cities'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'states') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('States'), array('controller' => 'states', 'action' => 'index'),array('title' => __l('States'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'countries') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Countries'), array('controller' => 'countries', 'action' => 'index'),array('title' => __l('Countries'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'user_educations') ? ' class="active"' : null; ?>
    		<li <?php echo $class;?>><?php echo $this->Html->link(__l('Educations'), array('controller' => 'user_educations', 'action' => 'index'), array('title' => __l('Educations'))); ?></li>
            <?php $class = ($this->request->params['controller'] == 'user_employments') ? ' class="active"' : null; ?>
    		<li <?php echo $class;?>><?php echo $this->Html->link(__l('Employments'), array('controller' => 'user_employments', 'action' => 'index'), array('title' => __l('Employments'))); ?></li>
    		<?php $class = ($this->request->params['controller'] == 'user_income_ranges') ? ' class="active"' : null; ?>
    		<li <?php echo $class;?>><?php echo $this->Html->link(__l('Income Ranges'), array('controller' => 'user_income_ranges', 'action' => 'index'), array('title' => __l('Income Ranges'))); ?></li>
    		<?php $class = ($this->request->params['controller'] == 'user_relationships') ? ' class="active"' : null; ?>
    		<li <?php echo $class;?>><?php echo $this->Html->link(__l('Relationships'), array('controller' => 'user_relationships', 'action' => 'index'), array('title' => __l('Relationships'))); ?></li>
    		<?php $class = ($this->request->params['controller'] == 'mail_chimp_lists') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('MailChimp Mailing Lists'), array('controller' => 'mail_chimp_lists', 'action' => 'index'), array('title' => __l('MailChimp Mailing Lists'))); ?></li>
		    <?php $class = ($this->request->params['controller'] == 'genders') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Genders'), array('controller' => 'genders', 'action' => 'index'),array('title' => __l('Genders'))); ?></li>
		    <?php $class = ($this->request->params['controller'] == 'privacy_types') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Privacy Types'), array('controller' => 'privacy_types', 'action' => 'index'),array('title' => __l('Privacy Types'))); ?></li>
            <?php if(Configure::read('charity.is_enabled') == 1):?>
			 <?php $class = ($this->request->params['controller'] == 'charity_categories') ? ' class="active"' : null; ?>
			 <li <?php echo $class;?>><?php echo $this->Html->link(__l('Charity Categories'), array('controller' => 'charity_categories', 'action' => 'index'), array('title' => __l('Charity Categories')));?></li>
            <?php endif; ?>			
        </ul>
	</li>
    <li class="no-bor">
		<h4><?php echo __l('Diagnostics (Developer purpose only)'); ?></h4>
		<ul class="admin-sub-links">
           <?php $class = ($this->request->params['controller'] == 'paypal_transaction_logs' && $this->request->params['named']['type'] == 'normal') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Payment Transaction Log'), array('controller' => 'paypal_transaction_logs', 'action' => 'index', 'type' => 'normal'),array('title' => __l('Payment Transaction Log'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'paypal_transaction_logs' && $this->request->params['named']['type'] == 'mass') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Mass Payment Transaction Log'), array('controller' => 'paypal_transaction_logs', 'action' => 'index', 'type' => 'mass'),array('title' => __l('Mass Payment Transaction Log'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'paypal_docapture_logs') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Paypal Docapture Log'), array('controller' => 'paypal_docapture_logs', 'action' => 'index'),array('title' => __l('Paypal Docapture Log'))); ?></li>
			<?php $class = ($this->request->params['controller'] == 'authorizenet_docapture_logs') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Authorizenet Docapture Log'), array('controller' => 'authorizenet_docapture_logs', 'action' => 'index'),array('title' => __l('Authorizenet Docapture Log'))); ?></li>
  			<?php $class = ($this->request->params['controller'] == 'users' && $this->request->params['action'] == 'admin_logs') ? ' class="active"' : null; ?>
			<li <?php echo $class;?>><?php echo $this->Html->link(__l('Debug & Error Log'), array('controller' => 'users', 'action' => 'logs'),array('title' => __l('Debug & Error Log'))); ?></li>
		</ul>
	</li>
</ul>
