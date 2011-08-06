<?php /* SVN: $Id: index.ctp 54451 2011-05-24 12:26:17Z arovindhan_144at11 $ */ ?>
<div class="userCashWithdrawals index js-response js-withdrawal_responses js-responses">
<h2><?php echo __l('Withdraw Fund Request');?></h2>
<?php if(!empty($userProfile['UserProfile']['paypal_account'])) : ?>
        <?php echo $this->element('../user_cash_withdrawals/add', array('cache' => array('config' => 'site_element_cache'))); ?>
<?php else:?>
	<div class="page-info">
	<?php
	if($this->Auth->user('user_type_id') != ConstUserTypes::Company):
		echo $this->Html->link(__l('Your PayPal account is empty, so click here to update PayPal account in your profile .'), array('controller' => 'users', 'action'=>'my_stuff#My_Account'), array('title' => __l('Edit paypal account')));
	else:
		$company = $this->Html->getCompany($this->Auth->user('id'));
		echo $this->Html->link(__l('Your PayPal account is empty, so click here to update PayPal account in your profile .'), array('controller' => 'companies', 'action'=>'edit', $company['Company']['id']), array('title' => __l('Edit paypal account')));
	endif;
	?>
	</div>
<?php endif;?>

<?php
?>
<?php echo $this->element('paging_counter');?>
<table class="list">
    <tr>
		<th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Requested On'), 'UserCashWithdrawal.created');?></div></th>
        <th class="dr"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Amount').' ('.Configure::read('site.currency').')', 'UserCashWithdrawal.amount');?></div></th>
        <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Status'),'WithdrawalStatus.name');?></div></th>
    </tr>
<?php
if (!empty($userCashWithdrawals)):
$i = 0;
foreach ($userCashWithdrawals as $userCashWithdrawal):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td><?php echo $this->Html->cDateTime($userCashWithdrawal['UserCashWithdrawal']['created']);?></td>
    	<td class="dr"><?php echo $this->Html->cCurrency($userCashWithdrawal['UserCashWithdrawal']['amount']);?></td>
		<td><?php echo $this->Html->cText($userCashWithdrawal['WithdrawalStatus']['name']);?></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="8" class="notice"><?php echo __l('No withdraw fund requests available');?></td>
	</tr>
<?php
endif;
?>
</table>

<?php if (!empty($userCashWithdrawals)):?>
	<div class="js-pagination">
		<?php
			echo $this->element('paging_links');
		?>
	</div>
<?php endif;?>
</div>