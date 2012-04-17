<?php /* SVN: $Id: admin_add.ctp 55560 2011-06-03 13:41:06Z anandam_023ac09 $ */ ?>
<?php echo $this->element('js_tiny_mce_setting', array('cache' => array('config' => 'site_element_cache')));?>
<div class="companies form clearfix">
	<h2><?php echo __l('Add Company');?></h2>
	<?php
		echo $this->Form->create('Company', array('class' => 'normal js-company-map'));
	?>
	<fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Account'); ?></legend>
	<?php
		echo $this->Form->input('User.username',array('label' => __l('Username')));
		echo $this->Form->input('User.passwd',array('label' => __l('Password')));
		echo $this->Form->input('name',array('label' => __l('Company Name')));
		echo $this->Form->input('phone',array('label' => __l('Phone')));
		echo $this->Form->input('url',array('label' => __l('URL'), 'info' => __l('eg. http://www.example.com')));
		echo $this->Form->input('User.email',array('label' => __l('Email')));
		echo $this->Form->input('operating_hours_ms',array('label' => __l('Operating Hours Malay')));		
		echo $this->Form->input('operating_hours',array('label' => __l('Operating Hours')));		
		echo $this->Form->input('is_online_account',array('label' =>__l('Online Account'), 'info' => __l('Only online company can login and make payment via site. Offline company can process manually. ')));
	?>
	</fieldset>
	<fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Address'); ?></legend>
	<?php
		echo $this->Form->input('address1',array('label' => __l('Address1')));
		echo $this->Form->input('address2',array('label' => __l('Address2')));
		
		echo $this->Form->input('country_id',array('label' => __l('Country'),'empty' => __l('Please Select')));
		echo $this->Form->autocomplete('State.name', array('label' => __l('State'), 'acFieldKey' => 'State.id', 'acFields' => array('State.name'), 'acSearchFieldNames' => array('State.name'), 'maxlength' => '255'));
		echo $this->Form->autocomplete('City.name', array('label' => __l('City'), 'acFieldKey' => 'City.id', 'acFields' => array('City.name'), 'acSearchFieldNames' => array('City.name'), 'maxlength' => '255'));		
		echo $this->Form->input('zip',array('label' => __l('Zip')));					
	?>
	</fieldset>	
	<fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Company Profile'); ?></legend>
	<?php
			echo $this->Form->input('is_company_profile_enabled', array('label' => __l('Enable company profile'), 'class' => 'js_company_profile js_company_profile_enable', 'info' => __l('Whether other users can view the company profile or not')));
			?><div class = "js-company_profile_show">
		 
		
            <?php echo $this->Form->input('Company.company_profile', array('label' => __l('Company Profile'),'type' => 'textarea', 'class' => 'js-editor'));?>
		
		</div>
		
	</fieldset>
	<fieldset class="form-block round-5">
		<legend class="round-5"><?php echo __l('Paypal Account'); ?></legend>
            <?php echo $this->Form->input('User.UserProfile.paypal_account');?>
		<?php
		echo $this->Form->input('bank_name',array('label' =>__l('Bank Name')));
		echo $this->Form->input('bank_account',array('label' =>__l('Bank Account')));
		echo $this->Form->input('bank_register_no',array('label' =>__l('Registration Number or IC Number')));
		?>
	</fieldset>
	<div class="">
	<fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Locate yourself on google maps'); ?></legend>
			<div class="show-map" style="">
			 
			<div id="js-map-container"></div>
			<p><?php echo __l('You can change the google map zooming level here, else default zooming level will be taken.'); ?></p>
			</div>
			<?php
			echo $this->Form->input('latitude',array('type' => 'hidden', 'id'=>'latitude'));
			echo $this->Form->input('longitude',array('type' => 'hidden', 'id'=>'longitude'));
			?>
			<?php
				$map_zoom_level = !empty($this->request->data['Company']['map_zoom_level']) ? $this->request->data['Company']['map_zoom_level'] : Configure::read('GoogleMap.static_map_zoom_level');
				echo $this->Form->input('Company.map_zoom_level',array('type' => 'hidden','value' => $map_zoom_level,'id'=>'zoomlevel'));
			?>
	</fieldset>
	</div>
		<div class="submit-block clearfix">
		<?php echo $this->Form->submit(__l('Add')); ?>
		</div>
		<?php echo $this->Form->end(); ?>
</div>
<?php
if(!empty($this->request->data['Company']['is_company_profile_enabled']) and $this->request->data['Company']['is_company_profile_enabled']==1)
{
   $show_company_profile = 1;
}
else{
	$show_company_profile = 0;
}
?>
<script type="text/javascript">
        $(document).ready(function() {
        $('.js_company_profile').companyprofile(<?php echo $show_company_profile; ?>);
        });
</script>