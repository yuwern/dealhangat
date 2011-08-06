 <li class="list-row clearfix" id="comment-<?php echo $userComment['UserComment']['id']; ?>" >
	    <div class="avatar">
			<?php echo $this->Html->getUserAvatarLink($userComment['PostedUser'], 'medium_thumb');?>        
			<span class="comment-arrow"></span>
        </div>
		<div class="data round-5">
            <p> <?php echo $this->Html->getUserLink($userComment['PostedUser']).' commented '.$this->Time->timeAgoInWords($userComment['UserComment']['created']);?></p>
		  <?php echo $this->Html->cText(nl2br($userComment['UserComment']['comment']));?>
		  <?php if ($userComment['UserComment']['posted_user_id'] == $this->Auth->user('id')) { ?>
        <div class="actions">
        	<?php echo $this->Html->link(__l('Delete'), array('controller' => 'user_comments', 'action' => 'delete', $userComment['UserComment']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?>
		</div>
		<?php } ?>
		</div>
	</li>
