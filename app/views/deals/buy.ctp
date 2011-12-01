<?php /* SVN: $Id: buy.ctp 54596 2011-05-25 12:35:27Z arovindhan_144at11 $ */ ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $this->Html->image('payment\step1.png') ?>
	

<h2><?php echo __l('Your Purchase'); ?></h2>
	<div class="buying-form">
	<?php echo $this->Form->create('Deal', array('action' => 'buy', 'class' => 'normal')); ?>
    	<table class="list">
        	<tr>
            	<th class="dl"><?php echo __l('Description'); ?></th>
                <th><?php echo __l('Quantity'); ?></th>
                <th class="dr"><?php echo __l('Price'); ?></th>
                <th class="dr"><?php echo __l('Total'); ?></th>
            </tr>
            <tr>
            	<td class="dl">
					<p class="deal-name"><?php echo $deal['Deal']['name'];?></p>
				<p class="gift-link"><?php
						 echo $this->Html->link(sprintf(__l('Give this %s as a gift'),Configure::read('site.name')), array('controller'=>'deals','action'=>'buy',$deal['Deal']['parent_id'],$deal['Deal']['id'],'type' => 'gift'), array('class' => 'gift', 'title' => sprintf(__l('Give this %s as a gift'),Configure::read('site.name'))));
                ?></p>
                </td>
				<td><?php
						$min_info = $deal['Deal']['buy_min_quantity_per_user'];
						$max_info = $deal['Deal']['buy_max_quantity_per_user'];
						if(empty($deal['Deal']['buy_max_quantity_per_user']) && empty($deal['Deal']['max_limit'])){
							$max_info = __l('Unlimited');
						}
						elseif(!empty($deal['Deal']['buy_max_quantity_per_user']) && !empty($deal['Deal']['max_limit'])){
							if(!empty($user_quantity)){
								$user_balance = $deal['Deal']['buy_max_quantity_per_user'] - $user_quantity;
							}
							else{
								$user_balance = $deal['Deal']['buy_max_quantity_per_user'];
							}
							$current_balance = $deal['Deal']['max_limit'] - $deal['Deal']['deal_user_count'];
                            if($current_balance  < $user_balance) {
                                $max_info = $current_balance;
                            } else{
								 $max_info = $user_balance;
							}							
						}
						elseif(!empty($deal['Deal']['buy_max_quantity_per_user']) && empty($deal['Deal']['max_limit'])){
							if(!empty($user_quantity)){
								$max_info = $deal['Deal']['buy_max_quantity_per_user'] - $user_quantity;
							}
							else{
								$max_info = $deal['Deal']['buy_max_quantity_per_user'];
							}
						}
						elseif(empty($deal['Deal']['buy_max_quantity_per_user']) && !empty($deal['Deal']['max_limit'])){
							$max_info = $deal['Deal']['max_limit'] - $deal['Deal']['deal_user_count'];
						}
						
						if(!empty($max_info)){
							if($max_info < $min_info){
								$max_info = $min_info;
							}
						}							
						echo $this->Form->input('quantity',array('label' => false, 'class' => 'js-quantity', 'after' => '<span class="info">' . sprintf(__l('Minimum Quantity: %s <br /> Maximum Quantity: %s'),$min_info,$max_info). '</span>'));?>
                        <?php echo $this->Form->input('user_available_balance',array('type' => 'hidden', 'value' => $user_available_balance));  ?>
                </td>
				<td class="dr"><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($this->request->data['Deal']['deal_amount'])); ?></td>
				<td class="dr">
					<?php if(Configure::read('site.currency_symbol_place') == 'left'):?>
						<?php echo Configure::read('site.currency');?><span class="js-deal-total"><?php echo $this->Html->cCurrency($this->request->data['Deal']['total_deal_amount']); ?></span>
					<?php else:?>
						<span class="js-deal-total"><?php echo $this->Html->cCurrency($this->request->data['Deal']['total_deal_amount']); ?></span><?php echo Configure::read('site.currency');?>
					<?php endif;?>
				</td>
            </tr>
			<?php if(Configure::read('wallet.is_handle_wallet_as_in_groupon') && $this->Auth->sessionValid()):?>
			<?php if(!empty($user_available_balance) && $user_available_balance != '0.00'):?>
			<tr>
				<td class="dr buy-dr" colspan="3"><?php echo Configure::read('site.name').' '.__l('bucks');?>
                    <span>(<?php
                        if($this->request->data['Deal']['total_deal_amount'] > $user_available_balance){
                            echo __l('You will have used all your Bucks.');
                        }elseif($this->request->data['Deal']['total_deal_amount'] < $user_available_balance){
                            $balance_amount = $user_available_balance - $this->request->data['Deal']['total_deal_amount'];
							echo "<span class='js-update-remaining-bucks'>".__l('You will have').' '.$balance_amount.' '.__l('Bucks remaining.')."</span>";
                        }elseif($this->request->data['Deal']['total_deal_amount'] == $user_available_balance){
                            echo __l('You will have used all your Bucks.');
                        }
                     ?>)</span>
                </td>
				<td class="dr buy-dr">
					<span>
						<?php 
							if($this->request->data['Deal']['total_deal_amount'] > $user_available_balance){
								$used_bucks = $user_available_balance;
							}elseif($this->request->data['Deal']['total_deal_amount'] < $user_available_balance){
								$used_bucks = $this->request->data['Deal']['total_deal_amount'];
							}elseif($this->request->data['Deal']['total_deal_amount'] == $user_available_balance){
								$used_bucks = $user_available_balance;
							} 
						?>
						<?php if(Configure::read('site.currency_symbol_place') == 'left'):?>
							<?php echo Configure::read('site.currency');?><span class="js-update-total-used-bucks"><?php echo $this->Html->cCurrency($used_bucks); ?>
						<?php else:?>
							<span class="js-update-total-used-bucks"><?php echo $this->Html->cCurrency($used_bucks);?></span> <?php echo Configure::read('site.currency'); ?>
						<?php endif;?>
					</span>
                </td>
			</tr>
			<?php endif;?>			
			<tr>
				<td class="dr buy-dr" colspan="3"><?php echo __l('My Price:').' '?></td>
				<td class="dr buy-dr">
				<?php $my_price = ($user_available_balance > $this->request->data['Deal']['total_deal_amount']) ? 0 : ($this->request->data['Deal']['total_deal_amount'] - $user_available_balance); ?>
				<?php if(Configure::read('site.currency_symbol_place') == 'left'):?>
					<?php echo Configure::read('site.currency');?> <span class="js-amount-need-to-pay"> <?php echo $this->Html->cCurrency($my_price); ?></span>
				<?php else:?>
					<span class="js-amount-need-to-pay"><?php echo $this->Html->cCurrency($my_price);?></span> <?php echo Configure::read('site.currency'); ?>
				<?php endif;?>
				</td>
			</tr>
			<?php endif;?>
			<?php if(Configure::read('charity.is_enabled') == 1 && $deal['Deal']['charity_percentage'] > 0):?>
                <tr>
				   <td class="dr buy-dr" colspan="4">
					<?php if(Configure::read('charity.who_will_choose') == ConstCharityWhoWillChoose::Buyer):?>
					<?php echo sprintf(__l('For every deal purchased, %s will donate %s of amount to charity that you selected from the pull-down'),Configure::read('site.name'),$deal['Deal']['charity_percentage'].'%');
					   echo $this->Form->input('charity_id',array('label' => false));  
					?>
					<?php else: ?>
						<?php 
						echo $this->Form->input('charity_id',array('type' => 'hidden'));  
						echo sprintf(__l('For every deal purchased, %s will donate %s of amount to'),Configure::read('site.name'),$deal['Deal']['charity_percentage'].'%');?>
						<?php if(!empty($deal['Charity']) && !(env('HTTPS'))): ?>
							<a href="<?php echo $deal['Charity']['url']; ?>" target="_blank"><?php echo $this->Html->cText($deal['Charity']['name']); ?></a>
						<?php else:  
						    echo __l('charity');
						endif; ?>
					<?php endif; ?>					
					</td>
				</tr>
		   <?php endif; ?>
        </table>
    
		 <?php 
            echo $this->Form->input('deal_id',array('type' => 'hidden')); 
            echo $this->Form->input('sub_deal_id',array('type' => 'hidden')); 
            echo $this->Form->input('user_id',array('type' => 'hidden')); 
            echo $this->Form->input('is_gift',array('type' => 'hidden')); 
            echo $this->Form->input('deal_amount',array('type' => 'hidden')); 
        ?>
        <?php if($this->request->data['Deal']['is_gift'] || !$this->Auth->sessionValid() || (!empty($user['User']['fb_user_id']) && empty($user['User']['email'])) ||!empty($gateway_options['paymentGateways'])):	 ?>
			<div class="login-left-block">
					<?php if($this->request->data['Deal']['is_gift']): ?>
						<div class="deal-gift-form">
							<?php
								echo $this->Form->input('gift_from',array('label' => __l('From'))); 
								echo $this->Form->input('gift_to',array('label' => __l('Friend Name'))); 
								echo $this->Form->input('gift_email',array('label' => __l('Friend Email'))); 
								echo $this->Form->input('message',array('type' => 'textarea', 'label' => __l('Message'))); 
							?>
						</div>
					<?php endif; ?>
					<?php if(Configure::read('wallet.is_handle_wallet_as_in_groupon')):?>
                    	<?php 
							$show_class= '';
							if($this->request->data['Deal']['total_deal_amount'] <= $user_available_balance)
								$show_class = 'hide';
							if($this->request->data['Deal']['deal_amount'] == 0)
								$show_class = '';	
						?>		
							  
					<div class="js-payment-gateway <?php echo $show_class; ?>">
						<?php $get_conversion_currency = $this->Html->getConversionCurrency();?>
						<?php if(isset($get_conversion_currency['supported_currency']) && empty($get_conversion_currency['supported_currency'])):?>
						<table>
								<tr>
									<td class="dl">
										<div class="page-info" id="currency-changing-info">
											<?php
												echo __l("<p>Note: Currently, Payment Gateways doesn't allow").' '.$get_conversion_currency['currency_code'].' '.__l("currency to be processed. It'll converted to").' '.$get_conversion_currency['conv_currency_code'].' '.__l("before processing. <strong>You wont be charged extra.</strong></p><p>You can also check the converted amount in <strong>My Transactions</strong>.</p>");
											?>
										</div>    
									</td>
								</tr>
						</table>
						<?php endif;?>
					<?php else:?>
						<div class="clearfix">
					<?php endif;?>
					<?php if(!$this->Auth->sessionValid() || (!empty($user['User']['fb_user_id']) && empty($user['User']['email']))): ?>
						<div class="deal-gift-form">
							<?php
								if(!$this->Auth->sessionValid()):
									echo $this->Form->input('User.username',array('label' => __l('Username'),'info' => __l('Must start with an alphabet. <br/> Must be minimum of 3 characters and <br/> Maximum of 20 characters <br/> No special characters and spaces allowed')));
									echo $this->Form->input('User.email',array('label' => __l('Email')));
									echo $this->Form->input('User.passwd', array('label' => __l('Password')));
									echo $this->Form->input('User.confirm_password', array('type' => 'password', 'label' => __l('Password Confirmation')));
								elseif(!empty($user['User']['fb_user_id']) && empty($user['User']['email'])):
									echo $this->Form->input('User.email',array('label' => __l('Email')));
								endif;  ?>
						</div>
					  <?php endif; ?>
					  <?php
						if(!isset($is_show_credit_card)):
							$is_show_credit_card = 0;
							if (empty($gateway_options['Paymentprofiles'])):
								$is_show_credit_card = 1;
							endif;
						endif;
					  ?>
                      <?php if($this->request->data['Deal']['deal_amount'] == 0){
					  			echo $this->Form->input('payment_gateway_id', array('type' => 'hidden'));
					  		}
							else{
					  echo $this->Form->input('payment_gateway_id', array('legend' => __l('Payment Type'), 'type' => 'radio', 'options' => $gateway_options['paymentGateways'], 'class' => 'js-payment-type {"is_show_credit_card":"' . $is_show_credit_card . '"}'));?>
                     
						&nbsp;<?php echo $this->Html->image('payment/banner1.png')?><br></br>
						&nbsp;<?php echo $this->Html->image('payment/banner2.png')?>


						<div class="user-payment-profile js-show-payment-profile <?php echo (!empty($gateway_options['paymentGateways'][ConstPaymentGateways::AuthorizeNet]) && (empty($this->request->data['Deal']['payment_gateway_id']) || $this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::AuthorizeNet)) ? '' : 'hide'; ?>">
						<?php 
							if (!empty($gateway_options['Paymentprofiles'])):
								echo $this->Form->input('payment_profile_id', array('legend' => __l('Pay with this card(s)'), 'type' => 'radio', 'options' => $gateway_options['Paymentprofiles']));
								echo $this->Html->link(__l('Add new card'), '#', array('class' => 'js-add-new-card'));
							endif;
						?>
					</div>
					<?php 
					if (false):
					/*
					if (!empty($gateway_options['paymentGateways'][ConstPaymentGateways::CreditCard]) || !empty($gateway_options['paymentGateways'][ConstPaymentGateways::AuthorizeNet])): 
					*/
					?>
						<div class="clearfix js-credit-payment <?php echo ($this->request->data['Deal']['payment_gateway_id'] == ConstPaymentGateways::CreditCard || (!empty($gateway_options['paymentGateways'][ConstPaymentGateways::AuthorizeNet]) && $is_show_credit_card)) ? '' : 'hide'; ?>">
						  <div class="billing-left">
						  <h3><?php echo __l('Billing Information'); ?></h3>
							<?php
								echo $this->Form->input('Deal.firstName', array('label' => __l('First Name')));
								echo $this->Form->input('Deal.lastName', array('label' => __l('Last Name')));
								echo $this->Form->input('Deal.creditCardType', array('label' => __l('Card Type'), 'type' => 'select', 'options' => $gateway_options['creditCardTypes']));
								echo $this->Form->input('Deal.creditCardNumber', array('AUTOCOMPLETE' => 'OFF', 'label' => __l('Card Number'))); ?>
								<div class="input date">
								<label><?php echo __l('Expiration Date'); ?> </label>
								<?php echo $this->Form->month('Deal.expDateMonth', array('value' => date('m')));
								echo $this->Form->year('Deal.expDateYear', date('Y'), date('Y')+25, array('value' => date('Y')+2));?>
								</div>
								<?php echo $this->Form->input('Deal.cvv2Number', array('AUTOCOMPLETE' => 'OFF', 'maxlength' =>'4', 'label' => __l('Card Verification Number:')));
							?>
							</div>
						  <div class="billing-right">
							<h3><?php echo __l('Billing Address'); ?></h3>
							<?php
								echo $this->Form->input('Deal.address', array('label' => __l('Address')));
								echo $this->Form->input('Deal.city', array('label' => __l('City')));
								echo $this->Form->input('Deal.state', array('label' => __l('State')));
								echo $this->Form->input('Deal.zip', array('label' => __l('Zip code')));
								echo $this->Form->input('Deal.country', array('label' => __l('Country'), 'type' => 'select', 'options' => $gateway_options['countries'], 'empty' => __l('Please Select')));
								echo $this->Form->input('Deal.is_show_new_card', array('type' => 'hidden', 'id' => 'UserIsShowNewCard'));
							 ?>   
							 </div>
						</div>
					<?php endif; ?>    
					
                 <?php } ?>
                 </div>
                <div class="submit-block clearfix">
					<?php if(Configure::read('wallet.is_handle_wallet_as_in_groupon')):?>
						<?php echo $this->Form->input('is_purchase_via_wallet', array('type' => 'hidden', 'value' => ($this->request->data['Deal']['total_deal_amount'] <= $user_available_balance) ? 1 : 0));?>
					<?php endif;?>                    
                    <?php echo $this->Form->submit(__l('Complete My Order'),array('title' => __l('Complete My Order'), 'class' => ((!empty($user_available_balance) || $user_available_balance != '0.00')  ? 'js-buy-confirm' : '')));?>
                    <div class="cancel-block">
                        <?php
                            if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'gift'){
                                 echo $this->Html->link(__l('Cancel'), array('controller' => 'deals', 'action' => 'buy',$deal['Deal']['id'], 'admin' => false), array('class' => 'cancel-button'));
                            } else {
                                echo $this->Html->link(__l('Cancel'), array('controller' => 'deals', 'action' => 'view',$deal['Deal']['slug'], 'admin' => false), array('class' => 'cancel-button'));
                            }
                        ?>
                    </div>
                </div>
			  </div>
       	<?php else: ?>
            <div class="submit-block clearfix">
                <?php echo $this->Form->submit(__l('Complete My Order'),array('title' => __l('Complete My Order'), 'class' => ($user_available_balance ? 'js-buy-confirm' : '')));?>
                <div class="cancel-block">
                    <?php
                        if(!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'gift'){
                             echo $this->Html->link(__l('Cancel'), array('controller' => 'deals', 'action' => 'buy',$deal['Deal']['id'], 'admin' => false), array('class' => 'cancel-button'));
                        }else{
                            echo $this->Html->link(__l('Cancel'), array('controller' => 'deals', 'action' => 'view',$deal['Deal']['slug'], 'admin' => false), array('class' => 'cancel-button'));
                        }
    
                    ?>
                </div>
            </div>
        <?php endif; ?>
		<?php	echo $this->Form->end();?>
    <?php if(!$this->Auth->sessionValid()):?>
		<div class="login-right-block js-right-block">
            <div class="login-message-lineheight js-login-message ">
                <h3><?php echo __l('Already Have An Account?');?></h3>
               
                <div class="clearfix">
                 <p class="login-info-block"><?php echo sprintf(__l('If you have purchased a %s before, you can sign in using your %s.'), Configure::read('site.name'),Configure::read('user.using_to_login')); ?></p>
                <div class="submit-block cancel-block submit-cancel-block">
                    <?php echo $this->Html->link(__l('Login'), '#', array('title' => __l('Sign In'), 'class' => "cancel-button js-toggle-show {'container':'js-login-form', 'hide_container':'js-login-message'}"));?>
                </div>
                </div>
                <div class="facebook-block">
            <?php if(!(!empty($this->request->params['prefix']) && $this->request->params['prefix'] == 'admin') and Configure::read('facebook.is_enabled_facebook_connect')):  ?>
                <div class="facebook-left">
                <p class="already-info"><?php echo __l('Already have an account on Facebook?'); ?></p>
                <p><?php echo sprintf(__l('Use it to sign in to %s!'), Configure::read('site.name')); ?></p>
                </div>
                <div class="facebook-right">
					<?php  if (env('HTTPS')) { $fb_prefix_url = 'https://www.facebook.com/images/fbconnect/login-buttons/connect_dark_medium_short.gif';}else{ $fb_prefix_url = 'http://static.ak.fbcdn.net/images/fbconnect/login-buttons/connect_light_medium_short.gif';}?>
					<?php echo $this->Html->link($this->Html->image($fb_prefix_url, array('alt' => __l('[Image: Facebook Connect]'), 'title' => __l('Facebook connect'))), array('controller' => 'users', 'action' => 'login','type'=>'facebook'), array('escape' => false)); ?>
                </div>
			
            <?php endif; ?>
            	</div>
            </div>
            <div class="js-login-form hide">
                <?php
				// Temp Fix Avoid teh Validation Message in login Page due the Validation the Another Form
				unset($this->validationErrors['User']['username']);
				unset($this->validationErrors['User']['passwd']);
				$subdealid = (!empty($sub_deal_id))? '/'.$sub_deal_id:'';
				echo $this->element('users-login', array('f' => 'deals/buy/'.$this->request->data['Deal']['deal_id'].''.$subdealid, 'cache' => array('config' => 'site_element_cache')));?>
            </div>
        </div>
    <?php endif;?>
	</div>
	
