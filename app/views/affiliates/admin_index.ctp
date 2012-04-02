<?php /* SVN: $Id: $ */ ?>
<?php if(empty($this->request->params['isAjax'])) : ?>
<div class="affiliates index">
<h2><?php echo __l('Affiliates');?></h2>
        <div class="clearfix add-block1 affiliate-links">
			<?php echo $this->Html->link(__l('Affiliate  Requests'), array('controller' => 'affiliate_requests', 'action' => 'index'), array('class' => 'affiliate-application', 'title' => __l('Affiliate Requests'))); ?>
			<?php echo $this->Html->link(__l('Affiliate Cash Withdrawal Requests'), array('controller' => 'affiliate_cash_withdrawals', 'action' => 'index'),array('class' => 'widthdraw', 'title' => __l('Affiliate Cash Withdrawal Requests'))); ?>
        	<?php echo $this->Html->link(__l('Widgets'), array('controller' => 'affiliate_widget_sizes', 'action' => 'index'),array('class' => 'affiliate-widget', 'title' => __l('Widgets'))); ?>
			<?php echo $this->Html->link(__l('Settings'), array('controller' => 'settings', 'action' => 'index', '#Affiliate'),array('class' => 'affiliate-settings', 'title' => __l('Settings'))); ?>
		</div>
<?php echo $this->element('admin_affiliate_stat'); ?>
<h3><?php echo __l('Commission History');?></h3>
    <div class="js-tabs">
        <ul class="clearfix">
            <li><?php echo $this->Html->link(__l('All'), array('controller'=>'affiliates','action'=>'index','filter_id' =>'All'), array('title' => __l('All')));?></li>
            
            <li><?php echo $this->Html->link(__l('Pending'), array('controller'=>'affiliates','action'=>'index','filter_id' => ConstAffiliateStatus::Pending), array('title' => __l('Pending')));?></li>
            <li><?php echo $this->Html->link(__l('Canceled'), array('controller'=>'affiliates','action'=>'index','filter_id' => ConstAffiliateStatus::Canceled), array('title' => __l('Canceled')));?></li>
            <li><?php echo $this->Html->link(__l('Pipeline'), array('controller'=>'affiliates','action'=>'index','filter_id' => ConstAffiliateStatus::PipeLine), array('title' => __l('Pipeline')));?></li>
            <li><?php echo $this->Html->link(__l('Completed'), array('controller'=>'affiliates','action'=>'index','filter_id' => ConstAffiliateStatus::Completed), array('title' => __l('Completed')));?></li>
        </ul>
    </div>
<?php else : ?>
<?php echo $this->element('paging_counter');?>
<table class="list">
    <tr>
        <th><?php echo $this->Paginator->sort(__l('Created'), 'created');?></th>
        <th><?php echo $this->Paginator->sort(__l('Affiliate User'), 'AffiliateUser.username');?></th>
        <th><?php echo __l('User').'/'. __l('Deal');?></th>
        <th><?php echo $this->Paginator->sort(__l('Type'), 'AffiliateType.name');?></th>
        <th><?php echo $this->Paginator->sort(__l('Status'), 'AffiliateStatus.name');?></th>
        <th><?php echo $this->Paginator->sort(__l('Commission'), 'commission_amount'). ' ('. Configure::read('site.currency').')';?></th>
    </tr>
<?php
if (!empty($affiliates)):
?>
<?php echo $this->Form->create('Affiliate' , array('action' => 'admin_index', 'type' => 'get', 'class' => 'normal search-form clearfix')); ?>
	<div class="clearfix date-time-block">
		<div class="input date-time clearfix">
			<div class="js-datetime">
				<?php echo $this->Form->input('from_date', array('label' => __l('From'), 'type' => 'date', 'minYear' => date('Y')-10, 'maxYear' => date('Y'), 'div' => false, 'empty' => __l('Please Select'), 'orderYear' => 'asc')); ?>
			</div>
		</div>
		<div class="input date-time end-date-time-block clearfix">
			<div class="js-datetime">
				<?php echo $this->Form->input('to_date', array('label' => __l('To '),  'type' => 'date', 'minYear' => date('Y')-10, 'maxYear' => date('Y'), 'div' => false, 'empty' => __l('Please Select'), 'orderYear' => 'asc')); ?>
			</div>
		</div>
	</div>  
	<?php
		echo $this->Form->submit(__l('Filter'));
	?>
<?php echo $this->Form->end(); ?>
<?php echo $this->Html->link(__l('CSV'), array_merge(array('controller' => 'affiliates', 'action' => 'index','city' => $city_slug, 'ext' => 'csv', 'admin' => true), $this->request->params['named']), array('title' => __l('CSV'), 'class' => 'export'));
$i = 0;
foreach ($affiliates as $affiliate):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
        <td> <?php echo $this->Html->cDateTimeHighlight($affiliate['Affiliate']['created']);?></td>
		<td><?php echo $this->Html->link($this->Html->cText($affiliate['AffiliateUser']['username']), array('controller'=> 'users', 'action'=>'view', $affiliate['AffiliateUser']['username'], 'admin' => false), array('escape' => false));?></td>
		<td> 
        	<?php 
				if($affiliate['Affiliate']['class'] == 'User'){
			?>	
					<?php echo $this->Html->link($this->Html->cText($affiliate['User']['username']), array('controller'=> 'users', 'action' => 'view', $affiliate['User']['username'], 'admin' => false), array('escape' => false));?>
			<?php
			   } else{
			?>	
					<?php echo $this->Html->cText($affiliate['DealUser']['Deal']['name']);?>
					(<?php echo $this->Html->link($this->Html->cText($affiliate['DealUser']['User']['username']), array('controller'=> 'users', 'action' => 'view', $affiliate['DealUser']['User']['username'], 'admin' => false), array('escape' => false));?>)
		<?php   } ?>
		</td>
        <td> <?php echo $this->Html->cText($affiliate['AffiliateType']['name']);?> </td>
		
		<td>
           <?php echo $this->Html->cText($affiliate['AffiliateStatus']['name']);   ?>
           <?php  if($affiliate['AffiliateStatus']['id'] == ConstAffiliateStatus::PipeLine): ?>
                   <?php echo '['.__l('Since').': '.$this->Html->cDateTimeHighlight($affiliate['Affiliate']['commission_holding_start_date']). ']';?>
           <?php endif; ?>
        </td>
		<td><?php echo $this->Html->cFloat($affiliate['Affiliate']['commission_amount']);?></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="11" class="notice"><?php echo __l('No commission history available');?></td>
	</tr>
<?php
endif;
?>
</table>

<?php
if (!empty($affiliates)) {
    echo $this->element('paging_links');
}
?>
<?php
endif;
if(empty($this->request->params['isAjax'])) : ?>
</div>
<?php endif; ?>
