<?php /* SVN: $Id: admin_index.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
	<?php 
		if(!empty($this->request->params['isAjax'])):
			echo $this->element('flash_message');
		endif;
	?>
<div class="userComments index js-responses">
<h2><?php echo __l('User Comments');?></h2>
    <?php echo $this->Form->create('UserComment' , array('class' => 'normal js-ajax-form','action' => 'update')); ?>
    <?php echo $this->Form->input('r', array('type' => 'hidden', 'value' => $this->request->url)); ?>
<?php echo $this->element('paging_counter');?>
<table class="list">
	<tr>
		<th><?php echo __l('Select'); ?></th>
		<th><?php echo __l('User'); ?></th>
		<th><?php echo __l('Commented User'); ?></th>
		<th><?php echo __l('Comments'); ?></th>
		<th><?php echo __l('Date'); ?></th>
	</tr>
<?php
if (!empty($userComments)):

$i = 0;
foreach ($userComments as $userComment):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr <?php echo $class;?>>
		<td>
		<div class="actions-block">
		<div class="actions round-5-left"><span><?php echo $this->Html->link(__l('Edit'), array('action'=>'edit', $userComment['UserComment']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span><span><?php echo $this->Html->link(__l('Delete'), array('action'=>'delete', $userComment['UserComment']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span>
		<span>
		<?php 
			if(!empty($userComment['UserComment']['ip'])):
				echo $this->Html->link(__l('Ban User IP'), array('controller'=> 'banned_ips', 'action' => 'add', $userComment['UserComment']['ip']), array('class' => 'network-ip','title'=>__l('Ban User IP'), 'escape' => false));
			endif;
			
			?>
		</span>
		</div>
		</div>
		<?php echo $this->Form->input('UserComment.' . $userComment['UserComment']['id'] . '.id', array('type' => 'checkbox', 'id' => 'admin_checkbox_' . $userComment['UserComment']['id'], 'class' => 'js-checkbox-list', 'label' => false)); ?>		
		</td>
		<td>
		<?php echo $this->Html->getUserLink($userComment['User']);?></td>
		<td>
			<?php echo $this->Html->getUserLink($userComment['PostedUser']);?>
        </td>
		<td class="dl"><?php echo $this->Html->cText($userComment['UserComment']['comment']);?></td>
		<td><?php echo $this->Html->cDateTime($userComment['UserComment']['created']);?></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td class="notice" colspan="9"><?php echo __l('No User Comments available');?></td>
	</tr>
<?php endif; ?>
</table>
<?php
if (!empty($userComments)) { ?>
          <div class="admin-select-block">
            <div>
                <?php echo __l('Select:'); ?>
                <?php echo $this->Html->link(__l('All'), '#', array('class' => 'js-admin-select-all', 'title' => __l('All'))); ?>
                <?php echo $this->Html->link(__l('None'), '#', array('class' => 'js-admin-select-none', 'title' => __l('None'))); ?>
            </div>
            <div class="admin-checkbox-button"><?php echo $this->Form->input('more_action_id', array('options' => $moreActions, 'class' => 'js-admin-index-autosubmit', 'label' => false, 'empty' => __l('-- More actions --'))); ?></div>
        </div>
        <div class="hide">
            <?php echo $this->Form->submit('Submit'); ?>
        </div>
 <?php   echo $this->element('paging_links'); }?>
<?php echo $this->Form->end(); ?>
</div>
