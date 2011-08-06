<?php /* SVN: $Id: index.ctp 59566 2011-07-08 10:39:56Z aravindan_111act10 $ */ ?>
<?php $count = 1;?>
<div class="deal-view-inner-block clearfix">
	<div class="main-shad">&nbsp;</div>
	<div class="side1">
		<?php
			foreach($deals as $deal):		    		           
				echo $this->element('../deals/view', array('deal' => $deal, 'count' => $count, 'get_current_city' => $get_current_city, 'cache' => array('config' => 'site_element_cache', 'key' => $get_current_city)));
				$count++;
			endforeach;
		?>
	</div>
	<?php echo $this->element('../deals/sidebar', array('deal' => $deal, 'count' => $count, 'get_current_city' => $get_current_city, 'cache' => array('config' => 'site_element_cache', 'key' => $get_current_city))); ?>
</div>