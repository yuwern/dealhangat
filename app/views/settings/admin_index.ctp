<h2><?php echo __l('Settings'); ?></h2>
<div class="js-accordion">
<?php
	foreach ($setting_categories as $setting_category):		
?>	<div>
		<h3><?php echo $this->Html->link($this->Html->cText($setting_category['SettingCategory']['name'], false), array('controller' => 'settings', 'action' => 'edit', $setting_category['SettingCategory']['id']), array('title' => $setting_category['SettingCategory']['name'], 'escape' => false)); ?></h3>
		<div></div>
	</div>
<?php
	endforeach;
?>
</div>