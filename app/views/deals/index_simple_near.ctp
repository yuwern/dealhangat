<?php  if(!empty($deals) && !empty($has_near_by_deal)): ?>
<div class="blue-bg top clearfix">
    <div class="deal-tl">
      <div class="deal-tr">
        <div class="deal-tm">
          <h3> <?php echo __l('Nearby Deals');?> </h3>
        </div>
      </div>
    </div>
    <div class="side-deal-cl">
      <div class="side-deal-cr">
        <div class="block1-inner blue-bg-inner clearfix">
          <div class="side-deal">
            <ol class="side-deal-list">
              <?php
                	foreach($deals as $deal):
						?>
              <li>
                <h4><?php echo $this->Html->link($deal['Deal']['name'], array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug']),array('title' =>sprintf(__l('%s'),$deal['Deal']['name'])));?></h4>
                <div class="clearfix">
                  <div class="deal1-img"> <?php echo $this->Html->link($this->Html->showImage('Deal', $deal['Attachment'][0], array('dimension' => 'small_big_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($deal['Deal']['name'], false)), 'title' => $this->Html->cText($deal['Deal']['name'], false))), array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug']),array('title' =>sprintf(__l('%s'),$deal['Deal']['name']), 'escape' => false));?> </div>
                  <div class="deal-button">
                    <div class="deal-price clearfix">
                      <div class="deal-price-l">
                        <div class="deal-price-r clearfix">
                           <div class="deal-currency">
							<?php if(!empty($deal['Deal']['is_subdeal_available'])): ?>								
								<?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['SubDeal'][0]['discounted_price']));?>
							<?php else:?>
								<?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['Deal']['discounted_price']));?>
							<?php endif;?>
							</div>
                          <div class="deal-value-info"> 
								<span> 
								<?php if(!empty($deal['Deal']['is_subdeal_available'])): ?>
									<?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['SubDeal'][0]['original_price']));?> <?php echo __l('Value');?>
								<?php else:?>
									<?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['Deal']['original_price']));?> <?php echo __l('Value');?> 
								<?php endif;?>
								</span>
							</div>
                        </div>
                      </div>
                    </div>
                    <?php echo $this->Html->link(__l('View it'), array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug']),array('title' => __l('View it')), null, false);?> </div>
                </div>
              </li>
              <?php
					endforeach;
                 ?>
				 <li class="view-all"><?php echo $this->Html->link(__l('View all'), array('controller' => 'deals', 'action' => 'index', 'type' => 'near'),array('title' => __l('View all')), null, false);?></li>
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
  <?php  endif; ?>