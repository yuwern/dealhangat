<?php /* SVN: $Id: admin_index.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<?php 
	if(!empty($this->request->params['isAjax'])):
		echo $this->element('flash_message');
	endif;
?>
<div class="countries index js-response js-responses">
    <h2><?php echo $pageTitle;?></h2>
    <?php echo $this->Form->create('Country', array('type' => 'post', 'class' => 'normal search-form clearfix js-ajax-form','action'=>'index'));?>
         <?php echo $this->Form->input('q', array('label' => __l('Keyword'))); ?>
        <?php echo $this->Form->submit(__l('Filter')); ?>
      <?php echo $this->Form->end(); ?>
         <div class="add-block">
            <?php echo $this->Html->link(__l('Add'),array('controller'=>'countries','action'=>'add'),array('class' => 'add', 'title' => __l('Add New Country')));?>
        </div>
        <div> 
            <?php echo $this->Form->create('Country' , array('action' => 'update','class'=>'normal js-ajax-form'));?>
            <?php echo $this->Form->input('r', array('type' => 'hidden', 'value' => $this->request->url)); ?>
            <?php echo $this->element('paging_counter');?>
            <div class="overflow-block">
				<table class="list">
                <tr>
                    <th rowspan="2"><?php echo __l('Select'); ?></th>
                    <th class="dl" rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Name'), 'Country.name');?></div></th>
                    <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Fips104'), 'Country.fips104');?></div></th>
                    <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Iso2'), 'Country.iso2');?></div></th>
                    <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Iso3'), 'Country.iso3');?></div></th>
                    <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Ison'), 'Country.ison');?></div></th>
                    <th rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Internet'), 'Country.internet');?></div></th>
                    <th class="dl" rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Capital'), 'Country.capital');?></div></th>
                    <th class="dl" rowspan="2"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Map Reference'), 'Country.map_reference');?></div></th>
                    <th colspan="2"><?php echo __l('Nationality');?></th>
                    <th colspan="2"><?php echo __l('Currency');?></th>                    
                </tr>
                <tr>
                    <th class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Singular'), 'Country.nationality_singular');?></div></th>
                    <th class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Plural'), 'Country.nationality_plural');?></div></th>
                    <th class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Name'), 'Country.currency');?></div></th>
                    <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Code'), 'Country.currency_code');?></div></th>

                </tr>
                <?php
                if (!empty($countries)):
                    $i = 0;
                    foreach ($countries as $country):
                        $class = null;
                        if ($i++ % 2 == 0) :
                            $class = ' class="altrow"';
                        endif;
                        ?>
                        <tr<?php echo $class;?>>
                            <td>
								<div class="actions-block">
									<div class="actions round-5-left">
										<?php  echo $this->Html->link(__l('Edit'), array('action'=>'edit', $country['Country']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?>
										<?php
										$delete_icon_show = 1;
										foreach($country['City'] as $city)
										{
                                            if($city['slug'] == Configure::read('site.city'))
                                                {
                                                    $delete_icon_show = 0;
                                                }
                                        }
                                        if(!empty($delete_icon_show))
                                        {
                                          echo $this->Html->link(__l('Delete'), array('action'=>'delete', $country['Country']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));
                                        } ?>
									</div>
								</div>
							<?php
                                echo $this->Form->input('Country.'.$country['Country']['id'].'.id',array('type' => 'checkbox', 'id' => "admin_checkbox_".$country['Country']['id'],'label' => false , 'class' => 'js-checkbox-list'));
                                ?>
                            </td>
                            <td class="dl"><?php echo $this->Html->cText($country['Country']['name']);?></td>
                            <td><?php echo $this->Html->cText($country['Country']['fips104']);?></td>
                            <td><?php echo $this->Html->cText($country['Country']['iso2']);?></td>
                            <td><?php echo $this->Html->cText($country['Country']['iso3']);?></td>
                            <td><?php echo $this->Html->cText($country['Country']['ison']);?></td>
                            <td><?php echo $this->Html->cText($country['Country']['internet']);?></td>
                            <td class="dl"><?php echo $this->Html->cText($country['Country']['capital']);?></td>
                            <td class="dl"><?php echo $this->Html->cText($country['Country']['map_reference']);?></td>
                            <td class="dl"><?php echo $this->Html->cText($country['Country']['nationality_singular']);?></td>
                            <td class="dl"><?php echo $this->Html->cText($country['Country']['nationality_plural']);?></td>
                            <td class="dl"><?php echo $this->Html->cText($country['Country']['currency']);?></td>
                            <td><?php echo $this->Html->cText($country['Country']['currency_code']);?></td>
                         </tr>
                        <?php
                    endforeach;
                else:
                    ?>
                    <tr>
                        <td class="notice" colspan="19"><?php echo __l('No countries available');?></td>
                    </tr>
                    <?php
                endif;
                ?>
            </table>
            </div>
            <?php if (!empty($countries)): ?>
                <div class="admin-select-block">
                    <div>
                        <?php echo __l('Select:'); ?>
                        <?php echo $this->Html->link(__l('All'), '#', array('class' => 'js-admin-select-all','title' => __l('All'))); ?>
                        <?php echo $this->Html->link(__l('None'), '#', array('class' => 'js-admin-select-none','title' => __l('None'))); ?>
                    </div>
                     <div>
                        <?php echo $this->Form->input('more_action_id', array('class' => 'js-admin-index-autosubmit', 'label' => false, 'empty' => __l('-- More actions --'))); ?>
                    </div>
                </div>
                 <div class="js-pagination">
                    <?php echo $this->element('paging_links');  ?>
                </div>
                <div class="hide">
                    <?php echo $this->Form->submit('Submit');  ?>
                </div>
                <?
            endif;
            echo $this->Form->end();
            ?>
        </div>
  
</div>