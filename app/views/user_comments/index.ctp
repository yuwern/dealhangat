<?php /* SVN: $Id: index.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<div class="userComments index js-response">
<h2>
<?php
if (!empty($username)):
    echo __l('Comments on ').$username;
else:
	echo __l('Comments');
endif;
?>
</h2>
<?php echo $this->element('paging_counter'); ?>
<ol class="commment-list clearfix js-comment-responses" start="<?php echo $this->Paginator->counter(array('format' => '%start%')); ?>">
<?php
if (!empty($userComments)):
    foreach($userComments as $userComment):
?>
    <li class="list-row clearfix <?php echo $class;?>" id="comment-<?php echo $userComment['UserComment']['id']; ?>" >
	    <div class="avatar">
			<?php echo $this->Html->getUserAvatarLink($userComment['PostedUser'], 'medium_thumb');?>                    
			<span class="comment-arrow"></span>
        </div>
		<div class="data round-5">
            <p> <?php echo $this->Html->getUserLink($userComment['PostedUser']).' commented '.$this->Time->timeAgoInWords($userComment['UserComment']['created']);?></p>
		  <?php echo $this->Html->cText(nl2br($userComment['UserComment']['comment']));?>
		  <?php if ($user['User']['id'] == $this->Auth->user('id') or $userComment['PostedUser']['id'] == $this->Auth->user('id')) { ?>
        <div class="actions">
        	<?php echo $this->Html->link(__l('Delete'), array('controller' => 'user_comments', 'action' => 'delete', $userComment['UserComment']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?>
		</div>
		<?php } ?>
		</div>
	</li>
<?php
    endforeach;
else:
?>
	<li>
		<p  class="notice"><?php echo __l('No comments available'); ?></p>
	</li>
<?php
endif;
?>
</ol>
<div class="js-pagination">
<?php
if (!empty($userComments)) {
    echo $this->element('paging_links');
}
?>
</div>
</div>