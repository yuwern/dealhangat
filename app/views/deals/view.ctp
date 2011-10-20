<?php /* SVN: $Id: view.ctp 59976 2011-07-12 05:50:35Z mohanraj_109at09 $ */ ?>
<?php if($this->request->params['action'] !='index'):
	if($this->Html->isAllowed($this->Auth->user('user_type_id')) and   $deal['Deal']['deal_status_id'] != ConstDealStatus::Open && $deal['Deal']['deal_status_id'] != ConstDealStatus::Tipped && $deal['Deal']['deal_status_id'] != ConstDealStatus::Draft && $deal['Deal']['deal_status_id'] != ConstDealStatus::PendingApproval  && $deal['Deal']['deal_status_id'] != ConstDealStatus::Upcoming ):?>
		<div id="missed_deal_announcement" class="announcement">
			  <p id="txt_missed_groupon">
				<?php echo __l('Oh no... You\'re too late for this ').' '.Configure::read('site.name').'!';?>
			  </p>
			  <div class="announcement_inner clearfix">
				<div class="left">
				  <p>
					<?php echo __l('Sign up for our daily email so you never miss another').' '.Configure::read('site.name').'!';?>
				  </p>
				</div>
			  </div>
	 </div>
	<?php endif; ?>
<?php endif; ?>
   <?php if($this->request->params['action'] =='view'): ?>
    <div class="deal-view-inner-block clearfix">
      <div class="main-shad">&nbsp;</div>
      <div class="side1">
     <?php endif; ?>
        <div class="block1 clearfix">
          <div class="side1-tl">
            <div class="side1-tr">
              <div class="side1-tm"> </div>
            </div>
          </div>
          <div class="side1-cl">
            <div class="side1-cr">
              <div class="block1-inner">
				<?php					
					if(empty($deal['Deal']['is_redeem_at_all_branch_address'])){
						if(count($deal['CompanyAddressesDeal']) == 1 && empty($deal['Deal']['is_redeem_in_main_address'])){
							$id =0;
							foreach($deal['Company']['CompanyAddress'] as $key => $company_address){
								if($deal['CompanyAddressesDeal'][0]['company_address_id'] == $company_address['id'])
									$id = $key;
							}
							$multiple_loc_message = $deal['Company']['CompanyAddress'][$id]['address1'];
						}
						else if(count($deal['CompanyAddressesDeal']) <= 0 && !empty($deal['Deal']['is_redeem_in_main_address'])){
							$multiple_loc_message = $deal['Company']['address1'];
						}
						else{
							$multiple_loc_message = __l('Multiple Location');						
						}
					}else{
						if(!empty($deal['Deal']['is_redeem_in_main_address']) && empty($deal['Company']['CompanyAddress']) ){
							$multiple_loc_message = $deal['Company']['address1'];
						} 
						else{						
							$multiple_loc_message = __l('Multiple Location');						
						}	
					}
				?>
                <h2 class="title">
        			<span class ="today-deal">
        				<?php if($this->request->params['action'] =='index'):?>
        					<?php	echo __l("Today's Deal").': ';?>
        				<?php endif; ?>
        			</span>
            		<?php
            			echo $this->Html->link($deal['Deal']['name'], array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug']),array('title' =>sprintf(__l('%s'),$deal['Deal']['name'])));
            		?>
	           	</h2>				
				<p class="company-msg-info">
				<span class="c-name"><?php echo $deal['Company']['name'] ;?></span>
				 <span class="c-message"><?php echo $multiple_loc_message;?></span></p>
					<div class="gallery-block">
						<div id='js-gallery'>
								<?php foreach($deal['Attachment'] as $attachment){?>
									<a><?php echo $this->Html->showImage('Deal', $attachment, array('dimension' => 'medium_big_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($deal['Deal']['name'], false)), 'title' => $this->Html->cText($deal['Deal']['name'], false)));?></a>
								<?php } ?>
						</div>
					</div>
				
                <div class="buy-block clearfix">
                  <div class="deal-block clearfix">
                       <dl class="deal-value clearfix">
    					 <dt><?php echo __l('Value');?></dt>
    					 <dd><?php echo (empty($deal['Deal']['is_subdeal_available'])) ? $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['Deal']['original_price'],false)) : $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['SubDeal'][0]['original_price'],false));?></dd>
    				  </dl>
    				  <dl class="deal-discount clearfix">
    					<dt><?php echo __l('Discount');?></dt>
    					<dd><?php echo (empty($deal['Deal']['is_subdeal_available'])) ? $this->Html->cFloat($deal['Deal']['discount_percentage']) . "%" : $this->Html->cFloat($deal['SubDeal'][0]['discount_percentage']) . "%"; ?></dd>
    				  </dl>
    				  <dl class="deal-save clearfix">
    					<dt><?php echo __l('You Save');?></dt>
    					<dd><?php echo (empty($deal['Deal']['is_subdeal_available'])) ?  $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['Deal']['savings'])) : $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['SubDeal'][0]['savings'])); ?></dd>
    	       		  </dl>
                 </div>
                 	<div class="clearfix">
                  <div class="tag clearfix">

                    <?php
                            $class1='';
                        if(!empty($deal['Deal']['is_enable_payment_advance'])){
                                  $class1='payment-price';

                        }?>
                      <p class="price <?php echo $class1; ?> ">
	                   <?php if(!empty($deal['Deal']['is_enable_payment_advance'])):?>
						  <span class="pay-advance"> <?php echo __l('Pay in Advance');?> </span>
					    <?php endif;?>

                      <?php echo (empty($deal['Deal']['is_subdeal_available'])) ? $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['Deal']['discounted_price'])) : $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['SubDeal'][0]['discounted_price']));?>
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
                                    	<?php foreach($deal['SubDeal'] as $subdeal){ ?>
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
						elseif($this->Html->isAllowed($this->Auth->user('user_type_id')) && $deal['Deal']['deal_status_id'] == ConstDealStatus::Upcoming):
						?>
							<span class="no-available" title="<?php echo __l('Upcoming');?>"><?php echo __l('Upcoming');?></span>
						<?php
							else:
						?>
							<span class="no-available" title="<?php echo __l('No Longer Available');?>"><?php echo __l('No Longer Available');?></span>
						<?php
						endif;
					endif;
                ?>
                   </div>
                   </div>
					<?php if(!empty($deal['Deal']['is_enable_payment_advance'])):?>
						<div class="clearfix pay-remaing-block">
							<?php
								echo __l('Pay remaining').' '.$this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['Deal']['payment_remaining'])).' ('.$this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['Deal']['original_price'] - $deal['Deal']['discount_amount'])).' - '.$this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['Deal']['pay_in_advance'])).') '.__l('directly to the merchant');
							?>
						</div>
					<?php endif;?>
                </div>
                <div class="clearfix">
                  <div class="section1">
                    <div class="price-block -block">
                    
                     <?php if($deal['Deal']['deal_status_id'] == ConstDealStatus::Open || $deal['Deal']['deal_status_id'] == ConstDealStatus::Tipped || $deal['Deal']['deal_status_id'] == ConstDealStatus::Closed): ?>
                      <div class="bought-block clearfix">
                            <?php if($deal['Deal']['deal_status_id'] == ConstDealStatus::Tipped || $deal['Deal']['deal_status_id'] == ConstDealStatus::Closed): ?>
                            <p class="bought-amount"><?php echo $this->Html->cInt($deal['Deal']['deal_user_count']);?> <?php echo __l('offers sold so far');?></p>
                            <div class="bought-info">
                            	<?php if($deal['Deal']['deal_status_id'] == ConstDealStatus::Tipped): ?>
                                <p class="deal-on"><?php echo __l('The deal is on!');?></p>
                              	 <p class="quick-info"> <?php echo __l('Get in quick or miss out!');?> </p>
                                <?php endif; ?>
                               <p class="tipped-info"><?php echo sprintf(__l('Tipped at %s with %s bought'),$this->Html->cDateTime($deal['Deal']['deal_tipped_time']),$this->Html->cInt($deal['Deal']['min_limit']));?></p>
                             </div>
                       <?php else: ?>
                        <div class="progress-tl">
                          <div class="progress-tr">
                            <div class="progress-tm"> </div>
                          </div>
                        </div>
                        <div class="progress-inner clearfix">
                          <h3><?php echo $this->Html->cInt($deal['Deal']['deal_user_count']);?> <?php echo __l('Bought');?></h3>
                            <?php
                                $pixels = round(($deal['Deal']['deal_user_count']/$deal['Deal']['min_limit']) * 100);
                            ?>
                            <p class="progress-bar round-5"><span class="arrow" style="left:<?php echo $pixels; ?>%"><?php echo $pixels; ?></span><span class="progress-status round-5" style="width:<?php echo $pixels; ?>%" title="<?php echo $pixels; ?>%">&nbsp;</span></p>
                            <p class="progress-value clearfix"><span class="progress-from">0</span><span class="progress-to"><?php echo $this->Html->cInt($deal['Deal']['min_limit']); ?></span></p>
                            <p class="progress-desc"><?php echo sprintf(__l('%s more needed to get the deal'),($deal['Deal']['min_limit'] - $deal['Deal']['deal_user_count'])) ?></p>
                         </div>
                        <div class="progress-bl">
                          <div class="progress-br">
                            <div class="progress-bm"> </div>
                          </div>
                        </div>
                      <?php endif; ?>
                      </div>
                  <?php endif; ?>
               <?php if($deal['Deal']['deal_status_id'] != ConstDealStatus::Upcoming && $deal['Deal']['deal_status_id'] != ConstDealStatus::Draft && $deal['Deal']['deal_status_id'] != ConstDealStatus::PendingApproval): ?>
               <div class="progress-block clearfix">
                        <div class="progress-tl">
                          <div class="progress-tr">
                            <div class="progress-tm"> </div>
                          </div>
                        </div>
                        <div class="progress-inner clearfix">
                    <?php if(($deal['Deal']['deal_status_id'] == ConstDealStatus::Open || $deal['Deal']['deal_status_id'] == ConstDealStatus::Tipped)): 
							if(empty($deal['Deal']['is_anytime_deal'])){
					?>
                        <dl class="progress-list">
                            <dt><?php echo __l('Time left to buy');?></dt>
                            <dd>
                                <div class="js-deal-end-countdown">&nbsp;</div>
                                <span class="js-time hide"><?php
                                    echo $end_time = intval(strtotime($deal['Deal']['end_date'].' GMT') - time());
                                ?></span>
                            </dd>
                         </dl>
                   <?php
				   			}
							else{
					?>
                    	 <dl class="progress-list">
                            <dt><?php echo __l('Time left to buy');?></dt>
                            <dd>
                                <span class="unlimited"><?php echo __l('Unlimited'); ?></span>
                            </dd>
                         </dl>
                    <?php 
							}
                        $per = (strtotime($deal['Deal']['end_date']) - strtotime($deal['Deal']['start_date']))  / 10;
                        $next =  round((strtotime(date('Y-m-d H:i:s')) - strtotime($deal['Deal']['start_date'])) / $per);
                        if($next <= 0){
                            $next = 1;
                        }
                        if($next >= 10){
                            $next = 10;
                        }
                    ?>
                    <div class="pg-img"><?php echo $this->Html->image("clock-img.png", array('alt'=> __l('[Image: Progress]'), 'title' => __l('Progress'))); ?></div>
                    <?php elseif($deal['Deal']['deal_status_id'] == ConstDealStatus::Closed || $deal['Deal']['deal_status_id'] == ConstDealStatus::Canceled || $deal['Deal']['deal_status_id'] == ConstDealStatus::Expired || $deal['Deal']['deal_status_id'] == ConstDealStatus::PaidToCompany): ?>
                        <dl class="progress-list progress-list1">
                            <dt><?php echo __l('This deal ended at:');?></dt>
                            <dd><?php echo $this->Html->cDateTime($deal['Deal']['end_date'])?></dd>
                         </dl>
                    <?php endif; ?>
                  </div>
                        <div class="progress-bl">
                          <div class="progress-br">
                            <div class="progress-bm"> </div>
                          </div>
                        </div>
                      </div>
			   <?php endif; ?>

                  <?php  if(($deal['Deal']['deal_status_id'] == ConstDealStatus::Open || $deal['Deal']['deal_status_id'] == ConstDealStatus::Tipped) && $this->Html->isAllowed($this->Auth->user('user_type_id'))):?>
                    <div class="clearfix">
                      <div class="buy-it-block">
                        <?php 
						if(empty($deal['Deal']['is_subdeal_available'])){
							echo $this->Html->link(__l('Buy it for a friend!'), array('controller'=>'deals','action'=>'buy',$deal['Deal']['id'],'type' => 'gift'), array('title' => __l('Buy it for a friend'),'class' =>'buy-it'));
						}
						else{
							echo $this->Html->link(__l('Buy it for a friend!'), '#', array('title' => __l('Buy it for a friend'),'class' =>"buy-it js-multiple-sub-deal {'opendialog': 'js-open-subdeal-gift-".$deal['Deal']['id']."'}"));
						}
						?>
                        	<div id="js-open-subdeal-gift-<?php echo $deal['Deal']['id']; ?>">
                                	<h2><?php if(!empty($deal['SubDeal'])) { echo ' '.__l('Choose your deal').':'; } ?> </h2>
									<ol class="multi-deal-list">
                                    	<?php foreach($deal['SubDeal'] as $subdeal){ ?>
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
                                                      <p class="deal-buy"> <?php echo $this->Html->link($this->Html->siteCurrencyFormat(($subdeal['discounted_price'])), array('controller'=>'deals','action'=>'buy', $deal['Deal']['id'], $subdeal['id'], 'type' => 'gift'),array('title' => __l('Buy').' - '.$this->Html->siteCurrencyFormat($subdeal['discounted_price']),'escape' => false));?></p>
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
                                    </ul>
                                </div>
    				  </div>
    				  </div>
                    <?php endif; ?>
                      <div class="share-block1 share-block clearfix">
                       <span><?php echo __l('Share This Deal: '); ?></span>
                        <ul class="share-list">               
							<?php
							/**************Get bitly url from city_deals******************/
								foreach($deal['City'] as $deal_city)
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
										$bityurl = $deal['City'][0]['CitiesDeal']['bitly_short_url_prefix'];
									else:
										$bityurl = $deal['City'][0]['CitiesDeal']['bitly_short_url_subdomain'];
									endif;
								endif;
            				?>
                            <li class="quick"><?php echo $this->Html->link(__l('Quick! Email a friend!'), 'mailto:?body='.__l('Check out the great deal on ').Configure::read('site.name').' - '.Router::url('/', true).$get_current_city.'/deal/'.$deal['Deal']['slug'].'&amp;subject='.__l('I think you should get ').Configure::read('site.name').__l(': ').$deal['Deal']['discount_percentage'].__l('% off at ').$deal['Company']['name'], array('target' => 'blank', 'title' => __l('Send a mail to friend about this deal'), 'class' => 'quick'));?></li>
							<li class="twitter-frame"><a href="http://twitter.com/share?url=<?php echo $bityurl;?>&amp;text=<?php echo urlencode_rfc3986($deal['Deal']['name'], false);?>&amp;lang=en&amp;via=<?php echo Configure::read('site.name'); ?>" data-count="none" class="twitter-share-button"><?php echo __l('Tweet!');?></a></li>
                            <li class="share-list"><fb:like href="<?php echo Router::url('/', true).$get_current_city.'/deal/'.$deal['Deal']['slug'];?>" layout="button_count" font="tahoma"></fb:like></li>
                        </ul>
                      </div>
                      
                    </div>
                  </div>
                  <div class="section2">
                    <div class="fine-print-block">
                        <h3><?php echo __l('The Fine Print');?></h3>
                        <?php 
						
							  if(!empty($deal['Deal']['coupon_expiry_date']) && empty($deal['Deal']['is_subdeal_available'])){
		                 		 echo __l('Expires '); 
		                         echo  $this->Html->cDateTime($deal['Deal']['coupon_expiry_date']);
							  }
							  else if(!empty($deal['Deal']['is_subdeal_available']) && !empty($deal['SubDeal'][0]['coupon_expiry_date']) ){
		                 		 echo __l('Expires '); 
		                         echo  $this->Html->cDateTime($deal['SubDeal'][0]['coupon_expiry_date']);
							  }	  
							  echo ' '.$this->Html->cHtml($deal['Deal']['coupon_condition']);
						?>
                        <?php echo $this->Html->link(__l('Read the Deal FAQ'), array('controller' => 'pages', 'action' => 'view','faq', 'admin' => false), array('target'=>'_blank', 'title' => __l('Read the deal FAQ')));?> <?php echo __l(' for the basics.'); ?>
                    </div>
                    <div class="highlight-block">
                      <h3><?php echo __l('Highlights');?></h3>
                      <?php echo $this->Html->cHtml($deal['Deal']['coupon_highlights']);?>
                    </div>
                  </div>
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
        <div class="block2 clearfix">
          <div class="side1-tl">
            <div class="block2-tr">
              <div class="side1-tm">
                <div class="block2-top"></div>
              </div>
            </div>
          </div>
          <div class="side1-cl">
            <div class="block2-cr">
              <div class="block2-inner clearfix">
                <div class="block2-l">
                   <h3><?php echo __l('Description');?></h3>
                    <?php echo $this->Html->cHtml($deal['Deal']['description']);?>
					<?php if(Configure::read('charity.is_enabled') == 1 && $deal['Deal']['charity_percentage'] > 0):?>
                    <div class="charity-block clearfix">
                    	<h3><?php echo __l('Charity');?></h3>
                        <?php if(Configure::read('charity.who_will_choose') == ConstCharityWhoWillChoose::Buyer):?>
                        <?php echo sprintf(__l('For every deal purchased, %s will donate %s of amount to charity'),Configure::read('site.name'),$deal['Deal']['charity_percentage'].'%'); ?>
                        <?php else: ?>
                            <?php echo sprintf(__l('For every deal purchased, %s will donate %s of amount to'),Configure::read('site.name'),$deal['Deal']['charity_percentage'].'%');?>
                            <?php if(!empty($deal['Charity'])): ?>
                                <a href="<?php echo $deal['Charity']['url']; ?>" target="_blank"><?php echo $this->Html->cText($deal['Charity']['name']); ?></a>
                            <?php else:  
                                echo __l('charity');
                            endif; ?>
                        <?php endif; ?>
                    </div>
				 	<?php endif; ?>                  
                    <div class="review-block clearfix">
                    <?php if(!empty($deal['Deal']['review'])){?>
        				<h3><?php echo __l('Reviews');?></h3>
        				<div class="big-text"><?php echo $this->Html->cHtml($deal['Deal']['review']);?></div>
			      <?php }?>
                 <?php if($deal['Deal']['deal_status_id'] != ConstDealStatus::Upcoming && $deal['Deal']['deal_status_id'] != ConstDealStatus::Draft && $deal['Deal']['deal_status_id'] != ConstDealStatus::PendingApproval): ?>
                   
                   
          <!--
          <div class="join-discussion-block">
		     	<?php if(!empty($deal['Topic'][0]['topic_discussion_count'])):?>
			     	  
					<div class="deal-area">
						<div class="deal-l">
              <?php echo $this->Html->getUserAvatarLink($deal['Topic'][0]['LastRepliedUser'], 'small_thumb');?>
						</div>
						<p class="deal-r">
							<?php echo $this->Html->truncate($deal['Topic'][0]['TopicDiscussion'][0]['comment'],50, array('ending' => '...')); ?>
							<?php echo $this->Html->link(__l(' more'), array('controller' => 'topic_discussions', 'action' => 'index', $deal['Topic'][0]['id'])); ?>
						</p>
					</div>
					
					<div class="discussion-block">                  
            <p class="first-comment">
              <?php echo $this->Html->link(__l('Join the discussion!'), array('controller' => 'topic_discussions', 'action' => 'index', $deal['Topic'][0]['id']),array('title'=>__l('Join the discussion!'),'class'=>'joing-link')); ?>
            </p>
						<p class="comment-info"><?php echo  $this->Html->cInt($deal['Topic'][0]['topic_discussion_count']).' Comments';?></p>
        	</div>
      	
				<?php else: ?>
					<p class="first-comment">
						<?php echo $this->Html->link(__l('Be the first to comment!'), array('controller' => 'topic_discussions', 'action' => 'index', (!empty($deal['Topic'][0]['id'])?$deal['Topic'][0]['id']:'')),array('title'=>__l('Be the first to comment!'),'class'=>'joing-link')); ?>
					</p>
					
				<?php endif; ?>
    		</div>
        -->
      
      
      
          	
    	   <?php endif; ?>
    	   
    	   
    	   
    	   
    	   
    	   
    	   
    	   <div class="fb-comments" data-href="<?php echo $canonical_url; ?>" data-num-posts="2" data-width="450"></div>     	  
     	  
     	  
                       </div> <!-- /review-block -->
                  <?php if(!empty($deal['Deal']['comment'])) {?>
						<h3><?php echo Configure::read('site.name').' '.__l('says');?></h3>
                       <?php echo $this->Html->cHtml($deal['Deal']['comment']);?>
                  <?php } ?>
               
                 <ul class="share-link clearfix">
                    <?php
						if(!empty($city_slug)):
							$tmpURL= $this->Html->getCityTwitterFacebookURL($city_slug);
						endif;
            		?>
                    <li><a href="<?php echo !empty($tmpURL['City']['twitter_url']) ? $tmpURL['City']['twitter_url'] : Configure::read('twitter.site_twitter_url'); ?>" title="<?php echo __l('Follow Us in Twitter'); ?>" target="_blank" class="twitter1"><?php echo __l('follow @');?><?php echo Configure::read('site.name');?><?php echo __l(' on Tweet'); ?></a></li>
                    <li><a href="<?php echo !empty($tmpURL['City']['facebook_url']) ? $tmpURL['City']['facebook_url'] : Configure::read('facebook.site_facebook_url'); ?>" title="<?php echo __l('See Our Profile in Facebook'); ?>" target="_blank" class="facebook1"><?php echo __l('follow @');?><?php echo Configure::read('site.name');?><?php echo __l(' on Facebook it'); ?></a></li>
                </ul>				
				</div>
                <div class="block2-r">
                     <h3><?php echo __l('Company Info:');?></h3>
                            <h5 class="big"><?php
								if($deal['Company']['is_company_profile_enabled'] && $deal['Company']['is_online_account']):
									echo $this->Html->link($this->Html->cText($deal['Company']['name'], false), array('controller' => 'companies', 'action' => 'view',   $deal['Company']['slug']),array('title' =>$this->Html->cText($deal['Company']['name'],false)), null, false);
								else:
									echo $this->Html->cText($deal['Company']['name']);
								endif;
	
						?></h5>
						<?php if(!empty($deal['Company']['url'])): ?>
							<a href="<?php echo $deal['Company']['url'];?>" title="<?php echo $this->Html->cText($deal['Company']['url'],false);?>" target="_blank"><?php echo $this->Html->cText($deal['Company']['url'],false);?></a>
						<?php endif; ?>
                     <?php if($deal['Deal']['is_redeem_in_main_address'] == 1) : ?>
                            <address>
                            <?php echo $this->Html->cText($deal['Company']['address1']);?>
                            <?php echo !empty($deal['Company']['City']['name']) ? $this->Html->cText($deal['Company']['City']['name']) : '';?><?php echo !empty($deal['Company']['State']['name']) ? $this->Html->cText($deal['Company']['State']['name']) : '';?> <?php echo $this->Html->cText($deal['Company']['zip']);?>
                            </address>
        			<?php endif; ?>
				   <?php if(!empty($deal['Company']['CompanyAddress'])):?>
                            <div class="map-info-r">
                              <?php 
                                $branch_address = array();
                                foreach($deal['CompanyAddressesDeal'] as $company_address_deal){
                                $branch_address[$company_address_deal['company_address_id']] = $company_address_deal['company_address_id'];
                                }	
							   ?>
                            <ol class="address-list clearfix">
                                    <?php 	
									$allowed_branch_addresses = array();
                                    foreach($deal['Company']['CompanyAddress'] as $address): 
                                       if((in_array($address['id'], $branch_address) && empty($deal['Deal']['is_redeem_at_all_branch_address'])) || !empty($deal['Deal']['is_redeem_at_all_branch_address'])):
											$allowed_branch_addresses[] = $address;
									  ?>
                                          <li>
                                            <address class="address<?php echo $count;?>">
													<?php if (!empty($address['address1']) || !empty($address['address2'])): ?>
                                                        <span class="street-name"><?php echo ((!empty($address['address1'])) ? $address['address1'] : '') . ' ' . ((!empty($address['address2'])) ? $address['address2'] : ''); ?></span>
                                                    <?php endif; ?>
													<?php if (!empty($address['City']['name']) || !empty($address['State']['name'])): ?>
                                                        <span><?php echo (!empty($address['City']['name'])) ? $address['City']['name'] . ', ' : ''; ?> <?php echo (!empty($address['State']['name'])) ? $address['State']['name'] : ''; ?></span>
                                                        <span><?php echo (!empty($address['Country']['name'])) ? $address['Country']['name'] : ''; ?></span>
                                                    <?php endif; ?>
													<?php if (!empty($address['zip'])): ?>
                                                        <span><?php echo $address['zip']; ?></span>
                                                    <?php endif; ?>
                                            </address>
                                        </li>
                                  	 <?php endif; ?> 
                                 <?php endforeach; ?> 
                            </ol>
                       </div>
     			  <?php endif; ?>
				 
                    <div class="map-block">
            			<?php $map_zoom_level = !empty($deal['Company']['map_zoom_level']) ? $deal['Company']['map_zoom_level'] : Configure::read('GoogleMap.static_map_zoom_level');?>
            			<?php
            			$company = $deal['Company'];
            			$company['CompanyAddress']= (!empty($allowed_branch_addresses) ? $allowed_branch_addresses : '');
            			if(Configure::read('GoogleMap.embedd_map') == 'Static'):
            				echo $this->Html->image($this->Html->formGooglemap($company,'192x192'));
            			else:
							echo $this->Html->formGooglemap($company,'192x192');
						endif;
            			?>
            			<?php if(Configure::read('GoogleMap.embedd_map') != 'Static'):?>
            				<small>
                				<?php if(env('HTTPS')) { ?>
                                    <a href="https://maps-api-ssl.google.com/maps?q=<?php echo $this->Html->url(array('controller' => 'companies', 'action' => 'view',$deal['Company']['slug'],$deal['Deal']['slug'],'ext' => 'kml'),true).'&amp;z='.$map_zoom_level.'&amp;source=embed' ?>" title="<?php echo $deal['Company']['name'] ?>" target="_blank" style="color:#0000FF;text-align:left"><?php echo __l('View Larger Map');?></a>
                                    <?php
                                       } else {
                                    ?>
                					<a href="http://maps.google.com/maps?q=<?php echo $this->Html->url(array('controller' => 'companies', 'action' => 'view',$deal['Company']['slug'],$deal['Deal']['slug'],'ext' => 'kml'),true).'&amp;z='.$map_zoom_level.'&amp;source=embed' ?>" title="<?php echo $deal['Company']['name'] ?>" target="_blank" style="color:#0000FF;text-align:left"><?php echo __l('View Larger Map');?></a>
                                    <?php
									}
									?>						
            				</small>
            			<?php endif;?>
        			</div>
				  
                </div>
              </div>
            </div>
          </div>
          <div class="side1-bl">
            <div class="block2-br">
              <div class="side1-bm">
                <div class="block2-bottom"></div>
              </div>
            </div>
          </div>
        </div>
          <?php if (($this->Auth->user('user_type_id') == ConstUserTypes::Company && $deal['Company']['user_id'] == $this->Auth->user('id')) || $this->Auth->user('user_type_id') == ConstUserTypes::Admin):?>
            <div>
			   <?php echo $this->element('deals-stats', array('deal_id' => $deal['Deal']['id'], 'cache' => array('config' => 'site_element_cache_1_min', 'key' => $deal['Deal']['id'])));?>
			</div>
            <div class="js-tabs">
    			<ul class="clearfix">
    				<li><?php echo $this->Html->link(__l('Deal Coupons'), '#tabs-'.$deal['Deal']['id']);?></li>
    			</ul>
    			<div id="tabs-<?php echo $deal['Deal']['id']; ?>" ><?php echo $this->element('deal_users-index', array('deal_id' => $deal['Deal']['id'], 'cache' => array('config' => 'site_element_cache'))); ?></div>
    		</div>
	<?php endif; ?>
    <?php if($this->request->params['action'] =='view'): ?>
      </div>  
      <?php endif;?>    
      	<?php if (($count == 1 || !empty($from_page)) && $this->request->params['action'] == 'view') {
        echo $this->element('../deals/sidebar', array('deal' => $deal, 'count' => $count, 'get_current_city' => $get_current_city, 'cache' => array('config' => 'site_element_cache_1_min', 'key' => $deal['Deal']['id'])));
        } ?>
      
     <?php if($this->request->params['action'] =='view'): ?> 
    </div>
    <?php endif; ?>
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
