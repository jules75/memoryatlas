<?php include_once '_top.php'; ?>

<?php require_once('../db.php'); ?>

<?php

function mongoIdToTimestamp($id) {
  return hexdec(substr($id, 0, 8));
}

?>

<table>
<thead>
  <tr>
  <td>Updated on</td>
  <td>By user</td>
  <td></td>
  </tr>
  </thead>
<?php foreach(get_entry_history($_GET['entry_id']) AS $entry): ?>
<tr>
<?php $url = "view.php?entry_id=" . $_GET['entry_id'] . "&revision_id=" . $entry->_id; ?>
<td><a href="<?php echo $url ?>"><?php echo date('Y-m-d H:i:s', mongoIdToTimestamp($entry->_id)); ?></a></td>
<td><?php echo $entry->user->id; ?></td>
</tr>
<?php endforeach; ?>
</table>


<?php include_once('_bottom.php'); ?>