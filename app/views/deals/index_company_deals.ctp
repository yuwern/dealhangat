<?php /* SVN: $Id: index_company_deals.ctp 60075 2011-07-12 12:07:47Z mohanraj_109at09 $ */?>
<?php if(empty($this->request->params['isAjax']) && empty($this->request->params['named']['stat'])): ?>
<h2><?php echo $headings; ?> </h2>
	<div class="js-tabs">
        <ul class="clearfix">
                <li><?php echo $this->Html->link(sprintf('Open (%s)',$dealStatusesCount[ConstDealStatus::Open]), array('controller' => 'deals', 'action' => 'index', 'filter_id' => ConstDealStatus::Open, 'company' => $company_slug), array('title' => __l('Open')));?></li>
                <?php $all = $dealStatusesCount[ConstDealStatus::Open]; ?>
        		<?php foreach($dealStatuses as $id => $dealStatus): ?>
                	<?php if($id != ConstDealStatus::Open): ?>
                        <li><?php echo $this->Html->link($dealStatus.' ('.$dealStatusesCount[$id].')', array('controller' => 'deals', 'action' => 'index', 'filter_id' => $id, 'company' => $company_slug), array('title' => $dealStatus));?></li>
                        <?php $all += $dealStatusesCount[$id]; ?>
                     <?php endif; ?>
                <?php endforeach; ?>
                <li><?php echo $this->Html->link(sprintf('All (%s)',$all),array('controller'=> 'deals', 'action'=>'index', 'type' => 'all', 'company' => $company_slug),array('title' => __l('All'))); ?></li>
            </ul>
    </div>
<?php else: ?>
     <?php if(!empty($this->request->params['named']['filter_id']) && (!empty($dealStatusesCount[$this->request->params['named']['filter_id']]))){
        $id = $this->request->params['named']['filter_id'];
     }else if(!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'all')){
        $id = $this->request->params['named']['type'];
     }
     ?>
     
	<div class="js-response js-responses js-search-responses">
	 <div class="info-details">
		<?php echo __l("Commission and Purchased amount is calculated only when the deal is closed. You can see the calculated amount in 'Paid to Company' tab."); ?>
	</div>
    <h2><?php echo $pageTitle; ?></h2>
     <?php echo $this->Form->create('Deal', array('url' => array('controller' => 'deals', 'action' => 'index','filter_id' => (!empty($this->request->params['named']['filter_id'])) ? $this->request->params['named']['filter_id'] : '', 'company' => $company_slug) ,'class' => 'normal js-ajax-form {"container" : "js-search-responses"}'));?>
	   <?php echo $this->Form->input('q', array('label' => __l('Keyword'))); ?>
	   <?php echo $this->Form->hidden('filter_id', array('value' => (!empty($this->request->params['named']['filter_id'])) ? $this->request->params['named']['filter_id'] : '')); ?>
	   <?php echo $this->Form->hidden('type', array('value' => (!empty($this->request->params['named']['type'])) ? $this->request->params['named']['type'] :'')); ?>
	   <?php echo $this->Form->hidden('company_slug', array('value' => $company_slug)); ?>
	   <div class="submit-block clearfix">
		<?php
		echo $this->Form->end(__l('Search')); ?>
		</div>
    <?php echo $this->element('paging_counter'); ?>
    <table class="list company-list">
        <tr>
	   <?php if(!empty($this->request->params['named']['filter_id']) && ( $this->request->params['named']['filter_id'] == ConstDealStatus::Upcoming || $this->request->params['named']['filter_id'] == ConstDealStatus::PendingApproval || $this->request->params['named']['filter_id'] == ConstDealStatus::Rejected || $this->request->params['named']['filter_id'] == ConstDealStatus::Canceled || $this->request->params['named']['filter_id'] == ConstDealStatus::Draft)){?>
            <th class="dl deal-name"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Deal Name'),'Deal.name') ; ?></div></th>

            <th class="dr"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Original Price'), 'Deal.original_price').' ('.Configure::read('site.currency').')'; ?></div></th>
            <th class="dr"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Discounted Price'), 'Deal.discounted_price').' ('.Configure::read('site.currency').')'; ?></div></th>
    <?php }else{ ?>
            <th rowspan="2" class="deal-name"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Deal Name'),'name') ; ?></div></th>
			<?php if(!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'all')):?>
				<th rowspan="2"  class="dl deal-name"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Status'),'DealStatus.name') ; ?></div></th>
			<?php endif;?>
            <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Original Price'),'Deal.original_price').' ('.Configure::read('site.currency').')'; ?></div></th>
            <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Discounted Price'),'Deal.discounted_price').' ('.Configure::read('site.currency').')'; ?></div></th>
            <th colspan="2"><?php echo __l('Quantity'); ?></th>
            <th colspan="2"><?php echo __l('Amount').' ('.Configure::read('site.currency').')';?></th>
            <?php if((!empty($this->request->params['named']['filter_id']) && ($this->request->params['named']['filter_id'] != ConstDealStatus::Expired)) || !empty($this->request->params['named']['type']) ){?>
                <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Commission'),'Deal.commission_percentage').' (%)'; ?></div></th>
                <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Bonus Amount'),'Deal.bonus_amount').' ('.Configure::read('site.currency').')'; ?></div></th>
                <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Commission Amount'),'total_commission_amount').' ('.Configure::read('site.currency').')'; ?></div></th>
            <?php } ?>
             <?php if(!empty($this->request->params['named']['filter_id']) && ($this->request->params['named']['filter_id'] == ConstDealStatus::Open  || $this->request->params['named']['filter_id'] == ConstDealStatus::Closed || $this->request->params['named']['filter_id'] == ConstDealStatus::PaidToCompany || $this->request->params['named']['filter_id'] == ConstDealStatus::Tipped)){?>
                  <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Quantity Sold'),'Deal.deal_user_count'); ?></div></th>
             <?php } ?>
        </tr>
        <tr>
            <th><?php echo __l('Target'); ?></th>
            <th><?php echo __l('Achieved'); ?></th>
            <th><?php echo __l('Target'); ?></th>
            <th><?php echo __l('Achieved'); ?></th>
        </tr>
    <?php } ?>
    <?php if(!empty($deals)): ?>
      <?php foreach($deals as $deal): ?>
	   <?php if(!empty($this->request->params['named']['filter_id']) && ( $this->request->params['named']['filter_id'] == ConstDealStatus::Upcoming || $this->request->params['named']['filter_id'] == ConstDealStatus::PendingApproval || $this->request->params['named']['filter_id'] == ConstDealStatus::Rejected || $this->request->params['named']['filter_id'] == ConstDealStatus::Canceled || $this->request->params['named']['filter_id'] == ConstDealStatus::Draft)){?>
        <tr>

            <td class="dl deal-name">
                <?php if(!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == ConstDealStatus::Draft):?>
                    <div class="actions-block">
                        <div class="actions round-5-left">
                            <span><?php echo $this->Html->link(__l('Edit'), array('controller' => 'deals', 'action'=>'edit', $deal['Deal']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span>
                            <span><?php echo $this->Html->link(__l('Delete'), array('controller' => 'deals', 'action'=>'delete', $deal['Deal']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span>
                            <span><?php echo $this->Html->link(__l('Save and send to admin approval'), array('controller' => 'deals', 'action'=>'update_status', $deal['Deal']['id']), array('class' => 'add js-delete', 'title' => __l('Save and send to admin approval')));?></span>
                        </div>
                    </div>
				<?php elseif(!empty($this->request->params['named']['filter_id']) && ( $this->request->params['named']['filter_id'] == ConstDealStatus::Upcoming || $this->request->params['named']['filter_id'] == ConstDealStatus::PendingApproval)):?>
				   <?php if(empty($deal['Deal']['is_subdeal_available'])):?>
                    <div class="actions-block">
                        <div class="actions round-5-left">
							<span><?php echo $this->Html->link(__l('Clone Deal'),array('controller'=>'deals', 'action'=>'add', 'clone_deal_id'=>$deal['Deal']['id']), array('class' => 'add', 'title' => __l('Clone Deal')));?></span>
                        </div>
                    </div>
					<?php endif; ?>
                <?php endif; ?>
                <?php 
					if(!empty($deal['Attachment'][0])):
						echo $this->Html->showImage('Deal', $deal['Attachment'][0], array('dimension' => 'medium_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($deal['Deal']['name'], false)), 'title' => $this->Html->cText($deal['Deal']['name'], false)));
					else:
						echo $this->Html->showImage('Deal', $deal['Attachment'], array('dimension' => 'medium_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($deal['Deal']['name'], false)), 'title' => $this->Html->cText($deal['Deal']['name'], false)));					
					endif;
				?>
                <?php echo $this->Html->link($deal['Deal']['name'], array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug']),array('title'=>$deal['Deal']['name']));?>
				<?php if(!empty($deal['Deal']['coupon_start_date'])):
					if(date('Y-m-d H:i:s') < $deal['Deal']['coupon_start_date']):
					?>
						<span class="pending-coupons" title="<?php echo __l('Coupon code can be used from'.' '.$this->Html->cDateTime($deal['Deal']['coupon_start_date'], false));?>"></span>
					<?php endif;?>
				<?php endif;?>
            </td>
            <?php if(!empty($deal['Deal']['is_subdeal_available']) && ($deal['Deal']['is_subdeal_available'] !=0) )
			     {
					 $original_price = $deal['SubDeal'][0]['original_price']; 
					 $disount_price =  $deal['SubDeal'][0]['discounted_price']; 
                 } else{
					  $original_price = $deal['Deal']['original_price']; 
					  $disount_price =  $deal['Deal']['discounted_price']; 
               } ?>
            <td class="dr"><?php echo $this->Html->cCurrency($original_price); ?></td>
            <td class="dr"><?php echo $this->Html->cCurrency($disount_price); ?></td>
        </tr>
        <?php } else {
			$rowspan = '';	
			$add_row='';
			$have_sub_deal = '';
			if($deal['Deal']['is_subdeal_available'] && $deal['Deal']['sub_deal_count'] > 0){
				$have_sub_deal = 1;
				$rowspan_count = $deal['Deal']['sub_deal_count']+1;
				$rowspan = 'rowspan="'.$rowspan_count.'"';
				foreach ($deal['SubDeal'] as $subDeal)
				{
					$add_row .= '<tr>
								<td nowrap>'.$subDeal['name'].'</td>
								<td nowrap>'.$this->Html->cCurrency($subDeal['original_price']).'</td>
								<td nowrap>'.$this->Html->cCurrency($subDeal['discounted_price']).'</td>
								<td nowrap>'.$this->Html->cInt($subDeal['min_limit']).'</td>
								<td nowrap>'.$this->Html->cInt($subDeal['deal_user_count']).'</td>
								<td nowrap>'.$this->Html->cCurrency($subDeal['discounted_price'] * $subDeal['min_limit']).'</td>
								<td nowrap>'.$this->Html->cCurrency($subDeal['discounted_price'] * $subDeal['deal_user_count']).'</td>
								<td nowrap>'.$this->Html->cFloat($subDeal['commission_percentage']).'</td>
								<td nowrap>'.$this->Html->cCurrency($subDeal['bonus_amount']).'</td>
								<td nowrap>'.$this->Html->cCurrency($subDeal['total_commission_amount']).'</td>
								<td nowrap>'.$this->Html->link($this->Html->cInt($subDeal['deal_user_count'], false),array('controller'=>'deal_users', 'action'=>'index', 'deal_id'=>$subDeal['id'], 'deal_user_view' =>'coupon'),array('class' => 'js-thickbox')).'</td>
							</tr>';
				}
			}		   
		?>
        <tr>
            <td class="dl deal-name" <?php echo ($rowspan != '') ? 'colspan="11"' : ''; ?>>
                <div class="actions-block">
                    <div class="actions round-5-left cities-action-block">
					<?php if(in_array($deal['Deal']['deal_status_id'], array(ConstDealStatus::Tipped,ConstDealStatus::Closed,ConstDealStatus::PaidToCompany))):?>
						    <span><?php echo $this->Html->link(__l('Coupons CSV'), array('controller' => 'deals', 'action' => 'coupons_export', 'deal_id' =>  $deal['Deal']['id'], 'city' => $city_slug, 'filter_id' => $id, 'ext' => 'csv'), array('class' => 'export', 'title' => __l('Coupons CSV')));?></span>
                            <span> <?php echo $this->Html->link(__l('Print of Coupons'),array('controller' => 'deals', 'action' => 'deals_print', 'filter_id' => $this->request->params['named']['filter_id'],'page_type' => 'print', 'deal_id' => $deal['Deal']['id'], 'company' => $company_slug),array('title' => __l('Print of Coupons'), 'target' => '_blank', 'class'=>'print-icon'));?></span>
						<?php endif; ?>
						<span><?php echo $this->Html->link(__l('List Coupons'), array('controller' => 'deal_coupons', 'action' => 'index', 'deal_id' =>  $deal['Deal']['id']), array('target' => '_blank', 'title' => __l('List Coupons')));?></span>
						<?php if(in_array($deal['Deal']['deal_status_id'], array(ConstDealStatus::Open, ConstDealStatus::Tipped,ConstDealStatus::Closed,ConstDealStatus::PaidToCompany))):?>
							<span>
							<?php echo $this->Html->link(__l('Quantity Sold').'('.$this->Html->cInt($deal['Deal']['deal_user_count'], false).')',array('controller'=>'deal_users', 'action'=>'index', 'deal_id'=>$deal['Deal']['id']),array('class' => 'js-thickbox'));?>							
							</span>
						<?php endif; ?>
					   <?php if(empty($deal['Deal']['is_subdeal_available'])):?>
							<span><?php echo $this->Html->link(__l('Clone Deal'),array('controller'=>'deals', 'action'=>'add', 'clone_deal_id'=>$deal['Deal']['id']), array('class' => 'add', 'title' => __l('Clone Deal')));?></span>
						<?php endif; ?>
                    </div>
                </div>
                <?php echo $this->Html->showImage('Deal', $deal['Attachment'][0], array('dimension' => 'medium_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($deal['Deal']['name'], false)), 'title' => $this->Html->cText($deal['Deal']['name'], false)));?>
                <?php echo $this->Html->link($deal['Deal']['name'], array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug']),array('title'=>$deal['Deal']['name']));?>
				<?php if(!empty($deal['Deal']['coupon_start_date'])):
					if(date('Y-m-d H:i:s') < $deal['Deal']['coupon_start_date']):
					?>
						<span class="pending-coupons" title="<?php echo __l('Coupon code can be used from'.' '.$this->Html->cDateTime($deal['Deal']['coupon_start_date'], false));?>"></span>
					<?php endif;?>
				<?php endif;?>
            </td>
			<?php if($rowspan == '') { ?>
			<?php if(!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'all')):?>
				<td class="dl" ><?php echo $this->Html->cText($deal['DealStatus']['name'], false) ; ?></td>
			<?php endif;?>
            <td class="dr" ><?php if(!$have_sub_deal) { echo $this->Html->cCurrency($deal['Deal']['original_price']); } ?></td>
            <td class="dr" ><?php if(!$have_sub_deal) { echo $this->Html->cCurrency($deal['Deal']['discounted_price']); } ?></td>
            <td ><?php if(!$have_sub_deal) { echo $this->Html->cInt($deal['Deal']['min_limit']); } ?></td>
            <td ><?php if(!$have_sub_deal) { echo $this->Html->cInt($deal['Deal']['deal_user_count']); } ?></td>
            <td class="dr" ><?php if(!$have_sub_deal) {  echo $this->Html->cCurrency($deal['Deal']['discounted_price'] * $deal['Deal']['min_limit']); } ?></td>
            <td class="dr" ><?php if(!$have_sub_deal) {  echo $this->Html->cCurrency($deal['Deal']['discounted_price'] * $deal['Deal']['deal_user_count']); } ?></td>
            <?php if((!empty($this->request->params['named']['filter_id']) && ($this->request->params['named']['filter_id'] != ConstDealStatus::Expired)) || !empty($this->request->params['named']['type']) ){?>
                <td ><?php if(!$have_sub_deal) {  echo $this->Html->cFloat($deal['Deal']['commission_percentage']); } ?></td>
                <td  class="dr"><?php if(!$have_sub_deal) { echo $this->Html->cCurrency($deal['Deal']['bonus_amount']); } ?></td>
                <td  class="dr"><?php if(!$have_sub_deal) { echo $this->Html->cCurrency($deal['Deal']['total_commission_amount']); } ?></td>
             <?php } ?>
             <?php if(!empty($this->request->params['named']['filter_id']) && ($this->request->params['named']['filter_id'] == ConstDealStatus::Open || $this->request->params['named']['filter_id'] == ConstDealStatus::Closed || $this->request->params['named']['filter_id'] == ConstDealStatus::PaidToCompany || $this->request->params['named']['filter_id'] == ConstDealStatus::Tipped)){?>
                 <td ><?php if(!$have_sub_deal) {  echo $this->Html->link($this->Html->cInt($deal['Deal']['deal_user_count'], false),array('controller'=>'deal_users', 'action'=>'index', 'deal_id'=>$deal['Deal']['id'], 'deal_user_view' =>'coupon'),array('class' => 'js-thickbox')); } ?></td>
            <?php } ?>
            <?php } ?>
        </tr>
		<?php
			echo $add_row;
			$rowspan='';
			$add_row='';
        ?>
       <?php } ?>
      <?php endforeach; ?>
    <?php else: ?>
        <tr><td class="notice" colspan="11"><?php echo __l('No deals available');?></td></tr>
    <?php endif; ?>
    </table>
	<?php
    if (!empty($deals)) {
        ?>
            <div class="js-pagination">
                <?php echo $this->element('paging_links'); ?>
            </div>
        <?php
    }
    ?>
    </div>
<?php endif; ?>