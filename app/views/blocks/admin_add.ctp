<h1>Add Block</h1>
<?php
echo $this->Form->create('Block');
echo $this->Form->input('title');
echo $this->Form->input('body', array('rows' => '3'));

$regions = array('left', 'right', 'bottom');
echo $this->Form->input('region', array('options' => $regions));
echo $this->Form->end('Save Block');
?>
