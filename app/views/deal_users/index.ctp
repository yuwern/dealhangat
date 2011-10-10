<?php /* SVN: $Id: index.ctp 60034 2011-07-12 09:32:10Z mohanraj_109at09 $ */ ?>
<?php if(empty($this->request->params['named']['type']) && empty($this->request->params['named']['deal_id']) && empty($this->request->data)): ?>
<?php if(!empty($pageTitle)): ?>
        <h2><?php echo $pageTitle;?></h2>
<?php endif; ?>
    <div class="js-tabs">
        <ul class="clearfix">
            <li><?php echo $this->Html->link(sprintf(__l('Available (%s)'),$available),array('controller' => 'deal_users', 'action' => 'index', 'deal_id' => $deal_id, 'type' => 'available'), array('title' => 'Available-'.$deal_id)); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Used (%s)'),$used),array('controller' => 'deal_users', 'action' => 'index', 'deal_id' => $deal_id, 'type' => 'used'), array('title' => 'Used-'.$deal_id)); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Expired (%s)'),$expired),array('controller' => 'deal_users', 'action' => 'index', 'deal_id' => $deal_id, 'type' => 'expired'), array('title' => 'Expired-'.$deal_id)); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Pending (%s)'),$open),array('controller' => 'deal_users', 'action' => 'index', 'deal_id' => $deal_id, 'type' => 'open'), array('title' => 'Pending-'.$deal_id)); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Canceled (%s)'),$canceled),array('controller' => 'deal_users', 'action' => 'index', 'deal_id' => $deal_id, 'type' => 'canceled'), array('title' => 'Canceled-'.$deal_id)); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Refund (%s)'),$refund),array('controller' => 'deal_users', 'action' => 'index', 'deal_id' => $deal_id, 'type' => 'refund'), array('title' => 'Refund-'.$deal_id)); ?></li>
            <?php if(!empty($deal_id)) {?>
                <li><?php echo $this->Html->link(sprintf(__l('Gifted Coupons (%s)'),$gifted_deals),array('controller' => 'deal_users', 'action' => 'index', 'deal_id' => $deal_id, 'type' => 'gifted_deals'), array('title' => 'Gifted Coupons-'.$deal_id)); ?></li>
            <?php }else{ ?>
                <li><?php echo $this->Html->link(sprintf(__l('Gifted Coupons (%s)'),$gifted_deals),array('controller' => 'deal_users', 'action' => 'index', 'user_id' => $this->Auth->user('id'), 'type' => 'gifted_deals'), array('title' => 'Gifted Coupons-'.$deal_id)); ?></li>			
                <li><?php echo $this->Html->link(sprintf(__l('Received Gift Coupons (%s)'),$recieved_gift), array('controller' => 'deal_users', 'action' => 'index', 'user_id' => $this->Auth->user('id'), 'type' => 'recieved_gift_deals'), array('title' => 'Received Gift Coupons-'.$deal_id)); ?></li>			
			<?php }?>
            <li><?php echo $this->Html->link(sprintf(__l('All (%s)'),$all_deals),array('controller'=> 'deal_users','deal_id'=>$deal_id, 'action'=>'index','type' => 'all'),array('title' => 'All-'.$deal_id)); ?></li>
        </ul>
    </div>
<?php else: ?>
	<div class="dealUsers index js-response js-responses">
		<?php if(($this->Auth->user('user_type_id') == ConstUserTypes::Company) && (!empty($this->request->params['named']['deal_id']) && (!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'available' || $this->request->params['named']['type'] == 'used')) ||  (!empty($show_coupon_code) && $this->request->params['named']['deal_user_view'] == 'coupon'))){ ?>
			<?php echo $this->Form->create('DealUser', array('type' => 'post', 'class' => 'normal search-form clearfix js-ajax-form', 'action'=>'index')); ?>
				<div>
					<?php 
						echo $this->Form->input('coupon_code', array('label' => __l('Coupon code')));
						echo $this->Form->input('deal_id', array('type' => 'hidden', 'value' => $this->request->params['named']['deal_id']));
						echo $this->Form->input('deal_user_view', array('type' => 'hidden', 'value' => $this->request->params['named']['deal_user_view']));
						if(!empty($this->request->data['DealUser']['type'])):
							echo $this->Form->input('type', array('type' => 'hidden'));
						endif;
						echo $this->Form->submit(__l('Search'));
					?>
				</div>
			<?php echo $this->Form->end(); ?>
		<?php } ?>
		<?php
			echo $this->Form->create('DealUser' , array('class' => 'normal js-ajax-form','action' => 'update'));
			echo $this->Form->input('r', array('type' => 'hidden', 'value' => $this->request->url));
			if (!empty($this->request->params['named']['deal_id'])):
				echo $this->Form->input('deal_id', array('type' => 'hidden', 'value' => $this->request->params['named']['deal_id']));
			elseif (!empty($this->request->params['named']['type'])):
				echo $this->Form->input('type', array('type' => 'hidden', 'value' => $this->request->params['named']['type']));
			endif;
		?>
        <?php echo $this->element('paging_counter');?>
		<?php if(empty($this->request->params['named']['type']) && empty($this->request->params['named']['deal_id'])): ?>
			<p><?php echo __l('Total Quantity Sold').': '.$this->Html->cInt($deal_user_count);?> </p>
			<p><?php echo __l('Expires On').': '.$this->Html->cDateTime($dealUser['Deal']['coupon_expiry_date']);?> </p>
		<?php endif; ?>
		<table class="list">
			<tr>
				<?php
                if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'open' && ($this->Auth->user('user_type_id') == ConstUserTypes::User || ($this->Auth->user('user_type_id') == ConstUserTypes::Company && empty($this->request->params['named']['deal_id'])))) { ?>
                    <th rowspan="2"><?php echo __l('Action'); ?></th>
                    <?php
                }
                if ((!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] != 'gifted_deals') && $this->request->params['named']['type'] == 'available') { ?>
				  <th rowspan="2" class="actions"><?php echo __l('Select');?></th>
				<?php } ?>
                <?php if (!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'available' || $this->request->params['named']['type'] == 'used') || (!empty($show_coupon_code) && $this->request->params['named']['deal_user_view'] == 'coupon')) { ?>
					<th rowspan="2" class="actions"><?php echo __l('Action');?></th>
				<?php } ?>
				<th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Purchased Date'), 'created');?></div></th>
				<?php if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'canceled') { ?>
					<th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Canceled Date'), 'modified');?></div></th>
				<?php } ?>
				<?php if(!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'recieved_gift_deals'): ?>
					<th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Gift From'),'gift_from');?></div></th>
				<?php endif;?>
                <?php if(!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'gifted_deals'): ?>
					<th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Gift To'),'gift_to');?></div></th>
				<?php endif;?>
                <?php if(($this->Auth->user('user_type_id') == ConstUserTypes::Company) && !empty($this->request->params['named']['deal_id'])): ?>
					<th rowspan="2" class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Username'), 'User.username');?></div></th>
					<?php if(!empty($deal_info['Deal']['is_subdeal_available'])):?>	
						<th rowspan="2" class="dl"><div class="js-pagination"><?php echo __l('Sub Deal');?></div></th>
					<?php endif;?>
                <?php endif; ?>
                <?php if(!empty($deal_id) || !empty($this->request->params['named']['deal_id'])): ?>
					<th rowspan="2" class="dr"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Amount'), 'discount_amount') . ' ('.Configure::read('site.currency').')';?></div></th>
				<?php else: ?>					
					<th rowspan="2" class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Deal'), 'deal_id');?></div></th>
				<?php endif; ?>
				<?php if (!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'available' || $this->request->params['named']['type'] == 'used') ||  (!empty($show_coupon_code) && $this->request->params['named']['deal_user_view'] == 'coupon')): ?>
					<?php if(($this->Auth->user('user_type_id') == ConstUserTypes::Company) ||  ($this->Auth->user('user_type_id') == ConstUserTypes::Admin)):?>
						<th class="dc" colspan='3'><div class="js-pagination"><?php echo __l('Coupon code');?></div></th>
					<?php elseif(($this->Auth->user('user_type_id') == ConstUserTypes::User)):?>
						<th class="dc" colspan='3'><div class="js-pagination"><?php echo __l('Coupon code');?></div></th>
					<?php else:?>
						<th class="dc" rowspan="2" ><div class="js-pagination"><?php echo __l('Coupon code');?></div></th>
					<?php endif;?>
				<?php endif;?>
				<th rowspan="2"><?php echo __l('Quantity');?></th>				
				<th rowspan="2"><?php echo __l('Purchased City');?></th>
				<?php if(Configure::read('charity.is_enabled') == 1):?>
				<th colspan="4"><?php echo __l('Charity');?></th>
				<?php endif; ?>
            </tr>
			<tr>
			<?php if(($this->Auth->user('user_type_id') == ConstUserTypes::Company) ||  ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) ||  ($this->Auth->user('user_type_id') == ConstUserTypes::User)):?>
				<?php if (!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'available' || $this->request->params['named']['type'] == 'used') ||  (!empty($show_coupon_code) && $this->request->params['named']['deal_user_view'] == 'coupon')): ?>
					<th><div class="js-pagination"><?php echo __l('Top Code');?></div></th>
					<th><div class="js-pagination"><?php echo __l('Bottom Code');?></div></th>
					<th class='dl'><div class="js-pagination"><?php echo __l('Action');?></div></th>
				<?php endif;?>
			<?php endif;?>	
			<?php if(Configure::read('charity.is_enabled') == 1):?>
				<th><div class="js-pagination"><?php echo __l('Charity');?></div></th>
				<th><div class="js-pagination"><?php echo __l('Amount');?></div></th>
				<th><div class="js-pagination"><?php echo __l('Seller Amount');?></div></th>
				<th><div class="js-pagination"><?php echo __l('Site  Amount');?></div></th>
			<?php endif; ?>
			</tr>				
			<?php
				if (!empty($dealUsers)):
					$i = 0;
					foreach ($dealUsers as $dealUser):
						$class = null;
						if ($i++ % 2 == 0) {
							$class = ' class="altrow"';
						}
						if($dealUser['DealUser']['deal_user_coupon_count'] != 0):
							$status_class = 'js-checkbox-active';
						else:
							$status_class = 'js-checkbox-inactive';
						endif;
			?>
			<tr<?php echo $class;?>>
                <?php
                if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'open' && ($this->Auth->user('user_type_id') == ConstUserTypes::User || ($this->Auth->user('user_type_id') == ConstUserTypes::Company && empty($this->request->params['named']['deal_id'])))) { ?>
                    <td>
                        <?php
							if (!empty($dealUser['DealUser']['is_gift']) && $dealUser['DealUser']['user_id'] != $this->Auth->user('id')):
								echo __l('N/A');
                            elseif(!empty($dealUser['DealUser']['is_canceled'])) :
                                echo __l('Canceled');
                            else :
                                echo $this->Html->link(__l('Cancel'), array('controller' => 'deal_users', 'action' => 'cancel_deal', $dealUser['DealUser']['id']), array('title' => __l('Cancel'), 'class' => 'js-deal-cancel deal-cancel'));
                            endif;
                            ?>
                    </td>
                    <?php
                }
                ?>
				<?php if (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'available') { ?>
					<td>
						<?php echo $this->Form->input('DealUser.'.$dealUser['DealUser']['id'].'.id', array('type' => 'checkbox', 'id' => "admin_checkbox_".$dealUser['DealUser']['id'], 'label' => false, 'class' => $status_class.' js-checkbox-list')); ?>
					</td>
				<?php } ?>
				<?php if (!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'available' || $this->request->params['named']['type'] == 'used') || (!empty($show_coupon_code) && $this->request->params['named']['deal_user_view'] == 'coupon')) { ?>
					<td>
						<?php
							echo $this->Html->link(__l('View Coupon'),array('controller' => 'deal_users', 'action' => 'view', 'filter_id' => (!empty($this->request->params['named']['type'])) ? $this->request->params['named']['type'] : '', $dealUser['DealUser']['id'],'admin' => false),array('title' => __l('View Coupon'), 'class'=>'js-thickbox','target' => '_blank', 'class'=>'view-icon js-thickbox'));
                            /*
                             * Commented out by Tate on 2011-10-10
                             * This was removed due to a business requirement.
                             */
							//echo $this->Html->link(__l('Print'),array('controller' => 'deal_users', 'action' => 'view', 'filter_id' => (!empty($this->request->params['named']['type'])) ? $this->request->params['named']['type'] : '', $dealUser['DealUser']['id'],'type' => 'print'),array('target'=>'_blank', 'title' => __l('Print'), 'class'=>'print-icon'));
						?>
					</td>
                <?php } ?>
				<td><?php echo $this->Html->cDateTime($dealUser['DealUser']['created']);?></td>
				<?php if(!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'canceled'): ?>
					<td><?php echo $this->Html->cDateTime($dealUser['DealUser']['modified']);?></td>
				<?php endif;?>
				<?php if(!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'recieved_gift_deals')): ?>
					<td><?php echo $this->Html->cText($dealUser['DealUser']['gift_from']);?></td>
				<?php endif;?>
				 <?php if(!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'gifted_deals')): ?>
					<td><?php echo $this->Html->cText($dealUser['DealUser']['gift_to']);?></td>
				<?php endif;?>
                <?php if(($this->Auth->user('user_type_id') == ConstUserTypes::Company) && !empty($this->request->params['named']['deal_id'])): ?>
                    <td class="dl"><?php echo $this->Html->cText($dealUser['User']['username']);?></td>
					<?php if(!empty($deal_info['Deal']['is_subdeal_available']) && !empty($dealUser['SubDeal']['name'])):?>	
						<td class="dl"><?php echo $this->Html->cText($dealUser['SubDeal']['name']);?></td>
					<?php endif;?>
                <?php endif; ?>
                <?php if(!empty($deal_id) || !empty($this->request->params['named']['deal_id'])): ?>
                    <td class="dr"><?php echo $this->Html->cCurrency($dealUser['DealUser']['discount_amount']);?></td>
                <?php else: ?>
                    <td class="deal-user-gift">
						<?php echo $this->Html->link($this->Html->showImage('Deal', $dealUser['Deal']['Attachment'][0], array('dimension' => 'medium_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($dealUser['Deal']['name'], false)), 'title' => $this->Html->cText($dealUser['Deal']['name'], false))),array('controller' => 'deals', 'action' => 'view', $dealUser['Deal']['slug']), array('title' => $dealUser['Deal']['name'], 'escape' => false)); ?>
						<?php echo $this->Html->link($this->Html->cText($dealUser['Deal']['name'].(!empty($dealUser['SubDeal']['name']) ? ' - '.$dealUser['SubDeal']['name'] : '')), array('controller' => 'deals', 'action' => 'view', $dealUser['Deal']['slug']), array('escape' => false, 'title' => $dealUser['Deal']['name'].(!empty($dealUser['SubDeal']['name']) ? ' - '.$dealUser['SubDeal']['name'] : '')));?>
						<?php 
							if(!empty($dealUser['Deal']['coupon_start_date'])):
								if(date('Y-m-d H:i:s') < $dealUser['Deal']['coupon_start_date']):
								?>
									<span class="pending-coupons" title="<?php echo __l('Coupon code can be used from'.' '.$this->Html->cDateTime($dealUser['Deal']['coupon_start_date'], false));?>"></span>
								<?php endif;?>
						<?php endif;?>
					</td>
				<?php endif; ?>
				<?php if (!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'available' || $this->request->params['named']['type'] == 'used') ||  (!empty($show_coupon_code) && $this->request->params['named']['deal_user_view'] == 'coupon') ):?>
					<td>
						<?php if (empty($dealUser['DealUser']['is_gift']) || (!empty($dealUser['DealUser']['is_gift']) && $dealUser['DealUser']['gift_email'] == $this->Auth->user('email')) || !empty($this->request->params['named']['deal_id'])):?>
							<ul class="coupon-code">
								<?php foreach ($dealUser['DealUserCoupon'] as $dealUserCoupon) { ?>
									<?php if ((!empty($coupon_find_id) && in_array($dealUserCoupon['id'], $coupon_find_id)) || (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'available' && empty($dealUserCoupon['is_used'])) || (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'used' && !empty($dealUserCoupon['is_used'])) || (!empty($show_coupon_code) && $this->request->params['named']['deal_user_view'] == 'coupon')) { ?>
										<li class="clearfix">
                                        <?php if(Configure::read('deal.deal_coupon_used_type') == 'click'){ ?>
														<span class="coupon-code"><?php echo $dealUserCoupon['coupon_code']; ?></span>
                                            <?php } else { ?>
												 <?php  
												 	if( $this->Auth->user('user_type_id') == ConstUserTypes::Company ){
														if( Configure::read('deal.deal_coupon_code_show_type') == 'bottom') {
															 echo $this->Form->input('DealUserCoupon.'.$dealUserCoupon['id'].'.coupon_code', array('type' => 'text', 'label' => false, 'div' => false)); 
														}
														else{
												?>
                                                		<span class="coupon-code"><?php echo $dealUserCoupon['coupon_code']; ?></span>
                                                	
											<?php 		}
													}
													else{
											?>
                                            		<span class="coupon-code"><?php echo $dealUserCoupon['coupon_code']; ?></span>
                                            <?php
													}	
												} ?>
											
										</li>
										<?php } ?>
									<?php }?>
								</ul>
							<?php else: ?>
								<?php echo '-';?>
							<?php endif;?>
						</td>
						<?php if(($this->Auth->user('user_type_id') == ConstUserTypes::Company) ||  ($this->Auth->user('user_type_id') == ConstUserTypes::Admin) ||  ($this->Auth->user('user_type_id') == ConstUserTypes::User)):?>
						<td>
						<?php if (empty($dealUser['DealUser']['is_gift']) || (!empty($dealUser['DealUser']['is_gift']) && $dealUser['DealUser']['gift_email'] == $this->Auth->user('email')) || !empty($this->request->params['named']['deal_id'])):?>
							<ul class="coupon-code">
								<?php foreach ($dealUser['DealUserCoupon'] as $dealUserCoupon) { ?>
									<?php if ((!empty($coupon_find_id) && in_array($dealUserCoupon['id'], $coupon_find_id)) || (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'available' && empty($dealUserCoupon['is_used'])) || (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'used' && !empty($dealUserCoupon['is_used'])) ||  (!empty($show_coupon_code) && $this->request->params['named']['deal_user_view'] == 'coupon')) { ?>
										<li class="clearfix">
                                        	<?php if(Configure::read('deal.deal_coupon_used_type') == 'click'){ ?>
														<span class="coupon-code"><?php echo $dealUserCoupon['unique_coupon_code']; ?></span>
                                            <?php } else { ?>
												 <?php  
												 	if( $this->Auth->user('user_type_id') == ConstUserTypes::Company ){
														if( Configure::read('deal.deal_coupon_code_show_type') == 'top') {
															 echo $this->Form->input('DealUserCoupon.'.$dealUserCoupon['id'].'.unique_coupon_code', array('type' => 'text', 'label' => false, 'div' => false)); 
														}
														else{
												?>
	                                                		<span class="coupon-code"><?php echo $dealUserCoupon['unique_coupon_code']; ?></span>
                                                	
														<?php } 
													}	
													else{		
											?>
                                                      <span class="coupon-code"><?php echo $dealUserCoupon['unique_coupon_code']; ?></span>  
											<?php	}
											} ?>
										</li>
										<?php } ?>
									<?php }?>
								</ul>
							<?php else: ?>
								<?php echo '-';?>
							<?php endif;?>
						</td>
						<td class='dl'>
						<?php if (empty($dealUser['DealUser']['is_gift']) || (!empty($dealUser['DealUser']['is_gift']) && $dealUser['DealUser']['gift_email'] == $this->Auth->user('email')) || !empty($this->request->params['named']['deal_id'])):?>
							<ul class="coupon-code">
								<?php foreach ($dealUser['DealUserCoupon'] as $dealUserCoupon) { ?>
									<?php if ((!empty($coupon_find_id) && in_array($dealUserCoupon['id'], $coupon_find_id)) || (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'available' && empty($dealUserCoupon['is_used'])) || (!empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'used' && !empty($dealUserCoupon['is_used'])) ||  (!empty($show_coupon_code) && $this->request->params['named']['deal_user_view'] == 'coupon')) { ?>
										<li class="clearfix">
											<?php
	$uselink = Router::url(array('controller' => 'deal_user_coupons', 'action' => 'coupon_update_status', $dealUser['DealUser']['id'], 'coupon_id' => $dealUserCoupon['id'],'use'), true);
	$undolink = Router::url(array('controller' => 'deal_user_coupons', 'action' => 'coupon_update_status', $dealUser['DealUser']['id'], 'coupon_id' => $dealUserCoupon['id'],'undo'), true);

												if($dealUserCoupon['is_used'] == 1) {
													$class = 'used';
													$statusMessage = 'Change status to not used';
												} else {
													$class = 'not-used';
													$statusMessage = 'Change status to used';
												}
												if($dealUser['Deal']['company_id'] == $user['Company']['id']) {
													$confirmation_message =  "{'divClass':'js-company-confirmation', }";
												} else {
													$confirmation_message = "{'divClass':'js-user-confirmation'}";
												}
											?>
											<?php if(empty($this->request->params['named']['deal_id'])) { ?>
												<?php echo $this->Html->link(__l('Print'),array('controller' => 'deal_users', 'action' => 'view',$dealUser['DealUser']['id'],'coupon_id' => $dealUserCoupon['id'],'type' => 'print'),array('target'=>'_blank', 'title' => __l('Print'), 'class'=>'print-icon'));?>
												<?php echo $this->Html->link(__l('View Coupon'),array('controller' => 'deal_users', 'action' => 'view',$dealUser['DealUser']['id'],'coupon_id' => $dealUserCoupon['id'],'admin' => false),array('title' => __l('View Coupon'), 'class'=>'js-thickbox','target' => '_blank', 'class'=>'view-icon js-thickbox'));?>
											<?php } ?>
											<?php
												$user = $this->Html->getCompany($this->Auth->user('id'));
												if ((!empty($this->request->params['named']['type']) && $this->request->params['named']['type']=='available') || !empty($this->request->params['named']['deal_id'])) {
													if (!empty($dealUserCoupon['is_used']) && $dealUser['Deal']['company_id'] == $user['Company']['id']) {
												?>
														<span class="<?php echo 'status-'.$dealUserCoupon['is_used']?>">
														<?php
                                                            if(!empty($dealUserCoupon['is_used'])){
                                                                $use_now = __l('Used');
																$types = Configure::read('deal.deal_coupon_used_type');
                                                                																
																if($types == 'click'){
																	echo $use_now;
                                                                	echo $this->Html->link(__l('Undo'), array('controller' => 'deal_user_coupons', 'action' => 'update_status', $dealUser['DealUser']['id'], 'coupon_id' => $dealUserCoupon['id'],'is_used'), array('class' => $class.' js-update-status','title' => $statusMessage));
																}
																// coupon code submit type
																if($types == 'submit' && $this->Auth->user('user_type_id') == ConstUserTypes::Company){
																	$code_type = (Configure::read('deal.deal_coupon_code_show_type') == 'top')? 'UniqueCouponCode' : 'CouponCode';
																	if($dealUser['Deal']['company_id'] == $user['Company']['id']) {
																		$confirmation_message =  "{'divClass':'js-company-confirmation', 'uselink':'".$uselink."', 'undolink':'".$undolink."', 'code_get':'".'DealUserCoupon'.$dealUserCoupon['id'].$code_type."', 'process':'undo'}";
																	} else {
																		$confirmation_message = "{'divClass':'js-user-confirmation', 'uselink':'".$uselink."', 'undolink':'".$undolink."', 'code_get':'".'DealUserCoupon'.$dealUserCoupon['id'].$code_type."', 'process':'undo'}";
																	}
																		echo $this->Html->link(__l('Undo'), array('controller' => 'deal_user_coupons', 'action' => 'coupon_update_status', $dealUser['DealUser']['id'], 'coupon_id' => $dealUserCoupon['id'],'undo'), array('class' => $class.' '.$confirmation_message.' js-coupon-update-status','title' => $statusMessage));
																}
                                                            }else{
																if(!empty($dealUser['Deal']['coupon_start_date'])):
																	if(date('Y-m-d H:i:s') >= $dealUser['Deal']['coupon_start_date']):
																		$use_now = __l('Use Now');
																		echo $this->Html->link($use_now, array('controller' => 'deal_user_coupons', 'action' => 'update_status', $dealUser['DealUser']['id'], 'coupon_id' => $dealUserCoupon['id'],'is_used'), array('class' => $class.' '.$confirmation_message.' js-update-status','title' => $statusMessage));
																	endif;
																endif;
                                                            }
															
														?>
														</span>
													<?php } ?>
													<?php if ($class == 'not-used')  { ?>
														<span class="<?php echo 'status-'.$dealUserCoupon['is_used']?>">
														<?php
                                                            if(!empty($dealUserCoupon['is_used'])){
                                                                $use_now = __l('Used');
                                                                echo $use_now;
															    echo $this->Html->link(__l('Undo'), array('controller' => 'deal_user_coupons', 'action' => 'update_status', $dealUser['DealUser']['id'], 'coupon_id' => $dealUserCoupon['id'], 'is_used'), array('class' => $class.' '.$confirmation_message.' js-update-status', 'title' => $statusMessage));
															}else {
																 if(!empty($dealUser['Deal']['coupon_start_date'])):
																	if(date('Y-m-d H:i:s') >= $dealUser['Deal']['coupon_start_date']):
																		$types = Configure::read('deal.deal_coupon_used_type');
																		$user_check = true;
																		if(!Configure::read('deal.is_user_can_change_coupon_type') && $this->Auth->user('user_type_id') == ConstUserTypes::User){
																			$user_check = false;
																		}
																		if($types == 'click' && $user_check){
																			$use_now = __l('Use Now');
																			echo $this->Html->link($use_now, array('controller' => 'deal_user_coupons', 'action' => 'update_status', $dealUser['DealUser']['id'], 'coupon_id' => $dealUserCoupon['id'], 'is_used'), array('class' => $class.' '.$confirmation_message.' js-update-status', 'title' => $statusMessage));
																		}
																		// coupon code submit type
																		if($types == 'submit' && $this->Auth->user('user_type_id') == ConstUserTypes::Company){
																			$code_type = (Configure::read('deal.deal_coupon_code_show_type') == 'top')? 'UniqueCouponCode' : 'CouponCode';
																			if($dealUser['Deal']['company_id'] == $user['Company']['id']) {
																				$confirmation_message =  "{'divClass':'js-company-confirmation', 'uselink':'".$uselink."', 'undolink':'".$undolink."', 'code_get':'".'DealUserCoupon'.$dealUserCoupon['id'].$code_type."', 'process':'undo'}";
																			} else {
																				$confirmation_message = "{'divClass':'js-user-confirmation', 'uselink':'".$uselink."', 'undolink':'".$undolink."', 'code_get':'".'DealUserCoupon'.$dealUserCoupon['id'].$code_type."', 'process':'undo'}";
																			}
																				echo $this->Html->link(__l('Use Now'), array('controller' => 'deal_user_coupons', 'action' => 'coupon_update_status', $dealUser['DealUser']['id'], 'coupon_id' => $dealUserCoupon['id'],'use'), array('class' => $class.' '.$confirmation_message.' js-coupon-update-status','title' => $statusMessage));
																		}						
																	endif;
																endif;
                                                            }
														?>
														</span>
													<?php } ?>
												<?php } ?>
											</li>
										<?php } ?>
									<?php }?>
								</ul>
							<?php else: ?>
								<?php echo '-';?>
							<?php endif;?>
						</td>
						<?php endif;?>
						<?php endif; ?>
					<td>
						<?php if(!empty($this->request->params['named']['type']) && $this->request->params['named']['type']=='available'):?>
							<?php echo $dealUser['DealUser']['quantity'] - $dealUser['DealUser']['deal_user_coupon_count'];?>
						<?php elseif(!empty($this->request->params['named']['type']) && $this->request->params['named']['type']=='used'):?>
							<?php echo $dealUser['DealUser']['deal_user_coupon_count'];?>
						<?php else:?>
							<?php echo $dealUser['DealUser']['quantity'];?>
						<?php endif;?>
					</td>					
				    <td><?php echo $this->Html->cText($dealUser['City']['name']);?></td>						
					<?php if(Configure::read('charity.is_enabled') == 1):?>
					<td>
						<?php 
							if(!empty($dealUser['CharitiesDealUser']['Charity']['name'])):
								echo $this->Html->cText($dealUser['CharitiesDealUser']['Charity']['name']);
							else:
								echo '-';
							endif;
						?>
					</td>		
					<td><?php echo Configure::read('site.currency') . $this->Html->cCurrency($dealUser['CharitiesDealUser']['amount']);?></td>		
					<td><?php echo Configure::read('site.currency') . $this->Html->cCurrency($dealUser['CharitiesDealUser']['site_commission_amount']);?></td>		
					<td><?php echo Configure::read('site.currency') . $this->Html->cCurrency($dealUser['CharitiesDealUser']['seller_commission_amount']);?></td>	
					<?php endif ?>
				</tr>
			<?php
			endforeach;
		else:
	?>
			<tr>
				<td colspan="14" class="notice"><?php echo sprintf(__l('No coupons available'));?></td>
			</tr>
	<?php
		endif;
	?>
	</table>
        <?php if (!empty($dealUsers) && !empty($this->request->params['named']['type']) && $this->request->params['named']['type'] == 'available'):?>
            <?php if(!empty($dealUser['Deal']['deal_status_id']) && ($dealUser['Deal']['deal_status_id'] != ConstDealStatus::PendingApproval || $dealUser['Deal']['deal_status_id'] != ConstDealStatus::Expired) && ((!empty($this->request->params['named']['type']) && ($this->request->params['named']['type']!='gifted_deals')))){?>
				<div class="admin-select-block">
					<div>
						<?php echo __l('Select:'); ?>
						<?php echo $this->Html->link(__l('All'), '#', array('class' => 'js-admin-select-all', 'title' => __l('All'))); ?>
						<?php echo $this->Html->link(__l('None'), '#', array('class' => 'js-admin-select-none', 'title' => __l('None'))); ?>
					</div>
				<div class="admin-checkbox-button"><?php echo $this->Form->input('more_action_id', array('options' => $moreActions, 'type' => 'select', 'class' => 'js-admin-index-autosubmit', 'label' => false, 'empty' => __l('-- More actions --'))); ?></div>
				</div>
            <?php } ?>    
            <div class="hide">
                <?php echo $this->Form->submit('Submit'); ?>
            </div>
		<?php endif;?>
        <?php if (!empty($dealUsers) && !empty($this->request->params['named']['deal_user_view']) && $this->request->params['named']['deal_user_view'] == 'coupon' && ($this->Auth->user('user_type_id') == ConstUserTypes::Company) && (Configure::read('deal.deal_coupon_used_type') == 'submit')):?>
       	<?php if(!empty($dealUser['Deal']['deal_status_id']) && ($dealUser['Deal']['deal_status_id'] == ConstDealStatus::Tipped || $dealUser['Deal']['deal_status_id'] == ConstDealStatus::Closed && $dealUser['Deal']['deal_status_id'] == ConstDealStatus::PaidToCompany)){?>
        				<div class="admin-select-block">
				<div class="admin-checkbox-button"><?php echo $this->Form->input('more_action_id', array('options' => $moreActions, 'type' => 'select', 'class' => 'js-index-autosubmit', 'label' => false, 'empty' => __l('-- More actions --'))); ?></div>
				</div>

        <?php } ?>
        <div class="hide">
                <?php echo $this->Form->submit('Submit'); ?>
            </div>
        <?php endif;?>
		<?php if (!empty($dealUsers)):?>
			<div class="js-pagination">
				<?php echo $this->element('paging_links'); ?>
			</div>    
		<?php endif;?>
        <?php echo $this->Form->end();?>
    </div>
<?php endif; ?>
