<?php /* SVN: $Id: admin_edit.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<div class="states form">
    <div>
        <div>
            <h2><?php echo __l('Edit State - ').$this->Html->cText($this->request->data['State']['name']); ?></h2>
        </div>
        <div>
            <?php echo $this->Form->create('State',  array('class' => 'normal','action'=>'edit'));?>
            <?php
                echo $this->Form->input('id');
                echo $this->Form->input('country_id',array('label' => __l('Country'),'empty'=>__l('Please Select')));
                echo $this->Form->input('name',array('label' => __l('Name')));
                echo $this->Form->input('code',array('label' => __l('Code')));
                echo $this->Form->input('adm1code',array('label' => __l('Admlcode')));
                echo $this->Form->input('is_approved', array('label' => __l('Approved?')));
            ?>
       
            <div class="submit-block clearfix">
            <?php
            	echo $this->Form->submit(__l('Update'));
            ?>
            </div>
        <?php
        	echo $this->Form->end(); ?>
        </div>
    </div>
</div>

