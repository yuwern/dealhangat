<?php /* SVN: $Id: add.ctp 4730 2010-05-14 13:50:53Z mohanraj_109at09 $ */ ?>
<div class="companyAddresses form clearfix">
<?php echo $this->Form->create('CompanyAddress', array('class' => 'normal js-company-map'));?>
	<fieldset class="form-block round-5">
	   <legend class="round-5"><?php echo __l('Address'); ?></legend>
		<?php
			echo $this->Form->input('address1', array('id'=> 'CompanyAddress1'));
			echo $this->Form->input('address2');
			echo $this->Form->autocomplete('State.name', array('label' => __l('State'), 'acFieldKey' => 'State.id', 'acFields' => array('State.name'), 'acSearchFieldNames' => array('State.name'), 'maxlength' => '255'));
			echo $this->Form->autocomplete('City.name', array('id'=> 'CityName','label' => __l('City'), 'acFieldKey' => 'City.id', 'acFields' => array('City.name'), 'acSearchFieldNames' => array('City.name'), 'maxlength' => '255'));
			echo $this->Form->input('country_id', array('options' => $countries, 'empty' => __l('Please Select')));
			echo $this->Form->input('phone');
			echo $this->Form->input('zip');
			//echo $this->Form->input('url');
			echo $this->Form->input('url', array('label' => __l('URL'), 'info' => __l('eg. http://www.example.com')));
			echo $this->Form->input('company_id', array('type' => 'hidden'));

		?>
	</fieldset>
	<fieldset class="form-block round-5">
        	<legend class="round-5"><?php echo __l('Locate yourself on google maps'); ?></legend>
			<div class="show-map" style="">			
			<div id="js-map-container"></div>
			<span class="map-info info"><?php echo __l('You can change the google map zooming level here, else default zooming level will be taken.'); ?></span>
			</div>
		 <?php
				echo $this->Form->input('latitude',array('type' => 'hidden','id'=>'latitude'));
				echo $this->Form->input('longitude',array('type' => 'hidden','id'=>'longitude'));
		?>
		<?php
			$map_zoom_level = !empty($this->request->data['Company']['map_zoom_level']) ? $this->request->data['Company']['map_zoom_level'] : Configure::read('GoogleMap.static_map_zoom_level');
			echo $this->Form->input('Company.map_zoom_level',array('type' => 'hidden','value' => $map_zoom_level,'id'=>'zoomlevel'));
		?>
	</fieldset>
	<div class="submit-block clearfix">
        <?php echo $this->Form->submit(__l('Update'));?>
    	<div class="cancel-block">
    	   <?php echo $this->Html->link(__l('Cancel'), array('controller' => 'companies', 'action'=> 'edit', $this->request->data['Company']['id']), array('title' => __l('Cancel'),'class' => 'cancel-button'));?>
    	</div>
	</div>
	 <?php echo $this->Form->end();?>
</div>
