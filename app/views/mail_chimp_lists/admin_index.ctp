<?php /* SVN: $Id: $ */ ?>
<div class="mailChimpLists index">
<h2><?php echo __l('Mail Chimp Lists');?></h2>
<div class="add-block"><?php echo $this->Html->link(__l('Add'), array('action' => 'add'), array('class' => 'add','title'=>__l('Add'))); ?></div>
<?php echo $this->element('paging_counter');?>
<table class="list">
    <tr>
        <th class="actions"><?php echo __l('Actions');?></th>
        <th><?php echo $this->Paginator->sort('created');?></th>
        <th><?php echo $this->Paginator->sort('city_id');?></th>
        <th><?php echo $this->Paginator->sort('list_id');?></th>
    </tr>
<?php
if (!empty($mailChimpLists)):

$i = 0;
foreach ($mailChimpLists as $mailChimpList):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td class="actions"><span><?php echo $this->Html->link(__l('Edit'), array('action' => 'edit', $mailChimpList['MailChimpList']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span> <span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $mailChimpList['MailChimpList']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span></td>
		<td><?php echo $this->Html->cDateTime($mailChimpList['MailChimpList']['created']);?></td>
		<td><?php echo $this->Html->cText($mailChimpList['City']['name']);?></td>
		<td><?php echo $this->Html->cText($mailChimpList['MailChimpList']['list_id']);?></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="6" class="notice"><?php echo __l('No Mail Chimp Lists available');?></td>
	</tr>
<?php
endif;
?>
</table>

<?php
if (!empty($mailChimpLists)) {
    echo $this->element('paging_links');
}
?>
</div>
