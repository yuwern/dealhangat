<?php /* SVN: $Id: $ */ ?>
<div class="affiliateTypes form">
<h2><?php echo __l('Commission Settings');?></h2>
<legend><?php echo $this->Html->link(__l('Settings'), array('controller'=> 'settings', 'action' => 'index', '#Affiliate'));?> &raquo; <?php echo __l('Commission Settings');?></legend>
<table  class="list">
	<tr> 
    	        <th><?php echo __l('Name');?></th>
                <th><?php echo __l('Commission');?></th>
                <th><?php echo __l('Commission Type');?></th>
                <th><?php echo __l('Active?');?></th>
    </tr>
<?php echo $this->Form->create('AffiliateType', array('class' => 'normal', 'action' => 'edit'));?>
	
    
	<?php
		$types = count($this->request->data['AffiliateType']);
		for($i=0; $i<$types; $i++){
	?>
    <tr> 
    <?php 
			echo $this->Form->input('AffiliateType.'.$i.'.id', array('label' => false)); ?>
<td> <?php			echo $this->Form->input('AffiliateType.'.$i.'.name', array('label' => false)); ?> </td>
<td> <?php			echo $this->Form->input('AffiliateType.'.$i.'.commission', array('label' => false));
			$options = $affiliateCommissionTypes;
			if($this->request->data['AffiliateType'][$i]['id'] == 1)
				unset($options[1]); ?> </td>
<td> <?php			echo $this->Form->input('AffiliateType.'.$i.'.affiliate_commission_type_id', array('options' => $options, 'label' => false)); ?> </td>
<td> <?php			echo $this->Form->input('AffiliateType.'.$i.'.is_active', array('label' =>false)); ?> </td>
    </tr> 
    <?php 
		}	
	?>
	
<tr> 
	<td colspan="4">	
		<?php echo $this->Form->submit(__l('Update'));?>
    </td>
</tr>        
	
	<?php echo $this->Form->end(); ?>
</table>    
</div>
