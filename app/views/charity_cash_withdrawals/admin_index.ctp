<?php if(empty($this->request->params['isAjax']) && empty($this->request->params['named']['stat'])): ?>
    <h2><?php echo __l('Charity Cash Withdrawals'); ?></h2>
     <div class="add-block">
	      <?php echo $this->Html->link(__l('Add'),array('controller'=>'charity_cash_withdrawals','action'=>'add'),array('title' => __l('Add'),	'class' =>'add'));?>	
  </div>
	<div class="js-tabs">
		<ul class="clearfix">
            <li><?php echo $this->Html->link(sprintf(__l('Pending').' (%s)',$pending), array('controller' => 'charity_cash_withdrawals', 'action' => 'index', 'filter_id' => ConstCharityCashWithdrawalStatus::Pending), array('escape' => false, 'title' => __l('Pending'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Success').' (%s)',$success), array('controller' => 'charity_cash_withdrawals', 'action' => 'index', 'filter_id' => ConstCharityCashWithdrawalStatus::Success), array('escape' => false, 'title' => __l('Success'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Failed').' (%s)',$failed), array('controller' => 'charity_cash_withdrawals', 'action' => 'index', 'filter_id' => ConstCharityCashWithdrawalStatus::Failed), array('escape' => false, 'title' => __l('Failed'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Approved').' (%s)',$approved), array('controller' => 'charity_cash_withdrawals', 'action' => 'index', 'filter_id' => ConstCharityCashWithdrawalStatus::Approved), array('escape' => false, 'title' => __l('Approved'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Rejected').' (%s)',$rejected), array('controller' => 'charity_cash_withdrawals', 'action' => 'index', 'filter_id' => ConstCharityCashWithdrawalStatus::Rejected), array('escape' => false, 'title' => __l('Rejected'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('All').' (%s)',($approved + $pending + $rejected + $success + $failed)), array('controller' => 'charity_cash_withdrawals', 'action' => 'index', 'filter_id' => 'all'), array('escape' => false, 'title' => __l('All'))); ?></li>
        </ul>
        
    </div>
<?php else: ?>
    <div class="charityCashWithdrawals index js-response">
        <h2><?php echo $this->Html->link(__l('Charites'), array('controller' => 'charites', 'action' => 'index'), array('title' => __l('Back to Charities')));?> &raquo; <?php echo $pageTitle;?></h2>
    <?php echo $this->Form->create('CharityCashWithdrawal' , array('class' => 'normal','action' => 'update')); ?> <?php echo $this->Form->input('r', array('type' => 'hidden', 'value' => $this->request->url)); ?> <?php echo $this->element('paging_counter');?>
 <div class="overflow-block">
    <table class="list">
        <tr>
             <?php if (!empty($this->request->params['named']['filter_id'])  && $this->request->params['named']['filter_id'] == ConstCharityCashWithdrawalStatus::Pending):?>
            <th>
                  <?php echo __l('Select'); ?>
            </th>
            <?php endif; ?>
            <th class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Charity'),'Charity.name');?></div></th>
            <th class="dr"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Amount'), 'CharityCashWithdrawal.amount').' ('.Configure::read('site.currency').')';?> </div></th>
            <?php if(!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'all') { ?>
                <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Status'),'CharityCashWithdrawal.name');?></div></th>
            <?php } ?>
        </tr>
    <?php
    if (!empty($charityCashWithdrawals)):
    
    $i = 0;
    foreach ($charityCashWithdrawals as $charityCashWithdrawal):
        $class = null;
        if ($i++ % 2 == 0) {
            $class = ' class="altrow"';
        }
    ?>
        <tr<?php echo $class;?>>
            <?php if (!empty($this->request->params['named']['filter_id'])  && $this->request->params['named']['filter_id'] == ConstCharityCashWithdrawalStatus::Pending):?>
                <td>
					<span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $charityCashWithdrawal['CharityCashWithdrawal']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span>
					
                    <?php echo $this->Form->input('CharityCashWithdrawal.'.$charityCashWithdrawal['CharityCashWithdrawal']['id'].'.id', array('type' => 'checkbox', 'id' => "admin_checkbox_".$charityCashWithdrawal['CharityCashWithdrawal']['id'], 'label' => false, 'class' => 'js-checkbox-list ' )); ?>				
                </td>
            <?php endif; ?>
            <td class="dl">          
            <?php echo $this->Html->cText($charityCashWithdrawal['Charity']['name']);?></td>
            <td class="dr"><?php echo $this->Html->cCurrency($charityCashWithdrawal['CharityCashWithdrawal']['amount']);?></td>
            <?php if(!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == 'all') { ?>
                <td>
					<?php 
						if($charityCashWithdrawal['CharityCashWithdrawalStatus']['id'] == ConstCharityCashWithdrawalStatus::Pending):
							echo __l('Pending');
						elseif($charityCashWithdrawal['CharityCashWithdrawalStatus']['id'] == ConstCharityCashWithdrawalStatus::Failed):
							echo __l('Failed');
						elseif($charityCashWithdrawal['CharityCashWithdrawalStatus']['id'] == ConstCharityCashWithdrawalStatus::Success):
							echo __l('Success');
						else:
							echo $this->Html->cText($charityCashWithdrawal['CharityCashWithdrawalStatus']['name']);
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
	<?php if (!empty($charityCashWithdrawal) && !empty($this->request->params['named']['filter_id'])  && $this->request->params['named']['filter_id'] == ConstCharityCashWithdrawalStatus::Pending):?>
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