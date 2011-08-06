<?php
	echo $this->Html->css('touch/jquery.mobile-1.0b1.min', null); 
	echo $this->Html->css('touch/style', null); 

	$this->Javascript->link('touch/jquery-1.6.1', false);
	$this->Javascript->link('touch/jquery.mobile-1.0b1', false);
	$this->Javascript->link('touch/common', false);	
?>