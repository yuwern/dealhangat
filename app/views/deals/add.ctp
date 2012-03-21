<?php /* SVN: $Id: add.ctp 55147 2011-05-31 11:13:31Z aravindan_111act10 $ */ ?>
<?php echo $this->element('js_tiny_mce_setting', array('cache' => array('config' => 'site_element_cache')));?>
<div class="deals form js-responses">
<?php 
if(empty($this->request->data['CloneAttachment'][0])) 
	echo $this->Form->create('Deal', array('action' => 'add', 'class' => 'normal js-upload-form {is_required:"true"}', 'enctype' => 'multipart/form-data'));
else
	echo $this->Form->create('Deal', array('action' => 'add', 'class' => 'normal js-upload-form {is_required:"false"}', 'enctype' => 'multipart/form-data'));	
?>

	<div class="js-validation-part">
		<h2><?php echo __l('Add Deal');?></h2>
		<?php if($this->Auth->user('user_type_id') == ConstUserTypes::Company):?>
			<div class="adddeal-img-block"><?php echo $this->Html->image('company-deal-flow.jpg', array('alt'=> __l('[Image: Company Deal Flow]'), 'title' => __l('Company Deal Flow'))); ?></div>
		<?php else: ?>
			<div class="adddeal-img-block"> <?php echo $this->Html->image('admin-deal-flow.jpg', array('alt'=> __l('[Image: Administrator Deal Flow]'), 'title' => __l('Administrator Deal Flow'))); ?></div>
		<?php endif; ?>
		<fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Type'); ?></legend>
			<div class="clearfix">
				<?php if(Configure::read('deal.is_enable_sub_deal')): ?>
					<?php echo $this->Form->input('is_subdeal_available', array('label' => __l('Add Sub Deals'), 'info' => __l('If checked, you can add multiple sub deals for this deal. You\'ll be redirected to sub deal section after filling up the below information.')));?>
				<?php endif; ?>					
				<?php if(Configure::read('deal.is_side_deal_enabled')): ?>
					<?php echo $this->Form->input('is_side_deal', array('label'=>__l('Side Deal'), 'info'=>__l('Side deals will be displayed in the side bar of the home page.')));?>
				<?php endif; ?>
				<?php 
					echo $this->Form->input('is_anytime_deal', array('label' => __l('Any Time Deal'), 'info' => __l('This type of deal does not have closing date or expiry date. It can only be closed manually by Site Administrator or Specifying Maximum Buy Quantity')));
				?>	
			</div>
		</fieldset>
		<fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('General'); ?></legend>
			<?php
				echo $this->Form->input('user_id', array('type' => 'hidden'));
				echo $this->Form->input('clone_deal_id', array('type' => 'hidden'));
				echo $this->Form->input('deal_category_id', array('empty'=>'Select','options'=>$categories));
				echo $this->Form->input('name',array('label' => __l('Name')));
				echo $this->Form->input('name_ms',array('label' => __l('Name in Malay')));
				if($this->Auth->user('user_type_id') == ConstUserTypes::Admin):
					echo $this->Form->input('company_id', array('label' => __l('Company'),'empty' =>__l('Please Select')));
					echo $this->Form->input('company_slug', array('type' => 'hidden'));
				else:
					echo $this->Form->input('company_id', array('type' => 'hidden'));
					echo $this->Form->input('company_slug', array('type' => 'hidden'));
				endif;
			?>
			<div class="clearfix date-time-block">
				<div class="input date-time clearfix required">
					<div class="js-datetime">
						<?php echo $this->Form->input('start_date', array('label' => __l('Start Date'),'minYear' => date('Y'), 'maxYear' => date('Y') + 10, 'div' => false, 'empty' => __l('Please Select'), 'orderYear' => 'asc')); ?>
					</div>
				</div>
				<div class="input date-time end-date-time-block clearfix required js-anytime-deal">
					<div class="js-datetime">
						<?php echo $this->Form->input('end_date', array('label' => __l('End Date'),'minYear' => date('Y'), 'maxYear' => date('Y') + 10, 'div' => false, 'empty' => __l('Please Select'), 'orderYear' => 'asc')); ?>
					</div>
				</div>
			</div>
			<div class="clearfix date-time-block">
				<div class="input date-time clearfix required">
					<div class="js-datetime">
						<?php echo $this->Form->input('coupon_start_date', array('label' => __l('Coupon Start Date'),'minYear' => date('Y'), 'maxYear' => date('Y') + 10, 'div' => false, 'empty' => __l('Please Select'), 'orderYear' => 'asc')); ?>
					</div>
				</div>
				<div class="input date-time end-date-time-block clearfix required js-anytime-deal">
					<div class="js-datetime">
						<?php echo $this->Form->input('coupon_expiry_date', array('label' => __l('Coupon End Date'),'minYear' => date('Y'), 'maxYear' => date('Y') + 10, 'div' => false, 'empty' => __l('Please Select'), 'orderYear' => 'asc')); ?>
					</div>
				</div>
			</div>
		</fieldset>
		<div class="js-subdeal-not-need">
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
							<div class="js-advance-payment-box hide">
								<?php
									echo $this->Form->input('pay_in_advance',array('label' => __l('Advance amount'), $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>'));
									echo $this->Form->input('payment_remaining',array('label' => __l('Pending amount'), 'type' => 'hidden', 'class' => '', $currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>'));									
								?>
								<dl class="result-list clearfix">
									<dt><?php echo __l('Pay in Advance').'('.Configure::read('site.currency').'):  '; ?></dt>
										<dd>
											<span id="js-pay_in_advance">0</span>
										</dd>
									<dt><?php echo __l('Pay remaining').'('.Configure::read('site.currency').'):  '; ?></dt>
										<dd>
											<span id="js-payment_remaining">0</span>
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
			<div class="clearfix">
				<div class="clearfix input-blocks">
					<div class=" input-block-left">
						<?php
							echo $this->Form->input('min_limit', array('label'=>__l('No of Min Coupons'), 'info' => __l('Minimum limit of coupons to be bought by users, in order for the deal to get tipped.'), 'class' => 'js-min-limt'));
						?>
					</div>
					<div class="js-subdeal-not-need">
						<div class="input-block-right">
							<?php	echo $this->Form->input('max_limit', array('label'=>__l('No of Max Coupons'), 'info' => __l('Maximum limit of coupons can be bought for this deal. Leave blank for no limit.'))); ?>
						</div>
					</div>
				</div>
				<div class="clearfix input-blocks">
					<div class=" input-block-left">
						<?php
							echo $this->Form->input('buy_min_quantity_per_user', array('label'=>__l('Minimum Buy Quantity'),'info' => __l('Minimum purchase per user including gifts.')));
						?>
					</div>
					<div class="input-block-right ">
						<?php
							echo $this->Form->input('buy_max_quantity_per_user', array('label'=>__l('Maximum Buy Quantity'),'info' => __l('Maximum purchase per user including gifts. Leave blank for no limit.')));
						?>
				   </div>
			   </div>
			</div>
		</fieldset> 
		<div class="js-subdeal-not-need">
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
							echo $this->Form->input('bonus_amount', array('label' => __l('Bonus Amount'),'value' => '0.00',$currecncy_place => '<span class="currency">'.Configure::read('site.currency'). '</span>'));
						?>
						<span class="info"> <?php echo __l('This is the flat fee that the company will pay for the whole deal.');?></span>
						<?php if(($this->Auth->user('user_type_id') != ConstUserTypes::Admin) && (Configure::read('deal.is_admin_enable_commission')) && Configure::read('deal.commission_amount_type') == 'fixed'):
	                             echo $this->Form->input('commission_percentage', array('Readonly' =>'Readonly', 'info' => __l('This is the commission that company will pay for the whole deal in percentage.'), 'label' => __l('Commission (%)')));
							 else:
								if($this->Auth->user('user_type_id') != ConstUserTypes::Admin && Configure::read('deal.is_admin_enable_commission') && Configure::read('deal.commission_amount_type') == 'minimum'):
									$comm_info = __l('This is the commission that company will pay for the whole deal in percentage. The Commission set must be greater than'.' '.$this->Html->siteCurrencyFormat($this->Html->cCurrency(Configure::read('deal.commission_amount'))));
								else:
									$comm_info = __l('This is the commission that company will pay for the whole deal in percentage.');
								endif;
								echo $this->Form->input('commission_percentage', array('info' => $comm_info, 'label' => __l('Commission (%)')));
							 endif; 
						?>
					</div>
					<div class="calculator-block round-5">
						<?php echo $this->element('../deals/commission_calculator', array('cache' => array('config' => 'site_element_cache', 'key' => $this->Auth->user('id')))); ?>
					</div>
				</div>
			</fieldset>
		</div>
		<fieldset class="form-block round-5 js-deal-cities">
			<legend class="round-5"><?php echo __l('Deal Cities'); ?></legend>
			<div class="input cities-block required">
				<label><?php echo __l('Cities');?></label>
			</div>
			<?php 
				if(empty($this->request->data['Deal']['City']) && empty($city_id)): ?>
					<div class="cities-checkbox-block clearfix">
						<?php
						echo $this->Form->input('City',array('label' =>false,'multiple'=>'checkbox')); ?>
					</div>
					<?php
				else:
				 ?>
				 <div class="cities-checkbox-block clearfix">
				 <?php
					echo $this->Form->input('City',array('label' => false,'multiple'=>'checkbox','value'=>$city_id));
				?>
					</div>
				<?php
				endif;
			?>
		</fieldset>  
	    <fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Description'); ?></legend>
			<?php
				if($this->Auth->user('user_type_id') == ConstUserTypes::Admin):
					//echo $this->Form->input('private_note', array('type' =>'textarea', 'label' => __l('Private Note'), 'info' => __l('This is for admin reference. It will not be displayed for other users.')));
					//echo $this->Form->input('private_note_ms', array('type' =>'textarea', 'label' => __l('Private Note Malay'), 'info' => __l('This is for admin reference. It will not be displayed for other users.')));
				endif;
				echo $this->Form->input('description', array('label' => __l('Description'),'type' =>'textarea', 'class' => 'js-editor'));
				echo $this->Form->input('description_ms', array('label' => __l('Description Malay'),'type' =>'textarea', 'class' => 'js-editor'));
			?>
	   </fieldset>
	</div><!-- End of validation part div -->	
   <fieldset class="form-block round-5">
		<legend class="round-5"><?php echo __l('Deal Image'); ?></legend>
			<div class="required">
			<div class="input required gig-img-label">
					<label><?php echo __l('Deal Images');?></label>
			
				<?php
					$redirect_check = (!empty($this->request->params['prefix']) && $this->request->params['prefix'] == 'admin') ? "true" : "false";
					if($this->Auth->user('user_type_id') == ConstUserTypes::Admin):
						$redirect_array = array('controller' => 'deals', 'action' => 'index', 'type' => 'success','admin' => true);
					else:
						$redirect_array = array('controller' => 'deals', 'action' => 'company', $this->request->data['Deal']['company_slug'], 'success','admin' => false);
					endif;
					echo $this->Form->uploader('Attachment.filename', array('type'=>'file', 'uController' => 'deals', 'uRedirectURL' => $redirect_array, 'uId' => 'dealID', 'uFiletype' => Configure::read('photo.file.allowedExt')));
				?>
		
				</div>
                <div class="clearfix attachment-delete-outer-block">
				<?php
				 if(!empty($this->request->data['CloneAttachment'][0])) {?>
                 	<ul>
					<?php
						
                	$i =0;
                	foreach($this->request->data['CloneAttachment'] as $CloneAttachment){ ?>
                    	<li>	
							<div class="attachment-delete-block">
							  <span class="delete-photo"> <?php echo __l('Delete Photo'); ?></span>
                    <?php 
                    echo $this->Form->input('OldAttachment.'.$CloneAttachment['id'].'.id', array('type' => 'checkbox', 'class'=>'','id' => "gig_checkbox_".$CloneAttachment['id'], 'label' => false));
                    echo $this->Form->input('CloneAttachment.'.$i.'.id', array('type' => 'hidden', 'value' => $CloneAttachment['id']));
					echo $this->Html->showImage('Deal', $CloneAttachment, array('dimension' => 'normal_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($this->request->data['Deal']['name'], false)), 'title' => $this->Html->cText($this->request->data['Deal']['name'], false), 'escape' => false));
					$i++;?>
					</div>
                    </li>
                    
					<?php 
					}?>
                    </ul>
                <?php }	?>
                </div>
			</div>
		</fieldset>
		 <?php if(Configure::read('charity.is_enabled') == 1):?>
			<fieldset class="form-block round-5 js-deal-cities">
			<legend class="round-5"><?php echo __l('Charity'); ?></legend>
			<span class="info">
				<?php echo __l('You can decide whether you can want to give amount to charity');?>
				<?php if(Configure::read('charity.who_will_choose') == ConstCharityWhoWillChoose::CompanyUser): ?>
					<p><?php echo __l('Amount to the charity will be given from the commission amount you have earned.');?></p>
				<?php else:?>
					<p><?php echo __l('Amount to the charity will be given from admin commission amount. Your profit wont be affected.');?></p>
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
         
		<fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Deal Coupons'); ?></legend>
				<div class="page-info"><?php echo __l('Users can use this coupon code at the time of purchase irrespective of types of users. If you leave this field be free or else entered the less coupons than the users can possible to purchase then system will automatically generate the coupons to compensate the total no of coupons users has purchased.');?></div>
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
							echo $this->Form->input('CompanyAddressesDeal.company_address_id',array('label' =>false,'multiple'=>'checkbox', 'checked' => true, 'options' => $branch_addresses));
						?>
					</div>
				<?php else:?>
					<span class="info"><?php echo __l('You don\'t have any branch address.');?></span>
				<?php endif;?>
			</div>
			<?php echo $this->Form->input('is_redeem_in_main_address', array('label' => __l('Can Redeem at Your Company Main Address?'), 'info' => __l('Uncheck this option, if you dont want to make the redeem location in your main company location. <br /><strong>Note:</strong> If all branch addresses unchecked, this option will automatically set as true')));?>
		</fieldset>
	   <!--fieldset class="form-block round-5">
        <legend class="round-5"><?php //echo __l('Review'); ?></legend>
		<?php
			//echo $this->Form->input('review', array('label' => __l('Review'),'type' => 'textarea', 'class' => 'js-editor'));
			//echo $this->Form->input('review', array('label' => __l('Review Malay'),'type' => 'textarea', 'class' => 'js-editor'));
		?>
		</fieldset-->
       <fieldset class="form-block round-5">
        <legend class="round-5"><?php echo __l('Coupon'); ?></legend>
		<?php
			echo $this->Form->input('coupon_condition', array('label' => __l('Coupon Condition'),'type' =>'textarea', 'class' => 'js-editor'));
			echo $this->Form->input('coupon_condition_ms', array('label' => __l('Coupon Condition Malay'),'type' =>'textarea', 'class' => 'js-editor'));
			echo $this->Form->input('coupon_highlights', array('label' => __l('Coupon Highlights'),'type' =>'textarea', 'class' => 'js-editor'));
			echo $this->Form->input('coupon_highlights_ms', array('label' => __l('Coupon Highlights Malay'),'type' =>'textarea', 'class' => 'js-editor'));
			//echo $this->Form->input('comment', array('label' => __l('Comment'),'type' =>'textarea', 'class' => 'js-editor'));
			//echo $this->Form->input('comment_ms', array('label' => __l('Comment Malay'),'type' =>'textarea', 'class' => 'js-editor'));
		?>
		</fieldset>
       <fieldset class="form-block round-5">
        <legend class="round-5"><?php echo __l('SEO'); ?></legend>
        <?php
			echo $this->Form->input('meta_keywords',array('label' => __l('Meta Keywords')));
			echo $this->Form->input('meta_description',array('label' => __l('Meta Description')));
	?>
	</fieldset>

	<div class="submit-block clearfix">
		<?php echo $this->Form->input('is_save_draft', array('type' => 'hidden', 'id' => 'js-save-draft'));?>
		<div class="js-subdeal-not-need">
			<?php
				echo $this->Form->submit(__l('Add'), array('class' => 'js-update-order-field'));
				echo $this->Form->submit(__l('Save as Draft'), array('name' => 'data[Deal][save_as_draft]', 'class' => 'js-update-order-field')); 
			?>
		</div>
		<div class="js-subdeal-need hide">
			<?php 			
				echo $this->Form->submit(__l('Continue'), array('name' => 'data[Deal][continue]', 'class' => 'js-update-order-field'));
			?>		
		</div>
   </div>
    <div class="info-details"><?php echo __l('Save this deal as a draft and view the preview of the deal. You can make changes untill you send it to upcoming status. Use the update button in edit page to send it to upcoming status.'); ?></div>
<?php echo $this->Form->end();
?>
</div>