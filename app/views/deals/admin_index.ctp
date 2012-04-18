<?php /* SVN: $Id: admin_index.ctp 59439 2011-07-08 05:33:11Z aravindan_111act10 $ */ ?>
	<?php 
		if(!empty($this->request->params['isAjax'])):
			echo $this->element('flash_message');
		endif;
	?>
<?php if(empty($this->request->params['isAjax']) && empty($this->request->params['named']['stat'])): ?>
	<div class="js-tabs">
		<?php 
			if(!empty($this->request->params['named']['company'])):
				$url= array(
					'controller' => 'deals',
					'action' => 'index',
					'company' => $this->request->params['named']['company'],
				);
			elseif(!empty($this->request->params['named']['city_slug'])):
				$url= array(
					'controller' => 'deals',
					'action' => 'index',
					'city_slug' => $this->request->params['named']['city_slug'],
				);
			else:
				$url= array(
					'controller' => 'deals',
					'action' => 'index',
				);			
			endif;
		?>
        <ul class="clearfix">
			<li>
				<?php $url['filter_id'] = ConstDealStatus::Open;?>
				<?php echo $this->Html->link(sprintf(__l('Open (%s)'),$dealStatusesCount[ConstDealStatus::Open]), $url, array('title' => __l('Open')));?>
				
			</li>
			<?php $all = $dealStatusesCount[ConstDealStatus::Open]; ?>
			<?php foreach($dealStatuses as $id => $dealStatus): ?>
			<?php if($id != ConstDealStatus::Open): ?>
				<li>
					<?php $url['filter_id'] = $id;?>
					<?php echo $this->Html->link(sprintf("%s", __l($dealStatus).' ('.$dealStatusesCount[$id].')'), $url, array('title' => __l($dealStatus)));?>
				</li>
				<?php $all += $dealStatusesCount[$id]; ?>
			 <?php endif; ?>
			<?php endforeach; ?>
			<?php $url['type'] ='all';?>
			<?php unset($url['filter_id']);?>
			<li><?php echo $this->Html->link(sprintf(__l('All (%s)'),$all),$url,array('title' => __l('All'))); ?></li>
		</ul>
    </div>
<?php else: ?>
	 <?php if(empty($this->request->data)): ?>
		 <?php if(!empty($this->request->params['named']['filter_id']) && (!empty($dealStatusesCount[$this->request->params['named']['filter_id']]))){
            $id = $this->request->params['named']['filter_id'];
         }else if(!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'all')){
            $id = $this->request->params['named']['type'];
         }
         ?>    
        <div class="js-response js-responses">
		 <div class="info-details">
			<?php echo __l("Commission and Purchased amount is calculated only when the deal is closed. You can see the calculated amount in 'Paid to Company' tab."); ?>
		</div>
            <h2><?php echo $pageTitle; ?>
			<?php 
			if(!empty($this->request->params['named']['company'])) {
				echo  ' - ' . ucfirst($this->request->params['named']['company']);
			} elseif(!empty($this->request->params['named']['city_slug'])) {
				echo  ' - ' . ucfirst($this->request->params['named']['city_slug']);
			} else {
				echo '';
			}
			?>
            </h2>
              <?php echo $this->Form->create('Deal' , array('type' => 'post', 'class' => 'normal search-form clearfix js-ajax-form {"container" : "js-search-responses"}','action' => 'index','url' => $this->request->params['named'])); ?>
                   <?php echo $this->Form->input('q', array('label' => __l('Keyword'))); ?>
					<?php echo $this->Form->input('filter_id', array('type' => 'hidden', 'value' => (!empty($this->request->params['named']['filter_id']) ? $this->request->params['named']['filter_id'] : ''))); ?>
                    <?php
                    echo $this->Form->submit(__l('Search'));
                    echo $this->Form->end();
            ?>
            <div class="clearfix add-block1">
                <?php echo $this->Html->link(__l('Add'), array('controller' => 'deals', 'action' => 'add'), array('class' => 'add','title' => __l('Add'))); ?>
                <?php echo $this->Html->link(__l('Manual Deal Status Update'), array('controller' => 'deals', 'action' => 'update_status'), array('class' => 'update-status', 'title' => __l('You can use this to update deals various status.This will be used in the scenario where cron is not working')));?>
            </div>
    <?php endif; ?>   
    		<div class="js-search-responses">   
				  <?php echo $this->Form->create('Deal' , array('class' => 'normal','action' => 'update')); ?>
                  <?php echo $this->Form->input('r', array('type' => 'hidden', 'value' => $this->request->url)); ?>
                   <?php echo $this->element('paging_counter');?>
                   <div class="overflow-block">
                  <table class="list">
                    <tr>
                      <?php	if(!empty($moreActions)): ?>
                          <th rowspan="2"><?php echo __l('Select'); ?></th>
                      <?php endif; ?>
					  <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Added On'), 'Deal.created'); ?></div></th>
                      <th class="dl" rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Deal'),'Deal.name'); ?></div></th>
                      <th class="dl deal-name" rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('User'),'User.username'); ?></div></th>
                      <th class="dl" rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Company'),'Company.name'); ?></div></th>
                      <th class="dl" rowspan="2"><div class="js-pagination"><?php echo __l('City'); ?></div></th>
                      <th class="dl" rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Side Deal'),'Deal.is_side_deal'); ?></div></th>
                        <?php if(!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'all')) { ?>
                            <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Status'), 'DealStatus.name'); ?></div></th>
                       <?php } ?>
                      <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Start Date'), 'Deal.start_date'); ?></div></th>
                      <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('End Date'), 'Deal.end_date'); ?></div></th>
                      <th colspan="4"><?php echo __l('Price').' ('.Configure::read('site.currency').')'; ?></th>
                      <th colspan="2"><?php echo __l('User Limit'); ?></th>
                      <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Quantity Sold'),'Deal.deal_user_count'); ?></div></th>
                      <th class="dr" rowspan="2"><div class="js-pagination">
					  <?php echo $this->Paginator->sort(sprintf(__l('Total Purchased Amount (%s)'),Configure::read('site.currency')),'Deal.total_purchased_amount'); ?></div></th>
                      <th colspan="3"><?php echo __l('Commission').' ('.Configure::read('site.currency').')'; ?></th>
                      <th class="dl" rowspan="2"><?php echo __l('Private Note'); ?></th>
                    </tr>
                    <tr>
                      <th class="dr"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Original Price'), 'Deal.original_price'); ?></div></th>
                      <th class="dr"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Discounted Price'),'Deal.discounted_price');?></div></th>
                      <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Discount Percentage'), 'Deal.discount_percentage').' (%)';?></div></th>
                      <th class="dr"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Discount Amount'), 'Deal.discount_amount').' ('.Configure::read('site.currency').')';?></div></th>
                      <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Minimum'),'Deal.min_limit'); ?></div></th>
                      <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Maximum'),'Deal.max_limit'); ?></div></th>
                      <th class="dr"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Bonus Amount'),'Deal.bonus_amount'); ?></div></th>
                      <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Commission Percentage'),'Deal.commission_percentage'); ?></div></th>
                      <th class="dr"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Total Commission Amount'), 'Deal.total_commission_amount'); ?></div></th>
                    </tr>
                    <?php
                    
                        if (!empty($deals)):
                            $i = 0;
                            foreach ($deals as $deal):
                            $status_class = '';
                                 $class = null;
                                if ($i++ % 2 == 0):
                                    $class = ' class="altrow"';
                                endif;
                                if($deal['Deal']['deal_status_id'] == ConstDealStatus::Open):
                                    $status_class = ' js-checkbox-active';
                                endif;
                                if($deal['Deal']['deal_status_id'] == ConstDealStatus::PendingApproval):
                                    $status_class = ' js-checkbox-inactive';
                                endif;
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
													<td nowrap>'.$this->Html->cFloat($subDeal['discount_percentage']).'</td>
													<td nowrap>'.$this->Html->cCurrency($subDeal['discount_amount']).'</td>
													<td nowrap>'.(!empty($subDeal['max_limit']) ? $this->Html->cInt($subDeal['max_limit']) : __l('No Limit')).'</td>
													<td nowrap>'.$this->Html->cInt($subDeal['deal_user_count']).'</td>
													<td nowrap>'.$this->Html->cCurrency($subDeal['total_purchased_amount']).'</td>
													<td nowrap>'.$this->Html->cCurrency($subDeal['bonus_amount']).'</td>
													<td nowrap>'.$this->Html->cFloat($subDeal['commission_percentage']).'</td>
													<td nowrap>'.$this->Html->cCurrency($subDeal['total_commission_amount']).'</td>
													</tr>';
									}
								}
                                ?>
                    <tr<?php echo $class;?>>
					  <?php	if(!empty($moreActions)): ?>
                          <td <?php echo $class;?> <?php echo $rowspan; ?>>
                            <div class="actions-block">
                                <div class="actions round-5-left">
                                  <?php if(!empty($this->request->params['named']['filter_id']) && (($this->request->params['named']['filter_id'] == ConstDealStatus::Tipped) || ($this->request->params['named']['filter_id'] == ConstDealStatus::Closed) || ($this->request->params['named']['filter_id'] == ConstDealStatus::PaidToCompany))):?>
                                       <?php echo $this->Html->link(__l('Coupons CSV'), array('controller' => 'deals', 'action' => 'coupons_export',  'admin' => false,'deal_id:'.$deal['Deal']['id'],'ext' => 'csv'), array('class' => 'export', 'title' => __l('Coupons CSV')));?>
                                        <span> <?php //
										 echo $this->Html->link(__l('Word'), array('controller' => 'deals', 'action' => 'coupons_document',$deal['Deal']['slug'], 'admin' => true),array('title' => __l('Word Document'), 'class' => 'export'));
										//echo $this->Html->link(__l('Print'),array('controller' => 'deals', 'action' => 'deals_print', 'filter_id' => $this->request->params['named']['filter_id'],'page_type' => 'print', 'deal_id' => $deal['Deal']['id']),array('title' => __l('Print'), 'class'=>'print-icon'));
									?></span>
                                   <?php endif; ?>
                                  <?php if(!empty($deal['Deal']['deal_status_id']) && $deal['Deal']['deal_status_id'] != ConstDealStatus::PendingApproval && $deal['Deal']['deal_status_id'] != ConstDealStatus::Rejected && $deal['Deal']['deal_status_id'] != ConstDealStatus::Draft && $deal['Deal']['deal_status_id'] != ConstDealStatus::Upcoming) {?>
                                  <?php echo $this->Html->link(sprintf(__l('Quantity Sold  (%s)'),$this->Html->cInt($deal['Deal']['deal_user_count'], false)),array('controller'=>'deal_users', 'action'=>'index', 'deal_id'=>$deal['Deal']['id']), array('class' => 'edit js-edit coupon-sold', 'title' => __l('Quantity Sold')));?>
									<?php } ?>
                                  <?php echo $this->Html->link(__l('Edit'), array('controller' => 'deals', 'action'=>'edit', $deal['Deal']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?>
                                  <?php echo $this->Html->link(__l('Delete'), array('action'=>'delete', $deal['Deal']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?>
								  <?php if(empty($deal['Deal']['is_subdeal_available'])):?>
									 <?php echo $this->Html->link(__l('Clone Deal'),array('controller'=>'deals', 'action'=>'add', 'clone_deal_id'=>$deal['Deal']['id']), array('class' => 'add', 'title' => __l('Clone Deal')));?>
								  <?php endif;?> 		
								  <?php if(!empty($deal['Deal']['deal_status_id']) && $deal['Deal']['deal_status_id'] != ConstDealStatus::PendingApproval && $deal['Deal']['deal_status_id'] != ConstDealStatus::Rejected && $deal['Deal']['deal_status_id'] != ConstDealStatus::Draft): 
								   echo $this->Html->link(__l('View Discussions'),array('controller'=>'topics', 'action'=>'index', 'deal_id'=>$deal['Deal']['id']), array('title' => __l('View Discussions'),'class' =>'view-icon')); 
								   endif; ?>
									<span>
										<?php echo $this->Html->link(__l('List Allocated Coupons'), array('controller' => 'deal_coupons', 'action' => 'index', 'deal_id' =>  $deal['Deal']['id']), array('target' => '_blank', 'title' => __l('List Coupons')));?>
									</span>
                                </div>
                            </div>
                              <?php echo $this->Form->input('Deal.'.$deal['Deal']['id'].'.id', array('type' => 'checkbox', 'id' => "admin_checkbox_".$deal['Deal']['id'], 'label' => false, 'class' => 'js-checkbox-list '. $status_class. '' )); ?>
                       </td>
                      <?php endif; ?> 
                      <td <?php echo $rowspan; ?>>
                          <?php	if(empty($moreActions)): ?>
                              <div class="actions-block">
                                    <div class="actions round-5-left">
                                      <?php if(!empty($this->request->params['named']['filter_id']) && (($this->request->params['named']['filter_id'] == ConstDealStatus::Tipped) || ($this->request->params['named']['filter_id'] == ConstDealStatus::Closed) || ($this->request->params['named']['filter_id'] == ConstDealStatus::PaidToCompany))):?>
                                            <span><?php echo $this->Html->link(__l('CSV'), array('controller' => 'deals', 'action' => 'coupons_export', 'admin' => false,'deal_id:'.$deal['Deal']['id'],'ext' => 'csv'), array('class' => 'export', 'title' => __l('CSV')));?></span>
                                            <span> <?php echo $this->Html->link(__l('Print'),array('controller' => 'deals', 'action' => 'deals_print', 'filter_id' => $this->request->params['named']['filter_id'],'page_type' => 'print', 'deal_id' => $deal['Deal']['id']),array('title' => __l('Print'), 'target' => '_blank', 'class'=>'print-icon'));?></span>
                                       <?php endif; ?>
                                      <?php if(!empty($deal['Deal']['deal_status_id']) && $deal['Deal']['deal_status_id'] != ConstDealStatus::PendingApproval && $deal['Deal']['deal_status_id'] != ConstDealStatus::Rejected && $deal['Deal']['deal_status_id'] != ConstDealStatus::Draft && $deal['Deal']['deal_status_id'] != ConstDealStatus::Upcoming) {?>
                                       <?php echo $this->Html->link(sprintf(__l('Quantity Sold  (%s)'),$this->Html->cInt($deal['Deal']['deal_user_count'], false)),array('controller'=>'deal_users', 'action'=>'index', 'deal_id'=>$deal['Deal']['id']), array('class' => 'edit js-edit coupon-sold', 'title' => __l('Quantity Sold')));?>
                                        <?php } ?>
                                      <?php echo $this->Html->link(__l('Edit'), array('controller' => 'deals', 'action'=>'edit', $deal['Deal']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?>
                                      <?php echo $this->Html->link(__l('Delete'), array('action'=>'delete', $deal['Deal']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?>
	  								  <?php if(empty($deal['Deal']['is_subdeal_available'])):?>
										<?php echo $this->Html->link(__l('Clone Deal'),array('controller'=>'deals', 'action'=>'add', 'clone_deal_id'=>$deal['Deal']['id']), array('class' => 'add', 'title' => __l('Clone Deal')));?>
									  <?php endif;?>
									  <?php if(!empty($deal['Deal']['deal_status_id']) && $deal['Deal']['deal_status_id'] != ConstDealStatus::PendingApproval && $deal['Deal']['deal_status_id'] != ConstDealStatus::Rejected && $deal['Deal']['deal_status_id'] != ConstDealStatus::Draft): 
									  echo $this->Html->link(__l('View Discussions'),array('controller'=>'topics', 'action'=>'index', 'deal_id'=>$deal['Deal']['id']), array('title' => __l('View Discussions'),'class' =>'view-icon'));
									  endif; ?>
                                    </div>
                                </div>
                          <?php endif; ?>
                        <?php echo $this->Html->cDateTimeHighlight($deal['Deal']['created']);?>
                     </td>                           
                      <td class="dl deal-name">
                      		 <?php echo $this->Html->showImage('Deal', (!empty($deal['Attachment'][0]) ? $deal['Attachment'][0] : ''), array('dimension' => 'medium_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($deal['Deal']['name'], false)), 'title' => $this->Html->cText($deal['Deal']['name'], false)));?>                      
							 <?php 
							 	if(isset($deal['City']) && !empty($deal['City'])):
								foreach($deal['City'] as $city_sub):
									$city_new_slug = $city_sub['slug'];
									break; // To show deal, in any one of the city (For now)
								endforeach;
								endif;
							?>
							<?php if (Cache::read('site.city_url', 'long') == 'prefix') { ?>
									<span><?php echo $this->Html->link($this->Html->cText($deal['Deal']['name']), array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug'], 'city' => (!empty($city_new_slug) ? $city_new_slug : ''), 'admin' => false), array('title'=>$this->Html->cText($deal['Deal']['name'],false),'escape' => false));?></span>									
                               <?php } elseif (Cache::read('site.city_url', 'long') == 'subdomain') {
                                    $subdomain = substr(env('HTTP_HOST'), 0, strpos(env('HTTP_HOST'), '.'));
                                    $sitedomain = substr(env('HTTP_HOST'), strpos(env('HTTP_HOST'), '.'));
                                    if (strlen($subdomain) > 0) {
                            ?>
                                        <a href="http://<?php echo $city_new_slug . $sitedomain.'deal/'.$deal['Deal']['slug']; ?>" title="<?php echo $deal['Deal']['name']; ?>"><?php echo $deal['Deal']['name']; ?></a>
                            <?php 
                                    } else {
                                        echo $this->Html->link($this->Html->cText($deal['Deal']['name']), array('controller' => 'deals', 'action' => 'view', $deal['Deal']['slug'], 'admin' => false), array('title'=>$this->Html->cText($deal['Deal']['name'],false),'escape' => false));
                                    }
                                }
                            ?>
                      
                      </td>
                      <td class="dl" <?php echo $rowspan; ?>>
                      <?php echo $this->Html->getUserAvatarLink($deal['User'], 'micro_thumb',false);?>
                      <?php echo $this->Html->getUserLink($deal['User']);?></td>
                      <td class="dl" <?php echo $rowspan; ?>>
						<?php echo $this->Html->link($deal['Company']['name'], array('controller' => 'deals', 'action'=>'index', 'company' => $deal['Company']['slug']),array('title' => sprintf(__l('%s'),$deal['Company']['name'])));?>
					  </td>
                      <td class="dl" <?php echo $rowspan; ?>>
					<?//php echo $this->Html->link($deal['City']['name'], array('controller' => 'deals', 'action'=>'index', 'city_slug' => $deal['City']['slug']),array('title' => sprintf(__l('%s'),$deal['City']['name'])));?>
					<?php
						$cities_list =array();
						if(isset($deal['City']) && !empty($deal['City'])):
						foreach($deal['City'] as $city_sub):
							$cities_list[] =  $this->Html->link($city_sub['name'], array('controller' => 'deals', 'action'=>'index', 'city_slug' => $city_sub['slug']),array('title' => sprintf(__l('%s'),$city_sub['name'])));
						endforeach;
						endif;
						echo implode(', ', $cities_list);
					?>
					  </td>
                      <td class="dl" <?php echo $rowspan; ?>><?php echo $this->Html->cBool($deal['Deal']['is_side_deal']);?></td>
					<?php if(!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'all')) { ?>
                        <td><?php echo $this->Html->cText($deal['DealStatus']['name']);?></td>
                      <?php } ?>
                      <td <?php echo $rowspan; ?>><?php echo $this->Html->cDateTime($deal['Deal']['start_date']);?></td>
                      <td <?php echo $rowspan; ?>><?php echo (!is_null($deal['Deal']['end_date']))? $this->Html->cDateTime($deal['Deal']['end_date']): ' - ';?></td>
                      <td><?php if(!$have_sub_deal) { echo $this->Html->cCurrency($deal['Deal']['original_price']); } ?></td>
                      <td><?php if(!$have_sub_deal) { echo $this->Html->cCurrency($deal['Deal']['discounted_price']); } ?></td>
                      <td><?php if(!$have_sub_deal) { echo $this->Html->cFloat($deal['Deal']['discount_percentage']); } ?></td>
                      <td class="dr"><?php if(!$have_sub_deal) { echo $this->Html->cCurrency($deal['Deal']['discount_amount']); } ?></td>
                      <td <?php echo $rowspan; ?>><?php echo $this->Html->cInt($deal['Deal']['min_limit']);?></td>
                      <td><?php echo $deal['Deal']['max_limit'] ? $this->Html->cInt($deal['Deal']['max_limit']) : __l('No Limit');?></td>
                      <td><?php echo $this->Html->link($this->Html->cInt($deal['Deal']['deal_user_count'], false),array('controller'=>'deal_users', 'action'=>'index', 'deal_id'=>$deal['Deal']['id']));?></td>
                      <td class="dr"><?php echo $this->Html->cCurrency($deal['Deal']['total_purchased_amount']);?></td>
                      <td class="dr"><?php if(!$have_sub_deal) { echo $this->Html->cCurrency($deal['Deal']['bonus_amount']); } ?></td>
                      <td><?php if(!$have_sub_deal) { echo $this->Html->cFloat($deal['Deal']['commission_percentage']); } ?></td>
                      <td class="dr"><?php echo $this->Html->cCurrency($deal['Deal']['total_commission_amount']);?></td>
                      <td <?php echo $rowspan; ?>><div class="js-truncate"><?php echo $this->Html->cText($deal['Deal']['private_note']); ?></div></td>
                    </tr>
                    <?php
							echo $add_row;
							$rowspan='';
							$add_row='';
                            endforeach;
                        else:
                            ?>
                    <tr>
                      <td colspan="12" class="notice"><?php echo __l('No Deals available');?></td>
                    </tr>
                    <?php
                        endif;
                        ?>
                  </table>
                  </div>
                  <?php if (!empty($deals)):?>
                      <div class="admin-select-block">
                      <?php
                      if(!empty($this->request->params['named']['filter_id'])) { ?>
                        <div>
                        	<?php if(!empty($moreActions)): ?>
								<?php echo __l('Select:'); ?>
                                <?php echo $this->Html->link(__l('All'), '#', array('class' => 'js-admin-select-all', 'title' => __l('All'))); ?>
                                <?php echo $this->Html->link(__l('None'), '#', array('class' => 'js-admin-select-none', 'title' => __l('None'))); ?>
                            <?php endif; ?>
                        </div>
                       <?php } ?>
                        <div class="admin-checkbox-button"><?php 
                            if(!empty($moreActions)):
                                echo $this->Form->input('more_action_id', array('class' => 'js-admin-index-autosubmit', 'label' => false, 'empty' => __l('-- More actions --')));
                            endif;
                             ?></div>
                        <div class="hide"> <?php echo $this->Form->submit(__l('Submit'));  ?> </div>
                      </div>
                      <div class="js-pagination"> <?php echo $this->element('paging_links'); ?> </div>
                  <?php endif; ?>
                  <?php echo $this->Form->end(); ?>
             </div>
    </div>
<?php endif; ?>
