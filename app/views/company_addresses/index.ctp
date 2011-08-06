<?php /* SVN: $Id: index.ctp 4730 2010-05-14 13:50:53Z mohanraj_109at09 $ */ ?>
<div class="companyAddresses index clearfix  js-responses js-response">
<h2><?php echo __l('Company Addresses');?></h2>
<div class="add-block js-company-branch-address-add">
<?php echo $this->Html->link(__l('Add Address'),array('controller' => 'company_addresses', 'action' => 'add', 'company_id' => $company_id),array('title'=>__l('Add Address'), 'class' => 'add')); ?>
</div>
<?php echo $this->element('paging_counter');?>
<ol class="list clearfix">
<?php
if (!empty($companyAddresses)):

$i = 0;
foreach ($companyAddresses as $companyAddress):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = "altrow";
	}
?>
	 <li class= "vcard clearfix <?php echo $class;?>" >
			<div class="address-actions">

					<?php echo $this->Html->link(__l('Edit'), array('action' => 'edit', $companyAddress['CompanyAddress']['id']), array('class' => 'edit js-inline-edit', 'title' => __l('Edit')));?>

					<?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $companyAddress['CompanyAddress']['id']), array('class' => 'delete js-on-the-fly-delete', 'title' => __l('Delete')));?>

			</div>
            <address>
				<?php echo $this->Html->cText($companyAddress['CompanyAddress']['address1']);?>
				<?php
					if(!empty($companyAddress['CompanyAddress']['address2'])):
						 echo $this->Html->cText($companyAddress['CompanyAddress']['address2']);
					endif;
				?>
				<?php echo $this->Html->cText($companyAddress['City']['name']);?>
				<?php echo $this->Html->cText($companyAddress['State']['name']);?>
				<?php echo $this->Html->cText($companyAddress['Country']['name']);?>
				<?php echo $this->Html->cText($companyAddress['CompanyAddress']['zip']);?>
            </address>
			<span class="phone"><?php echo  !empty($companyAddress['CompanyAddress']['phone'])? $this->Html->cText($companyAddress['CompanyAddress']['phone']) : '&nbsp;';?></span>
			<span class="url"><?php echo  !empty($companyAddress['CompanyAddress']['url'])? $this->Html->cText($companyAddress['CompanyAddress']['url']) : '&nbsp;';?></span>
	</li>
<?php
    endforeach;
else:
?>
	<li class="notice"><?php echo __l('No Company Addresses available');?></li>
<?php
endif;
?>
</ol>
<div class="js-pagination clearfix">
<?php
if (!empty($companyAddresses)) {
    echo $this->element('paging_links');
}
?>
</div>
</div>
