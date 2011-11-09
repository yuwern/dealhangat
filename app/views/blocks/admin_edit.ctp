<h1>Edit Block</h1>
<?php
    echo $this->Form->create('Block', array('action' => 'edit'));
    echo $this->Form->input('title');
    echo $this->Form->input('body', array('rows' => '3'));
   
    $regions = array('left', 'right', 'bottom');
    echo $this->Form->input('region', array('options' => $regions));

    echo $this->Form->input('id', array('type' => 'hidden'));
    echo $this->Form->end('Save Block');
?>
