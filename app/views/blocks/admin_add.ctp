<div id="add">
<div class="blocks form">
<?php echo $this->Form->create('Block', array('class' => 'normal')); ?>
    <fieldset>
    <h2><?php echo __l('Add Block');?></h2>
    <?php
    echo $this->Form->input('title');

    $languages = $this->Html->getLanguage();
    echo $this->Form->input('lang', array('label' => 'Language', 'options' => $languages));
        
    echo $this->Form->input('body', array('rows' => '3'));

    $regions = array('left', 'right', 'bottom');
    echo $this->Form->input('region', array('options' => $regions));
    ?>
    <div class="submit-block clearfix">
    <?php
    echo $this->Form->end('Save Block');
    ?>
    </div>
    </fieldset>
</div>
</div>
