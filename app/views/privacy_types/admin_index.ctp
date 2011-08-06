<?php /* SVN: $Id: $ */ ?>
<div class="privacyTypes index js-response">
<h2><?php echo __l('Privacy Types');?></h2>
<?php echo $this->element('paging_counter');?>
<table class="list">
    <tr>
       <th class="actions"><?php echo __l('Action');?></th>
        <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Name'),'name');?></div></th>
    </tr>
<?php
if (!empty($privacyTypes)):

$i = 0;
foreach ($privacyTypes as $privacyType):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
		<td class="actions">
                        	<span><?php echo $this->Html->link(__l('Edit'), array('action' => 'edit', $privacyType['PrivacyType']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span>
            </td>
          <td>  <?php echo $this->Html->cText($privacyType['PrivacyType']['name']);?></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="6" class="notice"><?php echo __l('No privacy types available');?></td>
	</tr>
<?php
endif;
?>
</table>
<?php
if (!empty($privacyTypes)) { ?>
     <div class="js-pagination">
                        <?php echo $this->element('paging_links'); ?>
                    </div>
<?php } ?>
</div>
