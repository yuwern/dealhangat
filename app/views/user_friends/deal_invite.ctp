<div class="invite_friends">
<h2><?php echo __l('Invite your friends for the deal').' - '.$deal['Deal']['name'];?></h2>
	<div class="js-tabs">
		<ul class="clearfix">			
			<li><?php echo $this->Html->link(__l('Share Via Social Media'), '#js-share-via-facebook'); ?></li>
            <!-- <li><?php echo $this->Html->link(__l('Import Friends'), array(
        'controller' => 'user_friends',
        'action' => 'import',
        'type' => 'deal',
        'deal' => $deal_slug
      )); ?></li> -->
			<li><?php echo $this->Html->link(__l('Invite to Your Friends'), array(
				'controller' => 'user_friends',
				'action' => 'myfriends',
				'type' => 'deal',
				'deal' => $deal_slug
			)); ?></li>
		</ul>
	<div id="js-share-via-facebook">
		<div class="clearfix">
			<ul class="share-list">
                <?php 
				/**************Get bitly url from city_deals******************/
					foreach($deal['City'] as $deal_city)
					{
						if($deal_city['slug'] == $city_slug)
						{
							if(Configure::read('site.city_url') == 'prefix'):
								$bityurl = $deal_city['CitiesDeal']['bitly_short_url_prefix'];
							else:
								$bityurl = $deal_city['CitiesDeal']['bitly_short_url_subdomain'];
							endif;
						}
					}
					if(Configure::read('referral.referral_enabled_option') == ConstReferralOption::GrouponLikeRefer || Configure::read('referral.referral_enabled_option') == ConstReferralOption::XRefer || Configure::read('affiliate.is_enabled')):
				//	$bityurl = $bityurl.'/r:'.$this->Auth->user('username');
					$deal_slug_uname = $deal_slug.'/city:'.$city_slug.'/r:'.$this->Auth->user('username');
					else:
					$deal_slug_uname = $deal_slug.'/city:'.$city_slug;
					endif;
				?>
					<li><a href="http://twitter.com/share?url=<?php echo $bityurl; ?>&amp;lang=en" data-count="none" class="twitter-share-button" target="blank"><?php echo __l('Tweet!');?></a></li>
					<li><?php echo $this->Html->link(__l('Quick! Email a friend!'), 'mailto:?body='.__l('Check out the great deal on ').Configure::read('site.name').'-'.Router::url('/', true).'deal/'.$deal_slug_uname.'&amp;subject='.__l('I think you should get ').Configure::read('site.name').__l(': ').$deal['Deal']['discount_percentage'].__l('% off at ').$deal['Company']['name'], array('target' => 'blank', 'title' => __l('Send a mail to friend about this deal'), 'class' => 'quick'));?></li>
					<li class="share-list"><fb:like href="<?php echo Router::url('/', true).'deal/'.$deal_slug_uname;?>" layout="button_count" font="tahoma"></fb:like></li>
            </ul>
           </div>		
					<?php if(Configure::read('referral.referral_enabled_option') == ConstReferralOption::GrouponLikeRefer || Configure::read('referral.referral_enabled_option') == ConstReferralOption::XRefer || Configure::read('affiliate.is_enabled')):?>
                    <div class="info-details">
                            <div class="clearfix">
                                <p>
                                    <?php
									if(Configure::read('affiliate.is_enabled')):    //If Affilate is enabled
                                        $affiliate_ref_link = __l(' & Affiliate Refferal Link');
                                    endif;
                                    if(Configure::read('referral.referral_enabled_option') == ConstReferralOption::GrouponLikeRefer):
                                        echo __l('Refer Friends, Get').' '.$this->Html->siteCurrencyFormat($this->Html->cCurrency(Configure::read('user.referral_amount'), false)).' '.$affiliate_ref_link;
                                    elseif(Configure::read('referral.referral_enabled_option') == ConstReferralOption::XRefer):
                                        if(Configure::read('referral.refund_type') == ConstReferralRefundType::RefundDealAmount):
                                            $refund_type = __l('Get a Free Deal!!!');
                                        else:
                                            $refund_type = __l('Get').' '.$this->Html->siteCurrencyFormat($this->Html->cCurrency(Configure::read('referral.refund_amount'), false)).' '.__l('');
                                        endif;
                                    
                                        echo __l('Refer').' '.Configure::read('referral.no_of_refer_to_get_a_refund').' '.__l('Friends').', '.$refund_type.' '.$affiliate_ref_link;
                                    elseif(Configure::read('affiliate.is_enabled')):    //If Affilate is enabled
                                        echo __l('Affiliate Refferal Link');
                                    endif;
                                    ?>
                                    <input type="text" class="refer-box" readonly="readonly" value="<?php echo Router::url(array('controller' => 'deals', 'action' => 'view', $deal_slug, 'city' => $city_slug, 'r' =>$this->Auth->user('username')), true);?>" onclick="this.select()"/>
                                    <?php //echo $this->Html->link('?', array('controller' => 'pages', 'action' => 'refer_friend'), array('target' => '_blank'));?>
                                </p>
                        
                        <?php /*?><?php if(Configure::read('referral.referral_enabled_option') == ConstReferralOption::GrouponLikeRefer || Configure::read('referral.referral_enabled_option') == ConstReferralOption::XRefer):?>			<ul class="share-list">
                                    <li class="quick"><?php echo $this->Html->link(__l('Mail it'), 'mailto:?body='.sprintf(__l('Check out %ss daily deal for coolest stuff in your city. '),Configure::read('site.name')).'-'.Router::url(array('controller' => 'deals', 'action' => 'view', $deal_slug, 'r' =>$this->Auth->user('username')), true).'&subject='.__l('I think you should get ').Configure::read('site.name'), array('class' => 'quick', 'target' => '_blank'));?></li>
                                    <li class="face"><?php echo $this->Html->link(__l('Share it on Facebook'), 'http://www.facebook.com/share.php?u='.Router::url(array('controller' => 'deals', 'action' => 'view', $deal_slug, 'r' =>$this->Auth->user('username')), true), array('class' => 'face','target' => '_blank'));?></li>
                                    <li><a href="http://twitter.com/share?url=<?php echo Router::url(array('controller' => 'deals', 'action' => 'view', $deal_slug, 'r' =>$this->Auth->user('username')), true);?>&amp;lang=en" data-count="none" class="twitter-share-button"><?php echo __l('Tweet it');?></a></li>
                                </ul>
                        <?php endif;?> <?php */?>
                            </div>
                    </div>
                    <?php endif;?>
		
	</div>

	</div>
	<div class="skip-block">
		<?php echo $this->Html->link(__l('Skip'),array('controller' => 'deals', 'action' => 'view', $deal_slug), array('class' => 'face', 'title' => __l('Skip')));?></li>
	</div>
	<div id="fb-root"></div>
		<script type="text/javascript">
		  window.fbAsyncInit = function() {
			FB.init({appId: '<?php echo Configure::read('facebook.app_id');?>', status: true, cookie: true,
					 xfbml: true});
		  };
		  (function() {
			var e = document.createElement('script'); e.async = true;
			e.src = document.location.protocol +
			  '//connect.facebook.net/en_US/all.js';
			document.getElementById('fb-root').appendChild(e);
		  }());
		</script>
</div>
