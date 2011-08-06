<?php /* SVN: $Id: admin_index.ctp 5508 2010-05-25 11:48:42Z senthilkumar_017ac09 $ */ ?>
<?php 
	if(!empty($this->request->params['isAjax'])):
		echo $this->element('flash_message');
	endif;
?>
<div class="businessSuggestions index">
<div class="js-response">
    <h2><?php echo __l('Business Suggestions');?></h2>
	<?php echo $this->element('paging_counter');?>
        <table class="list">
            <tr>
                <th class="dl"><?php echo $this->Paginator->sort(__l('Email'), 'BusinessSuggestion.email');?></th>
                <th class="dl"><?php echo __l('User');?></th>
                <th class="dl"><?php echo __l('Suggestion');?></th>
                <th class="dc"><?php echo __l('Suggested On');?></th>
                
            </tr>
        <?php
        if (!empty($businessSuggestions)):
        
        $i = 0;
        foreach ($businessSuggestions as $businessSuggestion):
            $class = null;
            if ($i++ % 2 == 0) {
                $class = ' class="altrow"';
            }
        ?>
            <tr<?php echo $class;?>>
                <td class="actions dl">
					<div class="actions-block">
						<div class="actions round-5-left">
							<span><?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $businessSuggestion['BusinessSuggestion']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?></span>
						</div>
					</div>
					<?php echo $this->Html->cText($businessSuggestion['BusinessSuggestion']['email']);?>
				</td>
                <td class="dl"><?php echo !empty($businessSuggestion['User']['username']) ? $this->Html->showImage('UserAvatar', $businessSuggestion['User']['UserAvatar'], array('dimension' => 'micro_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($businessSuggestion['User']['username'], false)), 'title' => $this->Html->cText($businessSuggestion['User']['username'], false))).$this->Html->getUserLink($businessSuggestion['User']) : 'Guest';?></td>
                <td class="dl"><?php echo $this->Html->cText($businessSuggestion['BusinessSuggestion']['suggestion']);?></td>
                <td class="dc"><?php echo $this->Html->cDateTime($businessSuggestion['BusinessSuggestion']['created']);?></td>
            </tr>
        <?php
            endforeach;
        else:
        ?>
            <tr>
                <td colspan="6" class="notice"><?php echo __l('No Business Suggestions available');?></td>
            </tr>
        <?php
        endif;
        ?>
        </table>
    <?php
    if (!empty($businessSuggestions)) {
		?>
            <?php echo $this->element('paging_links'); ?>
        <?php
    }
    ?>
</div>
</div>