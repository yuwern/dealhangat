<?php /* SVN: $Id: edit.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<div class="topics form">
<?php echo $this->Form->create('Topic', array('class' => 'normal'));?>
	<fieldset>
 		<h2><?php echo __l('Edit Topic');?></h2>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('topic_type_id',array('label' => __l('Topic Type')));
		echo $this->Form->input('city_id',array('label' => __l('City')));
		echo $this->Form->input('deal_id',array('label' => __l('Deal')));
		echo $this->Form->input('name',array('label' => __l('Name')));
		echo $this->Form->input('content',array('label' => __l('Content')));		
	?>
	</fieldset>
<?php echo $this->Form->end(__l('Update'));?>
</div>
