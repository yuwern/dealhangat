<?php /* SVN: $Id: $ */ ?>
<div class="userEmployments index">
<h2><?php echo __l('Employments');?></h2>
<div class="add-block"><?php echo $this->Html->link(__l('Add'), array('controller' => 'user_employments', 'action' => 'add'), array('class' => 'add','title'=>__l('Add'))); ?></div>
<?php echo $this->element('paging_counter');?>
<table class="list">
    <tr>
        <th class="actions"><?php echo __l('Actions');?></th>
        <th><?php echo $this->Paginator->sort('employment');?></th>
        <th><?php echo $this->Paginator->sort(__l('Active?'),'is_active');?></th>
    </tr>
<?php
if (!empty($userEmployments)):

$i = 0;
foreach ($userEmployments as $userEmployment):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td class="actions"><span><?php echo $this->Html->link(__l('Edit'), array('action' => 'edit', $userEmployment['UserEmployment']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span> <span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $userEmployment['UserEmployment']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span></td>
		<td><?php echo $this->Html->cText($userEmployment['UserEmployment']['employment']);?></td>
		<td><?php echo $this->Html->cBool($userEmployment['UserEmployment']['is_active']);?></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="6" class="notice"><?php echo __l('No Employments available');?></td>
	</tr>
<?php
endif;
?>
</table>

<?php
if (!empty($userEmployments)) {
    echo $this->element('paging_links');
}
?>
</div>
