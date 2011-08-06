<?php /* SVN: $Id: admin_edit.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<div class="topics form">
<?php echo $this->Form->create('Topic', array('class' => 'normal'));?>
    <h2><?php echo sprintf(__l('Edit Topic')); ?></h2>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('topic_type_id',array('label' => __l('Topic Type')));
		if($topic['Topic']['topic_type_id'] == ConstTopicType::DealTalk && !empty($topic['Deal']['name'])):
			echo $this->Form->input('Deal.name',array('label' => __l('Deal Name'), 'readonly' => true, 'value' => $topic['Deal']['name']));
		endif;
		echo $this->Form->input('name',array('label' => __l('Name')));
		echo $this->Form->input('content',array('label' => __l('Content')));
	?>
   <div class="submit-block clearfix">
            <?php
            	echo $this->Form->submit(__l('Update'));
            ?>
            </div>
        <?php
        	echo $this->Form->end(); ?>
</div>
