	<div class="deal-view-inner-block clearfix">
	<div class="side1">
          <?php if(!empty($deals)): ?>
		  <?php 
		  foreach($deals as $deal): ?>
		 <div class="block1 clearfix" style='margin-top:10px'>
			<div class="side1-tl">
			  <div class="side1-tr">
				<div class="side1-tm"> </div>
			  </div>
			</div>
			<div class="side1-cl">
			  <div class="side1-cr">
				<div class="block1-inner g-box">
				  <h2 class="title"> 
					<!--span class="today-deal"><?php	//echo __l("Today's Deal").': ';?></span--> 
					<?php
						echo $this->Html->link($deal['Deal']['name'], array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug']),array('title' =>sprintf(__l('%s'),$deal['Deal']['name'])));
					?>
					</h2>
				<p class="company-msg-info">
					<span class="c-name"><?php echo $deal['Company']['name'] ;?></span>	
				</p>
			 <!-- Gallery Box -->
				<div class="gallery-box">
					<div class="rate">
						<div class="r1-rm"><sup>RM</sup><?php echo $this->Html->cCurrency($deal['Deal']['discounted_price']); ?></div>
						<div class="r2"><span><?php echo $deal['Deal']['deal_user_count']." "; ?><?php echo __l('Sales'); ?></span></div>
						<div class="r3">
							<div class="r3-l"><?php echo $this->Html->cInt($deal['Deal']['discount_percentage'], array('span'=>false))."%"; ?><br><span><?php echo __l('Discount');?></span></div>
							<div class="r3-r"><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($deal['Deal']['discount_amount'], array('span'=>false))); ?><br><span><?php echo __l('Savings'); ?></span></div>
							<div class="clearfix"></div>
						</div>
						<p class="beli_btn">	
						<?php	
							if(empty($deal['Deal']['is_subdeal_available'])){
								echo $this->Html->link(__l('Buy Now'), array('controller'=>'deals','action'=>'buy',$deal['Deal']['id']), array('title' => __l('Buy Now')));
							}else{
								echo $this->Html->link(__l('Buy Now'), '#', array('title' => __l('Buy Now'),'class' =>"button button1 js-multiple-sub-deal {'opendialog': 'js-open-subdeal-".$deal['Deal']['id']."'}"));
							}
						?>
						</p>
						<?php 
							if(empty($deal['Deal']['is_subdeal_available'])){
								echo $this->Html->link(__l('Present to a member!!'), array('controller'=>'deals','action'=>'buy',$deal['Deal']['id'],'type' => 'gift'), array('title' => __l('Present to a member!'),'class' =>'hadi'));
							}
							else{
								echo $this->Html->link(__l('Present to a member!!'), '#', array('title' => __l('Present to a member!'),'class' =>"hadi js-multiple-sub-deal {'opendialog': 'js-open-subdeal-gift-".$deal['Deal']['id']."'}"));
							}
						?>
						</div>
				  <div class="gallery">
					<div class="dtime-box">
						<h1><?php echo __l('Time left to buy');?></h1>
                            <?php if(($deal['Deal']['deal_status_id'] == ConstDealStatus::Open || $deal['Deal']['deal_status_id'] == ConstDealStatus::Tipped)): 
                                    if(empty($deal['Deal']['is_anytime_deal'])){
                            ?>
                                <div class="time">
                                    <div class="js-deal-end-countdown">&nbsp;</div>
                                    <span class="js-time hide">
                                        <?php
                                            echo $end_time = intval(strtotime($deal['Deal']['end_date'].' GMT') - time());
                                        ?>
                                    </span>
                                </div>
                                <?php
                                    } else {
                                ?>
                                <div class="timeunlimited">
                                    <span class="unlimited"><?php echo __l('Unlimited'); ?></span>
                                </div>
                                <?php 
                                    }
                                endif;
                            ?>
						<p class="a-center">
							<?php	
								if(empty($deal['Deal']['is_subdeal_available'])){
									echo $this->Html->link(__l('Buy Now'), array('controller'=>'deals','action'=>'buy',$deal['Deal']['id']), array('title' => __l('Buy Now'), 'class'=>'link-btn'));
								}else{
									echo $this->Html->link(__l('Buy Now'), '#', array('title' => __l('Buy Now'),'class' =>"button link-btn button1 js-multiple-sub-deal {'opendialog': 'js-open-subdeal-".$deal['Deal']['id']."'}"));
								}
							?>
						</p>
					</div>						 
				  <?php  echo $this->Html->link($this->Html->showImage('Deal', $deal['Attachment'][0], array('dimension' => 'small_new_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($deal['Deal']['name'], false)), 'title' => $this->Html->cText($deal['Deal']['name'], false))),array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug']),array('title'=>$deal['Deal']['name'],'escape' =>false, 'class'=>'js-image-timer '));?></div>
				  <div class="clearfix"></div>
				  </div>
				  <!-- Gallery Box -->
				  <!-- Share Box -->
                    <div class="share">
                       <span><?php echo __l('Share This Deal: '); ?></span>
                        <ul class="share">               
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
                            <li><fb:like href="<?php echo Router::url('/', true).$get_current_city.'/deal/'.$deal['Deal']['slug'];?>" layout="button_count" font="tahoma"></fb:like></li>
							<li class="tw">
                            <a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo $bityurl;?>" data-text="<?php echo urlencode_rfc3986($deal['Deal']['name'], false);?>">Tweet</a>
                            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></li>
                            <li><?php echo $this->Html->link(__l('Quick! Email a friend!'), 'mailto:?body='.__l('Check out the great deal on ').Configure::read('site.name').' - '.Router::url('/', true).$get_current_city.'/deal/'.$deal['Deal']['slug'].'&amp;subject='.__l('I think you should get ').Configure::read('site.name').__l(': ').$deal['Deal']['discount_percentage'].__l('% off at ').$deal['Company']['name'], array('target' => 'blank', 'title' => __l('Email'), 'class' => 'mail'));?></li>
                        </ul>
						<span class="ket-btn">
							<?php echo $this->Html->link(__l('+ Learn more'), array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug']),array('title' =>sprintf(__l('%s'),$deal['Deal']['name'])));?>
						</span>
						<div class="clearfix"></div>
                      </div>				  
				  <!-- Share Box -->
				</div>
			  </div>
			</div>
		  </div>
		 <?php endforeach;?>
		 <?php endif;?>
		  <div id="fb-root"></div>
			<script type="text/javascript">
			  window.fbAsyncInit = function() {
				FB.init({appId: '147267432014750', status: true, cookie: true,
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
<?php echo $this->element('../deals/sidebar', array('deal' => $deal, 'get_current_city' => $get_current_city, 'cache' => array('config' => 'site_element_cache_1_min', 'key' => $deal['Deal']['id'])));?>                
</div>
