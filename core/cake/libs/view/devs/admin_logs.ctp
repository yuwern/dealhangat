<div class="users stats">
	<div>
		<h2><?php echo __l('Memory Status'); ?></h2>
		<dl class="list clearfix">
			<dt class="altrow"><?php echo __l('Used Cache Memory');?></dt>
			<dd class="altrow"><?php echo $tmpCacheFileSize; ?></dd>
			<dt><?php echo __l('Used Log Memory');?></dt>
			<dd><?php echo $tmpLogsFileSize; ?></dd>
		</dl>
	</div>
	<h2><?php echo __l('Recent Errors & Logs'); ?></h2>
	<div>
		<h3><?php echo __l('Error Log')?></h3>
		<?php echo $this->Html->link(__l('Clear Error Log'), array('controller' => 'devs', 'action' => 'clear_logs', 'type' => 'error')); ?>
		<div><textarea rows="15" cols="80"><?php echo !empty($error_log) ? $error_log : '';?></textarea></div>
		<h3><?php echo __l('Debug Log')?></h3>
		<?php echo $this->Html->link(__l('Clear Debug Log'), array('controller' => 'devs', 'action' => 'clear_logs', 'type' => 'debug')); ?>
		<div><textarea rows="15" cols="80"><?php echo !empty($debug_log) ? $debug_log : '';?></textarea></div>
		<h3><?php echo __l('Email Log')?></h3>
		<?php echo $this->Html->link(__l('Clear Email Log'), array('controller' => 'devs', 'action' => 'clear_logs', 'type' => 'email')); ?>
		<div><textarea rows="15" cols="80"><?php echo !empty($email_log) ? $email_log : '';?></textarea></div>
	</div>
</div>