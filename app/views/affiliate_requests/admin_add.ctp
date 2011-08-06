<?php /* SVN: $Id: $ */ ?>
<div class="affiliateRequests form">
<h2><?php echo __l('Add Affiliate Request');?></h2>
<?php echo $this->Form->create('AffiliateRequest', array('class' => 'normal'));?>
	<fieldset>
	<?php
		echo $this->Form->input('user_id');
		echo $this->Form->input('site_category_id', array('label' => __l('Site Category')));
		echo $this->Form->input('site_name', array('label' => __l('Site Name')));
		echo $this->Form->input('site_description', array('label' => __l('Site Description')));
		echo $this->Form->input('site_url', array('label' => __l('Site URL')));
		echo $this->Form->input('why_do_you_want_affiliate', array('label' => __l('Why Do You Want an Affiliate?')));
		echo $this->Form->input('is_web_site_marketing', array('label' => __l('Web Site Marketing?')));
		echo $this->Form->input('is_search_engine_marketing', array('label' => __l('Search Engine Marketing?')));
		echo $this->Form->input('is_email_marketing', array('label' => __l('Email Marketing?')));
		echo $this->Form->input('special_promotional_method', array('label' => __l('Special Promotional Method')));
		echo $this->Form->input('special_promotional_description', array('label' => __l('Special Promotional Description')));
		echo $this->Form->input('is_approved', array('legend' => __l('Approved?'), 'type' => 'radio', 'options' => array(0 => 'Waiting for approval', 1 => 'Approved', '2' => 'Rejected')));
	?>
	</fieldset>
	<div class="clearfix submit-block">
        <?php echo $this->Form->end(__l('Add'));?>
    </div>
</div>
