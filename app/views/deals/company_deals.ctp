<div class="js-response js-responses">
	<?php if(!empty($company_deals)){ ?>
		<?php echo $this->element('paging_counter'); ?>
		<ol class="deal-user-list">
		<?php foreach($company_deals as $company_deal){ ?>
			<li class="clearfix">
    			<div class="company-list-image">
                        <?php echo $this->Html->showImage('Deal', $company_deal['Attachment'][0], array('dimension' => 'medium_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($company_deal['Deal']['name'], false)), 'title' => $this->Html->cText($company_deal['Deal']['name'], false)));?>
                </div>
                <div class="company-list-content">
					<h3>
                    <?php echo $this->Html->link($company_deal['Deal']['name'], array('controller' => 'deal', 'action' => 'view', $company_deal['Deal']['slug']),array('title' => sprintf(__l('%s'),$company_deal['Deal']['name'])));?>
					</h3>
                    <dl class="list statistics-list">
                        <dt><?php echo __l('Created On: ');?></dt>
                            <dd><?php echo $this->Html->cDateTime($company_deal['Deal']['created']);?></dd>
                        <dt><?php echo __l('Status: ');?></dt>
                            <dd><?php echo $this->Html->cText($company_deal['DealStatus']['name']);?></dd>
                    </dl>
             </div>
            </li>
		<?php } ?>
		</ol>
		<div class="js-pagination"> <?php echo $this->element('paging_links'); ?> </div>
	<?php }else{ ?>
        <p class="notice "><?php echo __l('No deals available');?></p>
    <?php } ?>
</div>
