<?php /* SVN: $Id: $ */ ?>
<?php 
	if(!empty($this->request->params['isAjax'])):
		echo $this->element('flash_message');
	endif;
?>
<div class="charities index js-response">
<h2><?php echo __l('Charities');?></h2>
<?php
	echo $this->Form->create('Charity' , array('action' => 'admin_index', 'type' => 'get', 'class' => 'normal search-form clearfix ')); //js-ajax-form
	echo $this->Form->input('Charity.q', array('label' => __l('Keyword')));
	echo $this->Form->submit(__l('Search'));
	echo $this->Form->end(); 
?>
 <div class="add-block">
	<?php echo $this->Html->link(__l('Add'),array('controller'=>'charities','action'=>'add'),array('title' => __l('Add'),	'class' =>'add'));?>	
    <?php echo $this->Html->link(__l('Charity Cash Withdrawal'),array('controller'=>'charity_cash_withdrawals','action'=>'index'),array('class' => 'widthdraw', 'title' => __l('Charity Cash Withdrawal')));?>
</div>
<?php echo $this->element('paging_counter');?>
<?php echo $this->Form->create('Charity' , array('class' => 'normal','action' => 'update')); ?>
<?php echo $this->Form->input('r', array('type' => 'hidden', 'value' => $this->request->url)); ?>
<div class="overflow-block">
<table class="list">
    <tr>
        <th><?php echo __l('Select');?></th>
        <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Name'),'name');?></div></th>
		<th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Category'),'CharityCategory.name');?></div></th>
		<th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Paypal Email'),'paypal_email');?></div></th>
        <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Received Amount'),'paid_amount');?></div></th>
		<th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Description'),'description');?></div></th>
        <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('URL'),'url');?></div></th>
		<th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Total Site Amount'),'total_site_amount');?></div></th>
		<th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Total Seller Amount'),'total_seller_amount');?></div></th>		
		<th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Total Amount'),'total_amount');?></div></th>
		<th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Available Amount'),'available_amount');?></div></th>
		<th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Paid Amount'),'paid_amount');?></div></th>		
		<th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Active?'),'is_active');?></div></th>
    </tr>
<?php
if (!empty($charities)):

$i = 0;
foreach ($charities as $charity):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
	if($charity['Charity']['is_active']):
		$status_class= 'js-checkbox-active';
	else:
		$status_class= 'js-checkbox-inactive';
	endif;
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $this->Form->input('Charity.'.$charity['Charity']['id'].'.id', array('type' => 'checkbox', 'id' => "admin_checkbox_".$charity['Charity']['id'], 'class' => $status_class.' js-checkbox-list', 'label' => false)); ?>
			<div class="actions-block">
				<div class="actions round-5-left">
					<span><?php echo $this->Html->link(__l('Edit'), array('action' => 'edit', $charity['Charity']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span> <span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $charity['Charity']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span>
				</div>
			</div>
		</td>
		<td>
       <?php echo $this->Html->cText($charity['Charity']['name']);?>
		</td>
		<td><?php echo $this->Html->cText($charity['CharityCategory']['name']);?></td>
		<td><?php echo $this->Html->cText($charity['Charity']['paypal_email']);?></td>
		<td><?php echo $this->Html->cCurrency($charity['Charity']['paid_amount']);?></td>
		<td><?php echo $this->Html->cText($charity['Charity']['description']);?></td>
		<td><?php if(!empty($charity['Charity']['url'])): ?><?php echo $this->Html->link($this->Html->cText($charity['Charity']['url'], false), $charity['Charity']['url'] ,array('target' => '_blank'));?> <?php endif; ?></td>
		<td><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($charity['Charity']['total_site_amount'])); ?></td>
		<td><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($charity['Charity']['total_seller_amount'])); ?></td>
		<td><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($charity['Charity']['total_amount'])); ?></td>
		<td><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($charity['Charity']['available_amount'])); ?></td>
		<td><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($charity['Charity']['paid_amount'])); ?></td>
		<td><span class="round-5 bool-<?php echo $charity['Charity']['is_active']; ?>"><?php echo $this->Html->cBool($charity['Charity']['is_active']);?></span></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="7" class="notice"><?php echo __l('No Charities available');?></td>
	</tr>
<?php
endif;
?>
</table>
</div>
	<?php if (!empty($charities)) {?>
		<div class="admin-select-block">
			<div>
				<?php echo __l('Select:'); ?>
				<?php echo $this->Html->link(__l('All'), '#', array('class' => 'js-admin-select-all','title' => __l('All'))); ?>
				<?php echo $this->Html->link(__l('None'), '#', array('class' => 'js-admin-select-none','title' => __l('None'))); ?>
				<?php echo $this->Html->link(__l('Active'), '#', array('class' => 'js-admin-select-approved','title' => __l('Active'))); ?>
				<?php echo $this->Html->link(__l('Inactive'), '#', array('class' => 'js-admin-select-pending','title' => __l('Inactive'))); ?>
			</div>
			<div class="admin-checkbox-button">
				<?php echo $this->Form->input('more_action_id', array('options' => $moreActions, 'class' => 'js-admin-index-autosubmit', 'label' => false, 'empty' => __l('-- More actions --'))); ?>
			</div>
		</div>
		<div class="js-pagination">
			<?php echo $this->element('paging_links');?>
		</div>
		<div class="hide">
			<?php echo $this->Form->submit(__l('Submit'));  ?>
		</div>
		<?php echo $this->Form->end(); ?>
	<?php }?>
</div>
