<?php /* SVN: $Id: admin_manage.ctp 54285 2011-05-23 10:16:38Z aravindan_111act10 $ */ ?>
<?php if(!empty($translations)): ?>
<h2><?php echo __l(sprintf('Edit Translations - %s', $languages[$this->request->data['Translation']['language_id']])); ?></h2>
<?php else: ?>
<h2><?php echo __l('Edit Translations'); ?></h2>
<?php endif; ?>
<div class="translations form">
<h3><?php echo __l('Translation Stats');?></h3>
<dl class="list clearfix">
	<dt><?php echo __l('Verified');?></dt>
		<dd><?php echo $this->Html->link($verified_count, array('controller' => 'translations', 'action' => 'manage', 'language_id' => $this->request->data['Translation']['language_id'], 'filter' => 'verified'), array('title' => __l('Verified')));?></dd>
	<dt><?php echo __l('Unverified');?></dt>
		<dd><?php echo $this->Html->link($unverified_count, array('controller' => 'translations', 'action' => 'manage', 'language_id' => $this->request->data['Translation']['language_id'], 'filter' => 'unverified'), array('title' => __l('Unverified')));?></dd>
</dl>
<div class = "notice">
	<?php echo __l('If you translated with Google Translate, it may not be perfect translation and it may have mistakes. So you need to manually check all translated texts. The translation stats will give summary of verified/unverified translated text.');?>
</div>
<?php echo $this->Form->create('Translation', array('action' => 'manage', 'class' => 'normal')); ?>
	<fieldset>
	<?php
		echo $this->Form->input('language_id');
		echo $this->Form->input('filter', array('type' => 'hidden'));
		echo $this->Form->input('q', array('label' => 'Keyword'));
		?>
		<div class="submit-block clearfix">
		<?php
		echo $this->Form->submit(__l('Submit'), array('name' => 'data[Translation][makeSubmit]'));
		?>
		</div>
		<?php
		if(!empty($translations)):
			echo $this->element('paging_counter');
		endif;		
?>

<table class="list">
<thead>
<th><?php echo __l('Verified'); ?></th>
<th><?php echo __l('Key'); ?></th>
<th><?php echo __l('Translate Text'); ?></th>
</thead>
<?php		
		if(!empty($translations)):
			foreach ($translations as $translation):
			?>
				<tr><td> <?php echo $this->Form->input('Translation.'.$translation['Translation']['id'].'.is_verified', array('checked' => ($translation['Translation']['is_verified'])?true:false, 'class' => '', 'label' => false)); ?></td>
                <td> <?php echo $translation['Translation']['key']; ?></td>
                 <td> <?php echo $this->Form->input('Translation.'.$translation['Translation']['id'].'.lang_text', array('label' => false, 'value' => $translation['Translation']['lang_text'])); ?></td>
                </tr>
		<?php	
            endforeach;
			?>
	<tr><td colspan="3">
	<div class="submit-update-block clearfix">
	            <?php 
				echo $this->Form->submit(__l('Update'), array('name' => 'data[Translation][makeUpdate]'));
			?>  
			</div>
</td>
	</tr>
            

            <?php
		else:
	?>
	<tr><td colspan="2">
	<?php echo __l('No translations available');?></td>
	</tr>
	<?php endif;?>
    </table>
	<?php  	if(!empty($translations)):
    			echo $this->element('paging_links');
			endif;
	?>

	</fieldset>
	<?php echo $this->Form->end(); ?>
</div>