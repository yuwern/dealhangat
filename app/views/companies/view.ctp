<?php /* SVN: $Id: view.ctp 54451 2011-05-24 12:26:17Z arovindhan_144at11 $ */ ?>
<div class="companies view">
    <h2><?php echo $this->Html->cText($company['Company']['name']);?></h2>
	<?php if (Configure::read('company.is_show_company_statistics')):?>
	<div class="main-content-block js-corner round-5 clearfix">
		<dl class="list statistics-list clearfix">
				<dt><?php echo __l('Deal Owned');?></dt>
				<dd>(<?php echo $this->Html->cInt($statistics['deal_created']);?>)</dd>
				<?php if (Configure::read('user.is_company_actas_normal_user')) {?>
					<?php if (Configure::read('company.is_show_referred_friends') && (Configure::read('referral.referral_enabled_option') == ConstReferralOption::GrouponLikeRefer)) {?>
					<dt><?php echo __l('Referred Users');?></dt>
						<dd>(<?php echo $this->Html->cInt($statistics['referred_users']);?>)</dd>
					<?php } ?>	
					<?php if (Configure::read('company.is_show_friend') && Configure::read('friend.is_enabled')) {?>
					<dt><?php echo __l('Friends');?></dt>
						<dd>(<?php echo $this->Html->cInt($statistics['user_friends']);?>)</dd>	
					<?php } ?>	
					<dt><?php echo __l('Deal Purchased');?></dt>
						<dd>(<?php echo $this->Html->cInt($statistics['deal_purchased']);?>)</dd>
					<dt><?php echo __l('Gift Sent');?></dt>
						<dd>(<?php echo $this->Html->cInt($statistics['gift_sent']);?>)</dd>
					<dt><?php echo __l('Gift Received');?></dt>
						<dd>(<?php echo $this->Html->cInt($statistics['gift_received']);?>)</dd>
				<?php } ?>
		</dl>
	</div>
<?php endif; ?>
    <div class="clearfix viewpage-content">
         <div class="clearfix">
			 <div class="user-avatar user-view-image">
			 <?php echo $this->Html->showImage('UserAvatar', $company['User']['UserAvatar'], array('dimension' => 'big_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($company['User']['username'], false)), 'title' => $this->Html->cText($company['User']['username'], false), 'escape' => false));?>
			 </div>
			 <div class="user-view-content">
				<dl class="list">
                    <?php if (Configure::read('Profile-is_show_address') && ($this->Html->checkForPrivacy('Profile-is_show_address', $this->Auth->user('id'), $company['Company']['user_id']) || $this->Auth->user('user_type_id') == ConstUserTypes::Admin)): ?>
    					<dt><?php echo __l('Address');?></dt>
    					<dd>
    						<address>
    							<?php echo $this->Html->cText($company['Company']['address1']);?>
    							<?php echo $this->Html->cText($company['City']['name']);?>
    							<?php echo $this->Html->cText($company['State']['name']);?>
    							<?php echo $this->Html->cText($company['Country']['name']);?>
    							<?php echo $this->Html->cText($company['Company']['zip']);?>
    						</address>
    					</dd>
                    <?php endif; ?>
				 <?php if(!empty($company['Company']['url'])): ?>
					<dt><?php echo __l('URL');?></dt>
					<dd>
						<a href="<?php echo $company['Company']['url'];?>" title="<?php echo $this->Html->cText($company['Company']['url'],false);?>" target="_blank"><?php echo $this->Html->cText($company['Company']['url'],false);?></a>
					</dd>
				<?php endif; ?>
				<?php if(!empty($company['Company']['phone'])): ?>
					<dt><?php echo __l('Phone');?></dt>
					<dd>
						<?php echo $this->Html->cText($company['Company']['phone']);?>
					</dd>
				<?php endif; ?>
				</dl>
				<p>
				<!--	
				-->
				</p>
			 </div>
		</div>
		<div class="clearfix company-profile-block">
		<?php if(!empty($company['Company']['company_profile'])): ?>
            <div class="company-description">
            <?php echo $this->Html->cHtml($company['Company']['company_profile']);?>
            </div>
		<?php endif; ?>
		<div class="clearfix">
		<div class="company-left-block">
		<?php if (Configure::read('Profile-is_show_address') && ($this->Html->checkForPrivacy('Profile-is_show_address', $this->Auth->user('id'), $company['Company']['user_id']) || $this->Auth->user('user_type_id') == ConstUserTypes::Admin)): ?>
    		<?php if(!empty($company['Company']['latitude']) and !empty($company['Company']['longitude'] )): ?>
				<?php $map_zoom_level = !empty($deal['Company']['map_zoom_level']) ? $deal['Company']['map_zoom_level'] : Configure::read('GoogleMap.static_map_zoom_level');?>
    			<?php if(env('HTTPS')) { ?>
						<a href="https://maps-api-ssl.google.com/maps?q=<?php echo $this->Html->url(array('controller' => 'companies', 'action' => 'view',$company['Company']['slug'],'ext' => 'kml'),true).'&amp;z='.$map_zoom_level ?>" title="<?php echo $company['Company']['name'] ?>" target="_blank">
				<?php } else { ?>
					<a href="http://maps.google.com/maps?q=<?php echo $this->Html->url(array('controller' => 'companies', 'action' => 'view',$company['Company']['slug'],'ext' => 'kml'),true).'&amp;z='.$map_zoom_level ?>" title="<?php echo $company['Company']['name'] ?>" target="_blank">
				<?php
					}
				?>	
    			<?php
    				$company['Company']['CompanyAddress'] = $company['CompanyAddress'];
					if(Configure::read('GoogleMap.embedd_map') == 'Static'):
						echo $this->Html->image($this->Html->formGooglemap($company['Company']));
					else:
						echo $this->Html->formGooglemap($company);
					endif;
    				?>
    			</a>
				<?php if(Configure::read('GoogleMap.embedd_map') != 'Static'):?>
				<small>
					<?php if(env('HTTPS')) { ?>
						<a href="https://maps-api-ssl.google.com/maps?q=<?php echo $this->Html->url(array('controller' => 'companies', 'action' => 'view',$company['Company']['slug'],'ext' => 'kml'),true).'&amp;z='.$map_zoom_level.'&amp;source=embed' ?>" title="<?php echo $company['Company']['name'] ?>" target="_blank" style="color:#0000FF;text-align:left"><?php echo __l('View Larger Map');?></a>
					<?php } else { ?>
						<a href="http://maps.google.com/maps?q=<?php echo $this->Html->url(array('controller' => 'companies', 'action' => 'view',$company['Company']['slug'],'ext' => 'kml'),true).'&amp;z='.$map_zoom_level.'&amp;source=embed' ?>" title="<?php echo $company['Company']['name'] ?>" target="_blank" style="color:#0000FF;text-align:left"><?php echo __l('View Larger Map');?></a>
					<?php
					}
				?>	
				</small>
			<?php endif;?>
    		<?php endif; ?>
        <?php endif; ?>
        </div>
        	<div class="company-right-block">
        <?php if (Configure::read('Profile-is_show_address') && ($this->Html->checkForPrivacy('Profile-is_show_address', $this->Auth->user('id'), $company['Company']['user_id']) || $this->Auth->user('user_type_id') == ConstUserTypes::Admin)): ?>
			<?php if(!empty($company['Company']['CompanyAddress'])):?>
				<h3><?php echo __l('Branch Address'); ?></h3>
				<div class="map-info clearfix">
					  <ol class="address-list clearfix">
					  <?php if(!empty($company['Company']['CompanyAddress'])):
							foreach($company['Company']['CompanyAddress'] as $address):
								?>
								<li class="vcard">
									  <address class="address<?php echo (!empty($count) ? $count : '');?>">
										  <span class="street-name"><?php echo $address['address1']. ' '.$address['address2']; ?></span><span><?php echo sprintf('%s %s %s', $this->Html->cText($address['City']['name']), $this->Html->cText($address['State']['name']), $this->Html->cText($address['Country']['name'])); ?></span><span><?php echo $address['zip']; ?></span>
									  </address>
									<span class="phone"><?php echo  !empty($address['phone'])? $this->Html->cText($address['phone']) : '&nbsp;';?></span>
									<span class="url"><?php echo  !empty($address['url'])? $this->Html->cText($address['url']) : '&nbsp;';?></span>
								</li>
					<?php  endforeach;
					  endif; ?>
					  </ol>
				</div>
			<?php endif; ?>
        <?php endif; ?>
        </div>
        </div>
        </div>
    </div>
	<div class="js-tabs">
        <ul class="clearfix">
            <?php if (Configure::read('user.is_company_actas_normal_user') && Configure::read('Profile-is_allow_comment_add') && $this->Html->isAllowed($this->Auth->user('user_type_id')) && ($this->Html->checkForPrivacy('Profile-is_allow_comment_add', $this->Auth->user('id'), $company['User']['id']))): ?>
				<li><?php echo $this->Html->link(__l('Comments'), '#tabs-1');?></li>
			<?php endif; ?>
        	<?php if (Configure::read('company.is_show_deal_owned')) :?>
                <li><?php echo $this->Html->link(__l('Deals Owned'), array('controller' => 'deals', 'action' => 'company_deals', 'company_id' =>  $company['Company']['id']),array('title' => __l('Deals Owned'))); ?></li>
            <?php endif; ?>
			<?php if (Configure::read('user.is_company_actas_normal_user')) {?>
				<?php if (Configure::read('company.is_show_deal_purchased')) {?>
					<li><?php echo $this->Html->link(__l('Deals Purchased'), array('controller' => 'deal_users', 'action' => 'user_deals', 'user_id' =>$company['Company']['user_id']),array('title' => __l('Deals Purchased'))); ?></li>
				<?php } ?>
				
				<?php if (Configure::read('company.is_show_friend') && Configure::read('friend.is_enabled') && $this->Auth->user('id') ) {?>
					<li><?php echo $this->Html->link(__l('Friends'), array('controller' => 'user_friends', 'action' => 'myfriends', 'user_id' =>$company['Company']['user_id']),array('title' => __l('Friends'))); ?></li>
				<?php } ?>
				
				<?php if (Configure::read('company.is_show_referred_friends') && (Configure::read('referral.referral_enabled_option') == ConstReferralOption::GrouponLikeRefer)) {?>
					<li><?php echo $this->Html->link(__l('Referred Users'), array('controller' => 'users', 'action' => 'referred_users', 'user_id' =>$company['Company']['user_id']),array('title' => __l('Referred Users'))); ?></li>
				<?php } ?>
			<?php } ?>
        </ul>
        <?php if (Configure::read('user.is_company_actas_normal_user') && Configure::read('Profile-is_allow_comment_add') && $this->Html->isAllowed($this->Auth->user('user_type_id')) && $this->Html->checkForPrivacy('Profile-is_allow_comment_add', $this->Auth->user('id'), $company['User']['id'])): ?>
			<div id='tabs-1'>
				<div class="main-content-block js-corner round-5">
					<div class="js-responses">
						<?php echo $this->element('user_comments-index', array('username' => $company['User']['username'], 'cache' => array('config' => 'site_element_cache'), 'key' => $company['User']['username']));?>
					</div>
				</div>
                <?php if($this->Auth->user('id') and $this->Auth->user('id')!= $company['User']['id']): ?>
                    <div class="main-content-block js-corner round-5">
                        <h2><?php echo __l('Add Your comments'); ?></h2>
                        <?php echo $this->element('../user_comments/add');?>
                    </div>
                <?php endif; ?>
			</div>
		<?php endif; ?>
    </div>
</div>

