<div class="blocks index">
<h2><?php echo __l('Blocks');?></h2>
<div class="clearfix add-block1">
</div>
<div class="staticblock index">
<div class="overflow-block">
<?php echo $this->Html->link('Add Block', array('controller' => 'blocks', 'action' => 'add')); ?>
<table>
    <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Created</th>
        <th>&nbsp;Region</th>
    </tr>

    <!-- Here is where we loop through our $blocks array, printing out block info -->

    <?php foreach ($blocks as $block): ?>
    <tr>
        <td><?php echo $block['Block']['id']; ?></td>
        <td>
            <?php echo $this->Html->link($block['Block']['title'], array('controller' => 'blocks', 'action' => 'view', $block['Block']['id'])); ?>
        </td>
        <td><?php echo $block['Block']['created']; ?></td>
        <td>&nbsp; <?php echo $block['Block']['region']; ?></td>
        <td>
            
        <!-- <a href="blocks/delete/<?php echo $block['Block']['id']; ?>">Delete</a> -->
        
        <?php echo $this->Html->link(__l('Delete'), array('action' => 'delete', $block['Block']['id']), array('class' => 'delete js-delete', 'title' => __l('Delete')));?>

            <?php echo $this->Html->link('Edit', array('action' => 'edit', $block['Block']['id']));?>

        </td>
    </tr>
    <?php endforeach; ?>

</table>
</div>


</div>
</div>
