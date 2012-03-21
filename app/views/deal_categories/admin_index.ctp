<?php /* SVN: $Id: $ */ ?>
<?php 
		if(!empty($this->request->params['isAjax'])):
			echo $this->element('flash_message');
		endif;
	?>
<div class="dealCategories index js-response js-responses">
<h2><?php echo __l('Deal Subscription Categories');?></h2>
<?php echo $this->Form->create('DealCategory' , array('type' => 'get', 'class' => 'normal search-form clearfix','action' => 'index')); ?>
		<?php echo $this->Form->input('q', array('label' => __l('Keyword'))); ?>
		<?php echo $this->Form->submit(__l('Search'));?>
	<?php echo $this->Form->end(); ?>
	<div class="add-block">
            <?php echo $this->Html->link(__l('Add'),array('controller'=>'deal_categories','action'=>'add'),array('class' => 'add', 'title' => __l('Add Category')));?>
        </div>
    <?php echo $this->Form->create('DealCategory' , array('class' => 'normal js-ajax-form','action' => 'update')); ?>
    <?php echo $this->Form->input('r', array('type' => 'hidden', 'value' => $this->request->url)); ?>
    <?php echo $this->element('paging_counter');?>
<table class="list">
    <tr>
        <th><?php echo __l('Select'); ?></th>             
        <th><div class="js-pagination"><?php echo $this->Paginator->sort('name');?></div></th>
		<th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Added On'),'created');?></div></th>
    </tr>
<?php
if (!empty($dealCategories)):

$i = 0;
foreach ($dealCategories as $dealCategory):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td class="actions">
		<div class="actions-block">
			<div class="actions round-5-left">
						<span><?php echo $this->Html->link(__l('Edit'), array('controler'=> 'deal_categories','action' => 'edit', $dealCategory['DealCategory']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span> <span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $dealCategory['DealCategory']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span>
		</div>
					</div>
					<?php echo $this->Form->input('DealCategory.'.$dealCategory['DealCategory']['id'].'.id', array('type' => 'checkbox', 'id' => "admin_checkbox_".$dealCategory['DealCategory']['id'], 'label' => false, 'class' => 'js-checkbox-list')); ?>
		</td>		
		<td><?php echo $this->Html->cText($dealCategory['DealCategory']['name']);?></td>
		<td><?php echo $this->Html->cDateTimeHighlight($dealCategory['DealCategory']['created']);?></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="5" class="notice"><?php echo __l('No Deal Categories available');?></td>
	</tr>
<?php
endif;
?>
</table>

<?php
if (!empty($dealCategories)):
    ?>
	<div class="admin-select-block">
        <div>
            <?php echo __l('Select:'); ?>
            <?php echo $this->Html->link(__l('All'), '#', array('class' => 'js-admin-select-all','title' => __l('All'))); ?>
            <?php echo $this->Html->link(__l('None'), '#', array('class' => 'js-admin-select-none','title' => __l('None'))); ?>
        </div>
         <div class="admin-checkbox-button">
            <?php echo $this->Form->input('more_action_id', array('class' => 'js-admin-index-autosubmit', 'label' => false, 'empty' => __l('-- More actions --'))); ?>
        </div>
        </div>
         <div class="js-pagination">
            <?php echo $this->element('paging_links'); ?>
        </div>
        <div class = "hide">
            <?php echo $this->Form->submit('Submit');  ?>
        </div>
        <?php
    echo $this->Form->end();
endif;
?>
</div>
