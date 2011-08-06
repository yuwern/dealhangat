<?php /* SVN: $Id: add.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<div class="js-tabs">
        <ul class="clearfix">
				 <li><?php echo $this->Html->link(__l('Add Topic'), '#js-add-from', array('title' => __l('Add Topic')));?></li>
                <li><?php echo $this->Html->link(__l('All Topics'), array('controller' => 'topics', 'action' => 'index', 'type' => 'all'), array('title' => __l('All Topics')));?></li>
                <?php foreach($topicTypes as $topicType){ ?>
                    <?php if($topicType['TopicType']['id'] == ConstTopicType::CityTalk) { ?>
                        <li><?php echo $this->Html->link(sprintf(__l('%s'), ucfirst($this->request->params['named']['city']).' Talk') , array('controller' => 'topics', 'action' => 'index', 'type' => $topicType['TopicType']['slug']), array('title' => sprintf(__l('%s Talk'),$this->request->params['named']['city'])));?></li>
                    <?php } elseif($topicType['TopicType']['id'] == ConstTopicType::GlobalTalk) {?>
                      <li><?php echo $this->Html->link(sprintf(__l('%s Global '),Configure::read('site.name')) , array('controller' => 'topics', 'action' => 'index', 'type' => $topicType['TopicType']['slug']), array('title' => sprintf(__l('%s'),$topicType['TopicType']['name'])));?></li>
                    <?php } else { ?>
                        <li><?php echo $this->Html->link(sprintf(__l('%s'),$topicType['TopicType']['name']), array('controller' => 'topics', 'action' => 'index', 'type' => $topicType['TopicType']['slug']), array('title' => sprintf(__l('%s'),$topicType['TopicType']['name'])));?></li>
                    <?php } ?>
                <?php }?>
        </ul>
	<div class="topics form" id = "js-add-from">
	<?php echo $this->Form->create('Topic', array('class' => 'normal'));?>
		<fieldset>
			<h2><?php 
				echo __l('Add Topic');
				if(!empty($topic_type) and $topic_type != 'all')
				{
                    if($topicTypes['1']['TopicType']['slug'] == $topic_type)
                    {
                        echo ' - '.sprintf(__l('%s Talk'), $this->request->params['named']['city']);
                    }elseif($topicTypes['2']['TopicType']['slug'] == $topic_type){
                         echo ' - '.sprintf(__l('%s Global - Talk'), Configure::read('site.name'));
                    }else {
					   echo ' - '.ucfirst($topic_type);
					}
				}
			
			?></h2>
			<div class="input text">
				<label><?php echo __l('Author'); ?></label>
				<div class="fromleft">
				<?php 
					echo $this->Html->getUserAvatarLink($user['User'], 'micro_thumb').' ';
					echo (!empty($user['Company']['name'])) ? $user['Company']['name'] : $user['User']['username'];?>
				</div>
			</div>
			<div>
				<?php
					echo $this->Form->input('name', array('label' => __l('Topic')));
					if(!empty($topic_type) && $topic_type != 'all'){
					  echo $this->Form->input('topic_type_id',array('type' => 'hidden','readonly' => 'readonly',  'options' => $topicsTypes));
					}else{
						echo $this->Form->input('topic_type_id',array('label' => __l('Forum'),'empty' => __l('Please Select'), 'options' => $topicsTypes));
					}
					echo $this->Form->input('content', array('label' => __l('Post'),'type' => 'textarea'));
					echo $this->Form->input('follow', array('type' => 'checkbox', 'label' => __l('Follow this topic by email')));
				?>
			</div>
		</fieldset>
	
	  <div class="submit-block clearfix">
        <?php
        	echo $this->Form->submit(__l('Post Your Comment'));
        ?>
        </div>
        <?php
        	echo $this->Form->end();
        ?>

	</div>
</div>
