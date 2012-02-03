<div class="side2">
<!-- Gift -->
<!--
  <div class="blue-bg deal-blue-bg clearfix">
    <div class="deal-tl">
      <div class="deal-tr">
        <div class="deal-tm">
          <h3> <?php echo __l('Give the Gift of').' '.Configure::read('site.name');?> </h3>
        </div>
      </div>
    </div>
    <div class="side1-cl">
      <div class="side1-cr">
        <div class="block1-inner blue-inner clearfix">
		<p>
        	<span>$50</span><?//php echo __l('available information is here');?>
		</p>
          <?php echo $this->Html->link(__l('Buy a').' '.Configure::read('site.name').' '.__l('Gift Card'), array('controller' => 'gift_users', 'action' => 'add'), array('class' => 'buy', 'title' => __l('Buy a').' '.Configure::read('site.name').' '.__l('Gift Card'))); ?> </div>
      </div>
    </div>
  </div>
-->
<!-- Gift -->

<!-- Virtual Store -->    
	<div class="sidebar-virtual-store">
		<h3><?php echo __l('Virtual Store'); ?></h3>
			<div class="button-new">
          		<?php echo $this->Html->link(__l('Virtual Store'), array('controller' => 'deals', 'action' => 'index', 'admin' => false,'type' => 'anytime'), array('title' => __l('Virtual Store')));?>
			</div>
			<p>
	          <?php echo __l('Nikmati diskaun hebat dan tawaran produk-produk menawan di Kedai DealHangat!'); ?>
			</p>
	</div>
<!-- Virtual Store --> 
  
<!-- Today's Main Deals -->  
  <?php  if(!empty($main_deals)): ?>
  <div class="sidebar-deals">
  		<h3>
  			<?php echo $this->Html->link(__l("Today's Main Deals"), array('controller' => 'deals', 'action' => 'index', 'type' => 'main'),array('title' => __l("Today's Main Deals")), null, false);?>
  		</h3>
  		
          <div class="side-deal">
            <ol class="side-deal-list">
              <?php
                	foreach($main_deals as $main_deal):
						?>
              <li>
                <h4><?php echo $this->Html->link($main_deal['Deal']['name'], array('controller' => 'deals', 'action' => 'view', $main_deal['Deal']['slug']),array('title' =>sprintf(__l('%s'),$main_deal['Deal']['name'])));?></h4>
                <div class="clearfix">
                  <div class="deal1-img"> <?php echo $this->Html->link($this->Html->showImage('Deal', (!empty($main_deal['Attachment'][0]) ? $main_deal['Attachment'][0] : ''), array('dimension' => 'small_big_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($main_deal['Deal']['name'], false)), 'title' => $this->Html->cText($main_deal['Deal']['name'], false))), array('controller' => 'deals', 'action' => 'view', $main_deal['Deal']['slug']),array('title' =>sprintf(__l('%s'),$main_deal['Deal']['name']), 'escape' => false));?> </div>
                  <div class="deal-button">
                  
                    	<div class="deal-price clearfix">

                          <div class="deal-currency">
								<?php if(!empty($main_deal['Deal']['is_subdeal_available'])): ?>								
                                    <?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($main_deal['SubDeal'][0]['discounted_price']));?>
                                <?php else:?>
                                    <?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($main_deal['Deal']['discounted_price']));?>
                                <?php endif;?>
						  </div>

                          <div class="deal-value-info"> 
                          	<span> 
								<?php if(!empty($main_deal['Deal']['is_subdeal_available'])): ?>
									<?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($main_deal['SubDeal'][0]['original_price']));?> <?php echo __l('Value');?>
								<?php else:?>
									<?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($main_deal['Deal']['original_price']));?> <?php echo __l('Value');?> 
								<?php endif;?>
                          	</span> 
                          </div>
                      
                    	</div>
                    
                    	<?php echo $this->Html->link(__l('View it'), array('controller' => 'deals', 'action' => 'view', $main_deal['Deal']['slug']),array('title' => __l('View it')), null, false);?>
                    
                    </div><!-- deal-button -->
                    
                </div><!-- clearfix -->
              </li>
              <?php
					endforeach;
                 ?>
             <li class="view-all"><?php echo $this->Html->link(__l('View all'), array('controller' => 'deals', 'action' => 'index', 'type' => 'main'),array('title' => __l('View all')), null, false);?></li>
            </ol>
          </div>
        
    
  </div>
  <?php endif; ?>
<!-- Today's Main Deals -->    
  
<!-- Today's Side Deals -->    
  <?php if(Configure::read('deal.is_side_deal_enabled') && !empty($side_deals)): ?>
  <div class="blue-bg top clearfix">
    <div class="deal-tl">
      <div class="deal-tr">
        <div class="deal-tm">
          <h3> <?php echo __l("Today's Side Deals");?> </h3>
        </div>
      </div>
    </div>
    <div class="side1-cl">
      <div class="side1-cr">
        <div class="block1-inner blue-bg-inner clearfix">
          <div class="side-deal">
            <ol class="side-deal-list">
              <?php
                	foreach($side_deals as $side_deal):
						?>
              <li>
                <h4><?php echo $this->Html->link($side_deal['Deal']['name'], array('controller' => 'deals', 'action' => 'view', $side_deal['Deal']['slug']),array('title' =>sprintf(__l('%s'),$side_deal['Deal']['name'])));?></h4>
                <div class="clearfix">
                  <div class="deal1-img"> <?php echo $this->Html->link($this->Html->showImage('Deal', $side_deal['Attachment'][0], array('dimension' => 'small_big_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($side_deal['Deal']['name'], false)), 'title' => $this->Html->cText($side_deal['Deal']['name'], false))), array('controller' => 'deals', 'action' => 'view', $side_deal['Deal']['slug']),array('title' =>sprintf(__l('%s'),$side_deal['Deal']['name']), 'escape' => false));?> </div>
                  <div class="deal-button">
                    <div class="deal-price clearfix">
                      <div class="deal-price-l">
                        <div class="deal-price-r clearfix">
                          <div class="deal-currency">
								<?php if(!empty($side_deal['Deal']['is_subdeal_available'])): ?>								
                                    <?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($side_deal['SubDeal'][0]['discounted_price']));?>
                                <?php else:?>
                                    <?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($side_deal['Deal']['discounted_price']));?>
                                <?php endif;?>
						  </div>
                          <div class="deal-value-info"> 
                          	<span>
								<?php if(!empty($side_deal['Deal']['is_subdeal_available'])): ?>
									<?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($side_deal['SubDeal'][0]['original_price']));?>
								<?php else:?>
									<?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($side_deal['Deal']['original_price']));?> <?php echo __l('Value');?> 
								<?php endif;?>
                            </span> 
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php echo $this->Html->link(__l('View it'), array('controller' => 'deals', 'action' => 'view', $side_deal['Deal']['slug']),array('title' => __l('View it')), null, false);?> </div>
                </div>
              </li>
              <?php
					endforeach;
                 ?>
             <li class="view-all"><?php echo $this->Html->link(__l('View all'), array('controller' => 'deals', 'action' => 'index', 'type' => 'side'),array('title' => __l('View all')), null, false);?></li>
            </ol>
          </div>
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
<!-- Today's Side Deals -->    
<?php echo $this->element('deals-nearby_simple', array('deal_id' => $deal['Deal']['id'], 'cache' => array('config' => 'site_element_cache', 'key' => $deal['Deal']['id']))); ?>




  
    <?php foreach ($blocks_left as $block): ?>
        <?php echo $this->Block->display($block); ?>
    <?php endforeach; ?>

    <?php foreach ($blocks_bottom as $block): ?>
        <?php echo $this->Block->display($block); ?>
    <?php endforeach; ?>


    <?php foreach ($blocks_right as $block): ?>
        <?php echo $this->Block->display($block); ?>
    <?php endforeach; ?>


  

<!-- Get Your Business On DH -->    
  <div class="sidebar-business">
          <h3><?php echo sprintf(__l('Get Your Business on').' %s!', Configure::read('site.name')); ?></h3>
          <p class="normal" ><?php echo __l('Learn More for the basics.'); ?> <?php echo sprintf(__l('about how').' %s '.__l('can help bring tonnes of customers to your door'), Configure::read('site.name'));?></p>
          <?php echo $this->Html->link(__l('Learn More'), array('controller' => 'pages', 'action' => 'view','company', 'admin' => false), array('title' => __l('Learn More'),'class'=>'learn'));?>
  </div>
<!-- Get Your Business On DH -->    

<!-- Contact Us -->    
  <div class="sidebar-contact">
          <h3><?php echo __l('Contact Us'); ?></h3>
          <ul>
          <li class="sidebar-contact-tel"><?php echo __l('03 6211 6207'); ?><br/><span><?php echo __l('(Weekdays 9am-5pm)'); ?></span></li>
          <li class="sidebar-contact-email"><a href="mailto:support@dealhangat.com" title="Email Kru DH"><?php echo __l('support@dealhangat.com'); ?></a></li>
          </ul>
  </div>



<!-- Contact Us -->  

<!-- FB Likes -->    
  
<?php
$facebook_like_box = Configure::read('facebook.like_box');
if(!empty($facebook_like_box)):?>
  <div class="sidebar-social-facebook">
	<h3><?php echo __l('Facebook'); ?></h3>
		<div class="facebook-block clearfix"><?php echo $facebook_like_box;?></div>
  </div>
<?php endif;  ?>
<!-- FB Likes -->    
  

<!-- Twitter -->    
  
<?php	if(Configure::read('twitter.is_twitter_feed_enabled')): ?>
  <div class="sidebar-social-twitter">
	<h3>Twitter</h3>
        <div class="twitter-block clearfix">
          <?php	echo strtr(Configure::read('twitter.tweets_around_city'),array(
    					'##CITY_NAME##' => ucwords($city_name),
    				));
                ?>
        </div>
  </div>
<?php endif; ?> 
<!-- Twitter -->    
 

<!-- FB Recent Activity -->
<!--
  
<?php
  $facebook_feeds_code = Configure::read('facebook.feeds_code');
  if(!empty($facebook_feeds_code)):?>
  <div class="blue-bg clearfix">
    <div class="deal-tl">
      <div class="deal-tr">
        <div class="deal-tm">
          <h3>Facebook</h3>
        </div>
      </div>
    </div>
    <div class="side1-cl">
      <div class="side1-cr">
        <div class="block1-inner blue-bg-inner clearfix">
          <div class="facebook-block clearfix"> <?php echo $facebook_feeds_code;?> </div>
        </div>
      </div>
    </div>
    <div class="side1-bl">
      <div class="side1-br">
        <div class="side1-bm"> </div>
      </div>
    </div>
  </div>
<?php endif;  ?>  
-->
<!-- FB Recent Activity -->    
 

</div><!-- end of sidebar -->

