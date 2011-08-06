<?php $total_array = $this->Html->total_saved(); ?>
<dt><?php echo __l('Total Saved: '); ?></dt>
    <dd><span><?php echo $this->Html->siteCurrencyFormat($this->Html->cCurrency($total_array['total_saved'])); ?></span></dd>
<dt><?php echo __l('Total Deals Bought: '); ?></dt>
    <dd><?php echo $this->Html->cInt($total_array['total_bought']); ?></dd>
 