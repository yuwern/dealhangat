<?php /* SVN: $Id: index_recent_deals.ctp 44785 2011-02-19 10:54:51Z aravindan_111act10 $ */?>
<div class="js-response">
  <div class="recentread-side1">
    <div class="side1-tl">
                  <div class="side1-tr">
                    <div class="side1-tm"> </div>
                  </div>
                </div>
                <div class="side1-cl">
                  <div class="side1-cr">
                    <div class="block1-inner clearfix">
            <h2><?php echo $sub_title;?> </h2>
        <?php echo $this->element('paging_counter'); ?>
	 </div>
                  </div>
                </div>
                <div class="side1-bl">
                  <div class="side1-br">
                    <div class="side1-bm"> </div>
                  </div>
                </div>
     	<ol class="recent-list near-list clearfix">
		<?php if(!empty($deals) && (!empty($has_near_by_deal) || $this->request->params['named']['type'] == 'main' || $this->request->params['named']['type'] == 'side')): ?>
		  
		  <?php 
		  $count = 1;
		  foreach($deals as $deal): 
		  if($count%3 == 0)
		  {
		  	$class =' class="last-deal"'; 
		  } else {
		  	$class =''; 
		  }
		  	?>
            <li<?php echo $class; ?>>
              <div class="deals-content clearfix">
                <div class="side1-tl">
                  <div class="side1-tr">
                    <div class="side1-tm"> </div>
                  </div>
                </div>
                <div class="side1-cl">
                  <div class="side1-cr">
                    <div class="block1-inner clearfix">
                      <div class="deal-img">
                         <?php  echo $this->Html->link($this->Html->showImage('Deal', $deal['Attachment'][0], array('dimension' => 'small_big_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($deal['Deal']['name'], false)), 'title' => $this->Html->cText($deal['Deal']['name'], false))),array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug']),array('title'=>$deal['Deal']['name'],'escape' =>false));?>
                       </div>
                      <div class="deal-info deal-info1">
<div class="clearfix">
<div class="price-left-block clearfix">
                        <h3><?php echo $this->Html->link(html_entity_decode($this->Html->truncate($deal['Deal']['name'],30, array('ending' => '...'))), array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug']),array('title'=>$deal['Deal']['name']));?></h3>
                        <!--<ul class="info-list clearfix">
                          <li><a href="#" title="Groupdeal.com">Groupdeal.com</a></li>
                          <li><a href="#" title="Deal from Group Deal">Deal from Group Deal</a></li>
                        </ul>-->
					<div class="bought-content">
                        <div class="sold clearfix">
                           <p class="bought-count">
							 <?php echo $this->Html->cInt($deal['Deal']['deal_user_count']);?>
						   </p>
						</div> `
					</div>

</div>
<div class="price-right-block-near clearfix">
                   <div class="bought-details clearfix">
							<dl class="price-count clearfix">
				            	<dt><?php echo('price: '); ?></dt>
                                <dd><?php echo (empty($deal['Deal']['is_subdeal_available'])) ? $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['Deal']['discounted_price'])) : $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['SubDeal'][0]['discounted_price']));?>
								</dd>
					        </dl>

                      	<?php
					if($this->Html->isAllowed($this->Auth->user('user_type_id')) && $deal['Deal']['deal_status_id'] != ConstDealStatus::Draft && $deal['Deal']['deal_status_id'] != ConstDealStatus::PendingApproval):
						if($deal['Deal']['deal_status_id'] == ConstDealStatus::Open || $deal['Deal']['deal_status_id'] == ConstDealStatus::Tipped):
							if(empty($deal['Deal']['is_subdeal_available'])){
								 echo $this->Html->link(__l('Buy Now'), array('controller'=>'deals','action'=>'buy',$deal['Deal']['id']), array('title' => __l('Buy Now'),'class' =>'near-button'));
							}
							else{
								 echo $this->Html->link(__l('Buy Now'), '#', array('title' => __l('Buy Now'),'class' =>"near-button near-button-multi js-multiple-sub-deal {'opendialog': 'js-open-subdeal-".$deal['Deal']['id']."'}"));
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
                                                      <p class="deal-buy"> <?php echo $this->Html->link($this->Html->siteCurrencyFormat(($subdeal['discounted_price'])), array('controller'=>'deals','action'=>'buy', $deal['Deal']['id'], $subdeal['id']),array('title' => __l('Buy').' - '.$this->Html->siteCurrencyFormat($subdeal['discounted_price']),'escape' => false));?></p>
                                                      <?php endif;?>
                                                 </div>
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
                         
    </div>


                        <?php if($deal['Deal']['deal_status_id'] != ConstDealStatus::Upcoming && $deal['Deal']['deal_status_id'] != ConstDealStatus::Draft && $deal['Deal']['deal_status_id'] != ConstDealStatus::PendingApproval): ?>
               <div class="clearfix">
                        <div class="clearfix">
                    <?php if(($deal['Deal']['deal_status_id'] == ConstDealStatus::Open || $deal['Deal']['deal_status_id'] == ConstDealStatus::Tipped)): 
							if(empty($deal['Deal']['is_anytime_deal'])){
					?>
                        <dl class="near-dl-list clearfix">
                            <dt><?php echo __l('Time Left: ');?></dt>
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
                    	 <dl class="near-dl-list clearfix">
                            <dt><?php echo __l('Time Left: ');?></dt>
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
                                       <?php elseif($deal['Deal']['deal_status_id'] == ConstDealStatus::Closed || $deal['Deal']['deal_status_id'] == ConstDealStatus::Canceled || $deal['Deal']['deal_status_id'] == ConstDealStatus::Expired || $deal['Deal']['deal_status_id'] == ConstDealStatus::PaidToCompany): ?>
                        <dl class="near-dl-list clearfix">
                            <dt><?php echo __l('This deal ended at:');?></dt>
                            <dd><?php echo $this->Html->cDateTime($deal['Deal']['end_date'])?></dd>
                         </dl>
                    <?php endif; ?>
                  </div>
                      </div>
			   <?php endif; ?>
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
          	</li>
            <?php $count++; ?>
			  <?php endforeach; ?>
			<?php else: ?>
				<li>
                   <div class="side1-tl">
                      <div class="side1-tr">
                        <div class="side1-tm"> </div>
                      </div>
                    </div>
                    <div class="side1-cl">
                    <div class="side1-cr">
                    <div class="block1-inner clearfix">
                    <p class="notice"><?php echo __l('No Deals available');?></p>
                     </div>
                    </div>
                  </div>
                    <div class="side1-bl">
                      <div class="side1-br">
                        <div class="side1-bm"> </div>
                      </div>
                    </div>
                </li>
			<?php endif; ?>
			</ol>
        	<div class="clearfix">
			<?php
			if (!empty($deals) && (!empty($has_near_by_deal) || $this->request->params['named']['type'] == 'main' || $this->request->params['named']['type'] == 'side')):
				echo $this->element('paging_links');
			endif;
			?>
		</div>
      
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