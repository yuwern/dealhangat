<?php
/* SVN FILE: $Id: admin.ctp 54451 2011-05-24 12:26:17Z arovindhan_144at11 $ */
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
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(), "\n";?>
	<title><?php echo Configure::read('site.name');?> | <?php echo sprintf(__l('Admin - %s'), $this->Html->cText($title_for_layout, false)); ?></title>
	<?php
		echo $this->Html->meta('icon'), "\n";
		echo $this->Html->meta('keywords', $meta_for_layout['keywords']), "\n";
		echo $this->Html->meta('description', $meta_for_layout['description']), "\n";
		require_once('_head.inc.ctp');
		echo $this->Asset->scripts_for_layout();
	?>
</head>
<?php	
	
	if (!empty($city_attachment['id']) && empty($this->request->params['requested']) && $this->request->params['controller'] != 'images' && empty($_SESSION['city_attachment'])):
		$_SESSION['city_attachment'] =  $this->Html->url($this->Html->getImageUrl('City', $city_attachment, array('dimension' => 'original')));
	endif; 		
?>
<body>
	<div id="<?php echo $this->Html->getUniquePageId();?>" class="content admin-content">
		<div id="header" class="clearfix">
		    <div id="header-content">
			<div class="clearfix">
			<h1>
				<?php echo $this->Html->link(Configure::read('site.name'), array('controller' => 'deals', 'action' => 'index', 'admin' => false), array('title' => Configure::read('site.name'))); ?>
			</h1>
		
			 <div class="admin-bar clearfix">
				<?php $title = ' title="' . strftime(Configure::read('site.datetime.tooltip') , strtotime('now')) . ' ' . Configure::read('site.timezone_offset') . '"'; ?>
                <div class="clearfix"><h3><?php echo __l('Current time: '); ?></h3><span <?php echo $title; ?>><?php echo strftime(Configure::read('site.datetime.format')); ?></span></div>
        		<div class="clearfix"><h3><?php echo __l('Last login: '); ?></h3><?php echo $this->Html->cDateTimeHighlight($this->Auth->user('last_logged_in_time')); ?></div>
    		</div>
		

			</div>
			  <div class="menu-block clearfix">
    			<ul class="menu admin-menu">
    				  <li><?php echo $this->Html->link(__l('Home'), array('controller' => 'deals', 'action' => 'index','admin' => false), array('escape' => false, 'title' => __l('Home')));?></li>
    				   <?php $class = (($this->request->params['controller'] == 'user_profiles') && ($this->request->params['action'] == 'my_account')) ? ' class="active"' : null; ?>
                        <li <?php echo $class;?>><?php echo $this->Html->link(__l('My Account'), array('controller' => 'user_profiles', 'action' => 'user_account', $this->Auth->user('id')), array('title' => __l('My Account')));?></li>
    					<li><?php echo $this->Html->link(__l('Logout'), array('controller' => 'users', 'action' => 'logout'), array('title' => __l('Logout')));?></li>
    				</ul>
				
            	<div class="admin-sub-header">
            	 <p class="admin-welcome-info"><?php echo __l('Welcome, ').$this->Html->link($this->Auth->user('username'), array('controller' => 'users', 'action' => 'stats', 'admin' => true),array('title' => $this->Auth->user('username'))); ?></p>
						<?php echo $this->element('lanaguge-change-block', array('cache' => array('config' => 'site_element_cache')));?>
						<?php echo $this->element('admin-cities-filter', array('cache' => array('config' => 'site_element_cache')));?>			     
    			</div>

            	</div>
		</div>
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
			 <div class="side1-tl">
                <div class="side1-tr">
                  <div class="side1-tm"> </div>
                </div>
            </div>
            <div class="side1-cl">
                <div class="side1-cr">
                    <div class="block1-inner clearfix">
                        <div class="admin-sideone js-corner round-10">
                            <?php
                                echo $this->element('admin-sidebar');
                            ?>
                        </div>
                        <div class="admin-sidetwo js-corner round-10">
                			<?php echo $content_for_layout;?>
            			</div>
        			</div>
    			</div>
    			</div>
                <div class="side1-bl">
                    <div class="side1-br">
                      <div class="side1-bm"> </div>
                    </div>
                </div>
		</div>
	    <div id="agriya" class="clearffix">			
          	<p class="copy">&copy;<?php echo date('Y');?> <?php echo $this->Html->link(Configure::read('site.name'), Router::Url('/',true), array('title' => Configure::read('site.name'), 'escape' => false));?>. <?php echo __l('All rights reserved');?>.</p>
        </div>
	</div>
	<?php echo $this->element('site_tracker');?>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
