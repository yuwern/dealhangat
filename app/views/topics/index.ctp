<?php /* SVN: $Id: index.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<?php if(empty($this->request->params['named']['type'])): ?>
	<div class="js-tabs">
        <ul class="clearfix">
                <li><?php echo $this->Html->link(__l('All Topics'), array('controller' => 'topics', 'action' => 'index', 'type' => 'all'), array('title' => __l('All Topics')));?></li>
                <?php foreach($topicTypes as $topicType){  ?>
                    <?php if($topicType['TopicType']['id'] == ConstTopicType::CityTalk) { ?>
                        <li><?php echo $this->Html->link(sprintf('%s '.__l('Talk'),ucfirst($city)), array('controller' => 'topics', 'action' => 'index', 'type' => $topicType['TopicType']['slug']), array('title' => sprintf('%s '.__l('Talk'),ucfirst($city))));?></li>
                    <?php } elseif($topicType['TopicType']['id'] == ConstTopicType::GlobalTalk) {?>
                      <li><?php echo $this->Html->link(sprintf('%s',Configure::read('site.name').' '.__l('Global')) , array('controller' => 'topics', 'action' => 'index', 'type' => $topicType['TopicType']['slug']), array('title' => sprintf('%s',Configure::read('site.name').' '.__l('Global'))));?></li>
                    <?php } else { ?>
                        <li><?php echo $this->Html->link(sprintf('%s',$topicType['TopicType']['name']), array('controller' => 'topics', 'action' => 'index', 'type' => $topicType['TopicType']['slug']), array('title' => sprintf('%s',$topicType['TopicType']['name'])));?></li>
                    <?php } ?>
                <?php }?>
        </ul>
    </div>
<?php else: ?>
    <div class="index js-response js-responses">
    <h2><?php echo $heading; ?></h2>
     <div class="add-block">
		<?php echo $this->Html->link(__l('Post New Topic'), array('controller' => 'topics', 'action' => 'add',$type), array('class' => 'add','title' => __l('Post New Topic'))); ?>
    </div>
    <?php echo $this->element('paging_counter');?>
    <table class="list topic-list">
        <tr>
            <th class="dl"><div class="js-pagination">
            <?php echo $this->Paginator->sort(__l('Topic'),'name');?></div></th>
			<?php if(!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'all')):?>
				<th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Category'),'topic_type_id');?></div></th>			
			<?php endif;?>
            <?php if(empty($this->request->params['named']['type'])) {?>
            <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Category'),'topic_type_id');?></div></th>
            <?php } ?>
            <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Replies'),'topic_discussion_count');?></div></th>
            <th class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Last Reply'),'last_replied_time');?></div></th>
        </tr>
    <?php
    if (!empty($topics)):
    $i = 0;
    foreach ($topics as $topic):
        $class = null;
        if ($i++ % 2 == 0) {
            $class = ' class="altrow"';
        }
    ?>
        <tr<?php echo $class;?>>
            <td class="dl"><h5>
            <?php if(!empty($topic['Topic']['deal_id']))
                {
                    echo $this->Html->link($this->Html->showImage('Deal', $topic['Deal']['Attachment'][0], array('dimension' => 'micro_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($topic['Deal']['name'], false)), 'title' => $this->Html->cText($topic['Deal']['name'], false))), array('controller' => 'deals', 'action' => 'view', $topic['Deal']['slug']), array('escape' => false, 'title' => $topic['Topic']['name']));
                }
              ?>
            <?php echo $this->Html->link($this->Html->cText($topic['Topic']['name']), array('controller' => 'topic_discussions', 'action' => 'index', $topic['Topic']['id']), array('escape' => false, 'title' => $topic['Topic']['name']));?></h5>
            <p><?php 
                if(!empty($topic['User']['username'])):
                    echo __l('Started by ').$this->Html->getUserAvatarLink($topic['User'], 'micro_thumb').' '.$this->Html->getUserLink($topic['User']).' '.__l(' on ').$this->Time->timeAgoInWords($topic['Topic']['created']);
                endif;
                    ?></p></td>
			<?php if(!empty($this->request->params['named']['type']) && ($this->request->params['named']['type'] == 'all')):?>
				<td class="dc"><?php
				if($topic['Topic']['city_id'] != 0 && $topic['Topic']['topic_type_id'] == ConstTopicType::CityTalk){
					echo $this->Html->cText($topic['City']['name']) . __l(' Talk');
				}else{
					echo $this->Html->cText($topic['TopicType']['name']);
				}?>
				</td>
			<?php endif;?>
			<?php if(empty($this->request->params['named']['type'])) {?>
            <td class="dc"><?php
            if($topic['Topic']['city_id'] != 0 && $topic['Topic']['topic_type_id'] == ConstTopicType::CityTalk){
                echo $this->Html->cText($topic['City']['name']) . __l(' Talk');
            }else{
                echo $this->Html->cText($topic['TopicType']['name']);
            }?></td>
            <?php } ?>
            <td class="dc"><?php
                if(!empty($topic['Topic']['topic_discussion_count']))
                {
                    $dicussion_count = $topic['Topic']['topic_discussion_count'];
                }
                else
                {
                    $dicussion_count = $topic['Topic']['topic_discussion_count'];
                }
                if($dicussion_count > 0)
                {
                    echo $this->Html->link($dicussion_count, array('controller' => 'topic_discussions', 'action' => 'index', $topic['Topic']['id']), array('escape' => false, 'title' => $topic['Topic']['name']));
                }
                else{
                    echo $this->Html->cInt($dicussion_count);
                }
                ?></td>
                    
            <td class="dl">
            	<p>

 					<?php if(!empty($topic['LastRepliedUser']['username'])):?>
						<?php 
							$username = (!empty($topic['LastRepliedUser']['Company']['name'])) ? $this->Html->cText($topic['LastRepliedUser']['Company']['name']) : $this->Html->cText($topic['LastRepliedUser']['username']);
                        ?>
                        <p>
                        <?php
						    echo $this->Html->getUserAvatarLink($topic['LastRepliedUser'], 'small_thumb').' '.$username;
						 ?>
						 <p>
						 <?php
							echo __l('on').' '.$this->Time->timeAgoInWords($topic['Topic']['last_replied_time']);
						 ?>
						 </p>
                    <?php else: ?>
                        <p><?php echo __l('N/A'); ?></p>
                    <?php endif; ?>
                </p>
            </td>
        </tr>
    <?php
        endforeach;
    else:
    ?>
        <tr>
            <td colspan="12" class="notice"><?php echo __l('No Topics available');?></td>
        </tr>
    <?php
    endif;
    ?>
    </table>
    
    <?php
    if (!empty($topics)) {
		?>
        <div class="js-pagination">
            <?php echo $this->element('paging_links'); ?>
        </div>
        <?php
    }
    ?>
    </div>
<?php endif; ?>