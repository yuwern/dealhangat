<?php
/* SVN FILE: $Id: default.ctp 17321 2010-08-03 15:43:55Z aravindan_111act10 $ */
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.console.libs.templates.skel.views.layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @version       $Revision: 7805 $
 * @modifiedby    $LastChangedBy: AD7six $
 * @lastmodified  $Date: 2008-10-30 23:00:26 +0530 (Thu, 30 Oct 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<?php echo $this->Html->charset(), "\n";?>
	<title><?php echo Configure::read('site.name');?> | <?php echo $this->Html->cText($title_for_layout, false);?></title>
	<?php
		echo $this->Html->meta('icon'), "\n";
		echo $this->Html->meta('keywords', $meta_for_layout['keywords']), "\n";
		echo $this->Html->meta('description', $meta_for_layout['description']), "\n";
		require_once('_head.inc.ctp');
		echo $this->Asset->scripts_for_layout();
	?>
</head>
<?php
	$align = '';
	$subscription_bgcolor = '';
    $subscriptionAttachment = $this->Html->getSubscriptionAttachment();
	$height = Configure::read('thumb_size.subscription_home_thumb.height');
	$width = Configure::read('thumb_size.subscription_home_thumb.width');
	if(!empty($height) && !empty($width))
	{
		$image_options = array(
                    'dimension' => 'subscription_home_thumb',
                    'class' => '',
                    'type' => 'jpg'
                );	
	} else {
		$image_options = array(
                    'dimension' => 'original',
                    'class' => '',
                    'type' => 'jpg'
                );	
	}
    $subscription_attachment_url = $this->Html->url($this->Html->getImageUrl('PageLogo', $subscriptionAttachment['Attachment'], $image_options));
	if(!empty($subscriptionAttachment)):
		 if(configure::read('subscription.is_bg_image_center')):
				$align = 'no-repeat center top';
			else:
				$align = 'repeat fixed left top';
			endif;
			$subscription_bgcolor = '';
		else:
		 	$subscription_bgcolor = configure::read('subscription.bgcolor');		
		endif;
 	$bgimage =  !empty($subscriptionAttachment) ? 'background:url('.$subscription_attachment_url.') '.$align.'':''.';'; 
	$bgcolor =	 !empty($subscription_bgcolor) ? 'background-color:#'.$subscription_bgcolor.'':''.';';	
	 
?>
<body class="subscription" style="<?php echo $bgimage.' '.$bgcolor;?>">
	<div id="<?php echo $this->Html->getUniquePageId();?>">
       <div class="clearfix">
            <h1>
				<?php
					$attachment = $this->Html->siteLogo();
					if (!empty($attachment['Attachment'])):
						echo $this->Html->link($this->Html->showImage('SiteLogo', $attachment['Attachment'], array('dimension' => 'site_logo_thumb', 'alt' => sprintf(__l('[Image: %s]'), Configure::read('site.name')), 'title' => Configure::read('site.name'), 'type' => 'png')), array('controller' => 'deals', 'action' => 'index', 'admin' => false), array('escape' => false));
					endif;
				?>
			</h1>
            <p class="hidden-info"><?php echo __l('Collective Buying Power');?></p>
        </div>

        <div id="main" class="clearfix">
          <?php
				if ($this->Session->check('Message.error')):
        				echo $this->Session->flash('error');
        		endif;
        		if ($this->Session->check('Message.success')):
        				echo $this->Session->flash('success');
        		endif;
				if ($this->Session->check('Message.flash')):
						echo $this->Session->flash();
				endif;
			?>
			<?php echo $content_for_layout;?>
		</div>
		<div id="footer">
			<div class="footer-wrapper-inner clearfix">
				<div id="agriya">
					<div class="clearfix"><p>&copy;<?php echo date('Y');?> <?php echo $this->Html->link(Configure::read('site.name'), Router::Url('/',true), array('title' => Configure::read('site.name'), 'escape' => false));?>. <?php echo __l('All rights reserved');?>.</p></div>
				</div></div>
				<div class="footer-r">
					<?php echo $this->element('subscription-index', array('cache' => array('config' => 'site_element_cache'))); ?>
				</div>
			</div>
		</div>
	</div>
	<?php echo $this->element('site_tracker', array('cache' => array('config' => 'site_element_cache'), 'plugin' => 'site_tracker')); ?>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
