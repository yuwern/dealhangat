<?php /* SVN: $Id: admin_index.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
	<?php 
		if(!empty($this->request->params['isAjax'])):
			echo $this->element('flash_message');
		endif;
	?>
<?php if(empty($this->request->params['isAjax'])): ?>
    <div  class="js-tabs">
    	<ul class="clearfix">
            <li><?php echo $this->Html->link(sprintf(__l('Approved Records (%s)'),$approved), array('controller' => 'states', 'action' => 'index', 'filter_id' => ConstMoreAction::Active),array('title' => __l('Approved Records'))); ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Disapproved Records (%s)'),$pending), array('controller' => 'states', 'action' => 'index', 'filter_id' => ConstMoreAction::Inactive),array('title' => __l('Disapproved Records'))) ?></li>
            <li><?php echo $this->Html->link(sprintf(__l('Total Records (%s)'),($pending + $approved)), array('controller' => 'states', 'action' => 'index'),array('title' => __l('Total Records'))) ?></li>
        </ul>
    </div>
<?php else: ?>
        <div class="states index js-response js-responses">
            <h2><?php echo $pageTitle; ?></h2>
            <?php echo $this->Form->create('State', array('type' => 'post', 'class' => 'normal search-form clearfix js-ajax-form {"container" : "js-responses"}', 'action'=>'index')); ?>
            <div class="filter-section">
                <div>
                    <?php echo $this->Form->input('q', array('label' => __l('Keyword'))); ?>
                    <?php echo $this->Form->input('filter_id', array('type' => 'hidden')); ?>
                     <?php echo $this->Form->submit(__l('Search'));?>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
            <div class="add-block">
                <?php echo $this->Html->link(__l('Add'),array('controller'=>'states','action'=>'add'),array('class' => 'add','title' => __l('Add New State')));?>
            </div>
            <div class="js-search-responses">   
                <?php
                echo $this->Form->create('State' , array('action' => 'update','class'=>'normal'));?>
                <?php echo $this->Form->input('r', array('type' => 'hidden', 'value' => $this->request->url)); ?>
                <?php if(!empty($this->request->params['named']['filter_id'])){?>
                <?php echo $this->Form->input('redirect_url', array('type' => 'hidden', 'value' => $this->request->params['named']['filter_id'])); ?>
                <?php } ?>
                <?php if(empty($this->request->data['State']['q'])){ ?>
                    <?php echo $this->element('paging_counter');?>
                <?php } ?>
                    <table class="list">
                        <tr>
                            <th><?php echo __l('Select'); ?></th>
                            <th><?php echo __l('Actions');?></th>
                            <th class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Country'), 'Country.name');?></div></th>
                            <th class="dl"><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Name'), 'State.name');?></div></th>
                            <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Code'), 'State.code');?></div></th>
                            <th><div class="js-pagination"><?php echo $this->Paginator->sort(__l('Adml Code'), 'State.adm1code');?></div></th>
                        </tr>
                        <?php
                            if (!empty($states)):
                            $i = 0;
                                foreach ($states as $state):
                                    $class = null;
                                    if ($i++ % 2 == 0) :
                                        $class = ' class="altrow"';
                                    endif;
                                    if($state['State']['is_approved'])  :
                                        $status_class = 'js-checkbox-active';
                                    else:
                                        $status_class = 'js-checkbox-inactive';
                                    endif;
                                    ?>
                                    <tr<?php echo $class;?>>
                                        <td>
                                        <div class="actions-block">
                                            <div class="actions round-5-left">
                                            <?php echo $this->Html->link(__l('Edit'), array('action'=>'edit', $state['State']['id']), array('class' => 'edit js-edit', 'title' => __l('Edit')));?>
                                            <?php $delete_icon_show = 1;
        										foreach($state['City'] as $city)
        										{
        										  if($city['slug'] == Configure::read('site.city'))
                                                        {
                                                            $delete_icon_show = 0;
                                                        }
                                                }
                                                if($delete_icon_show != 0)
                                                {
                                                   echo $this->Html->link(__l('Delete'), array('action'=>'delete', $state['State']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                            <?php
                                            if($delete_icon_show != 0)
                                                {
                                                    echo $this->Form->input('State.'.$state['State']['id'].'.id',array('type' => 'checkbox', 'id' => "admin_checkbox_".$state['State']['id'],'label' => false , 'class' => $status_class.' js-checkbox-list'));
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if($state['State']['is_approved']):?>
                                            <?php echo $this->Html->link(__l('Approved'),array('controller'=>'states','action'=>'update_status',$state['State']['id'],'disapprove'),array('class' =>'approve','title' => __l('Click here to Disapprove')));?>
                                            <?php else:?>
                                            <?php echo $this->Html->link(__l('Disapproved'),array('controller'=>'states','action'=>'update_status',$state['State']['id'],'approve') ,array('class' =>'pending','title' => __l('Click here to Approve')));?>
                                            <?php endif; ?>
                                            
                                        </td>
                                        <td class="dl"><?php echo $this->Html->cText($state['Country']['name']);?></td>
                                        <td class="dl"><?php echo $this->Html->cText($state['State']['name']);?></td>
                                        <td><?php echo $this->Html->cText($state['State']['code']);?></td>
                                        <td><?php echo $this->Html->cText($state['State']['adm1code']);?></td>
                                    </tr>
                                    <?php
                                endforeach;
                        else:
                            ?>
                            <tr>
                                <td class="notice" colspan="6"><?php echo __l('No states available');?></td>
                            </tr>
                            <?php
                        endif;
                        ?>
                    </table>
                    <?php
                     if (!empty($states)) : ?>
                         <div class="admin-select-block">
                            <div>
                                <?php echo __l('Select:'); ?>
                                    <?php echo $this->Html->link(__l('All'), '#', array('class' => 'js-admin-select-all','title'=>__l('All'))); ?>
                                    <?php echo $this->Html->link(__l('None'), '#', array('class' => 'js-admin-select-none','title'=>__l('None'))); ?>
                                    <?php if(!isset($this->request->params['named']['filter_id'])) { ?>
                                        <?php echo $this->Html->link(__l('Disapproved'), '#', array('class' => 'js-admin-select-pending','title'=>__l('Disapproved'))); ?>
                                        <?php echo $this->Html->link(__l('Approved'), '#', array('class' => 'js-admin-select-approved','title'=>__l('Approved'))); ?>
                                    <?php } ?>
                            </div>
                             <div>
                                 <?php echo $this->Form->input('more_action_id', array('class' => 'js-admin-index-autosubmit', 'label' => false, 'empty' => __l('-- More actions --'))); ?>
                            </div>
                            </div>
                            <div class="js-pagination">
                            <?php  echo $this->element('paging_links'); ?>
                            </div>
                           
                            <div class="hide">
                                <?php echo $this->Form->submit('Submit');  ?>
                            </div>
                        <?php
                     endif; ?>
                    <?php echo $this->Form->end();?>
            </div>
                </div>
<?php endif; ?>