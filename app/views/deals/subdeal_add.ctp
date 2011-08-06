<?php /* SVN: $Id: add.ctp 51248 2011-04-22 11:37:47Z lakshmi_150act10 $ */ ?>
<fieldset class="form-block round-5">
		<legend class="round-5"><?php echo __l('Deal Information'); ?></legend>
		<dl class="list">
			<dt><?php echo __l('Deal');?></dt>
				<dd>
					<?php echo $this->Html->cText($deal['Deal']['name']);?>
				</dd>
			<dt><?php echo __l('Current Deal Status');?></dt>
				<dd>
					<?php echo $this->Html->cText($deal['DealStatus']['name']);?>
				</dd>
			<dt><?php echo __l('No. of min. coupons');?></dt>
				<dd>
					<?php echo $this->Html->cText($deal['Deal']['min_limit']);?>
				</dd>
			<dt><?php echo __l('No. of max. coupons');?></dt>
				<dd>
					<?php echo $this->Html->cText($deal['Deal']['max_limit']);?>
				</dd>
			<dt><?php echo __l('Deal Lifetime');?></dt>
				<dd>
                    <p><?php echo __l('Created On').' '.$this->Html->cDateTime($deal['Deal']['created']);?></p>
    				<p><?php echo __l('Start(ed) On').' '.$this->Html->cDateTime($deal['Deal']['start_date']);?></p>
	    			<p><?php echo __l('End(ed) On').' '.$this->Html->cDateTime($deal['Deal']['end_date']);?></p>
                </dd>
	</fieldset>

<?php 	echo $this->Form->create('Deal', array('action' => 'subdeal_add', 'class' => 'normal')); 
		echo $this->Form->input('Deal.main_deal_id',array('type' => 'hidden'));
		unset($this->request->data['Deal']['main_deal_id']);
		$count = 2;
		if(!empty($this->request->data['Deal'])){
			$count = count($this->request->data['Deal']);
		}
		for($i=0; $i< $count; $i++){	
?>
<?php if($i == 2){ ?>
	<div class="js-subdeal-addmore-deal">
<?php } ?>
   <div id="js-subdeal-<?php echo $i; ?>" class="js-subdeal-<?php echo $i; ?> {'available': '<?php echo (!empty($this->request->data['Deal'][$i]['id'])) ? $this->request->data['Deal'][$i]['id'] : 'Notpresent';?>', 'main_deal_id': '<?php echo (!empty($this->request->data['Deal'][$i]['id'])) ? $deal['Deal']['id'] : '';?>'}">
   
   <fieldset class="form-block round-5">
		<legend class="round-5"><?php echo __l('SubDeal').' #'.($i+1); ?></legend>
	   <fieldset class="form-block round-5">
		<legend class="round-5"><?php echo __l('General'); ?></legend>
		<?php
			echo $this->Form->input('Deal.'.$i.'.name',array('label' => __l('Name')));
		?>				
			
						  <?php	echo $this->Form->input('Deal.'.$i.'.max_limit', array('label'=>__l('No. of max. coupons'), 'info' => __l('Maximum limit of coupons can be bought for this deal. Leave blank for no limit.'))); ?>
				
		  </fieldset>		  
		   <fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Price'); ?></legend>
				<?php
					if(Configure::read('site.currency_symbol_place') == 'left'):
						$currecncy_place = 'between';
					else:
						$currecncy_place = 'after';
					endif;	
				?>
				<?php
					$class = "{ 'DealOriginalPrice': 'Deal".$i."OriginalPrice', 'DealDiscountPercentage': 'Deal".$i."DiscountPercentage', 'DealDiscountAmount': 'Deal".$i."DiscountAmount', 'DealDiscountedPrice': 'Deal".$i."DiscountedPrice', 'DealSavings': 'Deal".$i."Savings', 'DealCalculatorDiscountedPrice': 'Deal".$i."CalculatorDiscountedPrice', 'DealCalculatorBonusAmount': 'Deal".$i."CalculatorBonusAmount', 'DealCalculatorCommissionPercentage': 'Deal".$i."CalculatorCommissionPercentage' , 'DealCalculatorMinLimit': 'Deal".$i."CalculatorMinLimit', 'DealBonusAmount': 'Deal".$i."BonusAmount', 'DealCommissionPercentage': 'Deal".$i."CommissionPercentage', 'ivalue': '".$i."'}";
					echo $this->Form->input('Deal.'.$i.'.original_price',array('label' => __l('Original price'),'class' => "js-price js-sub-deal-price ".$class , $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>')); ?>
					<div class="two-col-form discount-form-block clearfix">
						<?php echo $this->Form->input('Deal.'.$i.'.discount_percentage', array('class' => "js-sub-deal-price ".$class ,'label' => __l('Discount (%)')));  ?>
						<span class="sep-or"><?php echo __l('OR'); ?></span>
						<?php echo $this->Form->input('Deal.'.$i.'.discount_amount', array('class' => "js-sub-deal-amount ".$class ,'label' => __l('Discount Amount'), $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>')); ?>
					</div>
					<?php echo $this->Form->input('Deal.'.$i.'.savings', array('type'=>'text',  'label' => __l('Savings for user'),  'readonly' => 'readonly', $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>'));
					echo $this->Form->input('Deal.'.$i.'.discounted_price', array('label' => __l('Discounted price for user'),'type'=>'text', 'readonly' => 'readonly', $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>'));
				?>
				<!-- ADVANCE/PARTIALLY PAYMENT -->
					<?php $is_adv_enabled = Configure::read('deal.is_enable_payment_advance'); ?>
					<?php if(Configure::read('deal.is_enable_payment_advance')): ?>
						<?php echo $this->Form->input('Deal.'.$i.'.is_enable_payment_advance', array('type' => 'checkbox', 'class' => 'js-enable-advance-payment {selected_container:"'.$i.'"}', 'label' => __l('Allow users to make partially payments?'), 'info' => __l('If checked, user can make a partial payment now and pay the remaining at the redeem location.')));?>
						<div class="js-advance-payment-box<?php echo '-'.$i;?>  <?php echo (!empty($this->request->data['Deal'][$i]['is_enable_payment_advance']) ? '' : 'hide');?>">
							<?php
								echo $this->Form->input('Deal.'.$i.'.pay_in_advance',array('label' => __l('Advance amount'), 'class' => 'js-pay-in-advance {selected_container:"'.$i.'"}', $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>'));
								echo $this->Form->input('Deal.'.$i.'.payment_remaining',array('label' => __l('Pending amount'), 'type' => 'hidden', 'class' => '', $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>'));									
							?>
							<dl class="result-list clearfix">
								<dt><?php echo __l('Pay in Advance').'('.Configure::read('site.currency').'):  '; ?></dt>
									<dd>
										<span id="js-pay_in_advance<?php echo '-'.$i;?>"><?php echo (!empty($this->request->data['Deal'][$i]['pay_in_advance']) ? $this->request->data['Deal'][$i]['pay_in_advance'] : '0');?></span>
									</dd>
								<dt><?php echo __l('Pay remaining').'('.Configure::read('site.currency').'):  '; ?></dt>
									<dd>
										<span id="js-payment_remaining<?php echo '-'.$i;?>"><?php echo (!empty($this->request->data['Deal'][$i]['payment_remaining']) ? $this->request->data['Deal'][$i]['payment_remaining'] : '0');?></span>
									</dd>
							</dl>
						</div>						
					<?php endif; ?>
                <div class="page-info">
				<?php
					echo __l('When you want to add as a free deal, just give 100% discount for this deal');
				 ?>
			     </div>
			</fieldset>
		   <fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Commission'); ?></legend>
			 <div class="page-info">
				<?php
					echo __l('Total Commission Amount = Bonus Amount + ((Discounted Price * Number of Buyers) * Commission Percentage/100))');
				 ?>
			</div>
			<div class="clearfix">
			<div class="amount-block commision-form-block">
			<?php
			echo $this->Form->input('Deal.'.$i.'.bonus_amount', array('class' => "js-sub-deal-bonus-amount ".$class ,'label' => __l('Bonus Amount'),'value' => '0.00',$currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>'));
            ?>
           <span class="info"> <?php echo __l('This is the flat fee that the company will pay for the whole deal.');?></span>
           <?php if(($this->Auth->user('user_type_id') != ConstUserTypes::Admin) && (Configure::read('deal.is_admin_enable_commission')) && Configure::read('deal.commission_amount_type') == 'fixed'):
					echo $this->Form->input('Deal.'.$i.'.commission_percentage', array('class' => "js-sub-deal-bonus-amount ".$class, 'readonly' =>'readonly', 'info' => __l('This is the commission that company will pay for the whole deal in percentage.'), 'label' => __l('Commission (%)')));
				else:
					 echo $this->Form->input('Deal.'.$i.'.commission_percentage', array('class' => "js-sub-deal-bonus-amount ".$class, 'info' => __l('This is the commission that company will pay for the whole deal in percentage.'), 'label' => __l('Commission (%)')));
				 endif; 
				?>
            <?php
        	//echo $this->Form->input('Deal.'.$i.'.commission_percentage', array('class' => "js-sub-deal-bonus-amount ".$class, 'info' => __l('This is the commission that company will pay for the whole deal in percentage.'), 'label' => __l('Commission (%)')));
			?>
			</div>
			<div class="calculator-block round-5">
				<?php echo $this->element('../deals/subdeal_commission_calculator', array('i'=> $i, 'class' => $class, 'cache' => array('config' => 'site_element_cache', 'key' => $this->Auth->user('id') . $i))); ?>
			</div>
			</div>
        </fieldset>
</fieldset>	
</div>
<?php 
		}
?>
<?php if($count >= 2){ ?>
	<?php if($i == 2){ ?>
		<div class="js-subdeal-addmore-deal">
	<?php } ?> 
	</div>   
<?php } ?>
<div class="more-info-block sub-deal-more-info">
<span class="js-subdeal-add add-more-deal {'id': '<?php echo $count; ?>'}"> Add more </span>
<span class="js-subdeal-delete delete-deal {'id': '<?php echo $count; ?>'}"> Delete </span>
</div>
<div class="submit-block clearfix">
	<?php echo $this->Form->input('is_save_draft', array('type' => 'hidden', 'id' => 'js-save-draft'));?>
	<?php
		echo $this->Form->submit(__l('Add'), array('class' => 'js-update-order-field'));
		echo $this->Form->submit(__l('Save as Draft'), array('name' => 'data[Deal][save_as_draft]', 'class' => 'js-update-order-field')); 
	?>
</div>
<?php
	echo $this->Form->end();
?>