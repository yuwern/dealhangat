<?php
/* SVN FILE: $Id: default.ctp 15696 2010-07-26 11:00:27Z josephine_065at09 $ */
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.console.libs.templates.skel.views.layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @version       $Revision: 7805 $
 * @modifiedby    $LastChangedBy: AD7six $
 * @lastmodified  $Date: 2008-10-30 23:00:26 +0530 (Thu, 30 Oct 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<?php echo $this->Html->charset(), "\n";?>
	<title><?php echo Configure::read('site.name');?> | <?php echo $this->Html->cText($title_for_layout, false);?></title>
	<?php
		echo $this->Html->meta('icon'), "\n";
		echo $this->Html->meta('keywords', $meta_for_layout['keywords']), "\n";
		echo $this->Html->meta('description', $meta_for_layout['description']), "\n";
		require_once('_head.inc.ctp');
		echo $this->Asset->scripts_for_layout();
	?>
</head>
<body>
	<div class="js-morecities top-slider  hide"> <?php echo $this->element('cities-index', array('cache' => array('config' => 'site_element_cache')));?></div>
	<div class="top-slider js-show-subscription hide">
	<?php if($this->Html->isAllowed($this->Auth->user('user_type_id')) && $this->request->params['controller'] != 'subscriptions'): ?>
          <?php echo $this->element('../subscriptions/add', array('cache' => array('config' => 'site_element_cache')));?>
      <?php endif; ?>
		<ul class="header-nav">
				<?php
                    $cityArray = array();
					if(!empty($city_slug)):
						$tmpURL= $this->Html->getCityTwitterFacebookURL($city_slug);
						$cityArray = array('city'=>$city_slug);
					endif;
				?>
				<li class="rss"><?php echo $this->Html->link(__l('RSS'), array_merge(array('controller'=>'deals', 'action'=>'index', 'ext'=>'rss'), $cityArray), array('target' => '_blank','title'=>__l('RSS Feed'))); ?></li>
				<li class="twitter"><a href="<?php echo !empty($tmpURL['City']['twitter_url']) ? $tmpURL['City']['twitter_url'] : Configure::read('twitter.site_twitter_url'); ?>" title="<?php echo __l('Follow Us in Twitter'); ?>" target="_blank"><?php echo __l('Twitter'); ?></a></li>
				<li class="facebook"><a href="<?php echo !empty($tmpURL['City']['facebook_url']) ? $tmpURL['City']['facebook_url'] : Configure::read('facebook.site_facebook_url'); ?>" title="<?php echo __l('See Our Profile in Facebook'); ?>" target="_blank"><?php echo __l('Facebook'); ?></a></li>
		</ul>

	<?php 
		if($this->Auth->sessionValid()  and  $this->Auth->user('user_type_id') == ConstUserTypes::Company):
				$company = $this->Html->getCompany($this->Auth->user('id'));
		endif; 
	?>
	</div>
	<div id= "<?php echo $this->Html->getUniquePageId();?>" class="content">
    <div id="header">
    <div id="header-content">
        <div class="clearfix">
            <h1>
				<?php
					$attachment = $this->Html->siteLogo();
					if (!empty($attachment['Attachment'])):
						echo $this->Html->link($this->Html->showImage('SiteLogo', $attachment['Attachment'], array('dimension' => 'site_logo_thumb', 'alt' => sprintf(__l('[Image: %s]'), Configure::read('site.name')), 'title' => Configure::read('site.name'), 'type' => 'png')), array('controller' => 'deals', 'action' => 'index', 'admin' => false), array('escape' => false));
					endif;
				?>
			</h1>
            <p class="hidden-info"><?php echo __l('Collective Buying Power');?></p>
              <div class="header-r">
              <div class="clearfix">
                <div class="global-block">
                <!--<div class="global-links"> </div>-->
                  <ul class="global-links-r">
                   <li class="down-arrow"><?php echo $this->Html->link(__l('Visit More Cities'), '#', array('title' => __l('Visit More Cities'), 'class' => "js-toggle-show {'container':'js-morecities1'}")); ?></li>
					<?php if($this->Html->isAllowed($this->Auth->user('user_type_id')) && $this->request->params['controller'] != 'subscriptions'): ?>
                        <li class="down-arrow"><?php echo $this->Html->link(sprintf(__l('Get Daily').' %s'.' '.__l('Alerts'), Configure::read('site.name')), '#', array('title' => sprintf(__l('Get Daily').' %s'.' '.__l('Alerts'), Configure::read('site.name')), 'class' => "js-toggle-show {'container':'js-show-subscription'}")); ?></li>
                    <?php endif; ?>
              	  <?php if($this->Html->isAllowed($this->Auth->user('user_type_id')) && (Configure::read('referral.referral_enabled_option') == ConstReferralOption::GrouponLikeRefer)): ?>
                          <li><?php echo $this->Html->link(__l('Refer Friends, Get').' '.$this->Html->siteCurrencyFormat($this->Html->cCurrency(Configure::read('user.referral_amount'), false)), array('controller' => 'pages', 'action' => 'refer_a_friend'), array('title' => __l('Refer Friends, Get').' '. $this->Html->siteCurrencyFormat($this->Html->cCurrency(Configure::read('user.referral_amount'), false))));?></li>
                  <?php endif; ?>
                    <li><?php echo $this->Html->link(__l('Contact Us'), array('controller' => 'contacts', 'action' => 'add', 'admin' => false), array('title' => __l('Contact Us')));?></li>
              </ul>
                </div>
                </div>
                <!--<div class="round-edge"></div>-->
                	<div class="clearfix">
				<?php echo $this->element('lanaguge-change-block', array('cache' => array('config' => 'site_element_cache')));?>
			
					<?php $total_array = $this->Html->total_saved(); ?>
							<dl class="total-list clearfix">
							<dt><?php echo __l('Total Saved: '); ?></dt>
							<dd><span><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($total_array['total_saved'])); ?></span></dd>
							<dt><?php echo __l('Total Bought: '); ?></dt>
							<dd><?php echo $this->Html->cInt($total_array['total_bought']); ?></dd>
						</dl>
				</div>
				<div class="clearfix">
                <?php if(!empty($city_name)): ?>
                    <div class="header-bot-l">
                        <h2><?php echo __l('Daily Deals on the Best in'); ?></h2>
                        <h3>
							<?php
								if (Cache::read('site.city_url', 'long') == 'prefix') {
									echo $this->Html->link($this->Html->cText($city_name), array('controller' => 'deals', 'action' => 'index', 'city' => $city_slug), array('title' => $this->Html->cText($city_name, false), 'escape' => false));
								} elseif (Cache::read('site.city_url', 'long') == 'subdomain') {
									$subdomain = substr(env('HTTP_HOST'), 0, strpos(env('HTTP_HOST'), '.'));
									$sitedomain = substr(env('HTTP_HOST'), strpos(env('HTTP_HOST'), '.'));
									if (strlen($subdomain) > 0) {
							?>
										<a href="http://<?php echo $city_slug . $sitedomain; ?>" title="<?php echo $this->Html->cText($city_name, false); ?>"><?php echo $this->Html->cText($city_name); ?></a>
							<?php
									} else {
										echo $this->Html->link($this->Html->cText($city_name), array('controller' => 'deals', 'action' => 'index', 'city' => $city_slug), array('title' => $this->Html->cText($city_name, false), 'escape' => false));
									}
								}
							?>
						</h3>
                    </div>
                <?php endif;?>
                <?php if($this->Auth->sessionValid()): ?>
                    <div class="header-bot-r">
                    <div class="clearfix">
                        <dl class="total-list clearfix">
                        	<dt><?php echo __l('Balance: '); ?></dt>
							<?php
								if (empty($user_available_balance)):
									$user_available_balance = 0;
								endif;
							?>
                            <dd><span><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($user_available_balance)); ?></span></dd>
                        </dl>
                        </div>
						<?php if ((Configure::read('company.is_user_can_withdraw_amount') && $this->Auth->user('user_type_id') == ConstUserTypes::Company) || (Configure::read('user.is_user_can_with_draw_amount') && $this->Auth->user('user_type_id') == ConstUserTypes::User)) { ?>
                            <p class="add-amount">
                            <?php echo $this->Html->link(__l('Withdraw Fund Request'), array('controller' => 'user_cash_withdrawals', 'action' => 'index'), array('title' => __l('Withdraw Fund Request'),'class'=>'width-draw'));?>
                            </p>
                        <?php } ?>
                        <?php if($this->Html->isAllowed($this->Auth->user('user_type_id'))): ?>
                            <p class="add-amount"><?php echo $this->Html->link(__l('Add amount to wallet'), array('controller' => 'users', 'action' => 'add_to_wallet'), array('class' => 'add add-wallet', 'title' => __l('Add amount to wallet'))); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                </div>
              </div>
        </div>
        <?php if($this->Auth->sessionValid()){ ?>
        <div class="menu-block clearfix">
  <?php      }
        else{ ?>
<div class="menu-block1 clearfix">
     <?php   } ?>
          
          	<ul class="menu clearfix">
          		<?php if($this->Html->isAllowed($this->Auth->user('user_type_id'))): ?>
                    <li <?php if($this->request->params['controller'] == 'deals' && $this->request->params['action'] == 'index' && !isset($this->request->params['named']['type']) && !isset($this->request->params['named']['company'])) { echo 'class="active"'; } ?>><?php echo $this->Html->link(__l('Today\'s Deals'), array('controller' => 'deals', 'action' => 'index', 'admin' => false), array('title' => __l('Today\'s Deals')));?></li>
                    <li <?php if($this->request->params['controller'] == 'deals' && (isset($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'recent')) { echo 'class="active"'; } else { echo 'class=""';}?>><?php echo $this->Html->link(__l('Recent Deals'), array('controller' => 'deals', 'action' => 'index', 'admin' => false,'type' => 'recent'), array('title' => __l('Recent Deals')));?></li>
               <?php endif; ?>
				<li <?php if($this->request->params['controller'] == 'topics' or $this->request->params['controller'] == 'topic_discussions' ) { echo 'class="active"'; } else { echo 'class=""';}?>><?php echo $this->Html->link(__l('Discussion'), array('controller' => 'topics', 'action' => 'index', 'admin' => false), array('title' => __l('Discussion')));?></li>

				<li <?php if($this->request->params['controller'] == 'pages' && $this->request->params['action'] == 'view'  && $this->request->params['pass'][0] == 'learn') { echo 'class="active"'; } ?>><?php echo $this->Html->link(sprintf(__l('How')." ".'%s'." " .__l('Works'), Configure::read('site.name')), array('controller' => 'pages', 'action' => 'view', 'learn', 'admin' => false), array('title' => sprintf(__l('How')." ".'%s'." ".__l(' Works'), Configure::read('site.name'))));?></li>

				<?php if(!$this->Auth->sessionValid()):
				$url = strstr($this->request->url,"/company/user/register");?>
					<li <?php if((!empty($url)) || ($this->request->params['controller'] == 'pages' && $this->request->params['action'] == 'view' &&  $this->request->params['pass'][0] == 'company')) { echo 'class="active"'; } else { echo 'class=""';}?>><?php echo $this->Html->link(__l('Business'), array('controller' => 'pages', 'action' => 'view', 'company', 'admin' => false), array('title' => __l('Business')));?></li>
				<?php endif; ?>
            </ul>
            <div class="menu-right">
            <p class="user-login-info">
                    <span class="user">
							<?php
						$reg_type_class='normal';
						if (!$this->Auth->sessionValid()): ?>
                           <span class="welcome-info"><?php  echo __l('Hi, Guest'); ?>
						  
							   <span <?php if($this->request->params['controller'] == 'users' && $this->request->params['action'] == 'login') { echo 'class="active"'; } ?>><?php echo $this->Html->link(__l('Login'), array('controller' => 'users', 'action' => 'login'), array('title' => __l('Login'),'class'=>'login-link'));?></span>
							   / <span <?php if($this->request->params['controller'] == 'users' && $this->request->params['action'] == 'register') { echo 'class="active"'; } ?>><?php echo $this->Html->link(__l('Register'), array('controller' => 'users', 'action' => 'register', 'admin' => false), array('title' => __l('Register'),'class'=>'login-link'));?></span>
						
							
							 <?php if(Configure::read('facebook.is_enabled_facebook_connect') && !empty($fb_login_url)):  ?>
								<?php  if (env('HTTPS')) { $fb_prefix_url = 'https://www.facebook.com/images/fbconnect/login-buttons/connect_dark_medium_short.gif';}else{ $fb_prefix_url = 'http://static.ak.fbcdn.net/images/fbconnect/login-buttons/connect_light_medium_short.gif';}?>
								<?php echo $this->Html->link($this->Html->image($fb_prefix_url, array('alt' => __l('[Image: Facebook Connect]'), 'title' => __l('Facebook connect'))), array('controller' => 'users', 'action' => 'login','type'=>'facebook'), array('escape' => false,'class'=>'facebook-link')); ?>
					
							 <?php endif; ?>
							 </span>
                        <?php  else:
								if($this->Auth->user('is_openid_register')):
									$reg_type_class='open-id';
								endif;
								if($this->Auth->user('fb_user_id')):
									$reg_type_class='facebook';
								endif;
								?>
							<?php
							$current_user_details = array(
								'username' => $this->Auth->user('username'),
								'user_type_id' =>  $this->Auth->user('user_type_id'),
								'id' =>  $this->Auth->user('id'),
								'fb_user_id' =>  $this->Auth->user('fb_user_id')
							);
                            if($this->Auth->user('user_type_id') != ConstUserTypes::Admin):
                                    echo __l('Hi, '); ?>
										<span class="<?php echo $reg_type_class; ?>">
											<?php echo $this->Html->getUserLink($current_user_details);?>
										</span>
									<?php
									$current_user_details['UserAvatar'] = $this->Html->getUserAvatar($this->Auth->user('id'));
									echo $this->Html->getUserAvatarLink($current_user_details, 'small_thumb');
                            else:?>
								<span class="<?php echo $reg_type_class; ?>">
									<?php echo $this->Html->getUserLink($current_user_details);?>
								</span>
                            <?php
							endif;
                        endif;
                    ?>

				<?php if($this->Auth->sessionValid()): ?>
					<?php if($this->Auth->user('fb_user_id') > 0): ?>
                        <?php echo $this->Html->link(__l('Logout'), $fb_logout_url, array('escape' => false,'class' => 'logout-link')); ?>
                    <?php else: ?>
                        <?php echo $this->Html->link(__l('Logout'), array('controller' => 'users', 'action' => 'logout'), array('class' => 'logout-link', 'title' => __l('Logout'))); ?>
                    <?php endif; ?>
				<?php endif; ?>
				</span>
            </p>
            <?php if($this->Auth->sessionValid()): ?>
              	<ul class="user-menu">

						<?php if($this->Auth->sessionValid()):?>
							<?php if($this->Auth->user('user_type_id') != ConstUserTypes::Company):?>
									<li <?php if($this->request->params['controller'] == 'users' && $this->request->params['action'] == 'my_stuff') { echo 'class="active"'; } ?>>
										<?php  echo $this->Html->link(__l('My Stuff'), array('controller' => 'users', 'action' => 'my_stuff'), array('title' => __l('My Stuff')));?>
									</li>
							<?php elseif($this->Auth->user('user_type_id') == ConstUserTypes::Company): ?>
									<?php if($this->Html->isAllowed($this->Auth->user('user_type_id'))): ?>
										<li <?php if($this->request->params['controller'] == 'users' && $this->request->params['action'] == 'my_stuff') { echo 'class="active"'; } ?>>
											<?php  echo $this->Html->link(__l('My Stuff'), array('controller' => 'users', 'action' => 'my_stuff'), array('title' => __l('My Stuff')));?>
										</li>
									<?php else: ?>
										<li <?php if($this->request->params['controller'] == 'transactions' && $this->request->params['action'] == 'index') { echo 'class="active"'; } ?>>
											<?php  echo $this->Html->link(__l('My Transactions'), array('controller' => 'transactions', 'action' => 'index'), array('title' => __l('Transactions')));?>
										</li>
										 <li <?php if($this->request->params['controller'] == 'companies' && $this->request->params['action'] == 'edit') { echo 'class="active"'; } ?>>
											 <?php echo $this->Html->link(__l('My Company'), array('controller' => 'companies', 'action' => 'edit',$company['Company']['id']), array('title' => __l('My Account'))); ?>
										 </li>
									<?php endif; ?>
								<?php if($this->Auth->user('user_type_id') == ConstUserTypes::Company && !empty($company['Company'])):?>
									<li <?php if($this->request->params['controller'] == 'deals' && $this->request->params['action'] == 'index' && !empty($this->request->params['named']['company'])) { echo 'class="active"'; } else { echo 'class=""';}?>><?php echo $this->Html->link(__l('My Deals'), array('controller' => 'deals', 'action' => 'index', 'company' => $company['Company']['slug'] ), array('title' => __l('My Deals')));?></li>
								<?php endif; ?>

								<li <?php if($this->request->params['controller'] == 'deals' && $this->request->params['action'] == 'add') { echo 'class="active"'; } ?>>
                                <?php echo $this->Html->link(__l('Add Deal'), array('controller' => 'deals', 'action' => 'add'), array('class'=>'add-deal', 'title' => __l('Add Deal')));?></li>
							<?php endif; ?>
						<?php endif; ?>
						<?php $url = Router::url(array('controller' => 'users', 'action' => 'my_stuff', 'admin' => false),true); ?>

              </ul>
            <?php endif; ?>
            </div>
          </div>
		     <!-- not in style-->
    <?php if($this->Auth->sessionValid() && $this->Auth->user('user_type_id') == ConstUserTypes::Admin): ?>
            <div class="admin-bar">
                <h3><?php echo __l('You are logged in as '); ?><?php echo $this->Html->link(__l('Admin'), array('controller' => 'users' , 'action' => 'stats' , 'admin' => true), array('title' => __l('Admin'))); ?></h3>
                <div><?php echo $this->Html->link(__l('Logout'), array('controller' => 'users' , 'action' => 'logout', 'admin' => true), array('title' => __l('Logout'))); ?></div>
            </div>
     <?php endif; ?>
 <!-- not in style end-->
 </div>
    </div>
        <div id="main" class="clearfix">
          <?php
				if ($this->Session->check('Message.error')):
        				echo $this->Session->flash('error');
        		endif;
        		if ($this->Session->check('Message.success')):
        				echo $this->Session->flash('success');
        		endif;
				if ($this->Session->check('Message.flash')):
						echo $this->Session->flash();
				endif;//view_compact
			?>
			<?php  if ($this->Session->check('Message.TransactionSuccessMessage')):?>
        			<div class="transaction-message info-details ">
						<?php echo $this->Session->read('Message.TransactionSuccessMessage');
							$this->Session->delete('Message.TransactionSuccessMessage');
						?>
					</div>
        	<?php  endif; ?>
			<?php if ($this->request->params['controller'] == 'topic_discussions' && ($this->request->params['action'] == 'index')):?>
				<?php echo $this->element("../deals/view_compact");?>
			<?php endif;?>
				<div class="side1">
					<div class="block1 round-10 maintance-block clearfix">
						<?php echo $content_for_layout;?>
					</div>
				</div>
				<div class="side2">
				<?php 
					if ($this->request->params['controller'] == 'topics' || $this->request->params['controller'] == 'topic_discussions'):
						if(Configure::read('twitter.is_twitter_feed_enabled')):
							echo strtr(Configure::read('twitter.tweets_around_city'),array(
								'##CITY_NAME##' => ucwords($city_name),
							)); 
						endif;
					endif;
				?>
				</div>
		</div>
		<div id="footer">
			<div class="footer-wrapper-inner">
				<div class="footer-section1">
					<h6><?php echo __l('Company'); ?></h6>
					<ul class="footer-nav">
						<li><?php echo $this->Html->link(__l('About'), array('controller' => 'pages', 'action' => 'view', 'about', 'admin' => false), array('title' => __l('About')));?> </li>
						<li><?php echo $this->Html->link(__l('Contact Us'), array('controller' => 'contacts', 'action' => 'add', 'admin' => false), array('title' => __l('Contact Us')));?></li>
						<li><?php echo $this->Html->link(__l('Terms & Policies'), array('controller' => 'pages', 'action' => 'view', 'term-and-conditions', 'admin' => false), array('title' => __l('Terms & Policies')));?></li>
					</ul>
				</div>
				<div class="footer-section2">
					<h6><?php echo __l('Learn More'); ?></h6>
					<?php $user_type = $this->Auth->user('user_type_id');?>
					<ul class="footer-nav">
						<li><?php echo $this->Html->link(__l('FAQ'), array('controller' => 'pages', 'action' => 'view', 'faq', 'admin' => false), array('title' => __l('FAQ')));?></li>
						<li><?php echo $this->Html->link(__l('API'), array('controller' => 'pages', 'action' => 'view', 'api', 'admin' => false), array('title' => __l('API')));?></li>
						<li><?php echo $this->Html->link(__l('Suggest a business'), array('controller' => 'business_suggestions', 'action' => 'add', 'admin' => false), array('title' => __l('Suggest a business'))); ?></li>
						<?php if(!$this->Auth->sessionValid()):
							$url = strstr($this->request->url,"/company/user/register");?>
							<li <?php if((!empty($url)) || ($this->request->params['controller'] == 'pages' && $this->request->params['action'] == 'view' &&  $this->request->params['pass'][0] == 'company')) { echo 'class="active"'; } else { echo 'class=""';}?>><?php echo $this->Html->link(Configure::read('site.name').' '.__l('for Your Business'), array('controller' => 'pages', 'action' => 'view', 'company', 'admin' => false), array('title' => Configure::read('site.name').' '.__l('for Your Business')));?></li>
						<?php endif; ?>
					</ul>
				</div>
				<div class="footer-section4">
					<h6><?php echo __l('Follow Us'); ?></h6>
					<ul class="footer-nav">
						<?php 
							if(!empty($city_slug)):
								$tmpURL= $this->Html->getCityTwitterFacebookURL($city_slug); 
							endif;						
						?>
						<li><a href="<?php echo !empty($tmpURL['City']['twitter_url']) ? $tmpURL['City']['twitter_url'] : Configure::read('twitter.site_twitter_url'); ?>" title="<?php echo __l('Follow Us in Twitter'); ?>" target="_blank"><?php echo __l('Twitter'); ?></a></li>
						<li><a href="<?php echo !empty($tmpURL['City']['facebook_url']) ? $tmpURL['City']['facebook_url'] : Configure::read('facebook.site_facebook_url'); ?>" title="<?php echo __l('See Our Profile in Facebook'); ?>" target="_blank"><?php echo __l('Facebook'); ?></a></li>
						<?php if($this->Html->isAllowed($this->Auth->user('user_type_id'))):?>
							<li><?php echo $this->Html->link(__l('Subscribe to Daily Email'), array('controller' => 'subscriptions', 'action' => 'add', 'admin' => false), array('title' => __l('Subscribe to Daily Email'))); ?></li>
						<?php endif;?>
						<li><?php echo $this->Html->link(__l('Topics'), array('controller' => 'topics', 'action' => 'index', 'admin' => false), array('title' => __l('Topics'))); ?></li>
					</ul>
				</div>
				<h6 class="logo"><?php echo $this->Html->link(Configure::read('site.name'), array('controller' => 'deals', 'action' => 'index', 'admin' => false), array('title' => Configure::read('site.name')))?></h6>
				<?php
					if (Configure::read('site.is_mobile_app')):
						$url = (env('HTTPS') )? 'https://m.' : 'http://m.';
						$url = $url . str_replace('www.', '', env('HTTP_HOST') . str_replace('/index.php', '', env('SCRIPT_NAME')));
						echo $this->Html->link(__l('Mobile/PDA Version'), $url, array('class' => 'mobile')); ?>
					endif;
				?>
				<p class="caption"><?php echo __l('Collective Buying Power');?></p>
			</div>
			<div id="agriya" class="clearfix">
				<p>&copy;<?php echo date('Y');?> <?php echo $this->Html->link(Configure::read('site.name'), Router::Url('/',true), array('title' => Configure::read('site.name'), 'escape' => false));?>. <?php echo __l('All rights reserved');?>.</p>
			</div>
		</div>
	</div>
	<?php echo $this->element('site_tracker', array('cache' => array('config' => 'site_element_cache'), 'plugin' => 'site_tracker')); ?>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
