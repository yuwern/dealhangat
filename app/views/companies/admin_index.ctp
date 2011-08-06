<?php /* SVN: $Id: admin_index.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<?php 
	if(!empty($this->request->params['isAjax'])):
		echo $this->element('flash_message');
	endif;
?>
<?php if(empty($this->request->params['isAjax']) && empty($this->request->params['named']['stat']) && empty($this->request->params['named']['type'])): ?>
		<div class="js-tabs">
			<ul class="clearfix">
					<li><?php echo $this->Html->link(sprintf(__l('Online Companies (%s)'),$online), array('controller' => 'companies', 'action' => 'index', 'main_filter_id' => ConstMoreAction::Online),array('title' => __l('Online Companies')));?></li>
					<li><?php echo $this->Html->link(sprintf(__l('Offline Companies (%s)'),$offline), array('controller' => 'companies', 'action' => 'index', 'main_filter_id' => ConstMoreAction::Offline),array('title' => __l('Offline Companies'))); ?></li>
					<li><?php echo $this->Html->link(sprintf(__l('All Companies (%s)'),$all),array('controller'=> 'companies', 'action'=>'index', 'main_filter_id' => 'all'),array('title' => __l('All Companies'))); ?></li>
				</ul>
		</div>
<?php else: ?>
	<div class="companies index js-response js-responses">
		<?php if(!empty($this->request->params['named']['main_filter_id']) && $this->request->params['named']['main_filter_id'] != ConstMoreAction::Online && $this->request->params['named']['main_filter_id'] !=ConstMoreAction::Offline && empty($this->request->params['named']['filter_id'])): ?>
			 <div class="js-tabs">      
				<ul class="clearfix">    
					<li><?php echo $this->Html->link(sprintf(__l('Active Companies(%s)'),$active), array('controller' => 'companies', 'action' => 'index', 'filter_id' => ConstMoreAction::Active,'main_filter_id' => $this->request->params['named']['main_filter_id']),array('title' => sprintf(__l('Active Companies(%s)'),$active)));?></li>
					<li><?php echo $this->Html->link(sprintf(__l('Inactive Companies(%s)'),$inactive), array('controller' => 'companies', 'action' => 'index', 'filter_id' => ConstMoreAction::Inactive, 'main_filter_id' => $this->request->params['named']['main_filter_id']),array('title' => sprintf(__l('Inactive Companies(%s)'),$inactive))); ?></li>
					<li><?php echo $this->Html->link(sprintf(__l('All(%s)'),$active + $inactive),array('controller'=> 'companies', 'action'=>'index', 'filter_id' => 'all','main_filter_id' => $this->request->params['named']['main_filter_id']),array('title' => sprintf(__l('All(%s)'),$active + $inactive))); ?></li>
				</ul>
			 </div>
		<?php else: ?>
				<div class="js-search-responses">
					<h2><?php echo $pageTitle; ?></h2>
					<div class="search-from">
						<?php echo $this->Form->create('Company', array('type' => 'post', 'class' => 'normal search-form clearfix js-ajax-form {"container" : "js-search-responses"}', 'action'=>'index')); ?>
							<?php echo $this->Form->input('q', array('label' => __l('Keyword')));?>
							<?php echo $this->Form->input('main_filter_id', array('type' => 'hidden', 'value' => !empty($this->request->params['named']['main_filter_id'])? $this->request->params['named']['main_filter_id']:'')); ?>
							<?php echo $this->Form->input('filter_id', array('type' => 'hidden', 'value' => !empty($this->request->params['named']['filter_id'])?$this->request->params['named']['filter_id']:'')); ?>
							
							<?php echo $this->Form->submit(__l('Search'),array('name' => 'data[Company][search]'));?>
							
						<?php echo $this->Form->end(); ?>
					 </div>   
					<div class="clearfix add-block1">
						<?php echo $this->Html->link(__l('Add'), array('controller' => 'companies', 'action' => 'add'), array('class' => 'add','title'=>__l('Add'))); ?>
                    <?php
							echo $this->Html->link(__l('CSV'), array_merge(array('controller' => 'companies', 'action' => 'index','city' => $city_slug, 'ext' => 'csv', 'admin' => true), $this->request->params['named']), array('title' => __l('CSV'), 'class' => 'export'));
						?>
                	</div>
						<div class="company-list">
						
						<?php   echo $this->Form->create('Company' , array('class' => 'normal js-ajax-form {"container" : "js-search-responses"}','action' => 'update'));?>
						<?php echo $this->Form->input('r', array('type' => 'hidden', 'value' => $this->request->url)); ?>
						<?php echo $this->element('paging_counter');?>
						<div class="overflow-block">
						<table class="list">
							<tr>
								<th class="actions"><?php echo __l('Select');?></th>
								 <th class="dl "><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Name'), 'Company.name');?></div></th>
								<th class="dl"><?php echo __l('Address');?></th>
								<th class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Email'), 'User.email');?></div></th>
								<th class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('User'), 'User.username');?></div></th>
								<th class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('URL'), 'Company.url');?></div></th>
								<?php if(!empty($this->request->params['named']['main_filter_id']) && $this->request->params['named']['main_filter_id'] != ConstMoreAction::Offline) { ?>
									<th><?php echo $this->Paginator->sort(__l('Profile Enabled'), 'Company.is_company_profile_enabled');?></th>
								<?php } ?>
								<th class="dr"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Available Balance Amount'), 'User.available_balance_amount').' ('.Configure::read('site.currency').')'; ?></div></th>
							</tr>
						<?php
						if (!empty($companies)):
						$i = 0;
						foreach ($companies as $company):
							$class = null;
							if ($i++ % 2 == 0) {
								$class = ' class="altrow"';
							}
							if($company['Company']['is_company_profile_enabled']):
								$status_class = 'js-checkbox-active';
							else:
								$status_class = 'js-checkbox-inactive';
							endif;
						?>
							<tr<?php echo $class;?>>
								<td class="actions">
								<div class="actions-block">
									<div class="actions round-5-left">
										<span><?php echo $this->Html->link(__l('Edit'), array('action' => 'edit', $company['Company']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span> 
										<span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $company['Company']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span>
										<?php if(!empty($this->request->params['named']['main_filter_id']) && $this->request->params['named']['main_filter_id'] != ConstMoreAction::Offline) { ?>
											<?php 
												if(Configure::read('user.is_email_verification_for_register') and (!$company['User']['is_active'] or !$company['User']['is_email_confirmed'])):
												  echo $this->Html->link(__l('Resend Activation'), array('controller' => 'users', 'action'=>'resend_activation', $company['User']['id'],'type' => 'company', 'admin' => false),array('title' => __l('Resend Activation'),'class' =>'recent-activation'));
												endif;
											?>
										<span><?php echo $this->Html->link(__l('Change Password'), array('controller' => 'users', 'action'=>'admin_change_password', $company['User']['id']), array('title' => __l('Change Password'),'class' => 'password'));?></span>
										<?php }?>
										<span><?php echo $this->Html->link(__l('Transactions'), array('controller' => 'transactions', 'action'=>'admin_index','user_id' => $company['User']['id']), array('title' => __l('Transactions'),'class' => 'transaction'));?></span>
									</div>
								</div>
								<?php echo $this->Form->input('Company.'.$company['Company']['id'].'.id', array('type' => 'checkbox', 'id' => "admin_checkbox_".$company['Company']['id'], 'label' => false, 'class' => $status_class.' js-checkbox-list')); ?>
								</td>
							
								<td class="dl">
								<?php
									if($company['Company']['is_online_account'] and $company['Company']['is_company_profile_enabled']):
										echo $this->Html->link($company['Company']['name'], array('controller' => 'companies', 'action'=>'view', $company['Company']['slug'], 'admin' => false),array('title' => sprintf(__l('%s'),$company['Company']['name'])));
									else:
										echo $company['Company']['name'];
									endif;
									
								
								?></td>
								<td class="dl">
								<div class="address-block-info">
									<?php if (!empty($company['Company']['address1'])): ?><p><?php echo $this->Html->cText($company['Company']['address1']);?></p><?php endif; ?>
									<?php if (!empty($company['Company']['address2'])): ?><p><?php echo $this->Html->cText($company['Company']['address2']);?></p><?php endif; ?>
									<?php if (!empty($company['City']['name'])): ?><p><?php echo $this->Html->cText($company['City']['name']);?></p><?php endif; ?>
									<?php if (!empty($company['State']['name'])): ?><p><?php echo $this->Html->cText($company['State']['name']);?></p><?php endif; ?>
									<?php if (!empty($company['Country']['name'])): ?><p><?php echo $this->Html->cText($company['Country']['name']);?></p><?php endif; ?>
									<?php if (!empty($company['Company']['zip'])): ?><p><?php echo $this->Html->cText($company['Company']['zip']);?></p><?php endif; ?>
									<?php if (!empty($company['Company']['phone'])): ?><p><span><?php echo __l('Ph: ') ?></span><?php echo $this->Html->cText($company['Company']['phone']);?></p><?php endif; ?>
								</div>
								</td>
								<td class="dl"><?php echo $this->Html->cText($company['User']['email']);?></td>
								<td class="dl">
							   <?php  echo $this->Html->showImage('UserAvatar', $company['User']['UserAvatar'], array('dimension' => 'micro_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($company['User']['username'], false)), 'title' => $this->Html->cText($company['Company']['name'], false)));?>
								<?php echo $company['User']['username'];?></td>
								<td class="dl"><a href="<?php echo $company['Company']['url']; ?>" target="_blank"><?php echo $this->Html->cText($company['Company']['url']);?></a></td>
								<?php if(!empty($this->request->params['named']['main_filter_id']) && $this->request->params['named']['main_filter_id'] != ConstMoreAction::Offline) { ?>
									<td><?php echo $this->Html->cBool($company['Company']['is_company_profile_enabled']);?></td>
								<?php } ?>
								<td class="dr"><?php echo $this->Html->cCurrency($company['User']['available_balance_amount']);?></td>
							</tr>
						<?php
							endforeach;
						else:
						?>
							<tr>
								<td colspan="21" class="notice"><?php echo __l('No Companies available');?></td>
							</tr>
						<?php
						endif;
						?>
						</table>
						</div>
					
						<?php
						if (!empty($companies)):
						?>
						
							<div class="admin-select-block">
							<div>
								<?php echo __l('Select:'); ?>
								<?php echo $this->Html->link(__l('All'), '#', array('class' => 'js-admin-select-all', 'title' => __l('All'))); ?>
								<?php echo $this->Html->link(__l('None'), '#', array('class' => 'js-admin-select-none', 'title' => __l('None'))); ?>
								<?php echo $this->Html->link(__l('Disabled'), '#', array('class' => 'js-admin-select-pending', 'title' => __l('Disabled'))); ?>
								<?php echo $this->Html->link(__l('Enabled'), '#', array('class' => 'js-admin-select-approved', 'title' => __l('Enabled'))); ?>
							</div>
								<div class="admin-checkbox-button"><?php echo $this->Form->input('more_action_id', array('class' => 'js-admin-index-autosubmit', 'label' => false, 'empty' => __l('-- More actions --'))); ?></div>
								</div>
							<div class="js-pagination">
								<?php echo $this->element('paging_links'); ?>
							</div>
						
							<div class="hide">
								<?php echo $this->Form->submit('Submit'); ?>
							</div>
						<?php
						endif;
						echo $this->Form->end();?>
					</div>
			</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
