<?php
	$this->Html->css('mobile/mobile_reset', null, array('inline' => false));
	$this->Html->css('jquery.autocomplete', null, array('inline' => false));
	$this->Html->css('jquery-ui-1.7.1.custom', null, array('inline' => false));
	$this->Html->css('ui.timepickr', null, array('inline' => false));
	$this->Html->css('colorbox', null, array('inline' => false));
	$this->Html->css('mobile/mobile_style', null, array('inline' => false));
	
		$this->Javascript->codeBlock('var cfg = ' . $this->Javascript->object($js_vars_for_layout) , array('inline' => false));
		$this->Javascript->link('libs/jquery', false);
		$this->Javascript->link('libs/jquery.form', false);
		$this->Javascript->link('libs/jquery.blockUI', false);
		$this->Javascript->link('libs/jquery.livequery', false);
		$this->Javascript->link('libs/jquery.uploader', false);	
		$this->Javascript->link('libs/AC_RunActiveContent', false);
		$this->Javascript->link('libs/jquery.fuploader', false);
		$this->Javascript->link('libs/jquery.metadata', false);
		$this->Javascript->link('libs/jquery.autocomplete', false);
		$this->Javascript->link('libs/jquery-ui-1.7.2.custom.min', false);
		$this->Javascript->link('libs/jquery.countdown', false);
		$this->Javascript->link('libs/jquery.timepickr', false);
		$this->Javascript->link('libs/jquery.overlabel', false);
		$this->Javascript->link('libs/jquery.colorbox', false);
		$this->Javascript->link('libs/date.format', false);
		$this->Javascript->link('libs/jquery.cookie', false);
		$this->Javascript->link('libs/jquery.truncate-2.3', false);
		$this->Javascript->link('libs/jquery.jmap', false);
		$this->Javascript->link('libs/jquery.address-1.2.1', false);
		$this->Javascript->link('libs/jquery.flash', false);
		$this->Javascript->link('libs/jquery.showcase', false);
    	$this->Javascript->link('common', false);
    
?>
