<?php /* SVN: $Id: admin_index.ctp 5508 2010-05-25 11:48:42Z senthilkumar_017ac09 $ */ ?>
<div class="userPaymentProfiles index clearfix  js-responses js-response">
<?php 
	if(!empty($this->request->params['isAjax'])):
		echo $this->element('flash_message');
	endif;
?>
    <h2><?php echo __l('Credit Cards');?></h2>
	<div class="clearfix add-block1">
		<?php echo $this->Html->link(__l('Add'), array('controller' => 'user_payment_profiles', 'action' => 'add'), array('class' => "js-toggle-show add {'container':'js-credit-card-form'}", 'title' => __l('Add'))); ?>
	</div>
	<div class="js-credit-card-form hide" >
		<?php echo $this->element('user_payment_profiles-add', array('cache' => array('config' => 'site_element_cache'), 'plugin' => 'site_tracker')); ?>
	</div>
	<?php echo $this->element('paging_counter');?>
	<table class="list">
		<tr>
			<th class="dl"><?php echo __l('Credit Card');?></th>
			<th class="dc"><?php echo __l('Default');?></th>
		</tr>
	<?php
		if (!empty($userPaymentProfiles)):
			$i = 0;
			foreach ($userPaymentProfiles as $userPaymentProfile):
				$class = null;
				if ($i++ % 2 == 0) {
					$class = ' class="altrow"';
				}
	?>
		<tr<?php echo $class;?>>
			<td class="actions dl">
				<div class="actions-block">
					<div class="actions round-5-left">
						<span><?php echo $this->Html->link(__l('Edit'), array('action' => 'edit', $userPaymentProfile['UserPaymentProfile']['id']), array('class' => 'edit js-inline-edit', 'title' => __l('Edit')));?></span>
						<span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $userPaymentProfile['UserPaymentProfile']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span>
						<?php if (empty($userPaymentProfile['UserPaymentProfile']['is_default'])): ?>
							<span><?php echo $this->Html->link(__l('Set as default'), array('action' => 'update', $userPaymentProfile['UserPaymentProfile']['id']), array('class' => 'update', 'title' => __l('Set as default')));?></span>
						<?php endif; ?>
					</div>
				</div>
				<?php echo $this->Html->cText($userPaymentProfile['UserPaymentProfile']['masked_cc']);?>
			</td>
			<td class="dc">
				<?php echo $this->Html->cBool($userPaymentProfile['UserPaymentProfile']['is_default']);?>
			</td>
		</tr>
	<?php
			endforeach;
		else:
	?>
		<tr>
			<td colspan="6" class="notice"><?php echo __l('No credit cards available');?></td>
		</tr>
	<?php
		endif;
	?>
	</table>
	<?php
		if (!empty($businessSuggestions)) {
			echo $this->element('paging_links');
		}
	?>
</div>