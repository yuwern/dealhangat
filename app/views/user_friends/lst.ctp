<?php /* SVN: $Id: lst.ctp 54451 2011-05-24 12:26:17Z arovindhan_144at11 $ */ ?>
<div class="userFriends lst">
    <div class="main-content-block js-corner round-5">
		<h2><?php echo __l('My Friends');?></h2>
        <p class="add-block people-information"><?php echo $this->Html->link(sprintf(__l('Find people you know on %s'), Configure::read('site.name')), array('controller' => 'user_friends', 'action' => 'import'), array('class' => 'add people-find js-people-find', 'title' => sprintf(__l('Find people you know on %s'), Configure::read('site.name'))));?></p>
     		<div class="js-tabs">
			<ul class="clearfix">
				<li class="received"><?php echo $this->Html->link(__l('Received Friends Requests'), '#received-request'); ?></li>
				<li class="sent"><?php echo $this->Html->link(__l('Sent Friends Requests'), '#sent-request'); ?></li>
			</ul>
			<div id="received-request">
	            <div class="friend-lst-block">
					<div class="js-tabs">
						<ul class="clearfix">
							<li class="accepted"><?php echo $this->Html->link(__l('Accepted'), '#received-accepted'); ?></li>
							<li class="pending"><?php echo $this->Html->link(__l('Pending'), '#received-pending'); ?></li>
							<li class="rejected"><?php echo $this->Html->link(__l('Rejected'), '#received-rejected'); ?></li>
						</ul>
						<div id="received-accepted" class="js-responses">
							<?php
								echo $this->element('user_friends-index', array('status' => ConstUserFriendStatus::Approved, 'type' => 'received', array('cache' => array('config' => 'site_element_cache'))));
							?>
						</div>
                        <div id="received-pending" class="js-responses">
                            <?php
                                echo $this->element('user_friends-index', array('status' => ConstUserFriendStatus::Pending, 'type' => 'received'), array('cache' => array('config' => 'site_element_cache')));
                            ?>
                        </div>
						<div id="received-rejected" class="js-responses">
							<?php
								echo $this->element('user_friends-index', array('status' => ConstUserFriendStatus::Rejected, 'type' => 'received', array('cache' => array('config' => 'site_element_cache'))));
							?>
						</div>
					</div>
				</div>
			</div>
			<div id="sent-request">
				<div class="friend-lst-block">
					<div class="js-tabs">
						<ul class="clearfix">
							<li class="accepted"><?php echo $this->Html->link(__l('Accepted'), '#sent-accepted'); ?></li>
                            <li class="pending"><?php echo $this->Html->link(__l('Pending'), '#sent-pending'); ?></li>
							<li class="rejected"><?php echo $this->Html->link(__l('Rejected'), '#sent-rejected'); ?></li>
						</ul>
						<div id="sent-accepted" class="js-responses">
							<?php
								echo $this->element('user_friends-index', array('status' => ConstUserFriendStatus::Approved, 'type' => 'sent', array('cache' => array('config' => 'site_element_cache'))));
							?>
						</div>
                        <div id="sent-pending" class="js-responses">
                            <?php
                                echo $this->element('user_friends-index', array('status' => ConstUserFriendStatus::Pending, 'type' => 'sent', array('cache' => array('config' => 'site_element_cache'))));
                            ?>
                        </div>
						<div id="sent-rejected" class="js-responses">
							<?php
								echo $this->element('user_friends-index', array('status' => ConstUserFriendStatus::Rejected, 'type' => 'sent', array('cache' => array('config' => 'site_element_cache'))));
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
     </div>
</div>