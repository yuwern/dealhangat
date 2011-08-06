<?php /* SVN: $Id: admin_edit.ctp 55560 2011-06-03 13:41:06Z anandam_023ac09 $ */ ?>
<?php echo $this->element('js_tiny_mce_setting', array('cache' => array('config' => 'site_element_cache')));?>
<div class="companies form clearfix js-responses js-response">
	<h2><?php echo __l('Edit Company');?></h2>
	<?php
		echo $this->Form->create('Company', array('class' => 'normal js-company-map'));
		echo $this->Form->input('id');
	?>
	<fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Account'); ?></legend>
	<?php
		echo $this->Form->input('name',array('label' => __l('Name')));
		echo $this->Form->input('phone',array('label' => __l('Phone')));
		echo $this->Form->input('url',array('label' => __l('URL'), 'info' => __l('eg. http://www.example.com')));
		echo $this->Form->input('User.email',array('label' => __l('Email')));
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
		echo $this->Form->error('state_id');
		echo $this->Form->autocomplete('City.name', array('label' => __l('City'), 'acFieldKey' => 'City.id', 'acFields' => array('City.name'), 'acSearchFieldNames' => array('City.name'), 'maxlength' => '255'));		
		echo $this->Form->error('city_id');
		echo $this->Form->input('zip',array('label' => __l('Zip')));
		
		
					
	?>
	</fieldset>	
	<fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Company Profile'); ?></legend>
	<?php
			echo $this->Form->input('is_company_profile_enabled', array('label' => __l('Enable company profile'), 'class' => 'js_company_profile js_company_profile_enable', 'info' => __l('Whether other users can view the company profile or not'))); ?>
           <div class = "js-company_profile_show">		 		
            <?php echo $this->Form->input('Company.company_profile', array('label' => __l('Company Profile'),'type' => 'textarea', 'class' => 'js-editor'));?>
		</div>
	</fieldset>
    <fieldset class="form-block round-5">
		<legend class="round-5"><?php echo __l('Paypal Account'); ?></legend>
            <?php echo $this->Form->input('User.UserProfile.paypal_account');?>
	</fieldset>
	<fieldset class="form-block round-5">
		<legend class="round-5"><?php echo __l('Branch Address'); ?></legend>
		<div class="add-block">
			<?php echo $this->Html->link(__l('Add address'),array('controller' => 'company_addresses', 'action' => 'add', 'company' => $this->request->data['Company']['slug']),array('title'=>__l('Add address'),'class' => "add")); ?>
		</div>
			<ol class="list clearfix">
				<?php
				$companyAddresses = $this->request->data['CompanyAddress'];
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

									<?php echo $this->Html->link(__l('Edit'), array('controller' => 'company_addresses', 'action' => 'edit', $companyAddress['id']), array('class' => 'edit js-inline-edit', 'title' => __l('Edit')));?>

									<?php echo $this->Html->link(__l('Delete'), array('controller' => 'company_addresses', 'action' => 'delete', $companyAddress['id']), array('class' => 'delete js-on-the-fly-delete', 'title' => __l('Delete')));?>

							</div>
							<address>
								<?php echo $this->Html->cText($companyAddress['address1']);?>
								<?php
									if(!empty($companyAddress['address2'])):
										 echo $this->Html->cText($companyAddress['address2']);
									endif;
								?>
								<?php echo $this->Html->cText($companyAddress['City']['name']);?>
                                <?php echo $this->Html->cText($companyAddress['State']['name']);?>
								<?php echo $this->Html->cText($companyAddress['Country']['name']);?>
								<?php echo $this->Html->cText($companyAddress['zip']);?>
							</address>
							<span class="phone"><?php echo  !empty($companyAddress['phone'])? $this->Html->cText($companyAddress['phone']) : '&nbsp;';?></span>
							<span class="url"><?php echo  !empty($companyAddress['url'])? $this->Html->cText($companyAddress['url']) : '&nbsp;';?></span>
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
	</fieldset>
	<div class="">
	<fieldset class="form-block round-5">
			<legend class="round-5"><?php echo __l('Locate yourself on google maps'); ?></legend>
			<div class="show-map" style="">			
			<div id="js-map-container"></div>
			<span class="map-info info"><?php echo __l('You can change the google map zooming level here, else default zooming level will be taken.'); ?></span>
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
		<?php echo $this->Form->submit(__l('Update')); ?>
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