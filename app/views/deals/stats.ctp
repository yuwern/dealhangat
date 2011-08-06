<div>
   <fieldset class="form-block round-5">
		<legend class="round-5"><?php echo __l('Deal Information'); ?></legend>
		<dl class="list">
			<dt><?php echo __l('Deal');?></dt>
				<dd>
					<?php echo $this->Html->cText($deal['Deal']['name']);?>
				</dd>
			<dt><?php echo __l('Current Deal Status');?></dt>
				<dd>
					<?php echo $this->Html->cText($deal['DealStatus']['name']);?>
				</dd>
			<?php if(!empty($deal['City'])):?>
			<dt><?php echo __l('Locations');?></dt>
				<dd>
					<?php 
						foreach($deal['City'] as $cities):
    						?>
    						<p>
    						<?php
    							echo $cities['name'];
                            ?>
                            </p>
                            <?php
						endforeach;
					?>
				</dd>	
			<?php endif;?>
			<dt><?php echo __l('Deal Lifetime');?></dt>
				<dd>
                    <p><?php echo __l('Created On').' '.$this->Html->cDateTime($deal['Deal']['created']);?></p>
    				<p><?php echo __l('Start(ed) On').' '.$this->Html->cDateTime($deal['Deal']['start_date']);?></p>
	    			<p><?php echo __l('End(ed) On').' '.$this->Html->cDateTime($deal['Deal']['end_date']);?></p>
                </dd>
	</fieldset>
	<fieldset class="form-block round-5">
		<legend class="round-5"><?php echo __l('Deal Sales/Purchase Information'); ?></legend>
		<dl class="list">
			<dt><?php echo __l('Coupon Expires On');?></dt>
				<dd>
					<?php echo $this->Html->cDateTime($deal['Deal']['coupon_expiry_date']);?>
				</dd>
			<dt><?php echo __l('Total Purchases');?></dt>
				<dd>
					<?php echo $this->Html->cInt($deal['Deal']['deal_user_count']);?>
			    </dd>
		</dl>
	</fieldset>
</div>