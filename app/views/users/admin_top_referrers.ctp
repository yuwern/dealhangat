<div class="users index">
<h2><?php echo __l('Top Referring Users');?></h2>
<p>Users which referred the most people</p>
<div class="clearfix add-user1">
</div>
<div class="staticuser index">
<div class="overflow-user">
<table class="list">
    <tr>
        <th>Id</th>
        <th>Username</th>
        <th>Referral Count</th>
    </tr>

    <!-- Here is where we loop through our $users array, printing out user info -->

    <?php foreach ($top_referrers as $user): ?>
    <tr>
        <td><?php echo $user['User']['id']; ?></td>
        <td><?php echo $user['User']['username']; ?></td>
        <td><?php echo $user['User']['referred_by_user_count']; ?></td>
    </tr>
    <?php endforeach; ?>

</table>
</div>


</div>
</div>
