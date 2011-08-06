<?php /* SVN: $Id: add.ctp 59627 2011-07-09 04:23:18Z arovindhan_144at11 $ */ ?>
<div class="giftUsers form">
	<h2><?php echo __l('Customize Your Gift Card');?></h2>
        <?php echo $this->Form->create('GiftUser', array('class' => 'normal'));?>
		<div class="clearfix">
			<div class="gift-card clearfix">
			<div class="gift-side1">
            <h3 class="gift-title"><span id="js-gift-from"><?php echo $this->request->data['GiftUser']['from']; ?></span></h3>
            <p> <?php echo __l('has given you'); ?></p>
            <p class="card-amount">
				<?php if(Configure::read('site.currency_symbol_place') == 'left'):?>
					<?php echo Configure::read('site.currency');?><span id="js-gift-amount"><?php echo $this->Html->cCurrency($this->request->data['GiftUser']['amount']); ?>
				<?php else:?>
					<span id="js-gift-amount"><?php echo $this->Html->cCurrency($this->request->data['GiftUser']['amount']);?></span> <?php echo Configure::read('site.currency'); ?>
				<?php endif;?>				
				</span></p>
            <p><?php echo sprintf(__l('credit to %s '), Router::url('/', true)); ?></p>
            <div class="remeber-block">
            <p><?php echo __l('Redemption Code:'); ?>
            </p>
            <p class="code-info">
            xxxxxx-xxxxxx
            </p>
            </div>
			</div>
			<div class="gift-side2">
            <dl class="card-info clearfix">
            <dt><?php echo __l('to'); ?></dt>
            <dd id="js-gift-to"><?php echo $this->request->data['GiftUser']['friend_name']; ?></dd>
            </dl>
            <p id="js-gift-message" class="card-message">
            <?php echo $this->request->data['GiftUser']['message']; ?>
            </p>
			</div>
			</div>
        	<div class="gift-form">
			<?php
				if(Configure::read('site.currency_symbol_place') == 'left'):
					$currecncy_place = 'between';
				else:
					$currecncy_place = 'after';
				endif;	
			?>		
        	<?php
				echo $this->Form->input('user_available_balance',array('type' => 'hidden', 'value' => $user_available_balance));
                echo $this->Form->input('user_id', array('type' => 'hidden'));
				echo $this->Form->input('from', array('label' => __l('From'),'type'=>'text', 'info' => __l('Name you want the recipient to see'), 'class' => '{"update" : "js-gift-from", "default_value" : "Gift Buyer"}'));
				if(!empty($user['User']['fb_user_id']) && empty($user['User']['email'])):
					echo $this->Form->input('User.email', array('label' => __l('Email')));
				endif; 	
        		echo $this->Form->input('friend_name', array('label' => __l('Friend Name'), 'class' => '{"update" : "js-gift-to", "default_value" : "Gift Receiver"}'));
        		echo $this->Form->input('friend_mail', array('label' => __l('Delivery to Email')));
        		echo $this->Form->input('message', array('label' => __l('Personal Message (Optional)'), 'class' => '{"update" : "js-gift-message", "default_value" : "Your Message"}'));
				echo $this->Form->input('amount', array('label' => __l('Gift Card Amount'),  $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>', 'class' => '{"update" : "js-gift-amount", "default_value" : "0"}'));
        	?>
        	</div>
        	</div>
			<div class="wallet-block">
			<?php if(Configure::read('wallet.is_handle_wallet_as_in_groupon')):?>
				<?php $my_price = ($user_available_balance > $this->request->data['GiftUser']['amount']) ? 0 : ($this->request->data['GiftUser']['amount'] - $user_available_balance); ?>
				<?php
					if(!empty($this->request->data['GiftUser']['amount'])){ //3 5
						if($this->request->data['GiftUser']['amount'] >= $user_available_balance){
							$gift_price = 0;
						}else{
							$gift_price = $user_available_balance - $this->request->data['GiftUser']['amount'];
						}					
					}else{
						$gift_price = $user_available_balance;
					}
				?>
				<table>
					<tr>
						<td class="dl">
							<p>
								<?php echo Configure::read('site.name').' '.__l('bucks')?>
								<span>
									(<?php
									if(empty($gift_price)){
										echo "<span class='js-update-remaining-bucks'>".__l('You will have used all your Bucks.')."</span>";
									}else{
										echo "<span class='js-update-remaining-bucks'>".__l('You will have').' '.$gift_price.' '.__l('Bucks remaining.')."</span>";						
									}
									?>)
								</span>
							</p>
						</td>
					</tr>
					<tr>
						<td>
							<p>
								<?php echo __l('My Price')?>
								<?php if(Configure::read('site.currency_symbol_place') == 'left'):?>
									<?php echo Configure::read('site.currency');?><span class="js-amount-need-to-pay"><?php echo $this->Html->cCurrency($my_price); ?></span>
								<?php else:?>
									<span class="js-amount-need-to-pay"><?php echo $this->Html->cCurrency($my_price);?></span> <?php echo Configure::read('site.currency'); ?>
								<?php endif;?>
							</p>
						</td>
					</tr>				
				</table>
			<?php endif;?>
			</div>		
			<?php
				$is_show_credit_card = 0;
				if (empty($gateway_options['Paymentprofiles'])):
					$is_show_credit_card = 1;
				endif;
			  ?>
			<div class="clearfix">
				<div class="js-payment-gateway">
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
				  <?php echo $this->Form->input('payment_gateway_id', array('legend' => __l('Payment Type'), 'type' => 'radio', 'options' => $gateway_options['paymentGateways'], 'class' => 'js-payment-type {"is_show_credit_card":"' . $is_show_credit_card . '"}'));?>
					<div class="user-payment-profile js-show-payment-profile <?php echo (!empty($gateway_options['paymentGateways'][ConstPaymentGateways::AuthorizeNet])) ? '' : 'hide'; ?>">
						<?php 
							if (!empty($gateway_options['Paymentprofiles'])):
								echo $this->Form->input('payment_profile_id', array('legend' => __l('Pay with this card(s)'), 'type' => 'radio', 'options' => $gateway_options['Paymentprofiles']));
								echo $this->Html->link(__l('Add new card'), '#', array('class' => 'js-add-new-card'));
							endif;
						?>
					</div>
					<?php if(!empty($gateway_options['paymentGateways'][ConstPaymentGateways::CreditCard]) || !empty($gateway_options['paymentGateways'][ConstPaymentGateways::AuthorizeNet])): ?>
					<div class="clearfix js-credit-payment <?php echo (!empty($gateway_options['paymentGateways'][ConstPaymentGateways::CreditCard]) && !empty($this->request->data['GiftUser']['payment_gateway_id']) && $this->request->data['GiftUser']['payment_gateway_id'] == ConstPaymentGateways::CreditCard || (!empty($gateway_options['paymentGateways'][ConstPaymentGateways::AuthorizeNet]) && $is_show_credit_card)) ? '' : 'hide'; ?>">
					  <div class="billing-left">
					  <h3><?php echo __l('Billing Information'); ?></h3>
						<?php
							echo $this->Form->input('GiftUser.firstName', array('label' => __l('First Name')));
							echo $this->Form->input('GiftUser.lastName', array('label' => __l('Last Name')));
							echo $this->Form->input('GiftUser.creditCardType', array('label' => __l('Card Type'), 'type' => 'select', 'options' => $gateway_options['creditCardTypes']));
							echo $this->Form->input('GiftUser.creditCardNumber', array('AUTOCOMPLETE' => 'OFF', 'label' => __l('Card Number'))); ?>
							<div class="input date">
							<label><?php echo __l('Expiration Date'); ?> </label>
							<?php echo $this->Form->month('GiftUser.expDateMonth', array('value' => date('m'))); 
							echo $this->Form->year('GiftUser.expDateYear', date('Y'), date('Y')+25, array('value' => date('Y')+2));?>
							</div>
							<?php echo $this->Form->input('GiftUser.cvv2Number', array('AUTOCOMPLETE' => 'OFF', 'maxlength' =>'4', 'label' => __l('Card Verification Number:')));
						?>
						</div>
					  <div class="billing-right">
						<h3><?php echo __l('Billing Address'); ?></h3>
						<?php
							echo $this->Form->input('GiftUser.address', array('label' => __l('Address')));
							echo $this->Form->input('GiftUser.city', array('label' => __l('City')));
							echo $this->Form->input('GiftUser.state', array('label' => __l('State')));
							echo $this->Form->input('GiftUser.zip', array('label' => __l('Zip code')));
							echo $this->Form->input('GiftUser.country', array('label' => __l('Country'), 'type' => 'select', 'options' => $gateway_options['countries'], 'empty' => __l('Please Select')));
							echo $this->Form->input('GiftUser.is_show_new_card', array('type' => 'hidden', 'id' => 'UserIsShowNewCard'));
						 ?>   
						 </div>
					</div>
				<?php endif; ?>
				</div>
				<?php if(Configure::read('wallet.is_handle_wallet_as_in_groupon')):?>
					<?php echo $this->Form->input('is_purchase_via_wallet', array('type' => 'hidden', 'value' => ($this->request->data['GiftUser']['amount'] <= $user_available_balance) ? 1 : 0));?>
				<?php endif;?>
                <?php echo $this->Form->input('group_wallet', array('type' => 'hidden', 'value' => Configure::read('wallet.is_handle_wallet_as_in_groupon')));?>
                 <div class="submit-block clearfix">
                    <?php
                    	echo $this->Form->submit(__l('Complete Purchase'));
                    ?>
                </div>
                <?php
                	echo $this->Form->end();
                ?>
			</div>
       
</div>