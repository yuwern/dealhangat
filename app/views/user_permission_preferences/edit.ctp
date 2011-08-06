<?php /* SVN: $Id: edit.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<div class="userPermissionPreferences form js-responses">
	<div class="js-permission-responses">
	<h2><?php echo sprintf(__l('Edit Privacy Settings - %s'), $this->request->data['User']['username']); ?></h2>
    <?php
		echo $this->Form->create('UserPermissionPreference', array('class' => 'normal js-ajax-form {"container" : "js-permission-responses"}'));
		echo $this->Form->input('User.id', array('type' => 'hidden'));
		echo $this->Form->input('User.username', array('type' => 'hidden'));
	?>
	<?php			
			foreach($userPreferenceCategories as $userPreferenceCategory):
	?>
	<fieldset class="form-block round-5">
			<legend class="round-5"><?php echo $this->Html->cText($userPreferenceCategory['UserPreferenceCategory']['name']); ?></legend>				
				<h3><?php echo $this->Html->cText($userPreferenceCategory['UserPreferenceCategory']['description']); ?></h3>
	<?php
				
				foreach ($this->request->data['UserPermissionPreference'] as $key => $val):
                    $isSiteSetting = Configure::read($key);
                    if(!$isSiteSetting) :
                        continue;
                    endif;
					$tmp_privacy = $privacyTypes;
                    if('Profile-is_allow_comment_add' == $key) :
                        unset($tmp_privacy[ConstPrivacySetting::EveryOne]);
                    endif;
					if('Profile-is_receive_email_for_new_comment' == $key) :
                        unset($tmp_privacy[ConstPrivacySetting::EveryOne]);
                    endif;
					$field = explode('-', $key);
					if ($field[0] == $userPreferenceCategory['UserPreferenceCategory']['name']):
						if ($field[1] != 'is_show_captcha'):
							echo $this->Form->input($key, array('type' => 'select', 'label' => Inflector::humanize(str_replace('is_','',$field[1])) , 'options' => $tmp_privacy));
						else:
							echo $this->Form->input($key, array('type' => 'select','label' => Inflector::humanize(str_replace('is_','',$field[1])), 'options' => array('1' => __l('Yes'), '0' => 'No')));
						endif;
					endif;
				endforeach;
	?>
    </fieldset>
    <?php
			endforeach;
			?>
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