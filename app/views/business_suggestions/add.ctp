<?php /* SVN: $Id: add.ctp 6686 2010-06-03 05:54:02Z sreedevi_140ac10 $ */ ?>
<div class="businessSuggestions form">
<h2><?php echo __l('Suggest a Business');?></h2>
<?php echo $this->Form->create('BusinessSuggestion', array('class' => 'normal'));?>
	<fieldset>
	<?php
		echo $this->Form->input('email',array('label' => __l('Email')));
		echo $this->Form->input('suggestion',array('label' =>__l('Suggestion')));
	?>
	</fieldset>
    <div class="submit-block clearfix">
        <?php
        	echo $this->Form->submit(__l('Suggest'));
        ?>
    </div>
        <?php
        	echo $this->Form->end();
        ?>

</div>