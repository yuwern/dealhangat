<?php /* SVN: $Id: admin_index.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
	<?php 
		if(!empty($this->request->params['isAjax'])):
			echo $this->element('flash_message');
		endif;
	?>
<?php if(empty($this->request->params['isAjax']) && empty($this->request->params['named']['stat'])): ?>
	<div class="js-tabs">
		<ul class="clearfix">
            <li><?php echo $this->Html->link(sprintf(__l('Pending').' (%s)',$pending), array('controller' => 'user_cash_withdrawals', 'action' => 'index', 'filter_id' => ConstWithdrawalStatus::Pending), array('escape' => false, 'title' => __l('Pending'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Approved').' (%s)',$approved), array('controller' => 'user_cash_withdrawals', 'action' => 'index', 'filter_id' => ConstWithdrawalStatus::Approved), array('escape' => false, 'title' => __l('Approved'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Rejected').' (%s)',$rejected), array('controller' => 'user_cash_withdrawals', 'action' => 'index', 'filter_id' => ConstWithdrawalStatus::Rejected), array('escape' => false, 'title' => __l('Rejected'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Success').' (%s)',$success), array('controller' => 'user_cash_withdrawals', 'action' => 'index', 'filter_id' => ConstWithdrawalStatus::Success), array('escape' => false, 'title' => __l('Success'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Failed').' (%s)',$failed), array('controller' => 'user_cash_withdrawals', 'action' => 'index', 'filter_id' => ConstWithdrawalStatus::Failed), array('escape' => false, 'title' => __l('Failed'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('All').' (%s)',($approved + $pending + $rejected + $success + $failed)), array('controller' => 'user_cash_withdrawals', 'action' => 'index', 'filter_id' => 'all'), array('escape' => false, 'title' => __l('All'))); ?></li>
        </ul>
    </div>
<?php else: ?>
    <div class="userCashWithdrawals index js-response">
    <h2><?php echo $pageTitle;?></h2>
    <?php echo $this->Form->create('UserCashWithdrawal' , array('class' => 'normal','action' => 'update')); ?> <?php echo $this->Form->input('r', array('type' => 'hidden', 'value' => $this->request->url)); ?> <?php echo $this->element('paging_counter');?>
    <table class="list">
        <tr>
		    <?php if (!empty($userCashWithdrawals) && (empty($this->request->params['named']['filter_id']) || (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] != ConstWithdrawalStatus::Approved && $this->request->params['named']['filter_id'] != ConstWithdrawalStatus::Success && $this->request->params['named']['filter_id'] != ConstWithdrawalStatus::Rejected))):?>
            <th>
                  <?php echo __l('Select'); ?>
            </th>
			<?php endif;?>
            <th class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('User'),'User.username');?></div></th>
            <th class="dr"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Amount'), 'UserCashWithdrawal.amount').' ('.Configure::read('site.currency').')';?> </div></th>
            <?php if(empty($this->request->params['named']['filter_id'])) { ?>
                <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Status'),'WithdrawalStatus.name');?></div></th>
            <?php } ?>
        </tr>
    <?php
    if (!empty($userCashWithdrawals)):
    
    $i = 0;
    foreach ($userCashWithdrawals as $userCashWithdrawal):
        $class = null;
        if ($i++ % 2 == 0) {
            $class = ' class="altrow"';
        }
    ?>
        <tr<?php echo $class;?>>
		    <?php if (!empty($userCashWithdrawals) && (empty($this->request->params['named']['filter_id']) || (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] != ConstWithdrawalStatus::Approved && $this->request->params['named']['filter_id'] != ConstWithdrawalStatus::Success && $this->request->params['named']['filter_id'] != ConstWithdrawalStatus::Rejected))):?>
			<td>
				<div class="actions-block">
					<div class="actions round-5-left">
						<span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $userCashWithdrawal['UserCashWithdrawal']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span>
					</div>
				</div>
                <?php echo $this->Form->input('UserCashWithdrawal.'.$userCashWithdrawal['UserCashWithdrawal']['id'].'.id', array('type' => 'checkbox', 'id' => "admin_checkbox_".$userCashWithdrawal['UserCashWithdrawal']['id'], 'label' => false, 'class' => 'js-checkbox-list ' )); ?>
			</td>
			<?php endif;?>
            <td class="dl">
		    <?php if (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] == ConstWithdrawalStatus::Success || $this->request->params['named']['filter_id'] == ConstWithdrawalStatus::Rejected):?>
				<div class="actions-block">
					<div class="actions round-5-left">
						<span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $userCashWithdrawal['UserCashWithdrawal']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span>
					</div>
				</div>
			<?php endif;?>
			<?php echo $this->Html->getUserAvatarLink($userCashWithdrawal['User'], 'micro_thumb',false);	?>
            <?php echo $this->Html->getUserLink($userCashWithdrawal['User']);?></td>
            <td class="dr"><?php echo $this->Html->cCurrency($userCashWithdrawal['UserCashWithdrawal']['amount']);?></td>
            <?php if(empty($this->request->params['named']['filter_id'])) { ?>
                <td><?php echo $this->Html->cText($userCashWithdrawal['WithdrawalStatus']['name']);?></td>
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
    <?php if (!empty($userCashWithdrawals) && (empty($this->request->params['named']['filter_id']) || (!empty($this->request->params['named']['filter_id']) && $this->request->params['named']['filter_id'] != ConstWithdrawalStatus::Approved && $this->request->params['named']['filter_id'] != ConstWithdrawalStatus::Success && $this->request->params['named']['filter_id'] != ConstWithdrawalStatus::Rejected))):?>
		<div class="admin-select-block">
			<div>
				<?php echo __l('Select:'); ?>
				<?php echo $this->Html->link(__l('All'), '#', array('class' => 'js-admin-select-all', 'title' => __l('All'))); ?>
				<?php echo $this->Html->link(__l('None'), '#', array('class' => 'js-admin-select-none', 'title' => __l('None'))); ?>
			</div>
			<div class="admin-checkbox-button"><?php echo $this->Form->input('more_action_id', array('class' => 'js-admin-index-autosubmit', 'label' => false, 'empty' => __l('-- More actions --'))); ?></div>
		</div>
		<div class="hide"> <?php echo $this->Form->submit('Submit');  ?> </div>
      <?php endif; ?>
			
    <?php
    if (!empty($userCashWithdrawals)) {
        ?>
            <div class="js-pagination">
                <?php echo $this->element('paging_links'); ?>
            </div>
        <?php
    }
    ?>
      <?php echo $this->Form->end(); ?>
    </div>
<?php endif; ?>