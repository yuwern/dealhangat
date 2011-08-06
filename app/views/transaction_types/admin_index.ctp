<?php /* SVN: $Id: $ */ ?>
<?php 
	if(!empty($this->request->params['isAjax'])):
		echo $this->element('flash_message');
	endif;
?>
<div class="transactionTypes index">
<h2><?php echo __l('Transaction Types');?></h2>
<?php echo $this->element('paging_counter');?>
<table class="list">
    <tr> 
        <th><?php echo $this->Paginator->sort(__l('Name'),'name');?></th>
        <th><?php echo $this->Paginator->sort(__l('Message'),'message');?></th>
        <th><?php echo $this->Paginator->sort(__l('Credit'),'is_credit');?></th>
     </tr>
<?php
if (!empty($transactionTypes)):

$i = 0;
foreach ($transactionTypes as $transactionType):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td class="dl">
		<div class="actions-block">
                       <div class="actions round-5-left">
                        	<span><?php echo $this->Html->link(__l('Edit'), array('action' => 'edit', $transactionType['TransactionType']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span>
                       </div>
             </div>
           <?php echo $this->Html->cText($transactionType['TransactionType']['name']);?></td>
          <td class="dl"><?php echo $this->Html->cText($transactionType['TransactionType']['message']);?></td>
		<td><?php echo $this->Html->cBool($transactionType['TransactionType']['is_credit']);?></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="7" class="notice"><?php echo __l('No Transaction Types available');?></td>
	</tr>
<?php
endif;
?>
</table>

<?php
if (!empty($transactionTypes)) {
    echo $this->element('paging_links');
}
?>
</div>
