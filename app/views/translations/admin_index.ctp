<?php /* SVN: $Id: admin_index.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<div class="translations index">
<h2><?php echo __l('Translations');?></h2>
<div class="add-block">
	<?php echo $this->Html->link(__l('Make New Translation'), array('controller' => 'translations', 'action' => 'add'), array('class' => 'add', 'title'=>__l('Make New Translation'))); ?>
	<?php echo $this->Html->link(__l('Add New Text'), array('controller' => 'translations', 'action' => 'add_text'), array('class' => 'add', 'title'=>__l('Add New Text'))); ?>
</div>
<div class = "notice">
	<?php echo __l('To make new translation default translated language English should be available.');?>
</div>
<h3><?php echo __l('Available Translations');?></h3>
<table class="list">
    <tr>
		<th><?php echo __l('Language');?></th>
		<th><?php echo __l('Percentage');?></th>
		<th><?php echo __l('Verified');?></th>
		<th><?php echo __l('Not Verified');?></th>
		<th class="actions"><?php echo __l('Manage');?></th>
    </tr>
<?php
if (!empty($translations)):

$i = 0;
foreach ($translations as $language_id => $translation):
	$class = null;
	if ($i++ % 2 == 0):
		$class = ' class="altrow"';
    endif;
?>
	<tr<?php echo $class;?>>
		<td><?php echo $this->Html->cText($translation['name']);?></td>
		<td>Verified: <?php $total = $translation['verified'] + $translation['not_verified'];
			echo round($translation['verified']*100/$total, 3)."% <br>Not Verified: ";
			echo round($translation['not_verified']*100/$total, 3)."% "; ?></td>
		<td><?php 
			if($translation['verified']){
				echo $this->Html->link($translation['verified'], array('action' => 'manage', 'filter' => 'verified', 'language_id' => $language_id));
			} else {
				echo $this->Html->cText($translation['verified']);
			}
			;?></td>
		<td><?php 
			if($translation['not_verified']){
				echo $this->Html->link($translation['not_verified'], array('action' => 'manage', 'filter' => 'unverified', 'language_id' => $language_id));
			} else {
				echo $this->Html->cText($translation['not_verified']);
			}
			;?></td>
		<td class="actions">
			<span><?php echo $this->Html->link(__l('Manage'), array('action' => 'manage', 'language_id' => $language_id), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span>
			<?php if($language_id != '42'):?>
				<span><?php echo $this->Html->link(__l('Delete'), array('action' => 'index', 'remove_language_id' => $language_id), array('class' => 'delete js-delete', 'title' => __l('Delete Translation')));?></span>
			<?php endif;?>
		</td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="7" class="notice"><?php echo __l('No Translations available');?></td>
	</tr>
<?php
endif;
?>
</table>
</div>