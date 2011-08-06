<?php 
	if(!empty($this->request->params['isAjax'])):
		echo $this->element('flash_message');
	endif;
?>
<div class="js-responses">
<?php 
if(!empty($settings_category['SettingCategory']['description'])):
?>
	<div class=" info-details"><?php echo $settings_category['SettingCategory']['description'];?> </div>
<?php endif;?>
<?php
	if(!empty($settings)):
		echo $this->Form->create('Setting', array('action' => 'edit', 'class' => 'normal js-ajax-form'));
			echo $this->Form->input('setting_category_id', array('label' => __l('Setting Category'),'type' => 'hidden'));
		// hack to delete the thumb folder in img directory
        if($settings[0]['SettingCategory']['name'] == 'Images'):
        	echo $this->Form->input('delete_thumb_images', array('type' => 'hidden', 'value' => '1'));
        endif;
		$inputDisplay = 0;
    	foreach ($settings as $setting):
		
			if($setting['Setting']['id'] != '227'):	// API Access Hidden
				$field_name = explode('.', $setting['Setting']['name']);
				if(isset($field_name[2]) && ($field_name[2] == 'is_not_allow_resize_beyond_original_size' || $field_name[2] == 'is_handle_aspect')){
					continue;
				}
				$options['type'] = $setting['Setting']['type'];
				$options['value'] = $setting['Setting']['value'];
				$options['div'] = array('id' => "setting-{$setting['Setting']['name']}");
				if($options['type'] == 'checkbox' && $options['value']):
					$options['checked'] = 'checked';
				endif;
				if($options['type'] == 'select'):
					$selectOptions = explode(',', $setting['Setting']['options']);
					$setting['Setting']['options'] = array();
					if(!empty($selectOptions)):
						foreach($selectOptions as $key => $value):
							if(!empty($value)):
								$setting['Setting']['options'][trim($value)] = trim($value);
							endif;
						endforeach;
					endif;
					$options['options'] = $setting['Setting']['options'];
				endif;
					if($setting['Setting']['id'] == '188' && !($ssl_enable)){
						$options['disabled'] = 'disabled';
					}
					if($setting['Setting']['id'] == '256' || $setting['Setting']['id'] == '259' || $setting['Setting']['id'] == '257') :
				?>
					<fieldset class="form-block round-5">
						<legend class="round-5">
							<?php
								if($setting['Setting']['id'] == '256') :
									echo __l('General'); 
								elseif($setting['Setting']['id'] == '259') :
									echo __l('Commission'); 
								elseif($setting['Setting']['id'] == '257') :
									echo __l('Withdrawal'); 
								endif;
							?>
						</legend>
					  <?php
					if($settings[0]['SettingCategory']['name'] == 'Affiliate' && $setting['Setting']['id'] == '259'):
					?>
					<div class="add-block affiliate-links">
					<?php
					echo $this->Html->link(__l('Commission Settings'), array('controller' =>'affiliate_types', 'action' => 'edit'), array('class' => 'affiliate-settings', 'title' => __l('Here you can update and modify affiliate types')));
					?>
					</div>
					<?php
					endif;
					?>
				<?php
				endif;
				if($setting['Setting']['id'] == '110' || $setting['Setting']['id'] == '266' || $setting['Setting']['id'] == '265' || $setting['Setting']['id'] == '194' || $setting['Setting']['id'] == '234' || $setting['Setting']['id'] == '239' || $setting['Setting']['id'] == '184' || $setting['Setting']['id'] == '192' || $setting['Setting']['id'] == '151'  || $setting['Setting']['id'] == '303' || $setting['Setting']['id'] == '305' || $setting['Setting']['id'] == '309') :
				?>
				  <fieldset class="form-block round-5">
						<legend class="round-5">
							<?php
								if($setting['Setting']['id'] == '265') :
									echo __l('Refer Master settings'); 						
								elseif($setting['Setting']['id'] == '110') :
									echo __l('Refer and Get Amount Settings');
								elseif($setting['Setting']['id'] == '266') :
									echo __l('Refer and Get Refund / Get Amount - Setting');
								elseif($setting['Setting']['id'] == '184' || $setting['Setting']['id'] == '151' || $setting['Setting']['id'] == '309') :
									echo __l('Other Info');
								elseif($setting['Setting']['id'] == '239' || $setting['Setting']['id'] == '192' || $setting['Setting']['id'] == '303') :
									echo __l('Application Info');
								elseif($setting['Setting']['id'] == '194' || $setting['Setting']['id'] == '234' || $setting['Setting']['id'] == '305') :
									echo __l('Credentials');	
								endif;
							?>
						</legend>
                        
				<?php
					if($settings_category['SettingCategory']['id'] == '8' && $setting['Setting']['id'] == '234'): 
					?>
						<div class=" info-details"><?php echo __l('Here you can update Facebook credentials . Click this link and Follow the steps. Please make sure that you have updated the API Key and Secret before you click this link.'); ?> </div>
					<?php
					elseif($settings_category['SettingCategory']['id'] == 18 && $setting['Setting']['id'] == '194'):
					?>
                    	<div class=" info-details"><?php echo __l('Here you can update Twitter credentials like Access key and Accss Token. Click this link and Follow the steps. Please make sure that you have updated the Consumer Key and  Consumer secret before you click this link.'); ?> </div>
					<?php
                    elseif($settings_category['SettingCategory']['id'] == 29 && $setting['Setting']['id'] == '305'):
					?>
                    	<div class=" info-details"><?php echo __l('Here you can update Foursquare credentials like User ID and Accss Token. Click this link and Follow the steps. Please make sure that you have updated the Consumer Key and  Consumer secret before you click this link.'); ?> </div>
					<?php
                    endif;
					if($setting['Setting']['id'] == '194') :
						echo $this->Html->link(__l('Update Twitter Credentials'), array('controller' => 'users', 'action' => 'login',  'type'=> 'twitter', 'admin'=>false), array('class' => 'twitter-link', 'title' => __l('Here you can update Twitter credentials like Access key and Accss Token. Click this link and Follow the steps. Please make sure that you have updated the Consumer Key and  Consumer secret before you click this link.')));	
					elseif($setting['Setting']['id'] == '234') :
						echo $this->Html->link(__l('Update Facebook Credentials'), $fb_login_url, array('class' => 'facebook-link', 'title' => __l('Here you can update Facebook credentials . Click this link and Follow the steps. Please make sure that you have updated the API Key and Secret before you click this link.')));
					elseif($setting['Setting']['id'] == '305') :
			 			echo $this->Html->link(__l('Update Foursquare Credentials'), array('controller' => 'users', 'action' => 'login',  'type'=> 'foursquare', 'admin'=>false), array('class' => 'foursquare-link', 'title' => __l('Here you can update Foursquare credentials . Click this link and Follow the steps. Please make sure that you have updated the API Key and Secret before you click this link.')));
					endif;	
				endif;
				if($setting['Setting']['id'] == '194' || $setting['Setting']['id'] == '195' || $setting['Setting']['id'] == '234' || $setting['Setting']['id'] == '235' || $setting['Setting']['id'] == '305' || $setting['Setting']['id'] == '306'):
				$options['readonly'] = TRUE;		
				endif;				
				if($setting['Setting']['name'] == 'site.language'):
					$options['options'] = $this->Html->getLanguage();				
				endif;
				if($setting['Setting']['name'] == 'site.timezone_offset'):
					$options['options'] = $timezoneOptions;				
				endif;
				if($setting['Setting']['name'] == 'site.city'):
					$options['options'] = $cityOptions;
				endif;
				if($setting['Setting']['name'] == 'site.currency_id'):
					$options['options'] = $this->Html->getCurrencies();	
				endif;
				if($setting['Setting']['name'] == 'site.paypal_currency_converted_id'):
					$options['options'] = $this->Html->getSupportedCurrencies();	
				endif;
				$options['label'] = $setting['Setting']['label'];
				if ($setting['SettingCategory']['name'] == 'Images' && $inputDisplay == 0):
					$options['class'] = 'image-settings';
					echo '<div class="outer-image-settings clearfix">';
				elseif($setting['SettingCategory']['name'] == 'Images'):
					$options['class'] = 'image-settings image-settings-height';
				endif;
				//barcode
				if($setting['Setting']['name'] == 'barcode.symbology'):
					$options['options'] = $barcodeSymbologies;
				endif;
				if ($setting['Setting']['name'] == 'user.referral_deal_buy_time' || $setting['Setting']['name'] == 'user.referral_cookie_expire_time'):
					$options['after'] = __l('hrs') . '<span class="info">' . $setting['Setting']['description'] . '</span>';
				endif;
				if (!empty($setting['Setting']['description']) && empty($options['after'])):
					$options['help'] = "{$setting['Setting']['description']}";
				endif;
				if($setting['Setting']['name'] == 'Site.logo'):
					 $options['after'] = '<div class="settings-site-logo">'.$this->Html->showImage('SiteLogo', $attachment['SiteLogo'], array('full_url' => true,'dimension' => 'site_logo_thumb', 'alt' => sprintf(__l('[Image: %s]'), "SiteLogo"), 'title' =>  __l('SiteLogo'), 'type' => 'png', 'class' => 'siteLogo')).'</div>';
				endif;
				//default account
				echo $this->Form->input("Setting.{$setting['Setting']['id']}.name", $options);
				if($setting['SettingCategory']['name'] == 'Images' && !$inputDisplay++):
					echo '<div class="input image-separator">X</div>';
				endif;
				if($setting['SettingCategory']['name'] == 'Images' && $inputDisplay == 2):
					echo '</div>';
				endif;
				if($setting['Setting']['id'] == '260' || $setting['Setting']['id'] == '258' || $setting['Setting']['id'] == '262' || $setting['Setting']['id'] == '169' || $setting['Setting']['id'] == '268' || $setting['Setting']['id'] == '265' || $setting['Setting']['id'] == '195' || $setting['Setting']['id'] == '235' || $setting['Setting']['id'] == '150' || $setting['Setting']['id'] == '220' || $setting['Setting']['id'] == '193' || $setting['Setting']['id'] == '200' || $setting['Setting']['id'] == '304' || $setting['Setting']['id'] == '306' || $setting['Setting']['id'] == '310') :
				$isSetlegend = 0;
					?>
					</fieldset>
				<?php
				endif;
				$inputDisplay = ($inputDisplay == 2) ? 0 : $inputDisplay;
				unset($options);
			endif;
		endforeach;
		if(!empty($beyondOriginals)){
            echo $this->Form->input('not_allow_beyond_original', array('label' => __l('Not Allow Beyond Original'),'type' => 'select', 'multiple' => 'multiple', 'options' => $beyondOriginals));
        }
        if(!empty($aspects)){
            echo $this->Form->input('allow_handle_aspect', array('label' => __l('Allow Handle Aspect'),'type' => 'select', 'multiple' => 'multiple', 'options' => $aspects));
        } ?>
    <div class="submit-block clearfix">
    <?php	echo $this->Form->end('Update'); ?>
    </div>
    <?php
	else:
?>
		<div class="notice"><?php echo __l('No settings available'); ?></div>
<?php
	endif;
?>
</div>