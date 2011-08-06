<div>
<?php if(!empty($user_deals)){ ?>
	<ol class="deal-user-list">
		<?php foreach($user_deals as $user_deal){ ?>
			<?php
				if(!empty($user_deal['DealUser']['is_used']) && ($user_deal['DealUser']['is_used'] == 1)) {
					$class = 'used';
				} else {
					$class = 'not-used';
				}
			?>
			<li class = "clearfix <?php echo $class;?>">
				<div class="company-list-image">
					<?php echo $this->Html->showImage('Deal', $user_deal['Deal']['Attachment'][0], array('dimension' => 'medium_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($user_deal['Deal']['name'], false)), 'title' => $this->Html->cText($user_deal['Deal']['name'], false)));?>
				</div>
				<div class="company-list-content">
					<h3><?php echo $this->Html->link($user_deal['Deal']['name'], array('controller' => 'deals', 'action' => 'view', $user_deal['Deal']['slug']),array('title' => sprintf(__l('%s'),$user_deal['Deal']['name'])));?></h3>
						<dl class="list statistics-list">
						<dt><?php echo __l('Purchased On: ');?></dt>
						<dd><?php echo $this->Html->cDate($user_deal['DealUser']['created']);?></dd>
						<dt><?php echo __l('Quantity: ');?></dt>
						<dd><?php echo $this->Html->cInt($user_deal['DealUser']['quantity']);?></dd>
					</dl>
				</div>
			</li>
		<?php } ?>
	</ol>
<?php } else { ?>
	<p class="notice"><?php echo __l('No coupons available');?></p>
<?php } ?>
</div>