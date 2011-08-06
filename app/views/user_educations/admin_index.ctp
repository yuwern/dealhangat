<?php /* SVN: $Id: $ */ ?>
<div class="userEducations index">
<h2><?php echo __l('Educations');?></h2>
<div class="add-block"><?php echo $this->Html->link(__l('Add'), array('controller' => 'user_educations', 'action' => 'add'), array('class' => 'add','title'=>__l('Add'))); ?></div>
<?php echo $this->element('paging_counter');?>
<table class="list">
    <tr>
        <th class="actions"><?php echo __l('Actions');?></th>
        <th><?php echo $this->Paginator->sort('education');?></th>
        <th><?php echo $this->Paginator->sort(__l('Active?'),'is_active');?></th>
    </tr>
<?php
if (!empty($userEducations)):

$i = 0;
foreach ($userEducations as $userEducation):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td class="actions"><span><?php echo $this->Html->link(__l('Edit'), array('action' => 'edit', $userEducation['UserEducation']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span> <span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $userEducation['UserEducation']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span></td>
		<td><?php echo $this->Html->cText($userEducation['UserEducation']['education']);?></td>
		<td><?php echo $this->Html->cBool($userEducation['UserEducation']['is_active']);?></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="6" class="notice"><?php echo __l('No Educations available');?></td>
	</tr>
<?php
endif;
?>
</table>

<?php
if (!empty($userEducations)) {
    echo $this->element('paging_links');
}
?>
</div>
