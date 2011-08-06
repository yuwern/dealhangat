<?php /* SVN: $Id: admin_edit.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<div class="topicDiscussions form">
<?php echo $this->Form->create('TopicDiscussion', array('class' => 'normal'));?>
    <h2><?php echo sprintf(__l('Edit Topic Discussion')); ?></h2>
    <h3><?php echo __l('Topic: ').$this->Html->cText($topic['Topic']['name']); ?></h3>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('comment',array('label' => __l('Comment')));
	?>
   <div class="submit-block clearfix">
            <?php
            	echo $this->Form->submit(__l('Update'));
            ?>
            </div>
        <?php
        	echo $this->Form->end(); ?>
</div>
