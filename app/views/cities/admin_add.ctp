<?php /* SVN: $Id: admin_add.ctp 59862 2011-07-11 09:47:46Z arovindhan_144at11 $ */ ?>
<div class="cities form">
	<div>
		<h2><?php echo __l('Add Cities');?></h2>
	</div>
	<div>
		<?php echo $this->Form->create('City', array('class' => 'normal','action'=>'add', 'enctype' => 'multipart/form-data'));?>
		<?php
			echo $this->Form->input('country_id', array('label' => __l('Country'),'empty'=> __l('Please Select')));
			echo $this->Form->input('state_id', array('label' => __l('State'),'empty'=> __l('Please Select')));
			echo $this->Form->input('language_id', array('label' => __l('Default Language'),'empty'=>__l('Please Select'),'info' => __l('select the default language for this city. If not selected, Site default language will be set.')));
			echo $this->Form->input('name',array('label' => __l('Name')));
			echo $this->Form->input('latitude',array('label' => __l('Latitude')));
			echo $this->Form->input('longitude',array('label' => __l('Longitude')));
			echo $this->Form->input('code',array('label' => __l('Code')));?>
			 <fieldset class="form-block round-5">
                <legend class="round-5"><?php echo __l('Facebook Details'); ?></legend>
    			<?php
        			echo $this->Form->input('facebook_url',array('label' =>__l('Facebook URL')));
					echo $this->Form->input('facebook_page_id',array('label' =>__l('Facebook Page ID'))); 
                ?>
              </fieldset>
              <fieldset class="form-block round-5">
                <legend class="round-5"><?php echo __l('Twitter Details'); ?></legend>
                <?php
        			echo $this->Form->input('twitter_url',array('label' =>__l('Twitter URL')));
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
			echo $this->Form->input('bgcolor', array('label' => __l('Background Color'), 'class'=>'js_colorpick', 'style' => 'background:#'.$this->data['City']['bgcolor'])); 	
        ?>
        </fieldset>
        <fieldset class="form-block round-5">
	        <legend class="round-5"><?php echo __l('Background Image'); ?></legend>

        <?php
			echo $this->Form->input('default_color', array('type' => 'hidden', 'value' => $this->data['City']['bgcolor']));
    	   	echo $this->Form->input('Attachment.filename', array('type' => 'file', 'label' => __l('City Background Image')));
			echo $this->Form->input('is_bg_image_center', array('label' => __l('Background Image Center?'), 'type' => 'checkbox'));
			echo $this->Form->input('thumb_width', array('label' => __l('Background Image Width')));
			echo $this->Form->input('thumb_height', array('label' => __l('Background Image Height')));
	   	?>
		</fieldset>        
	   	</div>
		<div class="submit-block">
		<?php echo $this->Form->submit(__l('Add'));?>
		</div>
		<?php echo $this->Form->end(); ?>
	</div>
</div>