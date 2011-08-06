<?php /* SVN: $Id: admin_edit.ctp 59862 2011-07-11 09:47:46Z arovindhan_144at11 $ */ ?>
<div class="cities form">
	<div>
		<h2><?php echo __l('Edit City - ').$this->Html->cText($this->request->data['City']['name'], false); ?></h2>
	</div>
	<div>
	<?php
		echo $this->Form->create('City', array('class' => 'normal','action'=>'edit','enctype' => 'multipart/form-data'));
		echo $this->Form->input('id');
   ?>
   <?php
		if (!empty($id_default_city)) {
			echo $this->Form->input('name',array('label' => __l('Name'), 'readonly' => true, 'info' => __l('You can not change default city name.')));
		} else {
			echo $this->Form->input('name',array('label' => __l('Name')));
		}
		echo $this->Form->input('country_id', array('label' => __l('Country'), 'empty' => __l('Please Select')));
		echo $this->Form->input('state_id', array('label' => __l('State'), 'empty' => __l('Please Select')));
		echo $this->Form->input('language_id', array('label' => __l('Default Language'),'empty'=> __l('Please Select'),'info' => __l('select the default language for this city. If not selected, Site default language will be set.')));
		echo $this->Form->input('latitude',array('label' => __l('Latitude')));
		echo $this->Form->input('longitude',array('label' => __l('Longitude')));
		echo $this->Form->input('code',array('label' => __l('Code')));
		echo $this->Form->input('slug', array('type' => 'hidden'));
	?>
	<fieldset class="form-block round-5">
		<legend class="round-5"><?php echo __l('Facebook Details'); ?></legend>
		<?php 
			if(empty($this->request->data['City']['fb_access_token'])):
				$fb_city_login_url = $facebookObj->getLoginUrl(array('cancel_url' => Router::url(array('controller' => $this->request->data['City']['slug'], 'action' => 'cities', 'fb_update', 'admin' => false), true), 'next' => Router::url(array('controller' => $this->request->data['City']['slug'], 'action' => 'cities', 'fb_update', 'admin' => false), true), 'req_perms' => 'email,publish_stream'));
				$update_link =  $this->Html->link(__l('Update').' '.__l('Facebook').' '.__l('Credentials'), $fb_city_login_url, array('class' => '', 'target' => '_blank', 'title' => __l('Update').' '.__l('Facebook').' '.__l('Credentials')));
				$info = __l('Facebook credentials for this city was not updated.').' '.$update_link.' '.__l('before giving Facebook Page ID');

			?>
				<span class="info"><?php echo $info;?></span>
			<?php endif;?>
		<?php echo $this->Form->input('facebook_url',array('label' =>__l('Facebook URL'))); ?>
        <?php echo $this->Form->input('facebook_page_id',array('label' =>__l('Facebook Page ID'))); ?>
	</fieldset>
	<fieldset class="form-block round-5">
		<legend class="round-5"><?php echo __l('Twitter Details'); ?></legend>
		<?php
			echo $this->Form->input('twitter_url',array('label' =>__l('Twitter URL')));
			if(Configure::read('site.city') != $this->request->data['City']['slug']):
				echo $this->Form->input('is_approved', array('label' =>__l('Approved?')));
			endif;
		?>
	</fieldset>	
    <fieldset class="form-block round-5">
		<legend class="round-5"><?php echo __l('Foursquare Details'); ?></legend>
		<?php
			echo $this->Form->input('foursquare_venue',array('label' =>__l('Venue ID')));
		?>
	</fieldset>
	<div>

    <fieldset class="form-block round-5">
		<legend class="round-5"><?php echo __l('Background Color'); ?></legend>

        <?php

			echo $this->Form->input('bgcolor', array('label' => __l('Background Color'), 'class'=>'js_colorpick', 'style' => 'background:#'.$this->request->data['City']['bgcolor'])); 
		?>
	</fieldset>  
    <fieldset class="form-block round-5">
	<legend class="round-5"><?php echo __l('Background Image'); ?></legend>
         
      
        <?php
    	   	echo $this->Form->input('Attachment.filename', array('type' => 'file', 'label' => __l('City Background Image')));
              echo $this->Form->input('is_bg_image_center', array('label' => __l('Background Image Center?'), 'type' => 'checkbox'));
            ?>
        <div class="bgimg-input-block">
		<?php
         if(!empty($this->request->data['Attachment']['id'])):

            echo $this->Form->input('OldAttachment.id',array('type' => 'checkbox', 'label' => __l('Delete?')));
            echo $this->Form->input('Attachment.id',array('type' => 'hidden', 'value' => $this->request->data['Attachment']['id']));
         
            ?>
            <div class="bg-img-subscription">
            <?php
            echo $this->Html->showImage('City', $this->request->data['Attachment'], array('dimension' => 'medium_thumb', 'alt' => sprintf(__l('[Image: %s]'), $this->Html->cText($this->request->data['City']['name'], false)), 'title' => $this->Html->cText($this->request->data['City']['name'], false), 'escape' => false ));
            ?>
            </div>
        <?php endif;?>
		</div>
            <?php
        
			echo $this->Form->input('thumb_width', array('label' => __l('Background Image Width')));
			echo $this->Form->input('thumb_height', array('label' => __l('Background Image Height')));

	   	?>
		</fieldset>       
	   	</div>
	<div class="submit-block">
		<?php echo $this->Form->submit(__l('Update'));	?>
	</div>
	<?php echo $this->Form->end(); ?>
	</div>
</div>
    <div class="clearfix" style="display:none">
        <img src="<?php echo $custom_thumb_url;?>" />
        <img src="<?php echo $original_thumb_url;?>" />
    </div> 