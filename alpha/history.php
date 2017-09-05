<?php include_once '_top.php'; ?>

<?php require_once('../db.php'); ?>

<table>
<thead>
  <tr>
  <td>Revision</td>
  <td>User</td>
  <td></td>
  </tr>
  </thead>
<?php foreach(get_entry_history($_GET['entry_id']) AS $entry): ?>
<tr>
<?php $url = "view.php?entry_id=" . $_GET['entry_id'] . "&revision_id=" . $entry->_id; ?>
<td><a href="<?php echo $url ?>"><?php echo $entry->_id; ?></a></td>
<td><?php echo $entry->user->id; ?></td>
</tr>
<?php endforeach; ?>
</table>


<?php include_once('_bottom.php'); ?>