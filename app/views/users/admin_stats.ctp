<div class="users stats">
    <div>
        <h2><?php echo __l('Dashboard'); ?></h2>
        <div>
           <table class="list">
			<tr>
				<th colspan='2'>&nbsp;</th>
				<?php foreach($periods as $key => $period){ ?>
				<th>
					<?php echo $period['display']; ?>
				</th>
				<?php } ?>
			</tr>
			<?php
			foreach($models as $unique_model){ ?>
				<?php foreach($unique_model as $model => $fields){
					$aliasName = isset($fields['alias']) ? $fields['alias'] : $model;
				?>
						<?php $element = isset($fields['rowspan']) ? 'rowspan ="'.$fields['rowspan'].'"' : ''; ?>
						<?php $element .= isset($fields['colspan']) ? 'colspan ="'.$fields['colspan'].'"' : ''; ?>
						<?php if(!isset($fields['isSub'])): ?>
							<tr>
							<td class="dr sub-title" <?php echo $element;?>>
								<?php echo $fields['display']; ?>
							</td>
						<?php endif;?>
						<?php if(isset($fields['isSub'])):	?>
							<td class="dr">
								<?php echo $fields['display']; ?>
							</td>
						<?php endif; ?>
						<?php if(!isset($fields['rowspan'])): ?>
							<?php foreach($periods as $key => $period) { ?>
									<td>
										<?php
                                            if(empty($fields['type'])) {
                                                $fields['type'] = 'cInt';
                                            }
                                            if (!empty($fields['link'])):
                                                $fields['link']['stat'] = $key;
                                                echo $this->Html->link($this->Html->{$fields['type']}(${$aliasName.$key}), $fields['link'], array('escape' => false, 'title' => __l('Click to View Details')));
											else:
                                                echo $this->Html->{$fields['type']}(${$aliasName.$key});
                                            endif;
                                        ?>
									</td>
							<?php } ?>
							</tr>
						<?php endif; ?>

				 <?php } ?>
			<?php } ?>
			</table>
        </div>
    </div>
    <div>
		<h2><?php echo __l('Recently Registered Users'); ?></h2>
		<div>
            <?php
                if (!empty($recentUsers)):
                    $users = '';
                    foreach ($recentUsers as $user):
						$users .= sprintf('%s, ',$this->Html->link($this->Html->cText($user['User']['username'], false), array('controller'=> 'users', 'action' => 'view', $user['User']['username'], 'admin' => false)));
					endforeach;
					echo substr($users, 0, -2);
				else:
			?>
				<p class="notice"><?php echo __l('Recently no users registered');?></p>
			<?php
				endif;
			?>
		</div>
	</div>
	<div>
        <h2><?php echo __l('Online Users') . ' (' . $this->Html->cInt(count($onlineUsers), false) . ')'?></h2>
        <div>
            <?php
                if (!empty($onlineUsers)):
                    $users = '';
					$i=0;
                    foreach ($onlineUsers as $user):
						$users .= sprintf('%s, ',$this->Html->link($this->Html->cText($user['User']['username'], false), array('controller'=> 'users', 'action' => 'view', $user['User']['username'], 'admin' => false)));
					if($i > 10){
						break;
					}
					$i++;
					endforeach;
					echo substr($users, 0, -2);
				else:
			?>
					<p class="notice"><?php echo __l('Recently no users online');?></p>
			<?php
				endif;
			?>
		</div>
	</div>
</div>