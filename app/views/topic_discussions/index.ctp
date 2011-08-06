<?php /* SVN: $Id: index.ctp 54451 2011-05-24 12:26:17Z arovindhan_144at11 $ */ ?>
<div class="topicDiscussions index">
<h2><?php echo $this->Html->cText($pageTitle);?></h2>
<div class="add-block">
<?php if(!empty($follow_topic_id)){ ?>
    <?php echo $this->Html->link(__l('Unfollow Topics'), array('controller' => 'topics_users', 'action'=>'delete', $follow_topic_id), array('class' => 'add', 'title' => __l('UnFollow')));?>
 <?php }else{ ?>
    <?php echo $this->Html->link(__l('Follow  Topics'), array('controller' => 'topics_users', 'action'=>'add',$this->request->data['TopicDiscussion']['topic_id']), array('class' => 'add', 'title' => __l('Follow')));?>
<?php } ?>
</div>
<?php echo $this->element('paging_counter');?>
<ol class="commment-list clearfix js-comment-responses" >
<?php
if (!empty($topicDiscussions)):

$i = 0;
foreach ($topicDiscussions as $topicDiscussion):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = 'altrow';
	}
?>
	<li class="list-row clearfix <?php echo $class;?>">
	    <div class="avatar">
        	<?php echo $this->Html->getUserAvatarLink($topicDiscussion['User'], 'medium_thumb');?>
			<span class="comment-arrow">&nbsp;</span>
        </div>
		<div class="data round-5">
            <p> <?php echo $this->Html->getUserLink($topicDiscussion['User']).' commented '.$this->Time->timeAgoInWords(_formatDate($topicDiscussion['TopicDiscussion']['created']));?></p>
		  <?php echo $this->Html->cText($topicDiscussion['TopicDiscussion']['comment']);?>
		</div>
	</li>
<?php
    endforeach;
else:
?>
	<li>
        	<p class="notice"><?php echo __l('No Topic Discussions available');?></p>
	</li>
<?php
endif;
?>
</ol>

<?php
if (!empty($topicDiscussions)) {
    echo $this->element('paging_links');
}
?>
<div>
        <?php
			if($this->Auth->user('id')):
				echo $this->element('../topic_discussions/add', array('cache' => array('config' => 'site_element_cache')));
			endif;		
		 ?>
</div>
</div>