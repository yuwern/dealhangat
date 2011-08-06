<?php /* SVN: $Id: $ */ ?>
<?php 
	if(!empty($this->request->params['isAjax'])):
		echo $this->element('flash_message');
	endif;
?>
<div class="genders index js-response">
<h2><?php echo __l('Genders');?></h2>
<?php echo $this->element('paging_counter');?>
<table class="list">
    <tr> 
       <th class="actions"><?php echo __l('Action');?></th>
        <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Name'),'name');?></div></th>
     </tr>
<?php
if (!empty($genders)):

$i = 0;
foreach ($genders as $gender):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td class="actions">
            <span><?php echo $this->Html->link(__l('Edit'), array('action' => 'edit', $gender['Gender']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?></span>
           </td>
          <td>  <?php echo $this->Html->cText($gender['Gender']['name']);?></td>
	</tr>
<?php
    endforeach;
else:
?>
	<tr>
		<td colspan="7" class="notice"><?php echo __l('No genders available');?></td>
	</tr>
<?php
endif;
?>
</table>

<?php
if (!empty($genders)) { ?>
     <div class="js-pagination">
                        <?php echo $this->element('paging_links'); ?>
                    </div>
<?php } ?>
</div>
