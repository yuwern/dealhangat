<li class="list-row clearfix">
	    <div class="avatar">
        	<?php echo $this->Html->getUserAvatarLink($topicDiscussion['User'], 'medium_thumb');?>
			<span class="comment-arrow">&nbsp;</span>
        </div>
		<div class="data round-5">
            <p> <?php echo $this->Html->getUserLink($topicDiscussion['User']).' commented '.$this->Time->timeAgoInWords($topicDiscussion['TopicDiscussion']['created']);?></p>
		  <?php echo $this->Html->cText($topicDiscussion['TopicDiscussion']['comment']);?>
		</div>
	</li>