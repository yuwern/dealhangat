<?php
	$this->Html->css('reset', null, array('inline' => false));
	$this->Html->css('widget', null, array('inline' => false));	
	$this->Html->css('style', null, array('inline' => false));
	
	if (isset($javascript)):
		$this->Javascript->codeBlock('var cfg = ' . $this->Javascript->object($js_vars_for_layout) , array('inline' => false));
		$this->Javascript->link('libs/jquery', false);
		$this->Javascript->link('libs/jcarousellite_1.0.1', false);
		
		if (env('HTTPS')) {
			$this->Javascript->link('https://platform.twitter.com/widgets.js', false);
		} else {
			$this->Javascript->link('http://platform.twitter.com/widgets.js', false);
		}
    	$this->Javascript->link('common', false);
    endif;
?>