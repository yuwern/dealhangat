<?php /* SVN: $Id: add.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<div class="topicDiscussions form js-ajax-form-container">
<h2><?php echo __l('Comment'); ?></h2>



<?php echo $this->Form->create('TopicDiscussion', array('class' => "normal js-comment-form {container:'js-ajax-form-container',responsecontainer:'js-responses'}"));?>
	<fieldset>
	
	<div class="input text">
        <label><?php echo __l('Author'); ?></label>
        <div class="fromleft">
        <?php 
			$user_details = array(
				'username' => $user['User']['username'],
				'user_type_id' =>  $user['User']['user_type_id'],
				'id' =>  $user['User']['id'],
				'UserAvatar' => $user['UserAvatar']
			);
		echo $this->Html->getUserAvatarLink($user_details, 'micro_thumb').' ';
		echo (!empty($user['Company']['name'])) ? $user['Company']['name'] : $user['User']['username'];?>
        </div>
    </div>
	<?php
		echo $this->Form->input('topic_id', array('type' => 'hidden'));
		 if(empty($this->request->data['TopicDiscussion']['follow']))
            {
		      echo $this->Form->input('follow', array('type' => 'checkbox', 'label' => __l('Follow this topic by email')));
		    }
		echo $this->Form->input('comment',array('label' => __l('Comment')));
		
	?>
	</fieldset>
   <div class="submit-block clearfix">
            <?php
            	echo $this->Form->submit(__l('Post your comment'));
            ?>
            </div>
        <?php
        	echo $this->Form->end(); ?>
</div>
