<?php /* SVN: $Id: admin_index.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
	<?php 
		if(!empty($this->request->params['isAjax'])):
			echo $this->element('flash_message');
		endif;
	?>
<div class="topicDiscussions index">
<h2><?php
if (!empty($this->request->params['named']['topic_id'])) {
        echo sprintf(__l('Topic - %s'), $this->Html->cText($topic['Topic']['name'], false));
    }else{
        echo __l('Topic Discussions');
    }?></h2>
<?php   echo $this->Form->create('TopicDiscussion' , array('class' => 'normal','action' => 'update')); ?>
<?php echo $this->Form->input('r', array('type' => 'hidden', 'value' => $this->request->url)); ?>
<?php echo $this->element('paging_counter');?>
<div class="overflow-block">
<table class="list">
    <tr>
        <th><?php echo __l('Select'); ?></th>
        <th class="dl"><?php echo $this->Paginator->sort(__l('Name'), 'Topic.name');?></th>
        <th class="dl"><?php echo $this->Paginator->sort(__l('User'), 'User.username');?></th>
        <th class="dl"><?php echo $this->Paginator->sort(__l('Comments'), 'TopicDiscussion.comment');?></th>
        <th><?php echo $this->Paginator->sort(__l('IP'), 'TopicDiscussion.ip');?></th>
        <th><?php echo $this->Paginator->sort(__l('Discussed On'), 'TopicDiscussion.created');?></th>
    </tr>
<?php
if (!empty($topicDiscussions)):

$i = 0;
foreach ($topicDiscussions as $topicDiscussion):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
        <td>
			<div class="actions-block">
				<div class="actions round-5-left">
					<span><?php echo $this->Html->link(__l('Edit'), array('action' => 'edit', $topicDiscussion['TopicDiscussion']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span> 
					<span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $topicDiscussion['TopicDiscussion']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span>
					<span>
					<?php echo $this->Html->link(__l('Ban User IP'), array('controller'=> 'banned_ips', 'action' => 'add', $topicDiscussion['TopicDiscussion']['ip']), array('class' => 'network-ip','title'=>__l('Ban User IP'), 'escape' => false));?>
					</span>

				</div>
			</div>
		<?php echo $this->Form->input('TopicDiscussion.'.$topicDiscussion['TopicDiscussion']['id'].'.id', array('type' => 'checkbox', 'id' => "admin_checkbox_".$topicDiscussion['TopicDiscussion']['id'], 'label' => false, 'class' =>' js-checkbox-list')); ?></td>
		<td class="dl"><?php echo $this->Html->link($this->Html->cText($topicDiscussion['Topic']['name']), array('controller'=> 'topic_discussions', 'action'=>'index', $topicDiscussion['Topic']['id'],'admin' => false), array('title'=>$this->Html->cText($topicDiscussion['Topic']['name'],false),'escape' => false));?></td>
		<td class="dl">
		<?php echo $this->Html->getUserAvatarLink($topicDiscussion['User'], 'micro_thumb',false);	?>
        <?php echo $this->Html->getUserLink($topicDiscussion['User']);?></td>
		<td class="dl"><div class="js-truncate"><?php echo $this->Html->cText($topicDiscussion['TopicDiscussion']['comment']);?></div></td>
		<td>
		<?php echo $this->Html->cText($topicDiscussion['TopicDiscussion']['ip']);?>		
        <?php echo ' ['.$topicDiscussion['TopicDiscussion']['dns'].']' . '('. $this->Html->link(__l('whois'), array('controller' => 'users', 'action' => 'whois', $topicDiscussion['TopicDiscussion']['ip'], 'admin' => false), array('target' => '_blank', 'title' => __l('whois'), 'escape' => false)) .')';?></td>
		<td><?php echo $this->Html->cDateTime($topicDiscussion['TopicDiscussion']['created']);?></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="8" class="notice"><?php echo __l('No Topic Discussions available');?></td>
	</tr>
<?php
endif;
?>
</table>
</div>
<?php
if (!empty($topicDiscussions)) { ?>
          <div class="admin-select-block">
            <div>
                <?php echo __l('Select:'); ?>
                <?php echo $this->Html->link(__l('All'), '#', array('class' => 'js-admin-select-all', 'title' => __l('All'))); ?>
                <?php echo $this->Html->link(__l('None'), '#', array('class' => 'js-admin-select-none', 'title' => __l('None'))); ?>
            </div>
            <div class="admin-checkbox-button"><?php echo $this->Form->input('more_action_id', array('class' => 'js-admin-index-autosubmit', 'label' => false, 'empty' => __l('-- More actions --'))); ?></div>
        </div>
        <div class="hide">
            <?php echo $this->Form->submit('Submit'); ?>
        </div>
 <?php   echo $this->element('paging_links'); }?>
<?php echo $this->Form->end(); ?>
</div>
