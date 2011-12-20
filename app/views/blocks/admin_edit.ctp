<div id="add">
<div class="blocks form">
<?php echo $this->Form->create('Block', array('action' => 'edit', 'class' => 'normal')); ?>
    <fieldset>
    <h2><?php echo __l('Edit Block');?></h2>
    <?php
        echo $this->Form->input('title');
        
        $languages = $this->Html->getLanguage();
        echo $this->Form->input('lang', array('label' => 'Language', 'options' => $languages));
        
        echo $this->Form->input('body', array('rows' => '3'));
   
        $regions = array('left', 'right', 'bottom');
        echo $this->Form->input('region', array('options' => $regions));

        echo $this->Form->input('id', array('type' => 'hidden'));
    ?>
    <div class="submit-block clearfix">
    <?php
    echo $this->Form->end('Update');
    ?>
    </div>
    </fieldset>
</div>
</div>
