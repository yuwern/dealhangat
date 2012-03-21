<?php /* SVN: $Id: admin_edit.ctp 54983 2011-05-28 12:18:42Z aravindan_111act10 $ */?>
<?php echo $this->element('js_tiny_mce_setting', array('cache' => array('config' => 'site_element_cache')));?>
<div class="deals form js-responses">
<?php echo $this->Form->create('Deal', array('class' => 'normal js-upload-form {is_required:"false"}', 'enctype' => 'multipart/form-data'));?>
	<fieldset>
    <h2><?php echo __l('Edit Deal');?></h2>
	<div class="js-validation-part">
		<fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Type'); ?></legend>
			<div class="clearfix">
				<div class="hide">
					<?php
						// Toggling Sub Deal Option Disabled in Edit Pages //
						echo $this->Form->input('is_subdeal_available', array('label' => __l('Add Sub Deals'), 'info' => __l('If checked, you can add multiple sub deals for this deal. You\'ll be redirected to sub deal section after filling up the below information.')));
					?>
				</div>
				<?php if(Configure::read('deal.is_side_deal_enabled')): ?>
					<?php echo $this->Form->input('is_side_deal', array('label'=>__l('Side Deal'), 'info'=>__l('Side deals will be displayed in the side bar of the home page.')));?>
				<?php endif; ?>
				<div>
					<?php 
						echo $this->Form->input('is_anytime_deal', array('label' => __l('Any Time Deal'), 'info' => __l('This type of deal does not have closing date or expiry date. It can only be closed manually by Site Administrator or Specifying Maximum Buy Quantity')));
					?>	
				</div>
			</div>
		</fieldset>
	    <fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('General'); ?></legend>
			<?//php echo $this->Html->link($this->Html->showImage('Deal', $this->request->data['Attachment'][0], array('dimension' => 'normal_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($this->request->data['Deal']['name'], false)), 'title' => $this->Html->cText($this->request->data['Deal']['name'], false))), array('controller' => 'deals', 'action' => 'view',  $deal['Deal']['slug'], 'admin' => false), null, array('inline' => false)); ?>
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('deal_category_id', array('empty'=>'Select', 'options'=>$categories));
				echo $this->Form->input('name_ms',array('label' => __l('Name Malay')));
				echo $this->Form->input('name',array('label' => __l('Name')));
				echo $this->Form->input('company_id', array('label' => __l('Company'),'empty' => 'Please Select'));
			?>
				<div class="clearfix date-time-block">
					<div class="input date-time clearfix required">
						<div class="js-datetime">
							<?php echo $this->Form->input('start_date', array('label' => __l('Start Date'),'minYear' => date('Y', strtotime($this->request->data['Deal']['start_date'])), 'maxYear' => date('Y') + 10, 'div' => false, 'empty' => __l('Please Select'), 'orderYear' => 'asc')); ?>
						</div>
					</div>
					<div class="input date-time end-date-time-block clearfix required js-anytime-deal">
						<div class="js-datetime">							
							<?php echo $this->Form->input('end_date', array('label' => __l('End Date'),'minYear' => date('Y', strtotime($this->request->data['Deal']['end_date'])), 'maxYear' => date('Y') + 10, 'div' => false, 'empty' => __l('Please Select'), 'orderYear' => 'asc')); ?>
						</div>
					</div>
				</div>
				<div class="clearfix date-time-block">
					<div class="input date-time clearfix required">
						<div class="js-datetime">
							<?php echo $this->Form->input('coupon_start_date', array('label' => __l('Coupon Start Date'),'minYear' => date('Y', strtotime($this->request->data['Deal']['coupon_start_date'])), 'maxYear' => date('Y') + 10, 'div' => false, 'empty' => __l('Please Select'), 'orderYear' => 'asc')); ?>
						</div>
					</div>
					<div class="input date-time end-date-time-block clearfix required js-anytime-deal">
						<div class="js-datetime">
							<?php echo $this->Form->input('coupon_expiry_date', array('label' => __l('Coupon End Date'),'minYear' => date('Y', strtotime($this->request->data['Deal']['coupon_expiry_date'])), 'maxYear' => date('Y') + 10, 'div' => false, 'empty' => __l('Please Select'), 'orderYear' => 'asc')); ?>
						</div>
					</div>
				</div>
		</fieldset>
		<div class="js-subdeal-not-need <?php echo (!empty($this->request->data['Deal']['is_subdeal_available']) ? 'hide' : '');?>">        
			<fieldset class="form-block round-5">
				<legend class="round-5"><?php echo __l('Price'); ?></legend>
				<div class="clearfix">
					<div class="price-form-block">	
						<?php
							if(Configure::read('site.currency_symbol_place') == 'left'):
								$currecncy_place = 'between';
							else:
								$currecncy_place = 'after';
							endif;	
						?>
						<?php
							echo $this->Form->input('original_price',array('label' => __l('Original Price'),'class' => 'js-price', $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>'));
						?>
						<div class="two-col-form discount-form-block clearfix">
							<?php echo $this->Form->input('discount_percentage', array('label' => __l('Discount (%)')));  ?>
							<span class="sep-or"><?php echo __l('OR'); ?></span>
							<?php echo $this->Form->input('discount_amount', array('label' => __l('Discount Amount'), $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>')); ?>
						</div>
						<?php 
							echo $this->Form->input('savings', array('type'=>'text',  'label' => __l('Savings for User'),  'readonly' => 'readonly', $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>'));
							echo $this->Form->input('discounted_price', array('label' => __l('Discounted Price for User'),'type'=>'text', 'readonly' => 'readonly', $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>'));
						?>
						<!-- ADVANCE/PARTIALLY PAYMENT -->
						<?php $is_adv_enabled = Configure::read('deal.is_enable_payment_advance'); ?>
						<?php if(Configure::read('deal.is_enable_payment_advance')): ?>
							<?php echo $this->Form->input('is_enable_payment_advance', array('type' => 'checkbox', 'class' => 'js-enable-advance-payment {selected_container:"none"}', 'label' => __l('Allow users to make partially payments?'), 'info' => __l('If checked, user can make a partial payment now and pay the remaining at the redeem location.')));?>
							<div class="js-advance-payment-box <?php echo (!empty($this->request->data['Deal']['is_enable_payment_advance']) ? '' : 'hide');?>">
								<?php
									echo $this->Form->input('pay_in_advance',array('label' => __l('Advance amount'), $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>'));
									echo $this->Form->input('payment_remaining',array('label' => __l('Pending amount'), 'type' => 'hidden', 'class' => '', $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>'));									
								?>
								<dl class="result-list clearfix">
									<dt><?php echo __l('Pay in Advance').'('.Configure::read('site.currency').'):  '; ?></dt>
										<dd>
											<span id="js-pay_in_advance"><?php echo $this->Html->cCurrency($this->request->data['Deal']['pay_in_advance']);?></span>
										</dd>
									<dt><?php echo __l('Pay remaining').'('.Configure::read('site.currency').'):  '; ?></dt>
										<dd>
											<span id="js-payment_remaining"><?php echo $this->Html->cCurrency($this->request->data['Deal']['payment_remaining']);?></span>
										</dd>
								</dl>
							</div>						
						<?php endif; ?>
					</div>
					<div class="calculator-block round-5">
					   <?php echo $this->element('../deals/budget_calculator', array('cache' => array('config' => 'site_element_cache', 'key' => $this->Auth->user('id')))); ?>
					</div>
				</div>   
				<div class="page-info">
					<?php
						echo __l('When you want to add as a free deal, just give 100% discount for this deal');
					 ?>
				</div>
			</fieldset>
		</div>
		<fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Coupons & Quantities'); ?></legend>
			<div class="clearfix input-blocks">
				<div class=" input-block-left">
					<?php echo $this->Form->input('min_limit', array('label'=>__l('No of Min Coupons'), 'info' => __l('Minimum limit of coupons to be bought by users, in order for the deal to get tipped.'), 'class' => 'js-min-limt')); ?>
				</div>
				<div class="js-subdeal-not-need <?php echo (!empty($this->request->data['Deal']['is_subdeal_available']) ? 'hide' : '');?>">        
					<div class="input-block-right ">
						<?php	echo $this->Form->input('max_limit', array('label'=>__l('No of Max Coupons'), 'info' => __l('Maximum limit of coupons can be bought for this deal. Leave blank for no limit.'))); ?>
					</div>
				</div>
			</div>
			<div class="clearfix input-blocks">
				<div class=" input-block-left">
					<?php 	echo $this->Form->input('buy_min_quantity_per_user', array('label'=>__l('Minimum Buy Quantity'),'info' => __l('How much minimum coupons user should buy for himself. Default 1'))); ?>
				</div>
				<div class="input-block-right ">
					<?php	echo $this->Form->input('buy_max_quantity_per_user', array('label'=>__l('Maximum Buy Quantity'),'info' => __l('How much coupons user can buy for himself. Leave blank for no limit.'))); ?>
				</div>
			</div>
		</fieldset>
		<div class="js-subdeal-not-need <?php echo (!empty($this->request->data['Deal']['is_subdeal_available']) ? 'hide' : '');?>">        
			<fieldset class="form-block round-5">
			   <legend class="round-5"><?php echo __l('Commission'); ?></legend>
					<div class="page-info"><?php echo __l('Total Commission Amount = Bonus Amount + ((Discounted Price * Number of Buyers) * Commission Percentage/100))'); ?></div>
					<div class="clearfix">
						<div class="amount-block commision-form-block">
							<?php
								echo $this->Form->input('bonus_amount', array('label' => __l('Bonus Amount'),$currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>', 'info' => __l('This is the flat fee that the company will pay for the whole deal.')));
								echo $this->Form->input('commission_percentage', array('info' => __l('This is the commission that company will pay for the whole deal in percentage.')));
							?>
						</div>
						<div class="calculator-block round-5">
							<?php echo $this->element('../deals/commission_calculator', array('cache' => array('config' => 'site_element_cache', 'key' => $this->Auth->user('id'))));;?>
						</div>
					</div>
			</fieldset>
		</div>
		<fieldset class="form-block round-5 js-deal-cities">
			<legend class="round-5"><?php echo __l('Deal Cities'); ?></legend>
			<div class="input cities-block required">
				<label><?php echo __l('Cities');?></label>
			</div>
			<?php if(empty($this->request->data['Deal']['City'])): ?>
				<div class="cities-checkbox-block">
					<?php
						echo $this->Form->input('City',array('label' =>false,'multiple'=>'checkbox'));
					?>
				</div>
				<?php else:?>
				<div class="cities-checkbox-block">
					<?php echo $this->Form->input('City',array('label' =>false,'multiple'=>'checkbox','value'=>$city_id));?>
				</div>
			<?php endif;?>
		</fieldset>  
	   <fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Description'); ?></legend>
			<?php
				/*if($this->Auth->user('user_type_id') == ConstUserTypes::Admin):
					echo $this->Form->input('private_note', array('type' =>'textarea', 'label' => __l('Private Note'), 'info' => __l('This is for admin reference. It will not be displayed for other users.')));
				endif;*/
			?>
			<?php echo $this->Form->input('description_ms', array('label' => __l('Description Malay'),'type' =>'textarea', 'class' => 'js-editor'));?>
			<?php echo $this->Form->input('description', array('label' => __l('Description'),'type' =>'textarea', 'class' => 'js-editor'));?>
       </fieldset>
    </div> 
    <!--validation div close-->
	   <fieldset class="form-block round-5">
		<legend class="round-5"><?php echo __l('Deal Image'); ?></legend>
		<div class="clearfix attachment-delete-outer-block">
			<ul>
				<?php 
					foreach($this->request->data['Attachment'] as $attachment){ 
				?>
					<li>	
					<div class="attachment-delete-block">
					  <span class="delete-photo"> <?php echo __l('Delete Photo'); ?></span>

					<?php	
						echo $this->Form->input('OldAttachment.'.$attachment['id'].'.id', array('type' => 'checkbox', 'class'=>'js-gig-photo-checkbox','id' => "gig_checkbox_".$attachment['id'], 'label' => false));
						echo $this->Html->showImage('Deal', $attachment, array('dimension' => 'normal_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($this->request->data['Deal']['name'], false)), 'title' => $this->Html->cText($this->request->data['Deal']['name'], false)));
					?>
					</div>
					</li>
				<?php } ?>
			</ul>
        </div>        
		<?php
			echo $this->Form->uploader('Attachment.filename', array('type'=>'file', 'uController' => 'deals', 'uRedirectURL' => array('controller' => 'deals', 'action' => 'index', 'admin' => true), 'uId' => 'dealID', 'uFiletype' => Configure::read('photo.file.allowedExt')));
		?>
		</fieldset>
        	<?php if(Configure::read('charity.is_enabled') == 1):?>
			<fieldset class="form-block round-5 js-deal-cities">
			<legend class="round-5"><?php echo __l('Charity'); ?></legend>			
			<span class="info">
				<?php echo __l('You can decide whether you can want to give amount to charity');?>
				<?php if(Configure::read('charity.who_will_choose') == ConstCharityWhoWillChoose::CompanyUser): ?>
					<span><?php echo __l('Amount to the charity will be given from the commission amount you have earned.');?></span>
				<?php else:?>
					<span><?php echo __l('Amount to the charity will be given from admin commission amount. Your profit wont be affected.');?></span>
				<?php endif;?>
			</span>
			<?php if(Configure::read('charity.who_will_choose') == ConstCharityWhoWillChoose::CompanyUser): ?>
				<?php echo $this->Form->input('charity_id', array('empty' =>__l('Please Select'))); ?>
			<?php endif; ?>
			<?php echo $this->Form->input('charity_percentage', array('label' => __l('Charity Percentage (%)'),'info' =>__l('Percentage of amount you would to like to give for charity.'))); ?>
			<?php if(Configure::read('charity.who_will_pay') == ConstCharityWhoWillPay::Admin || Configure::read('charity.who_will_pay') == ConstCharityWhoWillPay::AdminCompanyUser): ?>
			<div class="page-info">
			<?php
				echo __l('Admin also pay same percentage of amount from his commission');
			 ?>
			 </div>
			 <?php endif; ?>
			</fieldset>  
		  <?php endif; ?>
           <?php if ($deal['Deal']['is_subdeal_available'] != 0): ?>
      <fieldset class="form-block round-5">
		<legend class="round-5"><?php echo __l('Sub Deal'); ?></legend>
        <?php   echo $this->Html->link(__l('Edit'), array('controller' => 'deals', 'action' => 'subdeal_edit', $deal['Deal']['id']), array('class' => 'edit', 'title' => __l('Edit')));?> 
        
        <ol class="list sub-deal-list clearfix">
			<?php
				if (!empty($subdeal)):
				$i = 0;
				foreach ($subdeal as $sub_deal):
				   $class = null;
					if ($i++ % 2 == 0) {
						$class = "altrow";
					}
					?>
                    <li class= "vcard clearfix <?php echo $class;?>" >
						<div class="address-actions">
							<?php echo $this->Html->link(__l('Delete'), array('controller' => 'deals', 'action' => 'subdeal_delete', $sub_deal['Deal']['id'], $deal['Deal']['id'], 'admin' => false), array('class' => 'delete js-on-the-fly-sub-deal-delete', 'title' => __l('Delete')));?>
						</div>
						<h3><?php echo $this->Html->cText($sub_deal['Deal']['name']);?></h3>
						<dl class="list">
							<dt><?php echo __l('Discounted Price: ');?></dt>
								<dd><?php echo $this->Html->cText($sub_deal['Deal']['discounted_price']);?></dd>
							<dt><?php echo __l('Max limit: ');?></dt>
								<dd><?php echo $this->Html->cInt($sub_deal['Deal']['max_limit']);?></dd>
						</dl>   
					</li>
				<?php
					endforeach;
				else:
				?>
					<li class="notice"><?php echo __l('No sub deals available');?></li>
				<?php
				endif;
				?>
				</ol>  
      
	  </fieldset>
        <?php endif; ?>
		<fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Deal Coupons'); ?></legend>
				<span><?php echo $this->Html->link(__l('List Existing Coupons'), array('controller' => 'deal_coupons', 'action' => 'index', 'deal_id' =>  $this->request->data['Deal']['id']), array('target' => '_blank', 'title' => __l('List Existing Coupons')));?></span>
				<div class="page-info"><?php echo __l("Users can use this coupon code at the time of purchase irrespective of types of users. If you leave this field be free or else entered the less coupons than the users can possible to purchase then system will automatically generate the coupons to compensate the total no of coupons users has purchased.<br/>(Coupons entered will be updated with existing.)");?></div>
					<?php 
						if(!empty($manual_coupon_codes)):
							echo $this->Form->input('old_coupon_code', array('type' => 'textarea', 'disabled' => true, 'value' => $manual_coupon_codes));
						endif;
					?>
					<?php echo $this->Form->input('coupon_code', array('type' => 'textarea', 'info' => __l('Comma seperated for multiple coupons. <br />e.g., 000781b0-1, 0004e1b0-6, 00a481b0-8')));?>
		</fieldset>
		<fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Deal Listing Locations'); ?></legend>
			<?php echo $this->Form->input('is_redeem_at_all_branch_address', array('label' => __l('Can Redeem at All Sub-locations?'), 'id' =>'js-redeem-all-branch', 'info' => __l('Uncheck this option, if you dont want to make the redeem location in all branch addresses.')));?>
			<div class="js-show-branch-addresses <?php echo (!isset($this->request->data['Deal']['is_redeem_at_all_branch_address']) ? 'hide' : (!empty($this->request->data['Deal']['is_redeem_at_all_branch_address']) ? 'hide' : ''));?>">
				<?php if(!empty($branch_addresses)):?>
					<span class="info"><?php echo __l('Uncheck your branch locations, where you dont want this deal to be redeemed.');?></span>
					<div class="clearfix">
						<?php
							echo $this->Form->input('CompanyAddressesDeal.company_address_id',array('label' =>false, 'multiple'=>'checkbox', 'value' => $branch_checked_addresses, 'options' => $branch_addresses));
						?>
					</div>
				<?php else:?>
					<span class="info"><?php echo __l('You don\'t have any branch address.');?></span>
				<?php endif;?>
			</div>
			<?php echo $this->Form->input('is_redeem_in_main_address', array('label' => __l('Can Redeem at Your Company Main Address?'), 'info' => __l('Uncheck this option, if you dont want to make the redeem location in your main company location. <br /><strong>Note: </strong>If all branch addresses unchecked, this option will automatically set as true')));?>
            
            
		</fieldset>
	   <!--fieldset class="form-block round-5">
        <legend class="round-5"><?php echo __l('Review'); ?></legend>
		<?php
			//echo $this->Form->input('Attachment.filename', array('type' => 'file', 'label' => __l('Product Image')));
			//echo $this->Form->input('review_ms', array('label' => __l('Review Malay'),'type' =>'textarea', 'class' => 'js-editor'));
			//echo $this->Form->input('review', array('label' => __l('Review'),'type' =>'textarea', 'class' => 'js-editor'));
		?>
		</fieldset-->
        <fieldset class="form-block round-5">
        <legend class="round-5"><?php echo __l('Coupon'); ?></legend>
		<?php
			echo $this->Form->input('coupon_condition_ms', array('label' => __l('Coupon Condition Malay'),'type' =>'textarea', 'class' => 'js-editor'));
			echo $this->Form->input('coupon_condition', array('label' => __l('Coupon Condition '),'type' =>'textarea', 'class' => 'js-editor'));
			echo $this->Form->input('coupon_highlights_ms', array('label' => __l('Coupon Highlights Malay'),'type' =>'textarea', 'class' => 'js-editor'));
			echo $this->Form->input('coupon_highlights', array('label' => __l('Coupon Highlights'),'type' =>'textarea', 'class' => 'js-editor'));
			//echo $this->Form->input('comment_ms', array('label' => __l('Comment Malay'),'type' =>'textarea', 'class' => 'js-editor'));
			//echo $this->Form->input('comment', array('label' => __l('Comment'),'type' =>'textarea', 'class' => 'js-editor'));
		?>
        </fieldset>
        <fieldset class="form-block round-5">
        <legend class="round-5"><?php echo __l('SEO'); ?></legend>
        <?php
			echo $this->Form->input('meta_keywords',array('label' => __l('Meta Keywords')));
			echo $this->Form->input('meta_description',array('label' => __l('Meta Description')));
	?>
    </fieldset>
	</fieldset>
    <div class="submit-block clearfix">
<?php echo $this->Form->submit(__l('Update'),array('name' => 'data[Deal][send_to_admin]')); ?>
    
		<?php
			if($deal['Deal']['deal_status_id'] == ConstDealStatus::Draft):
				echo $this->Form->submit(__l('Update Draft'));
			endif;
			?>
			<div class="cancel-block">
			<?php
			echo $this->Html->link(__l('Cancel'), array('controller' => 'deals', 'action' => 'index', 'admin' => true), array('class' => 'cancel-button'));

		?>
    </div>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
