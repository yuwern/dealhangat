<?php /* SVN: $Id: admin_add.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<div class="translations form">
<h2><?php echo __l('Add New Translation');?></h2>
<?php echo $this->Form->create('Translation', array('class' => 'normal'));?>
	<fieldset>
 		<h3><?php echo $this->Html->link(__l('Translations'), array('action' => 'index'));?> &raquo; <?php echo __l('Add New Translation');?></h3>
	<?php
		echo $this->Form->input('from_language', array('value' => __l('English'), 'disabled' => true));
		echo $this->Form->input('language_id', array('label' => __l('To Language')));?>
        <div class="submit-block clearfix">
        <?php
		echo $this->Form->submit('Manual Translate', array('name' => 'data[Translation][manualTranslate]'));
	?>
	</div>
    <div class="notice">
	<p><?php echo __l('Manual Translate: It will only populate site labels for selected new language. You need to manually enter all the equivalent translated label');?>
</div>
        <div class="submit-block clearfix">
		<?php
		echo $this->Form->submit('Google Translate', array('name' => 'data[Translation][googleTranslate]'));
	?>
	</div>
    <div class="notice">
	<p><?php echo __l('Google Translate: It will automatically translate site labels into selected language with Google');?>
</div>
	</fieldset>
<?php echo $this->Form->end();?>
</div>
