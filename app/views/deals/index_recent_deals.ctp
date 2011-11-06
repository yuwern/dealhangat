<?php /* SVN: $Id: index_recent_deals.ctp 59852 2011-07-11 09:20:34Z vinothraja_091at09 $ */?>
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
     	<ol class="recent-list clearfix">
		<?php if(!empty($deals)): ?>
		  <?php foreach($deals as $deal): ?>
            <li>
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
                      <div class="deal-info">
                        <h3><?php echo $this->Html->link($this->Html->truncate($deal['Deal']['name'], 45, array('ending' => '...')), array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug']),array('title'=>$deal['Deal']['name']));?></h3>
                        <!--<ul class="info-list clearfix">
                          <li><a href="#" title="Groupdeal.com">Groupdeal.com</a></li>
                          <li><a href="#" title="Deal from Group Deal">Deal from Group Deal</a></li>
                        </ul>-->
                        <div class="recent-deal-description">
                        <?php echo $this->Html->truncate($deal['Deal']['description'],230, array('ending' => '...'));?>
                        </div>
                      </div>
                      <div class="bought-content">
                        <div class="sold clearfix">
                           <p class="bought-count">
        					   <?php echo $this->Html->cInt($deal['Deal']['deal_user_count']); ?>
                            </p>
							 <p class="deals-time"><?php echo __l("Ended on"); ?> <?php echo $this->Html->cDateTimeHighlight($deal['Deal']['end_date']); ?></p>
                        </div>
                       
                       <div class="bought-details">
                         	<dl class="price-count clearfix">
				            	<dt><?php echo __l('Price'); ?></dt>
                                <dd><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['Deal']['discounted_price'])); ?></dd>
					       </dl>
                          <div class="clearfix">
                            <dl class="price-sount-list">
                              	<dt><?php echo __l('Value'); ?></dt>
				            	<dd><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['Deal']['original_price'])); ?></dd>
                            </dl>
                            <dl class="price-sount-list">
                             	<dt><?php echo __l('Discount');?></dt>
    					        <dd><?php echo $this->Html->cInt($deal['Deal']['discount_percentage']) . "%"; ?></dd>
                            </dl>
                            <dl class="price-sount-list">
                                 <dt><?php echo __l('Savings'); ?></dt>
    					         <dd><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['Deal']['discount_amount'])); ?></dd>
                            </dl>
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
          	</li>
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
			if (!empty($deals)):
				echo $this->element('paging_links');
			endif;
			?>
		</div>
      
      </div>
      </div>
