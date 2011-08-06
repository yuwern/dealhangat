<?php /* SVN: $Id: admin_index.ctp 54451 2011-05-24 12:26:17Z arovindhan_144at11 $ */ ?>
<?php 
		if(!empty($this->request->params['isAjax'])):
			echo $this->element('flash_message');
		endif;
?>

<div class="citySuggestions index">
<div class="js-response">
    <h2><?php echo __l('City Suggestions');?></h2>
	<?php echo $this->element('paging_counter');?>
        <table class="list">
            <tr>
                <th class = "dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('City Name'),'CitySuggestion.name');?></div></th>
                <th class = "dc"><?php echo __l('No. of Requests');?></th>
                
            </tr>
        <?php
        if (!empty($citySuggestions)):
        
        $i = 0;
        foreach ($citySuggestions as $citySuggestion):
            $class = null;
            if ($i++ % 2 == 0) {
                $class = ' class="altrow"';
            }
        ?>
            <tr<?php echo $class;?>>
                <td class = "dl"><?php echo $this->Html->cText($citySuggestion['CitySuggestion']['name']);?></td>
                <td class = "dc"><?php echo $this->Html->link($this->Html->cInt($citySuggestion['0']['count'], false),array('controller'=>'city_suggestions', 'action'=>'index', 'type'=>'recent_suggestion', 'name'=>urlencode_rfc3986($citySuggestion['CitySuggestion']['name'])),array('class' => 'js-thickbox'));?></td>
            </tr>
        <?php
            endforeach;
        else:
        ?>
            <tr>
                <td colspan="6" class="notice"><?php echo __l('No City Suggestions available');?></td>
            </tr>
        <?php
        endif;
        ?>
        </table>
        <div class="js-pagination">
    <?php
    if (!empty($citySuggestions)) {
		?>
            <?php echo $this->element('paging_links'); ?>
        <?php
    }
    ?>
    </div>
	<?php echo $this->element('city_suggest-index', array('cache' => array('config' => 'site_element_cache'))); ?>
</div>
</div>