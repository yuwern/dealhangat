<?php /* SVN: $Id: admin_index.ctp 2077 2010-04-20 10:42:36Z josephine_065at09 $ */ ?>
<?php if(empty($this->request->params['isAjax']) && empty($this->request->params['named']['stat'])): ?>
	<div class="js-tabs">
		<ul class="clearfix">
            <li><?php echo $this->Html->link(sprintf(__l('Pending').' (%s)',$pending), array('controller' => 'affiliate_cash_withdrawals', 'action' => 'index', 'filter_id' => ConstAffiliateCashWithdrawalStatus::Pending), array('escape' => false, 'title' => __l('Pending'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Success').' (%s)',$success), array('controller' => 'affiliate_cash_withdrawals', 'action' => 'index', 'filter_id' => ConstAffiliateCashWithdrawalStatus::Success), array('escape' => false, 'title' => __l('Success'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Failed').' (%s)',$failed), array('controller' => 'affiliate_cash_withdrawals', 'action' => 'index', 'filter_id' => ConstAffiliateCashWithdrawalStatus::Failed), array('escape' => false, 'title' => __l('Failed'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Approved').' (%s)',$approved), array('controller' => 'affiliate_cash_withdrawals', 'action' => 'index', 'filter_id' => ConstAffiliateCashWithdrawalStatus::Approved), array('escape' => false, 'title' => __l('Approved'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Rejected').' (%s)',$rejected), array('controller' => 'affiliate_cash_withdrawals', 'action' => 'index', 'filter_id' => ConstAffiliateCashWithdrawalStatus::Rejected), array('escape' => false, 'title' => __l('Rejected'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('All').' (%s)',($approved + $pending + $rejected + $success + $failed)), array('controller' => 'affiliate_cash_withdrawals', 'action' => 'index', 'filter_id' => 'all'), array('escape' => false, 'title' => __l('All'))); ?></li>
        </ul>
    </div>
<?php else: ?>
    <div class="affiliateCashWithdrawals index js-response">
        <h2><?php echo $this->Html->link(__l('Affiliates'), array('controller' => 'affiliates', 'action' => 'index'), array('title' => __l('Back to Affiliates')));?> &raquo; <?php echo $pageTitle;?></h2>
    <?php echo $this->Form->create('AffiliateCashWithdrawal' , array('class' => 'normal','action' => 'update')); ?> <?php echo $this->Form->input('r', array('type' => 'hidden', 'value' => $this->request->url)); ?> <?php echo $this->element('paging_counter');?>
 <div class="overflow-block">
    <table class="list">
        <tr>
            <?php if (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] != ConstAffiliateCashWithdrawalStatus::Approved && $this->request->params['named']['filter_id'] != ConstAffiliateCashWithdrawalStatus::Success && $this->request->params['named']['filter_id'] != ConstAffiliateCashWithdrawalStatus::Rejected && $this->request->params['named']['filter_id'] != 'all'):?>
            <th>
                  <?php echo __l('Select'); ?>
            </th>
            <?php endif; ?>
            <th class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('User'),'User.username');?></div></th>
            <th class="dr"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Amount'), 'AffiliateCashWithdrawal.amount').' ('.Configure::read('site.currency').')';?> </div></th>
            <?php if(!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'all') { ?>
                <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Status'),'AffiliateCashWithdrawal.name');?></div></th>
            <?php } ?>
        </tr>
    <?php
    if (!empty($affiliateCashWithdrawals)):
    
    $i = 0;
    foreach ($affiliateCashWithdrawals as $affiliateCashWithdrawal):
        $class = null;
        if ($i++ % 2 == 0) {
            $class = ' class="altrow"';
        }
    ?>
        <tr<?php echo $class;?>>
            <?php if (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] != ConstAffiliateCashWithdrawalStatus::Approved && $this->request->params['named']['filter_id'] != ConstAffiliateCashWithdrawalStatus::Success && $this->request->params['named']['filter_id'] != ConstAffiliateCashWithdrawalStatus::Rejected && $this->request->params['named']['filter_id'] != 'all'):?>
                <td>
					<span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $affiliateCashWithdrawal['AffiliateCashWithdrawal']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span>
					
                    <?php echo $this->Form->input('AffiliateCashWithdrawal.'.$affiliateCashWithdrawal['AffiliateCashWithdrawal']['id'].'.id', array('type' => 'checkbox', 'id' => "admin_checkbox_".$affiliateCashWithdrawal['AffiliateCashWithdrawal']['id'], 'label' => false, 'class' => 'js-checkbox-list ' )); ?>				
                </td>
            <?php endif; ?>
            <td class="dl">
            <?php echo $this->Html->showImage('UserAvatar', $affiliateCashWithdrawal['User']['UserAvatar'], array('dimension' => 'micro_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($affiliateCashWithdrawal['User']['username'], false)), 'title' => $this->Html->cText($affiliateCashWithdrawal['User']['username'], false)));?>
            <?php echo $this->Html->link($this->Html->cText($affiliateCashWithdrawal['User']['username']), array('controller'=> 'users', 'action'=>'view', $affiliateCashWithdrawal['User']['username'],'admin' => false), array('title'=>$this->Html->cText($affiliateCashWithdrawal['User']['username'],false),'escape' => false));?></td>
            <td class="dr"><?php echo $this->Html->cCurrency($affiliateCashWithdrawal['AffiliateCashWithdrawal']['amount']);?></td>
            <?php if(!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'all') { ?>
                <td>
					<?php 
						if($affiliateCashWithdrawal['AffiliateCashWithdrawalStatus']['id'] == ConstAffiliateCashWithdrawalStatus::Pending):
							echo __l('Pending');
						elseif($affiliateCashWithdrawal['AffiliateCashWithdrawalStatus']['id'] == ConstAffiliateCashWithdrawalStatus::Approved):
							echo __l('Approved');
						elseif($affiliateCashWithdrawal['AffiliateCashWithdrawalStatus']['id'] == ConstAffiliateCashWithdrawalStatus::Rejected):
							echo __l('Rejected');
						elseif($affiliateCashWithdrawal['AffiliateCashWithdrawalStatus']['id'] == ConstAffiliateCashWithdrawalStatus::Failed):
							echo __l('Failed');
						elseif($affiliateCashWithdrawal['AffiliateCashWithdrawalStatus']['id'] == ConstAffiliateCashWithdrawalStatus::Success):
							echo __l('Success');
						else:
							echo $this->Html->cText($affiliateCashWithdrawal['AffiliateCashWithdrawalStatus']['name']);
						endif;
					?>
				</td>
            <?php } ?>
        </tr>
    <?php
        endforeach;
    else:
    ?>
        <tr>
            <td colspan="8" class="notice"><?php echo __l('No records available');?></td>
        </tr>
    <?php
    endif;
    ?>
    </table>
    </div>
    <?php if (!empty($affiliateCashWithdrawals) && (empty($this->request->params['named']['filter_id']) || (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] != ConstAffiliateCashWithdrawalStatus::Approved && $this->request->params['named']['filter_id'] != ConstAffiliateCashWithdrawalStatus::Success && $this->request->params['named']['filter_id'] != ConstAffiliateCashWithdrawalStatus::Rejected && $this->request->params['named']['filter_id'] != 'all'))):?>
      <div class="admin-select-block">
        <div>
            <?php echo __l('Select:'); ?>
            <?php echo $this->Html->link(__l('All'), '#', array('class' => 'js-admin-select-all', 'title' => __l('All'))); ?>
            <?php echo $this->Html->link(__l('None'), '#', array('class' => 'js-admin-select-none', 'title' => __l('None'))); ?>
        </div>
        <div class="admin-checkbox-button"><?php echo $this->Form->input('more_action_id', array('class' => 'js-admin-index-autosubmit', 'label' => false, 'empty' => __l('-- More actions --'))); ?></div>
     
      </div>
         <div class="hide"> <?php echo $this->Form->submit('Submit');  ?> </div>
      <div class="js-pagination"> <?php echo $this->element('paging_links'); ?> </div>
      <?php endif; ?>
      <?php echo $this->Form->end(); ?>
    </div>
<?php endif; ?>