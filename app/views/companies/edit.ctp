<?php /* SVN: $Id: edit.ctp 55560 2011-06-03 13:41:06Z anandam_023ac09 $ */ ?>
<?php if($this->Auth->user('user_type_id') == ConstUserTypes::Company):?> 
	<?php if(empty($this->request->params['isAjax']) or !$this->request->params['isAjax']):?>		 
		 <?php echo $this->element('js_tiny_mce_setting', array('cache' => array('config' => 'site_element_cache')));?>
   <?php endif; ?>
<?php endif; ?>
<div class="companies form js-responses">
<div class="js-tabs">
	<ul class="clearfix">
		<li><?php echo $this->Html->link(__l('My Profile'), '#my-profile'); ?></li>
		 <li><?php echo $this->Html->link(__l('Branch Addresses'), array('controller' => 'company_addresses', 'action' => 'index', 'admin' => false), array('title' => __l('Branch Addresses')));?></li>
		<li><?php echo $this->Html->link(__l('Change Password'),array('controller'=> 'users', 'action'=>'change_password'),array('title' => __l('Change Password'))); ?></li>
        <li><?php echo $this->Html->link(__l('Privacy Settings'), array('controller' => 'user_permission_preferences', 'action' => 'edit', $this->Auth->user('id'), 'admin' => false), array('title' => __l('Privacy Settings')));?></li>
		<li><?php echo $this->Html->link(__l('My Connections'), array('controller' => 'users', 'action' => 'profile_image', 'connect' => 'linked_accounts', $this->Auth->user('id'), 'admin' => false), array('title' => 'My Connections', 'rel'=> '#Connect')); ?></li>
        <!--<li><?php //echo $this->Html->link(__l('My API'), array('controller' => 'users', 'action' => 'my_api', $this->Auth->user('id'), 'admin' => false), array('title' => __l('Request API Key')));?></li>-->
	</ul>
	<div id='my-profile' class="clearfix">
		<?php echo $this->Form->create('Company', array('class' => 'normal js-company-map js-ajax-form', 'enctype' => 'multipart/form-data'));?>
			<div>
				<h2><?php echo __l('Edit Company');?></h2>
			</div>
           <fieldset class="form-block round-5">
               <legend class="round-5"><?php echo __l('Account'); ?></legend>
                <?php
                    echo $this->Form->input('id');
                    echo $this->Form->input('name',array('label' => __l('Company Name')));
                    echo $this->Form->input('phone',array('label' => __l('Phone')));
                    echo $this->Form->input('url',array('label' => __l('URL'), 'info' => __l('eg. http://www.example.com')));
				?>
			</fieldset>
			<?php if(Configure::read('company.is_user_can_withdraw_amount')): ?>
				<fieldset class="form-block round-5">
	               <legend class="round-5"><?php echo __l('PayPal'); ?></legend>
					<?php echo $this->Form->input('User.UserProfile.paypal_account', array('label' => __l('PayPal Account'))); ?>
				</fieldset>
			<?php endif; ?>
           <fieldset class="form-block round-5">
               <legend class="round-5"><?php echo __l('Address'); ?></legend>
					<?php
                        echo $this->Form->input('address1',array('label' => __l('Address1')));
                        echo $this->Form->input('address2',array('label' => __l('Address2')));
                        echo $this->Form->input('country_id',array('label' => __l('Country')));
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
						echo $this->Form->input('is_company_profile_enabled', array('label' => __l('Enable Company Profile'), 'class' => 'js_company_profile_enable', 'info' => __l('Whether other users can view the company profile or not')));
						?>
                        <div class = "js-company_profile_show">
							<?php echo $this->Form->input('Company.company_profile', array('label' => __l('Company Profile'),'type' => 'textarea', 'class' => 'js-editor'));   ?>
                        </div>
			</fieldset>
           <fieldset class="form-block round-5">
               <legend class="round-5"><?php echo __l('Logo'); ?></legend>
                <div class="company-profile-image">
					<?php echo $this->Html->getUserAvatarLink($this->request->data['User'], 'normal_thumb');
					echo $this->Form->input('UserProfile.language_id', array('empty' => __l('Please Select'),'label' => __l('Profile Language'), 'value' => $this->request->data['User']['UserProfile']['language_id'], 'info'=>__l('This will be the default site languge after logged in')));
					?>
                </div>               
				<?php 
                    echo $this->Form->input('UserAvatar.filename', array('type' => 'file','size' => '33', 'label' => __l('Upload Logo'),'class' =>'browse-field'));
                    echo $this->Form->input('User.id',array('type' => 'hidden'));
                ?>
           </fieldset>
        <div class="">
		<fieldset class="form-block round-5">
			 <?php
					echo $this->Form->input('latitude',array('type' => 'hidden', 'id'=>'latitude'));
					echo $this->Form->input('longitude',array('type' => 'hidden', 'id'=>'longitude'));
			?>
			
			<legend class="round-5"><?php echo __l('Locate Yourself on Google Maps'); ?></legend>
				<div class="show-map">
					<div id="js-map-container"><?php echo __l('Please update address info to generate location Map'); ?></div>
					<span class="map-info info"><?php echo __l('You can change the google map zooming level here, else default zooming level will be taken.'); ?></span>
				</div>
				
			<?php
				$map_zoom_level = !empty($this->request->data['Company']['map_zoom_level']) ? $this->request->data['Company']['map_zoom_level'] : Configure::read('GoogleMap.static_map_zoom_level');
				echo $this->Form->input('Company.map_zoom_level',array('type' => 'hidden','value' => $map_zoom_level,'id'=>'zoomlevel'));
			?>
		</fieldset>
		</div>
	    <div class="submit-block clearfix">
        <?php
        	echo $this->Form->submit(__l('Update'));
        ?>
        </div>
        <?php
        	echo $this->Form->end();
        ?>
	</div>
</div>
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