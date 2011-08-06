<?php /* SVN: $Id: admin_index.ctp 54577 2011-05-25 10:39:06Z arovindhan_144at11 $ */ ?>
	<?php 
		if(!empty($this->request->params['isAjax'])):
			echo $this->element('flash_message');
		endif;
	?>

<?php if(empty($this->request->params['isAjax']) && empty($this->request->params['named']['deal_id'])): ?>
	<div class="js-tabs">
           <ul class="clearfix">
                <li><?php echo $this->Html->link(sprintf(__l('Available (%s)'), $available), array('controller' => 'deal_users', 'action' => 'index', 'filter_id' => 'available'), array('title' => __l('Available')));?></li>
                <li><?php echo $this->Html->link(sprintf(__l('Used (%s)'), $used), array('controller' => 'deal_users', 'action' => 'index', 'filter_id' => 'used'), array('title' => __l('Used'))); ?></li>
                <li><?php echo $this->Html->link(sprintf(__l('Expired (%s)'), $expired), array('controller' => 'deal_users', 'action' => 'index', 'filter_id' => 'expired'), array('title' => __l('Expired'))); ?></li>
                <li><?php echo $this->Html->link(sprintf(__l('Pending (%s)'),$open),array('controller' => 'deal_users', 'action' => 'index', 'filter_id' => 'open'), array('title' => __l('Pending'))); ?></li>
                <li><?php echo $this->Html->link(sprintf(__l('Canceled (%s)'),$canceled),array('controller' => 'deal_users', 'action' => 'index', 'filter_id' => 'canceled'), array('title' => __l('Canceled'))); ?></li>
                <li><?php echo $this->Html->link(sprintf(__l('Gifted Coupons (%s)'),$gifted_deals),array('controller' => 'deal_users', 'action' => 'index', 'filter_id' => 'gifted_deals'), array('title' => __l('Gifted Coupons'))); ?></li>
                <li><?php echo $this->Html->link(sprintf(__l('Refunded Coupons (%s)'),$refunded),array('controller' => 'deal_users', 'action' => 'index', 'filter_id' => 'refunded'), array('title' => __l('Refunded Coupons'))); ?></li>
               <li><?php  echo $this->Html->link(sprintf(__l('All (%s)'), $all),array('controller' => 'deal_users', 'action' => 'index', 'filter_id' => 'all'), array('title' => __l('All'))); ?></li>
               
            </ul>
     </div>
<?php else: ?>
    	<div class="dealUsers index js-response js-responses">
		 <div class="info-details">
			<?php echo __l("Commission and Purchased amount is calculated only when the deal is closed. You can see the calculated amount in 'Paid to Company' tab."); ?>
		</div>
        <h2><?php echo __l('Deal Coupons');?></h2>
        <?php echo $this->Form->create('DealUser', array('type' => 'post', 'class' => 'normal search-form clearfix js-ajax-form {"container" : "js-responses"}', 'action'=>'index')); ?>
            <div>
                    <?php echo $this->Form->autocomplete('deal_name', array('label' => __l('Deal'), 'acFieldKey' => 'Deal.id', 'acFields' => array('Deal.name'), 'acSearchFieldNames' => array('Deal.name'), 'maxlength' => '255'));?>
					<?php if (!empty($this->request->params['named']['filter_id']) && ($this->request->params['named']['filter_id'] == 'available' || $this->request->params['named']['filter_id'] == 'used')): ?>
						<?php echo $this->Form->input('coupon_code', array('label' => __l('Coupon code')));?>
					<?php endif;?>
                    <?php if(!empty($this->request->data['DealUser']['filter_id'])): ?>
						<?php echo $this->Form->input('filter_id', array('type' => 'hidden'));?>
                    <?php elseif(!empty($this->request->data['DealUser']['deal_id'])): ?>
						<?php echo $this->Form->input('deal_id', array('type' => 'hidden'));?>
                    <?php endif; ?>
                    <?php echo $this->Form->submit(__l('Search'),array('name' => 'data[DealUser][search]'));?>
        </div>
        <?php echo $this->Form->end(); ?>
		<?php echo $this->Form->create('DealUser' , array('class' => 'normal js-ajax-form','action' => 'update'));?>
        <?php echo $this->Form->input('r', array('type' => 'hidden', 'value' => $this->request->url.$param_string)); ?>
        <?php echo $this->element('paging_counter');?>
        <div class="overflow-block">
        <table class="list">
            <tr>
				<?php if(!empty($this->request->params['named']['filter_id'])  && ($this->request->params['named']['filter_id'] != 'expired')): ?>
					<th rowspan="2"><?php echo __l('Select'); ?></th>
				<?php endif;?>
                <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Purchased Date'),'DealUser.created');?></div></th>
				<?php if(!empty($this->request->params['named']['filter_id'])  && ($this->request->params['named']['filter_id'] == 'canceled')): ?>
	                <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Canceled Date'),'DealUser.modified');?></div></th>
				<?php endif;?>
                <th rowspan="2" class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('User'),'User.username');?></div></th>
                <th rowspan="2" class="dl deal-name"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Deal'), 'Deal.name');?></div></th>
				<?php if ((!empty($this->request->params['named']['filter_id']) && ($this->request->params['named']['filter_id'] == 'available' || $this->request->params['named']['filter_id'] == 'used')) || (empty($this->request->params['named']['filter_id']) && !empty($is_show_coupon_code))): ?>
					<th class="dl" colspan="2"><div class="js-pagination"><?php echo __l('Coupon Code');?></div></th>
				<?php endif;?>
                <th rowspan="2" class="dr"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Price'), 'DealUser.discount_amount').' ('.Configure::read('site.currency').')';?></div></th>
				<?php if(!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'gifted_deals'): ?>
                    <th rowspan="2" class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Gift Email'), 'DealUser.gift_email');?></div></th>
                    <th rowspan="2" class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Message'), 'DealUser.message');?></div></th>
                <?php endif; ?>				
                <th rowspan="2" class="dc"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Quantity'), 'DealUser.quantity');?></div></th>       
				<th rowspan="2"><?php echo __l('Purchased City');?></th>
				<?php if(Configure::read('charity.is_enabled') == 1):?>
				<th colspan="4"><?php echo __l('Charity');?></th>
				<?php endif; ?>
            </tr>
			<tr>
				<?php if ((!empty($this->request->params['named']['filter_id']) && ($this->request->params['named']['filter_id'] == 'available' || $this->request->params['named']['filter_id'] == 'used')) || (empty($this->request->params['named']['filter_id']) && !empty($is_show_coupon_code))): ?>
					<th><div class="js-pagination"><?php echo __l('Top Code');?></div></th>
					<th><div class="js-pagination"><?php echo __l('Bottom Code');?></div></th>					
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
			if($dealUser['DealUser']['deal_user_coupon_count'] == $dealUser['DealUser']['quantity']):
                $status_class = 'js-checkbox-active';
            else:
                $status_class = 'js-checkbox-inactive';
            endif;
        ?>
            <tr<?php echo $class;?>>
			<?php if(!empty($this->request->params['named']['filter_id'])  && ($this->request->params['named']['filter_id'] != 'expired')): ?>
                <td>
                    <div class="actions-block">
                        <div class="actions round-5-left">
                            <span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $dealUser['DealUser']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span>
                            <?php if(!$dealUser['DealUser']['is_repaid'] && !$dealUser['DealUser']['is_canceled']): ?>
                                <span><?php echo $this->Html->link(__l('Print'),array('controller' => 'deal_users', 'action' => 'view',$dealUser['DealUser']['id'],'type' => 'print', 'filter_id' => $this->request->params['named']['filter_id'], 'admin' => false),array('title' => __l('Print'), 'class'=>'print-icon','target' => '_blank'));?></span>
                                <span><?php echo $this->Html->link(__l('View Coupon'),array('controller' => 'deal_users', 'action' => 'view',$dealUser['DealUser']['id'], 'filter_id' => $this->request->params['named']['filter_id'],'admin' => false),array('title' => __l('View Coupon'), 'class'=>'view-icon js-thickbox','target' => '_blank'));?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php echo $this->Form->input('DealUser.'.$dealUser['DealUser']['id'].'.id', array('type' => 'checkbox', 'id' => "admin_checkbox_".$dealUser['DealUser']['id'], 'label' => false, 'class' => $status_class.' js-checkbox-list')); ?>
				</td>
				<?php endif;?>
                <td><?php echo $this->Html->cDateTime($dealUser['DealUser']['created']);?></td>
				<?php if(!empty($this->request->params['named']['filter_id'])  && ($this->request->params['named']['filter_id'] == 'canceled')): ?>
					<td><?php echo $this->Html->cDateTime($dealUser['DealUser']['modified']);?></td>
				<?php endif; ?>
                <td class="dl">
                <?php echo $this->Html->getUserAvatarLink($dealUser['User'], 'micro_thumb',false);?>
                <?php echo $this->Html->getUserLink($dealUser['User']);?></td>
                <td class="dl deal-name">
					<?php echo $this->Html->showImage('Deal', $dealUser['Deal']['Attachment'][0], array('dimension' => 'medium_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($dealUser['Deal']['name'], false)), 'title' => $this->Html->cText($dealUser['Deal']['name'], false)));?>
					<span>
						<?php echo $this->Html->link($this->Html->cText($dealUser['Deal']['name'].(!empty($dealUser['SubDeal']['name']) ? ' - '.$dealUser['SubDeal']['name'] : '')), array('controller' => 'deals', 'action' => 'view', $dealUser['Deal']['slug'], 'admin' => false), array('title'=>$this->Html->cText($dealUser['Deal']['name'].(!empty($dealUser['SubDeal']['name']) ? ' - '.$dealUser['SubDeal']['name'] : ''),false),'escape' => false));?>
					</span>
				</td>
				<?php if ((!empty($this->request->params['named']['filter_id']) && ($this->request->params['named']['filter_id'] == 'available' || $this->request->params['named']['filter_id'] == 'used')) || (empty($this->request->params['named']['filter_id']) && !empty($is_show_coupon_code))): ?>
                <td class="dl">
					<ul>
					<?php foreach($dealUser['DealUserCoupon'] as $dealUserCoupon){?>
						<?php if((!empty($coupon_find_id) && in_array($dealUserCoupon['id'],$coupon_find_id)) || (empty($coupon_find_id) && $this->request->params['named']['filter_id'] == 'available' && $dealUserCoupon['is_used'] == '0') || (empty($coupon_find_id) && $this->request->params['named']['filter_id'] == 'used' && $dealUserCoupon['is_used'] == '1') || (empty($coupon_find_id) && $this->request->params['named']['filter_id'] != 'used' && $this->request->params['named']['filter_id'] != 'available' )){?>
							<?php 
								if(!empty($dealUserCoupon['is_used'])):
									$image = 'icon-used.png';
								else:
									$image = 'icon-not-used.png';
								endif;
							?>
							<li>
								<?php echo $this->Html->cText($dealUserCoupon['coupon_code']).' ';?>
								<?php if(!empty($this->request->params['named']['filter_id'])  && ($this->request->params['named']['filter_id'] != 'used') && ($this->request->params['named']['filter_id'] != 'available')): ?>
									<?php echo $this->Html->image($image);?>
								<?php endif;?>
							</li>
						<?php }?>
					<?php } ?>
					</ul>
				</td>
                <td class="dl">
					<ul>
					<?php foreach($dealUser['DealUserCoupon'] as $dealUserCoupon){?>
						<?php if((!empty($coupon_find_id) && in_array($dealUserCoupon['id'],$coupon_find_id)) || (empty($coupon_find_id) && $this->request->params['named']['filter_id'] == 'available' && $dealUserCoupon['is_used'] == '0') || (empty($coupon_find_id) && $this->request->params['named']['filter_id'] == 'used' && $dealUserCoupon['is_used'] == '1') || (empty($coupon_find_id) && $this->request->params['named']['filter_id'] != 'used' && $this->request->params['named']['filter_id'] != 'available' )){?>
							<li>
								<?php echo $this->Html->cText($dealUserCoupon['unique_coupon_code']).' ';?>
							</li>
						<?php }?>
					<?php } ?>
					</ul>
				</td>
				<?php endif;?>
				<td class="dr"><?php echo $this->Html->cFloat($dealUser['DealUser']['discount_amount']);?></td>
				<?php if(!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'gifted_deals'): ?>
                    <td><?php echo $this->Html->cText($dealUser['DealUser']['gift_email']);?></td>
                    <td class="dl"><?php echo $this->Html->cText($dealUser['DealUser']['message']);?></td>
                <?php endif; ?>			
				<td class="dc"><?php echo $this->Html->cInt($dealUser['DealUser']['quantity']);?></td>  
				<td><?php echo $this->Html->cText($dealUser['City']['name']);?></td>	
				<?php if(Configure::read('charity.is_enabled') == 1):?>
				<td><?php echo $this->Html->cText((!empty($dealUser['CharitiesDealUser']['Charity']['name']) ? $dealUser['CharitiesDealUser']['Charity']['name'] : ''));?></td>		
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
                <td colspan="14" class="notice"><?php echo __l('No coupons available');?></td>
            </tr>
        <?php
        endif;
        ?>
        </table>
        </div>
		<?php if (!empty($dealUsers)):?>
			<?php if(!empty($this->request->params['named']['filter_id'])  && ($this->request->params['named']['filter_id'] != 'expired')): ?>
            <div class="admin-select-block">
            <div>
                <?php echo __l('Select:'); ?>
                <?php echo $this->Html->link(__l('All'), '#', array('class' => 'js-admin-select-all', 'title' => __l('All'))); ?>
                <?php echo $this->Html->link(__l('None'), '#', array('class' => 'js-admin-select-none', 'title' => __l('None'))); ?>
                <?php if($this->request->params['named']['filter_id'] == 'all' || (!empty($this->request->params['named']['deal_id']))) { ?>
                    <?php echo $this->Html->link(__l('Use Now'), '#', array('class' => 'js-admin-select-approved', 'title' => __l('Use Now'))); ?>
                    <?php echo $this->Html->link(__l('Not Used'), '#', array('class' => 'js-admin-select-pending', 'title' => __l('Not Used'))); ?>
                <?php } ?>
            </div>
            <div class="admin-checkbox-button"><?php echo $this->Form->input('more_action_id', array('options' => $moreActions, 'type'=>'select','class' => 'js-admin-index-autosubmit', 'label' => false, 'empty' => __l('-- More actions --'))); ?></div>
            </div>
			<?php endif; ?>
            <div class="js-pagination">
            <?php echo $this->element('paging_links'); ?>
            </div>    
        <?php  endif;  ?>
        <div class="hide">
            <?php echo $this->Form->end('Submit'); ?>
        </div>
        </div>
<?php endif; ?>