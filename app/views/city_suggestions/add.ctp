<?php /* SVN: $Id: add.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<div class="citySuggestions form">
<h2><?php echo __l('City not listed? No problem!');?></h2>
<?php echo $this->Form->create('CitySuggestion', array('class' => 'normal'));?>
	<fieldset>
	<?php
		echo $this->Form->input('email',array('label' => __l('Email')));
		echo $this->Form->input('name',array('label' =>__l('City Name')));
	?>
	</fieldset>
    <div class="submit-block clearfix">
    <?php echo $this->Form->submit(__l('Suggest a city'));?>
    </div>
    <?php echo $this->Form->end();?>
</div>