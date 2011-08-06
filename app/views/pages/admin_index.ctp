<?php /* SVN: $Id: admin_index.ctp 54577 2011-05-25 10:39:06Z arovindhan_144at11 $ */ ?>
<div class="pages index">
<h2><?php echo __l('Pages');?></h2>
<div class="clearfix add-block1">
	<?php echo $this->Html->link(__l('Add'), array('controller' => 'pages', 'action' => 'add'), array('class' => 'add','title' => __l('Add'))); ?>
</div>
<div class="staticpage index">
<?php echo $this->element('paging_counter');?>
<div class="overflow-block">
<table class="list">
    <tr>
        <th class="dl"><?php echo $this->Paginator->sort(__l('Title'),'title');?></th>
        <th class="dl"><?php echo $this->Paginator->sort(__l('Content'),'content');?></th>
    </tr>
<?php
if (!empty($pages)):

$i = 0;
foreach ($pages as $page):
	$class = null;
	if ($i++ % 2 == 0) :
		$class = ' class="altrow"';
    endif;
?>
	<tr<?php echo $class;?>>
		<td class="dl">
        <div class="actions-block">
            <div class="actions round-5-left">
                <?php if($page['Page']['slug'] != 'pre-launch'): ?>
				<span><?php echo $this->Html->link(__l('View'), array('controller' => 'pages', 'action' => 'view', $page['Page']['slug']), array('class' => 'view', 'title' => __l('View')));?></span>
				<?php endif; ?>
                <span><?php echo $this->Html->link(__l('Edit'), array('action' => 'edit', $page['Page']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span>
                <span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $page['Page']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span>
            </div>
        </div>
		<?php echo $this->Html->cText($page['Page']['title']);?></td>
		<td class="dl"><?php echo $this->Html->cText($this->Html->truncate($page['Page']['content'], 100, array('ending' => '...')));?></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="17" class="notice"><?php echo __l('No Pages available');?></td>
	</tr>
<?php
endif;
?>
</table>
</div>
<?php
if (!empty($pages)) :
    echo $this->element('paging_links');
endif;
?>

</div>
</div>
