<?php if(!empty($deal['Deal']['name'])): ?>
<div class="side1 discussion-side1-block">
 <div class="side1-tl">
    <div class="side1-tr">
      <div class="side1-tm"> </div>
    </div>
 </div>
 <div class="side1-cl">
    <div class="side1-cr">
        <div class="block1-inner">
        <div class="clearfix">
           <div class="topic-share-block round-5 clearfix">
        		<span class="topic-share-deal"><?php echo __l('Share This Deal: '); ?></span>
        		<ul class="share-list">
        			<?php
					/**************Get bitly url from city_deals******************/
						$get_current_city = $this->request->params['named']['city'];
						foreach($deal['Deal']['City'] as $deal_city)
						{
							if($deal_city['slug'] == $get_current_city)
							{
								if(Configure::read('site.city_url') == 'prefix'):
									$bityurl = $deal_city['CitiesDeal']['bitly_short_url_prefix'];
								else:
									$bityurl = $deal_city['CitiesDeal']['bitly_short_url_subdomain'];
								endif;
							}
						}
						// If currenct city is not the deal viewing city, showing first city as the default city //
						if(empty($bityurl)):
							if(Configure::read('site.city_url') == 'prefix'):
								$bityurl = $deal['Deal']['City'][0]['CitiesDeal']['bitly_short_url_prefix'];
							else:
								$bityurl = $deal['Deal']['City'][0]['CitiesDeal']['bitly_short_url_subdomain'];
							endif;
						endif;
        			?>
                    <li class="quick"><?php echo $this->Html->link(__l('Quick! Email a friend!'), 'mailto:?body='.__l('Check out the great deal on ').Configure::read('site.name').' - '.Router::url('/', true).$this->request->params['named']['city'].'/deal/'.$deal['Deal']['slug'].'&amp;subject='.__l('I think you should get ').Configure::read('site.name').__l(': ').$deal['Deal']['discount_percentage'].__l('% off at ').$deal['Deal']['Company']['name'], array('target' => 'blank', 'title' => __l('Send a mail to friend about this deal'), 'class' => 'quick'));?></li>
        			<li class="twitter-share"><a href="http://twitter.com/share?url=<?php echo $bityurl;?>&amp;text=<?php echo urlencode_rfc3986($deal['Deal']['name']);?>&amp;lang=en" data-count="none" class="twitter-share-button"><?php echo __l('Tweet!');?></a></li>
        			<li class="share-list share-list1"><fb:like href="<?php echo Router::url('/', true).$this->request->params['named']['city'].'/deal/'.$deal['Deal']['slug'];?>" layout="button_count" font="tahoma"></fb:like></li>
        		</ul>
          </div>
      </div>
    <div class="block1 topic-discussion-block clearfix">
         <div class="topic-discussion1">
            <div class="topic-discussion-tag clearfix">
              <p class="topic-price">
              <?php echo (empty($deal['Deal']['is_subdeal_available'])) ? $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['Deal']['discounted_price'])) : $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['Deal']['SubDeal'][0]['discounted_price']));?>
              </p>
    			<?php
    				if($this->Html->isAllowed($this->Auth->user('user_type_id')) && $deal['Deal']['deal_status_id'] != ConstDealStatus::Draft && $deal['Deal']['deal_status_id'] != ConstDealStatus::PendingApproval):
    					if($deal['Deal']['deal_status_id'] == ConstDealStatus::Open || $deal['Deal']['deal_status_id'] == ConstDealStatus::Tipped):
							if(empty($deal['Deal']['is_subdeal_available'])){
								 echo $this->Html->link(__l('Buy Now'), array('controller'=>'deals','action'=>'buy',$deal['Deal']['id']), array('title' => __l('Buy Now'),'class' =>'button'));
							}
							else{
								 echo $this->Html->link(__l('Buy Now'), '#', array('title' => __l('Buy Now'),'class' =>"button button1 js-multiple-sub-deal {'opendialog': 'js-open-subdeal-".$deal['Deal']['id']."'}"));
							?>
                            	<div  id="js-open-subdeal-<?php echo $deal['Deal']['id']; ?>">
                                   <h2><?php echo ' '.__l('Choose your deal').':'; ?> </h2>
                                	<ol class="multi-deal-list">
                                    	<?php foreach($deal['Deal']['SubDeal'] as $subdeal){ ?>
                                    	<li class="clearfix">
                                                <div class="multi-left-block">
                                                	<h3> <?php echo $this->Html->cText($subdeal['name']);?></h3>
                                                      <dl class="multi-deal-list">
                                                         <dt><?php echo __l('Value');?></dt>
                                                         <dd><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($subdeal['original_price']));?></dd>
                                                        <dt><?php echo ' - '.__l('Discount');?></dt>
                                                        <dd><?php echo $this->Html->cInt($subdeal['discount_percentage']) . "%"; ?></dd>
                                                        <dt><?php echo ' - '.__l('Save');?></dt>
                                                        <dd><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($subdeal['savings'])); ?></dd>
                                                     
                                                      
                                                      </dl>
                                                    </div>
						    <div class="multi-center-block"> 
						       <?php echo $this->Html->cInt($subdeal['deal_user_count']); ?>
                                                      <?php echo ' '.__l('Bought');?> 
						    </div>
                                                    <div class="multi-right-block">
                                                      <?php if( !empty($subdeal['max_limit']) && $subdeal['deal_user_count'] >= $subdeal['max_limit']):?>
                                                      <p class='sold-out'><?php echo __l('sold out'); ?></p>
                                                      <?php else: ?>
														<?php if(!empty($subdeal['is_enable_payment_advance'])):?>
															<span class="pay-advance"> <?php echo __l('Pay in Advance');?> </span>
														<?php endif;?>
                                                      <p class="deal-buy"> <?php echo $this->Html->link($this->Html->siteCurrencyFormat(($subdeal['discounted_price'])), array('controller'=>'deals','action'=>'buy', $deal['Deal']['id'], $subdeal['id']),array('title' => __l('Buy').' - '.$this->Html->siteCurrencyFormat($subdeal['discounted_price']),'escape' => false));?></p>
                                                      <?php endif;?> 
                                                 </div>
												<?php if(!empty($subdeal['is_enable_payment_advance'])):?>
												<div class="clearfix">
													<?php
														echo __l('Pay remaining').' '.$this->Html->siteCurrencyFormat($this->Html->cCurrency($subdeal['payment_remaining'])).' ('.$this->Html->siteCurrencyFormat($this->Html->cCurrency($subdeal['original_price'] - $subdeal['discount_amount'])).' - '.$this->Html->siteCurrencyFormat($this->Html->cCurrency($subdeal['pay_in_advance'])).') '.__l('directly to the merchant');
													?>
												</div>
												<?php endif;?>
                                        </li>
                                        <?php } ?>
                                    </ol>
                                </div>
                            <?php								 
								 			
							}
						else:
    					?>
    						<span class="no-available" title="<?php echo __l('No Longer Available');?>"><?php echo __l('No Longer Available');?></span>
    					<?php
    					endif;
    				endif;
                ?>
		
			<div class="return-deal"><?php echo $this->Html->link('<<< '.__l('Return to The Deal'), array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug']), array('title' => __l('Return to The Deal')));?></div>
            </div>
    		  <h2 class="topic-discussion-title">
        		<?php if($deal['Deal']['deal_status_id'] == ConstDealStatus::Open || $deal['Deal']['deal_status_id'] == ConstDealStatus::Tipped):?>
        			<span class ="today-deal">
        				<?php if($this->request->params['action'] =='index'):?>
        					<?php echo __l("Today's Deal").': ';?>
        				<?php endif; ?>
        			</span>
        		<?php endif; ?>
        		<?php
        			echo $this->Html->link($deal['Deal']['name'], array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug']),array('title' =>sprintf(__l('%s'),$deal['Deal']['name'])));
        		?>
    		</h2>
		 </div>
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
        </div>
       	</div>
        <div class="side1-bl">
            <div class="side1-br">
              <div class="side1-bm"> </div>
            </div>
      </div>
</div>
<?php endif; ?>
