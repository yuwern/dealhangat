<?php /* SVN: $Id: $ */ ?>
<div class="blockedUsers index">
	<?php 
		if(!empty($this->request->params['isAjax'])):
			echo $this->element('flash_message');
		endif;
	?>
<h2><?php echo __l('Blocked Users');?></h2>
<?php echo $this->Form->create('BlockedUser', array('type' => 'get', 'class' => 'normal', 'action'=>'index')); ?>
	<div class="filter-section">
		<div>
			<?php echo $this->Form->input('user_id',array('label' => __l('User'),'empty' => __l('Please Select'))); ?>
			<?php echo $this->Form->input('blocked_user_id',array('label' => __l('Blocked User'),'empty' => __l('Please Select'))); ?>
            <?php echo $this->Form->input('q', array('label' =>__l('Keyword'))); ?>
        </div>
		<div>
			<?php echo $this->Form->submit(__l('Search'));?>
		</div>
	</div>
<?php echo $this->Form->end(); ?>
<?php 
	echo $this->Form->create('BlockedUser' , array('class' => 'normal','action' => 'update'));
	echo $this->Form->input('r', array('type' => 'hidden', 'value' => $this->request->url));
	
?>
<?php echo $this->element('paging_counter');?>
<table class="list">
    <tr>
    	<th><?php echo __l('Select'); ?></th>
        <th class="actions"><?php echo __l('Actions');?></th>
        <th><?php echo $this->Paginator->sort('user_id');?></th>
        <th><?php echo $this->Paginator->sort('blocked_user_id');?></th>
    </tr>
<?php
if (!empty($blockUsers)):

$i = 0;
foreach ($blockUsers as $blockedUser):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
    	<td><?php echo $this->Form->input('BlockedUser.'.$blockedUser['BlockedUser']['id'].'.id', array('type' => 'checkbox', 'id' => "admin_checkbox_".$blockedUser['BlockedUser']['id'], 'label' => false, 'class' => ' js-checkbox-list')); ?></td>
		<td class="actions"> <span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $blockedUser['BlockedUser']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span></td>
		<td><?php echo $this->Html->getUserLink($blockedUser['User']);?></td>
		<td><?php echo $this->Html->getUserLink($blockedUser['Blocked']);?></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="6" class="notice"><?php echo __l('No Blocked Users available');?></td>
	</tr>
<?php
endif;
?>
</table>
<?php
if (!empty($blockUsers)):
?>
	<div>
		<?php echo __l('Select:'); ?>
		<?php echo $this->Html->link(__l('All'), '#', array('class' => 'js-admin-select-all', 'title' => __l('All'))); ?>
		<?php echo $this->Html->link(__l('None'), '#', array('class' => 'js-admin-select-none', 'title' => __l('None'))); ?>
	</div>
	<div class="js-pagination">
        <?php echo $this->element('paging_links'); ?>
    </div>
	<div class="admin-checkbox-button"><?php echo $this->Form->input('more_action_id', array('class' => 'js-admin-index-autosubmit', 'label' => false, 'empty' => __l('-- More actions --'))); ?></div>
    <div class=hide>
	    <?php echo $this->Form->submit('Submit'); ?>
    </div>
<?php
endif;
echo $this->Form->end();
?>

</div>
