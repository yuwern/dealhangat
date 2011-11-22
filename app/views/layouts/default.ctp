<?php
/* SVN FILE: $Id: default.ctp 59854 2011-07-11 09:23:11Z mohanraj_109at09 $ */
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
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<?php echo $this->Html->charset(), "\n";?>
	<title><?php echo Configure::read('site.name');?> | <?php echo $this->Html->cText($title_for_layout, false);?></title>
	<?php
		echo $this->Html->meta('icon'), "\n";
		echo $this->Html->meta('keywords', $meta_for_layout['keywords']), "\n";
		echo $this->Html->meta('description', $meta_for_layout['description']), "\n";
	?>
	<link href="<?php echo Router::url('/', true) . $this->request->params['named']['city'] .'.rss';?>" type="application/rss+xml" rel="alternate" title="RSS Feeds" target="_blank" />
	<?php
		require_once('_head.inc.ctp');
		echo $this->Asset->scripts_for_layout();
	// For other than Facebook (facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)), wrap it in comments for XHTML validation...
if (strpos(env('HTTP_USER_AGENT'), 'facebookexternalhit')===false):
    echo '<!--', "\n";
endif;
    ?>
	<meta content="<?php echo Configure::read('facebook.app_id');?>" property="og:app_id" />
	<meta content="<?php echo Configure::read('facebook.app_id');?>" property="fb:app_id" />
	<?php if(!empty($meta_for_layout['deal_name'])):?>
		<meta property="og:site_name" content="<?php echo Configure::read('site.name'); ?>"/>
		<meta property='og:title' content='<?php echo $meta_for_layout['deal_name'];?>'/>
	<?php endif;?>
	<?php if(!empty($meta_for_layout['deal_image'])):?>
		<meta property="og:image" content="<?php echo $meta_for_layout['deal_image'];?>"/>
	<?php else:?>
		<meta property="og:image" content="<?php echo Router::url(array(
				'controller' => 'img',
				'action' => 'blue-theme',
				'logo-email.png',
				'admin' => false
			) , true);?>"/>
	<?php endif;?>
	<?php
if (strpos(env('HTTP_USER_AGENT'), 'facebookexternalhit')===false):
    echo '-->', "\n";
endif;
// <--
?>
</head>

<?php	
	$align = '';
	$city_bgcolor = '';
	$bg_color = (!empty($bgcolor))? $bgcolor :'';
	$height = Configure::read('thumb_size.city_background_thumb.height');
	$width = Configure::read('thumb_size.city_background_thumb.width');
	if(!empty($height) && !empty($width))
	{
		$image_options = array(
                    'dimension' => 'city_background_thumb',
                    'class' => '',
                    'type' => 'jpg'
                );	
	} else {
		$image_options = array(
                    'dimension' => 'original',
                    'class' => '',
                    'type' => 'jpg'
                );	
	}
	if (!empty($city_attachment['id']) && empty($this->request->params['requested']) && $this->request->params['controller'] != 'images' && empty($_SESSION['city_attachment'])):
		$_SESSION['city_attachment'] =  $this->Html->url($this->Html->getImageUrl('City', $city_attachment, $image_options));
		
		if($is_bg_image_center):
			$align = 'no-repeat center top';
		else:
			$align = 'repeat fixed left top';
		endif;
		$city_bgcolor = '';
	elseif(!empty($bgcolor)):
		$city_bgcolor = $bg_color;		
	endif; 	
	$bgattachment =  !empty($_SESSION['city_attachment']) ? 'background:url('.$_SESSION['city_attachment'].') '.$align.'':''.';'; 
	$bgcolor =	 !empty($city_bgcolor) ? 'background-color:#'.$city_bgcolor.'':''.';';	 
?>


<body>

<div id="container">
  
  <!-- FACEBOOK PLUGIN -->
  <div id="fb-root"></div>
  <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=147267432014750";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));</script>
  <!-- FACEBOOK PLUGIN -->  
  
  
	<!-- DROPDOWN CITY SELECTOR -->  
	<div class="top-slider1 js-morecities1 hide">
	<div class="cities-index-block">
		<?php 
			echo $this->element('cities-index', array('cache' => array('key' => $city_id, 'config' => 'site_element_cache_20_min')));
        ?>
    </div>
    </div>
	<!-- DROPDOWN CITY SELECTOR -->    
    
	<!-- DROPDOWN MAILING LIST -->  
	<div class="top-slider1 js-show-subscription hide">
	<div class="clearfix header-subscription-block">
	<?php if($this->Html->isAllowed($this->Auth->user('user_type_id')) && $this->request->params['controller'] != 'subscriptions'): ?>
        <div class="header-subscription-left-block">
          <?php echo $this->element('../subscriptions/add', array('step' => 1, 'cache' => array('config' => 'site_element_cache', 'key' => $city_slug)));?>
        </div>
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
	</div>
	<?php
		if($this->Auth->sessionValid()  and  $this->Auth->user('user_type_id') == ConstUserTypes::Company):
				$company = $this->Html->getCompany($this->Auth->user('id'));
		endif;
	?>
	</div>
	<!-- DROPDOWN MAILING LIST -->  
	
	
	<!-- WRAPPER -->
	<div id="wrapper">
	
	<!-- HEADER WRAPPER -->
	<div id="header-wrapper">
	
	<!-- BODY CONTENT --> 	
	<div id="<?php echo $this->Html->getUniquePageId();?>" class="content">


	<!-- HEADER BEGINS -->
   	<div id="header">
    <div id="header-content">
            
      <div class="clearfix">
        <h1>
        	<?php echo $this->Html->link(Configure::read('site.name'), array('controller' => 'deals', 'action' => 'index', 'admin' => false), array('title' => Configure::read('site.name'))); ?>
		</h1>
        <p class="hidden-info"><?php echo __l('Collective Buying Power');?></p>
        
        
        <?php if($this->Auth->sessionValid() && $this->Auth->user('user_type_id') == ConstUserTypes::Admin): ?>
            <div class="admin-bar">
                <h3><?php echo __l('You are logged in as '); ?><?php echo $this->Html->link(__l('Admin'), array('controller' => 'users' , 'action' => 'stats' , 'admin' => true), array('title' => __l('Admin'))); ?></h3>
                <div><?php echo $this->Html->link(__l('Logout'), array('controller' => 'users' , 'action' => 'logout', 'admin' => true), array('title' => __l('Logout'))); ?></div>
            </div>
     <?php endif; ?>
     <!-- header right -->
        <div class="header-r">
          <div class="clearfix">
            <div class="global-block">
            
                 <ul class="global-links-r">
                    <li><?php echo $this->element('lanaguge-change-block'); ?></li>

                    <!-- Referral benefits -->
					<?php if(Configure::read('referral.referral_enabled_option') == ConstReferralOption::GrouponLikeRefer):?>
						<?php $class = ($this->request->params['controller'] == 'pages') && ($this->request->params['pass'][0] == 'refer_a_friend') ? ' class="active referral"' : ' class="referral"'; ?>
                          <li <?php echo $class;?>><?php echo $this->Html->link(__l('Refer Friends, Get').' '.$this->Html->siteCurrencyFormat($this->Html->cCurrency(Configure::read('user.referral_amount'), false)), array('controller' => 'pages', 'action' => 'refer_a_friend'), array('title' => __l('Refer Friends, Get').' '. $this->Html->siteCurrencyFormat($this->Html->cCurrency(Configure::read('user.referral_amount'), false))));?></li>
					<?php elseif(Configure::read('referral.referral_enabled_option') == ConstReferralOption::XRefer):?>
							<?php 
								if(Configure::read('referral.refund_type') == ConstReferralRefundType::RefundDealAmount):
									$refund_type = __l('Get a Free Deal!!!');
								else:
									$refund_type = __l('Get').' '.$this->Html->siteCurrencyFormat($this->Html->cCurrency(Configure::read('referral.refund_amount'), false)).' '.__l('');
								endif;
								$msg = __l('Refer').' '.Configure::read('referral.no_of_refer_to_get_a_refund').' '.__l('Friends').', '.$refund_type;
							?>
						<?php $class = ($this->request->params['controller'] == 'pages')  && ($this->request->params['pass'][0] == 'refer_friend') ? ' class="active"' : null; ?>
                          <li <?php echo $class;?>><?php echo $this->Html->link($msg, array('controller' => 'pages', 'action' => 'refer_friend'), array('title' => $msg));?></li>
					<?php endif; ?>
					<?php $class = ($this->request->params['controller'] == 'contacts' && $this->request->params['action'] == 'add') ? ' class="active"' : null; ?>
                    <!-- <li <?php echo $class;?>><?php echo $this->Html->link(__l('Contact Us'), array('controller' => 'contacts', 'action' => 'add', 'admin' => false), array('title' => __l('Contact Us')));?></li> -->
              </ul>  
			<!-- global links right --> 
              
             </div>
          </div>
          <div class="city-block clearfix">
           	<?php if($this->Html->isAllowed($this->Auth->user('user_type_id')) && $this->request->params['controller'] != 'subscriptions'): ?>
          <?php echo $this->element('../subscriptions/add', array('step' => 2,'cache' => array('config' => 'site_element_cache')));?>
        <?php endif; ?>
            <div class="city-desc-block clearfix">
              <?php if(!empty($city_name)): ?>
                        <h2><?php echo __l("Today's Best Deals in"); ?></h2>
                        <?php echo $this->Html->link($this->Html->cText($city_name), '#', array('title' => $this->Html->cText($city_name, false),'class' => "city-name js-toggle-show {'container':'js-morecities1'}", 'escape' => false)); ?>
              <?php endif;?>
            </div>
          </div>
        </div>
     <!-- header right end -->   
          
      </div>
      
      <div class="clearfix">
      	            <dl class="total-list clearfix">
                <?php if($this->Auth->sessionValid() && $this->Html->isWalletEnabled('is_enable_for_add_to_wallet')): ?>
                <dt><?php echo __l('Balance: '); ?></dt>
                	<dd><span><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($user_available_balance)); ?></span></dd>
                <?php endif; ?>
            </dl>
      </div>
      <!-- change language end -->         
               
            <?php if($this->Auth->sessionValid() && $this->Html->isWalletEnabled('is_enable_for_add_to_wallet')): ?>
         
                      <div class=" clearfix">
                      <div class="add-amount">
            	<?php if ((Configure::read('company.is_user_can_withdraw_amount') && $this->Auth->user('user_type_id') == ConstUserTypes::Company) || (Configure::read('user.is_user_can_with_draw_amount') && $this->Auth->user('user_type_id') == ConstUserTypes::User)) { ?>
                   <?php $class = ($this->request->params['controller'] == 'user_cash_withdrawals' && $this->request->params['action'] == 'user_cash_withdrawals') ? " "."active" : null; ?>
                	<?php echo $this->Html->link(__l('Withdraw Fund Request'), array('controller' => 'user_cash_withdrawals', 'action' => 'index'), array('title' => __l('Withdraw Fund Request'),'class'=>'width-draw'.$class));?>
				<?php } ?>
				<?php if($this->Html->isAllowed($this->Auth->user('user_type_id'))): ?>
                   <?php $class = ($this->request->params['controller'] == 'users' && $this->request->params['action'] == 'add_to_wallet') ? " "."active" : null; ?>
				<?php echo $this->Html->link(__l('Add Amount to Wallet'), array('controller' => 'users', 'action' => 'add_to_wallet'), array('class' => 'add add-wallet'.$class, 'title' => __l('Add amount to wallet'))); ?>
                <?php endif; ?>
				<?php if(Configure::read('affiliate.is_enabled') && $this->Auth->user('is_affiliate_user')):?>
                    <?php $class = ($this->request->params['controller'] == 'affiliates' && $this->request->params['action'] == 'index') ? " "."active" : null; ?>
			 <?php echo $this->Html->link(__l('Affiliate Commission History'), array('controller' => 'affiliates', 'action' => 'index'), array('class' => 'affiliate-history'.$class,'title' => __l('Affiliate Commission History')));?>
				<?php endif;?>
            </div>   </div>
             <?php endif; ?>
	
        
      
      <div class="menu-block clearfix">
        <ul class="menu clearfix">
        	<?php if($this->Html->isAllowed($this->Auth->user('user_type_id'))): ?>
                <li <?php if($this->request->params['controller'] == 'deals' && $this->request->params['action'] == 'index' && !isset($this->request->params['named']['type']) && !isset($this->request->params['named']['company'])) { echo 'class="active"'; } ?>><?php echo $this->Html->link(__l('Today\'s Deals'), array('controller' => 'deals', 'action' => 'index', 'admin' => false), array('title' => __l('Today\'s Deals')));?></li>
                <li <?php if($this->request->params['controller'] == 'deals' && (isset($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'recent')) { echo 'class="active"'; } else { echo 'class=""';}?>><?php echo $this->Html->link(__l('Recent Deals'), array('controller' => 'deals', 'action' => 'index', 'admin' => false,'type' => 'recent'), array('title' => __l('Recent Deals')));?></li>
               <?php endif; ?>
				<li <?php if($this->request->params['controller'] == 'topics' or $this->request->params['controller'] == 'topic_discussions' ) { echo 'class="active"'; } else { echo 'class=""';}?>><?php echo $this->Html->link(__l('Discussion'), array('controller' => 'topics', 'action' => 'index', 'admin' => false), array('title' => __l('Discussion')));?></li>

				<li <?php if($this->request->params['controller'] == 'pages' && $this->request->params['action'] == 'view'  && $this->request->params['pass'][0] == 'how_it_works') { echo 'class="active"'; } ?>><?php echo $this->Html->link(sprintf(__l('How It Works')), array('controller' => 'pages', 'action' => 'view', 'how_it_works', 'admin' => false), array('title' => sprintf(__l('How It Works'))));?></li>

				<?php if(!$this->Auth->sessionValid()):
				$url = strstr($this->request->url,"/company/user/register");?>
					<li <?php if((!empty($url)) || ($this->request->params['controller'] == 'pages' && $this->request->params['action'] == 'view' &&  $this->request->params['pass'][0] == 'company')) { echo 'class="active"'; } else { echo 'class=""';}?>><?php echo $this->Html->link(__l('Business'), array('controller' => 'pages', 'action' => 'view', 'company', 'admin' => false), array('title' => __l('Business')));?></li>
				<?php endif; ?>
				    <?php if($this->Auth->sessionValid()): ?>
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
                       <li <?php if($this->request->params['controller'] == 'companies' && $this->request->params['action'] == 'dashboard') { echo 'class="active"'; } ?>>
							<?php  echo $this->Html->link(__l('Dashboard'), array('controller' => 'companies', 'action' => 'dashboard'), array('title' => __l('Dashboard')));?>
						</li>
						<li <?php if($this->request->params['controller'] == 'transactions' && $this->request->params['action'] == 'index') { echo 'class="active"'; } ?>>
							<?php  echo $this->Html->link(__l('My Transactions'), array('controller' => 'transactions', 'action' => 'index'), array('title' => __l('My Transactions')));?>
						</li>
						 <li <?php if($this->request->params['controller'] == 'companies' && $this->request->params['action'] == 'edit') { echo 'class="active"'; } ?>>
							 <?php echo $this->Html->link(__l('My Company'), array('controller' => 'companies', 'action' => 'edit',$company['Company']['id']), array('title' => __l('My Company'))); ?>
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


            <?php endif; ?>
        </ul>
        <div class="menu-right">
        	<?php
				$reg_type_class='normal';
			
          if (!$this->Auth->sessionValid()): ?>

			  <div class="openid-block clearfix">

              <!-- <h5><?php echo __l('Sign In using: '); ?></h5> -->

              <ul class="open-id-list">
                <li class="face-book">
				<?php if(Configure::read('facebook.is_enabled_facebook_connect') && !empty($fb_login_url)):  ?>
						<?php echo $this->Html->link(__l('Sign in with Facebook'), array('controller' => 'users', 'action' => 'login','type'=>'facebook'), array('title' => __l('Sign in with Facebook'), 'escape' => false)); ?>
					 <?php endif; ?>
				</li>
				<?php if(Configure::read('twitter.is_enabled_twitter_connect')):?>
					<li class="twiiter"><?php echo $this->Html->link(__l('Sign in with Twitter'), array('controller' => 'users', 'action' => 'login',  'type'=> 'twitter', 'admin'=>false), array('class' => 'Twitter', 'title' => __l('Sign in with Twitter')));?></li>
				<?php endif;?>
				<?php if(Configure::read('foursquare.is_enabled_foursquare_connect')):?>
					<li class="foursquare"><?php echo $this->Html->link(__l('Sign in with Foursquare'), array('controller' => 'users', 'action' => 'login',  'type'=> 'foursquare', 'admin'=>false), array('class' => 'Foursquare', 'title' => __l('Sign in with Foursquare')));?></li>
				<?php endif;?>
                <?php if(Configure::read('user.is_enable_yahoo_openid')):?>
				<li class="yahoo"><?php echo $this->Html->link(__l('Sign in with Yahoo'), array('controller' => 'users', 'action' => 'login', 'type'=>'yahoo'), array('title' => __l('Sign in with Yahoo')));?></li>
				<?php endif;?>
				<?php if(Configure::read('user.is_enable_gmail_openid')):?>
				<li class="gmail"><?php echo $this->Html->link(__l('Sign in with Gmail'), array('controller' => 'users', 'action' => 'login', 'type'=>'gmail'), array('title' => __l('Sign in with Gmail')));?></li>
				<?php endif;?>
				<?php if(Configure::read('user.is_enable_openid')):?>
				<li class="open-id"><?php 	echo $this->Html->link(__l('Sign in with OpenID'), array('controller' => 'users', 'action' => 'login','type'=>'openid'), array('class'=>'','title' => __l('Sign in with OpenID')));?></li>
				<?php endif;?>
              </ul>
              
			  </div>


              <ul class="menu-link clearfix">
                <li <?php if($this->request->params['controller'] == 'users' && $this->request->params['action'] == 'register') { echo 'class="active"'; } ?> ><?php echo $this->Html->link(__l('Join us'), array('controller' => 'users', 'action' => 'register', 'admin' => false), array('title' => __l('Join us'),'class'=>'login-link'));?></li>
                <li <?php if($this->request->params['controller'] == 'users' && $this->request->params['action'] == 'login') { echo 'class="active"'; } ?>><?php echo $this->Html->link(__l('Sign in'), array('controller' => 'users', 'action' => 'login'), array('title' => __l('Sign in'),'class'=>'login-link'));?></li>
              </ul>
			  

			  
			  
               <?php endif; ?>
            <?php if ($this->Auth->sessionValid()): ?>
              <p class="user-login-info">
                    <?php
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
                         
                            echo __l('Hi, '); ?>
    							<span class="<?php echo $reg_type_class; ?>">
    								<?php echo $this->Html->getUserLink($current_user_details);?>
    							</span>
    						<?php
    						$current_user_details['UserAvatar'] = $this->Html->getUserAvatar($this->Auth->user('id'));
    						echo $this->Html->getUserAvatarLink($current_user_details, 'small_thumb'); ?>

                            <?php
						
                        endif;
                    ?>

				<?php if($this->Auth->sessionValid()): ?>
                    <?php echo $this->Html->link(__l('Logout'), array('controller' => 'users', 'action' => 'logout'), array('class' => 'logout-link', 'title' => __l('Logout'))); ?>
                </p>
			   <?php endif; ?>
        </div>
      </div>

    </div>
  </div>  
  	<!-- HEADER -->

  
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
				endif;
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
			<?php if (!($this->request->params['controller'] == 'deals' && ($this->request->params['action'] == 'view' || ($this->request->params['action'] == 'index' && empty($this->request->params['named']['company']))))): ?>
				<div class="side1">
    			    <div class="side1-tl">
                        <div class="side1-tr">
                          <div class="side1-tm"> </div>
                        </div>
                     </div>
                     <div class="side1-cl">
                        <div class="side1-cr">
                            <div class="block1-inner">
                    			<?php endif; ?>
                    			<?php echo $content_for_layout;?>
                    				<?php if (!($this->request->params['controller'] == 'deals' && ($this->request->params['action'] == 'view' || ($this->request->params['action'] == 'index' && empty($this->request->params['named']['company']))))): ?>
            				</div>
            				</div>
        				</div>
        				
                        <div class="side1-bl">
                            <div class="side1-br">
                              <div class="side1-bm"> </div>
                            </div>
                      </div>
				</div>
			<?php endif; ?>
			
				<?php
					if ($this->request->params['controller'] == 'topics' || $this->request->params['controller'] == 'topic_discussions'):
                    ?>
                    	<div class="side2">
                        <div class="blue-bg1 deal-blue-bg clearfix">
                              <div class="tweet-tl">
                                <div class="tweet-tr">
                                  <div class="tweet-tm">
                                    <h3>tweets around</h3>
                                  </div>
                                </div>
                              </div>
                              <div class="side1-cl">
                                <div class="side1-cr">
                                  <div class="block1-inner blue-bg-inner clearfix">
                                  <?php	if(Configure::read('twitter.is_twitter_feed_enabled')):
                        				echo strtr(Configure::read('twitter.tweets_around_city'),array(
                        					'##CITY_NAME##' => ucwords($city_name),
                        				));
                        			endif;
                                    ?>
                                  </div>
                                </div>
                              </div>
                              <div class="side1-bl">
                                <div class="side1-br">
                                  <div class="side1-bm"> </div>
                                </div>
                              </div>
                        </div>

                    </div>
                <?php	endif;
				?>
				<?php if ($this->request->params['controller'] == 'deals' && $this->request->params['action'] == 'buy'):?>
				<div class="side2">
					 <div class="side1-tl">
                        <div class="side1-tr">
                          <div class="side1-tm"> </div>
                        </div>
                     </div>
                     <div class="side1-cl">
                      <div class="side1-cr">
                         <div class="block1-inner">
							<?php echo $this->element('deal-faq', array('cache' => array('config' => 'site_element_cache')));?>
                         </div>
                		</div>
            			</div>
                        <div class="side1-bl">
                            <div class="side1-br">
                              <div class="side1-bm"> </div>
                            </div>
                      </div>
				</div>
				<?php endif;?>
			
				</div>

<div id="footer">
 
    <!-- <div class="footer-tl">
      <div class="footer-tr">
        <div class="footer-tm"> </div>
      </div>
    </div>
    
    <div class="footer-cl">
      <div class="footer-cr">
      -->
      
        <div class="footer-inner clearfix">
          <div class="footer-wrapper-inner clearfix">
          
            <div class="footer-section1">
              <!-- <div class="footer-left">
                <div class="footer-right"> -->
                 <h6><?php echo __l('Company'); ?></h6>
                <!-- </div>
              </div> -->
              <ul class="footer-nav">
                <?php $class = ($this->request->params['controller'] == 'pages' && $this->request->params['action'] == 'view' && $this->request->params['pass'][0] == 'about') ? ' class="active"' : null; ?>
               	<li <?php echo $class;?>><?php echo $this->Html->link(__l('About'), array('controller' => 'pages', 'action' => 'view', 'about', 'admin' => false), array('title' => __l('About')));?> </li>
                <?php $class = ($this->request->params['controller'] == 'contacts' && $this->request->params['action'] == 'add') ? ' class="active"' : null; ?>
				<li <?php echo $class;?>><?php echo $this->Html->link(__l('Contact Us'), array('controller' => 'contacts', 'action' => 'add', 'admin' => false), array('title' => __l('Contact Us')));?></li>
                <?php $class = ($this->request->params['controller'] == 'pages' && $this->request->params['action'] == 'view' && $this->request->params['pass'][0] == 'term-and-conditions') ? ' class="active"' : null; ?>
				<li <?php echo $class;?>><?php echo $this->Html->link(__l('Terms & Conditions'), array('controller' => 'pages', 'action' => 'view', 'term-and-conditions', 'admin' => false), array('title' => __l('Terms & Conditions')));?></li>
                 <?php $class = ($this->request->params['controller'] == 'pages' && $this->request->params['action'] == 'view' && $this->request->params['pass'][0] == 'privacy_policy') ? ' class="active"' : null; ?>
				<li <?php echo $class;?>><?php echo $this->Html->link(__l('Privacy Policy'), array('controller' => 'pages', 'action' => 'view', 'privacy_policy', 'admin' => false), array('title' => __l('Privacy Policy')));?></li>

              </ul>
            </div>
            <div class="footer-section2">
              <div class="footer-left">
                <div class="footer-right">
                 	<h6><?php echo __l('Learn More'); ?></h6>
                </div>
              </div>
              	<?php $user_type = $this->Auth->user('user_type_id');?>
    			 <ul class="footer-nav">
                  <?php $class = ($this->request->params['controller'] == 'pages' && $this->request->params['action'] == 'view' && $this->request->params['pass'][0] == 'faq') ? ' class="active"' : null; ?>
    				<li <?php echo $class;?>><?php echo $this->Html->link(__l('FAQ'), array('controller' => 'pages', 'action' => 'view', 'faq', 'admin' => false), array('title' => __l('FAQ')));?></li>
    			  <?php $class = ($this->request->params['controller'] == 'business_suggestions' && $this->request->params['action'] == 'add') ? ' class="active"' : null; ?>
                	<li <?php echo $class;?>><?php echo $this->Html->link(__l('Suggest a business'), array('controller' => 'business_suggestions', 'action' => 'add', 'admin' => false), array('title' => __l('Suggest a business'))); ?></li>
    				<?php if(!$this->Auth->sessionValid()):
    					$url = strstr($this->request->url,"/company/user/register");?>
    					<li <?php if((!empty($url)) || ($this->request->params['controller'] == 'pages' && $this->request->params['action'] == 'view' &&  $this->request->params['pass'][0] == 'company')) { echo 'class="active"'; } else { echo 'class=""';}?>><?php echo $this->Html->link(Configure::read('site.name').' '.__l('for Your Business'), array('controller' => 'pages', 'action' => 'view', 'company', 'admin' => false), array('title' => Configure::read('site.name').' '.__l('for Your Business')));?></li>
    				<?php endif; ?>
					<?php if(Configure::read('affiliate.is_enabled')):?>
						<?php $class = ($this->request->params['controller'] == 'affiliates') ? ' class="active"' : null; ?>
						<li <?php echo $class;?>><?php echo $this->Html->link(__l('Affiliates'), array('controller' => 'affiliates', 'action' => 'index'),array('title' => __l('Affiliates'))); ?></li>
					<?php endif; ?>
                    <?php 
					if($this->Auth->sessionValid() && $this->Auth->user('user_type_id') != ConstUserTypes::Company):
					$class = ($this->request->params['controller'] == 'subscriptions' && $this->request->params['action'] == 'manage_subscription') ? ' class="active"' : null; ?>
                    <li <?php echo $class;?>><?php echo $this->Html->link(__l('Manage Subscriptions'), array('controller' => 'subscriptions', 'action' => 'manage_subscription'),array('title' => __l('Manage Subscriptions'))); ?></li>
                    <?php endif; ?>
    			</ul>
            </div>
            <div class="footer-section3">
              <div class="footer-left">
                <div class="footer-right">
                	<h6><?php echo __l('Follow Us'); ?></h6>
                </div>
              </div>
              <ul class="footer-nav">
                 	<?php
    					if(!empty($city_slug)):
    						$tmpURL= $this->Html->getCityTwitterFacebookURL($city_slug);
    					endif;
    				?>

    				<li class="face2"><a href="<?php echo !empty($tmpURL['City']['facebook_url']) ? $tmpURL['City']['facebook_url'] : '#'; ?>" title="<?php echo __l('See Our Profile in Facebook'); ?>" target="_blank"><?php echo __l('Facebook'); ?></a></li>
                    <li class="tweet2"><a href="<?php echo !empty($tmpURL['City']['twitter_url']) ? $tmpURL['City']['twitter_url'] : '#'; ?>" title="<?php echo __l('Follow Us in Twitter'); ?>" target="_blank"><?php echo __l('Twitter'); ?></a></li>
                	<?php if($this->Html->isAllowed($this->Auth->user('user_type_id'))):?>
    				<li class="mail2"><?php echo $this->Html->link(__l('Email'), array('controller' => 'subscriptions', 'action' => 'add', 'admin' => false), array('title' => __l('Email'))); ?></li>
    				<?php endif;?>
    					<?php
                    $cityArray = array();
					if(!empty($city_slug)):
						$tmpURL= $this->Html->getCityTwitterFacebookURL($city_slug);
						$cityArray = array('city'=>$city_slug);
					endif;
				?>
    			     <li class="rss2"><?php echo $this->Html->link(__l('RSS'), array_merge(array('controller'=>'deals', 'action'=>'index', 'ext'=>'rss'), $cityArray), array('target' => '_blank','title'=>__l('RSS Feed'))); ?></li>
               </ul>
            </div>
            
            <!-- FIX -->
            <?php if(Configure::read('site.is_touch_app') or Configure::read('site.is_mobile_app')): ?>
            <div class="mobile-left-block">

              <div class="clearfix">
			  <?php
				if (Configure::read('site.is_touch_app')): ?>
                <div class="mobile-left">
                 <div class="mobile-right">
                    <?php 
						$url = (env('HTTPS') )? 'https://touch.' : 'http://touch.';
						$url = $url . str_replace('www.', '', env('HTTP_HOST') . str_replace('/index.php', '', env('SCRIPT_NAME')));
						echo $this->Html->link(__l('Mobile Touch Version'), $url, array('class' => 'touch-mobile')); ?>
                </div>
              </div>
			<?php endif; ?>
            	<?php
				if (Configure::read('site.is_mobile_app')): ?>
                <div class="mobile-left">
                 <div class="mobile-right">
                    <?php 
						$url = (env('HTTPS') )? 'https://m.' : 'http://m.';
						$url = $url . str_replace('www.', '', env('HTTP_HOST') . str_replace('/index.php', '', env('SCRIPT_NAME')));
						echo $this->Html->link(__l('Mobile/PDA Version'), $url, array('class' => 'mobile')); ?>
                </div>
              </div>
			<?php endif; ?>
			</div>
			</div>
            <?php endif; ?>
			<!-- END OF FIX -->
			
          </div>
          <div id="agriya" class="clearfix">
			<p class="copy">&copy; 2011 Tawaran Hangat Sdn. Bhd. Semua Hak Cipta Terpelihara.</p>          
          	<!-- <p class="copy">&copy;<?php echo date('Y');?> <?php echo $this->Html->link(Configure::read('site.name'), Router::Url('/',true), array('title' => Configure::read('site.name'), 'escape' => false));?>. <?php echo __l('All rights reserved');?>.</p> -->
          </div>
        </div>
      
    <!--  </div>
    </div>
    footer-cl -->
    
  </div>
<!-- footer ends --> 
  
</div>
	<!-- BODY CONTENT ENDS -->

	</div>
	<!-- HEADER WRAPPER ENDS -->

	</div>
	<!-- WRAPPER ENDS -->

</div>
<!-- CONTAINER ENDS -->

	<?php echo $this->element('site_tracker', array('cache' => array('config' => 'site_element_cache'), 'plugin' => 'site_tracker')); ?>
	<?php echo $this->element('sql_dump'); ?>

</body>
</html>
