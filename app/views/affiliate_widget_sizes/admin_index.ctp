<?php /* SVN: $Id: $ */ ?>
<div class="affiliateWidgetSizes index js-response">
<h2><?php echo $this->Html->link(__l('Affiliates'), array('controller' => 'affiliates', 'action' => 'index'), array('title' => __l('Back to Affiliates')));?> &raquo; <?php echo __l('Affiliate Widget Sizes');?></h2>
<div class="clearfix add-block1">
  <?php echo $this->Html->link(__l('Add'), array('controller' => 'affiliate_widget_sizes', 'action' => 'add'), array('class' => 'add','title'=>__l('Add'))); ?>
</div>
<?php echo $this->element('paging_counter');?>
<table class="list">
    <tr>
        <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Created On'),'created');?></div></th>
        <th><div class="js-pagination"><?php echo $this->Paginator->sort('name');?></div></th>
        <th><?php echo __l('Logo');?></th>
        <th><div class="js-pagination"><?php echo $this->Paginator->sort('width');?></div></th>
        <th><div class="js-pagination"><?php echo $this->Paginator->sort('height');?></div></th>
        <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Display Side Deal?'),'is_display_side_deal');?></div></th>
    </tr>
<?php
if (!empty($affiliateWidgetSizes)):

$i = 0;
foreach ($affiliateWidgetSizes as $affiliateWidgetSize):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
         <td>  <div class="actions-block">
                <div class="actions round-5-left cities-action-block">
                      <span><?php echo $this->Html->link(__l('Edit'), array('action' => 'edit', $affiliateWidgetSize['AffiliateWidgetSize']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span>
                      <span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $affiliateWidgetSize['AffiliateWidgetSize']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span> 
                 </div>
            </div>  
		<?php echo $this->Html->cDateTimeHighLight($affiliateWidgetSize['AffiliateWidgetSize']['created']);?></td>
		<td><?php echo $this->Html->cText($affiliateWidgetSize['AffiliateWidgetSize']['name']);?></td>
        <td><?php echo $this->Html->showImage('AffiliateWidgetSize', $affiliateWidgetSize['Attachment'], array('dimension' => 'original', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($affiliateWidgetSize['AffiliateWidgetSize']['name'], false)), 'title' => $this->Html->cText($affiliateWidgetSize['AffiliateWidgetSize']['name'], false)));?>                      
        </td>
		<td><?php echo $this->Html->cInt($affiliateWidgetSize['AffiliateWidgetSize']['width']);?></td>
		<td><?php echo $this->Html->cInt($affiliateWidgetSize['AffiliateWidgetSize']['height']);?></td>
		<td><?php echo $this->Html->cBool($affiliateWidgetSize['AffiliateWidgetSize']['is_display_side_deal']);?></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="8" class="notice"><?php echo __l('No Affiliate Widget Sizes available');?></td>
	</tr>
<?php
endif;
?>
</table>
<?php
if (!empty($affiliateWidgetSizes)):?>
<div class="js-pagination">
            <?php echo $this->element('paging_links');?>
</div>
<?php endif; ?>
</div>






